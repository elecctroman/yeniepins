<?php

namespace App\Controllers;

use App\Models\AccountStock;
use App\Models\EpinKey;
use App\Models\Order;
use App\Models\Product;
use App\Models\Variant;
use Core\CSRF;
use Core\Controller;
use Core\Response;
use Core\Security;
use Core\Session;
use function money_fmt;

class OrderController extends Controller
{
    protected Order $orders;
    protected Product $products;
    protected Variant $variants;
    protected EpinKey $epinKeys;
    protected AccountStock $accountStock;
    protected string $reservationFile;

    public function __construct()
    {
        parent::__construct();
        $this->orders = new Order();
        $this->products = new Product();
        $this->variants = new Variant();
        $this->epinKeys = new EpinKey();
        $this->accountStock = new AccountStock();
        $this->reservationFile = __DIR__ . '/../../storage/cache/order_reservations.json';
        $this->ensureReservationStorage();
        require_once __DIR__ . '/../../config/mail.php';
    }

    public function create(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/odeme');
        }

        Session::start();
        $userId = (int)Session::get('user_id', 0);
        if ($userId <= 0) {
            Session::flash('error', 'Sipariş oluşturmak için giriş yapmalısınız.');
            Response::redirect('/giris');
        }

        $cart = Session::get('cart', ['items' => []]);
        $items = $cart['items'] ?? [];
        if (!$items) {
            Session::flash('error', 'Sepetinizde ürün bulunmuyor.');
            Response::redirect('/sepet');
        }

        $input = $this->input();
        $paymentMethod = isset($input['payment_method']) && $input['payment_method'] !== ''
            ? substr(Security::sanitize($input['payment_method']), 0, 50)
            : 'sandbox';
        $couponCode = $cart['coupon']['code'] ?? null;
        if ($couponCode) {
            $couponCode = Security::sanitize((string)$couponCode);
        }

        $orderNo = $this->orders->nextOrderNo();
        $subtotal = 0.0;
        $taxTotal = 0.0;
        $discountTotal = isset($cart['discount_total']) ? (float)$cart['discount_total'] : 0.0;
        $orderItemsPayload = [];
        $reservationsQueue = [];

        foreach ($items as $item) {
            $productId = (int)($item['product_id'] ?? 0);
            $variantId = isset($item['variant_id']) && $item['variant_id'] !== null
                ? (int)$item['variant_id']
                : null;
            $quantity = max(1, (int)($item['qty'] ?? 1));

            $product = $this->products->findById($productId);
            if (!$product || ($product['status'] ?? '') !== 'active') {
                $this->releaseReservationQueue($reservationsQueue);
                Session::flash('error', 'Sepetteki bir ürün artık aktif değil.');
                Response::redirect('/sepet');
            }

            $variant = null;
            if ($variantId) {
                $variant = $this->variants->find($variantId);
                if (!$variant || (int)$variant['product_id'] !== $productId) {
                    $this->releaseReservationQueue($reservationsQueue);
                    Session::flash('error', 'Seçili varyant geçersiz hale gelmiş.');
                    Response::redirect('/sepet');
                }
            }

            $available = $this->products->availableStock($productId, $variantId);
            if (($product['stock_policy'] ?? 'track') === 'track'
                && $available !== PHP_INT_MAX
                && $quantity > $available) {
                $this->releaseReservationQueue($reservationsQueue);
                Session::flash('error', '"' . ($product['name'] ?? 'Ürün') . '" için yeterli stok bulunmuyor.');
                Response::redirect('/sepet');
            }

            $unitPrice = (float)$product['price'];
            if ($variant && $variant['price_override'] !== null) {
                $unitPrice = (float)$variant['price_override'];
            }

            $lineSubtotal = $unitPrice * $quantity;
            $lineTax = $lineSubtotal * ((float)($product['tax_rate'] ?? 0) / 100);
            $subtotal += $lineSubtotal;
            $taxTotal += $lineTax;

            $orderItemsPayload[] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'qty' => $quantity,
                'unit_price' => $unitPrice,
                'tax_rate' => (float)($product['tax_rate'] ?? 0),
                'type' => $product['type'],
                'delivery' => $product['delivery'],
            ];
            $currentIndex = array_key_last($orderItemsPayload);

            if (($product['stock_policy'] ?? 'track') === 'track'
                && ($product['delivery'] ?? 'instant') === 'instant'
                && in_array($product['type'], ['epin', 'license', 'account'], true)) {
                $token = $this->buildReservationToken($orderNo, (int)$currentIndex);
                if (in_array($product['type'], ['epin', 'license'], true)) {
                    $reserved = $this->epinKeys->reserveKeys($productId, $quantity, $token);
                    if (count($reserved) < $quantity) {
                        $this->epinKeys->releaseReservation($token);
                        $this->releaseReservationQueue($reservationsQueue);
                        Session::flash('error', '"' . ($product['name'] ?? 'Ürün') . '" için stok ayrılırken sorun oluştu.');
                        Response::redirect('/sepet');
                    }
                    $reservationsQueue[$currentIndex] = [
                        'type' => 'epin',
                        'token' => $token,
                        'product_id' => $productId,
                        'qty' => $quantity,
                    ];
                } elseif ($product['type'] === 'account') {
                    $reserved = $this->accountStock->reserveAccounts($productId, $quantity, $token);
                    if (count($reserved) < $quantity) {
                        $this->accountStock->releaseReservation($token);
                        $this->releaseReservationQueue($reservationsQueue);
                        Session::flash('error', 'Hesap stokları rezerve edilemedi.');
                        Response::redirect('/sepet');
                    }
                    $reservationsQueue[$currentIndex] = [
                        'type' => 'account',
                        'token' => $token,
                        'product_id' => $productId,
                        'qty' => $quantity,
                    ];
                }
            }
        }

        $total = max(0.0, $subtotal + $taxTotal - $discountTotal);

        try {
            $result = $this->orders->createOrder([
                'order_no' => $orderNo,
                'customer_id' => $userId,
                'email' => (string)Session::get('user_email', ''),
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'discount_total' => $discountTotal,
                'total' => $total,
                'coupon_code' => $couponCode,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
            ], $orderItemsPayload);
        } catch (\Throwable $exception) {
            $this->releaseReservationQueue($reservationsQueue);
            Session::flash('error', 'Sipariş oluşturulurken bir hata meydana geldi.');
            Response::redirect('/sepet');
        }

        $reservationMap = [];
        foreach ($result['item_ids'] as $index => $orderItemId) {
            if (isset($reservationsQueue[$index])) {
                $reservationMap[$orderItemId] = $reservationsQueue[$index];
            }
        }

        if ($reservationMap) {
            $reservations = $this->loadOrderReservations();
            $reservations[$result['order_no']] = [
                'order_id' => $result['order_id'],
                'items' => $reservationMap,
                'created_at' => time(),
            ];
            $this->saveOrderReservations($reservations);
        }

        Session::set('cart', ['items' => []]);
        Session::set('last_order_no', $result['order_no']);
        Session::flash('success', 'Siparişiniz oluşturuldu. Ödeme adımına yönlendiriliyorsunuz.');
        Response::redirect('/odeme?order_no=' . rawurlencode($result['order_no']));
    }

    public function paymentCallback(array $params = []): void
    {
        $gateway = $params['gateway'] ?? 'sandbox';
        $input = $this->input();
        $orderNo = Security::sanitize($input['order_no'] ?? '');
        if ($orderNo === '') {
            $this->json([
                'status' => 'error',
                'message' => 'Sipariş numarası bulunamadı.',
            ], 422);
            return;
        }

        $status = strtolower($input['status'] ?? 'failed');
        $paymentRef = $input['payment_ref'] ?? ($gateway . '-' . substr(Security::generateToken(6), 0, 12));

        $order = $this->orders->findWithItemsByNo($orderNo);
        if (!$order) {
            $this->json([
                'status' => 'error',
                'message' => 'Sipariş bulunamadı.',
            ], 404);
            return;
        }

        if (in_array($status, ['success', 'paid', 'completed'], true)) {
            $delivery = $this->finalizeOrder($order, $gateway, $paymentRef);
            $this->json([
                'status' => 'ok',
                'order_no' => $orderNo,
                'message' => 'Sipariş teslim edildi.',
                'delivered' => $delivery,
            ]);
            return;
        }

        $state = match ($status) {
            'cancelled', 'canceled' => 'cancelled',
            'refunded' => 'refunded',
            default => 'failed',
        };

        $this->orders->markFailed((int)$order['id'], $gateway, $paymentRef, $state);
        $this->releaseOrderReservations($orderNo);

        $this->json([
            'status' => 'ok',
            'order_no' => $orderNo,
            'message' => 'Ödeme durumu güncellendi: ' . $state,
        ]);
    }

    public function adminRedeliver(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/admin/siparisler');
        }

        $orderNo = Security::sanitize($params['orderNo'] ?? '');
        if ($orderNo === '') {
            Session::flash('error', 'Sipariş numarası geçersiz.');
            Response::redirect('/admin/siparisler');
        }

        $order = $this->orders->findWithItemsByNo($orderNo);
        if (!$order) {
            Session::flash('error', 'Sipariş bulunamadı.');
            Response::redirect('/admin/siparisler');
        }

        $hasDelivery = false;
        foreach ($order['items'] as $item) {
            if (!empty($item['delivered_data'])) {
                $hasDelivery = true;
                break;
            }
        }

        if (!$hasDelivery) {
            Session::flash('warning', 'Bu sipariş için otomatik teslimat bulunmuyor.');
            Response::redirect('/admin/siparisler?no=' . rawurlencode($orderNo));
        }

        $message = $this->composeDeliverySummary($order, $order['items']);
        $email = $order['email'] ?? $order['customer_email'] ?? null;
        if ($email) {
            if (class_exists('\\MailerStub')) {
                \MailerStub::send($email, 'Siparişiniz teslim edildi', $message);
            }
            Session::flash('success', 'Teslimat bilgileri yeniden gönderildi.');
        } else {
            Session::flash('warning', 'Müşteriye ait e-posta adresi bulunamadı.');
        }

        Response::redirect('/admin/siparisler?no=' . rawurlencode($orderNo));
    }

    public function adminInitiateRefund(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/admin/siparisler');
        }

        $orderNo = Security::sanitize($params['orderNo'] ?? '');
        if ($orderNo === '') {
            Session::flash('error', 'Sipariş numarası geçersiz.');
            Response::redirect('/admin/siparisler');
        }

        $order = $this->orders->findWithItemsByNo($orderNo);
        if (!$order) {
            Session::flash('error', 'Sipariş bulunamadı.');
            Response::redirect('/admin/siparisler');
        }

        $this->releaseOrderReservations($orderNo);
        $this->orders->updateStatus((int)$order['id'], 'refunded');
        Session::flash('success', 'İade süreci başlatıldı.');
        Response::redirect('/admin/siparisler?no=' . rawurlencode($orderNo));
    }

    protected function finalizeOrder(array $order, string $gateway, string $paymentRef): array
    {
        $reservations = $this->loadOrderReservations();
        $reservationItems = $reservations[$order['order_no']]['items'] ?? [];
        $updatedItems = [];

        foreach ($order['items'] as $item) {
            $deliveryData = $item['delivered_data'] ?? null;
            if (isset($reservationItems[$item['id']])) {
                $reservation = $reservationItems[$item['id']];
                if (in_array($reservation['type'], ['epin', 'license'], true)) {
                    $keys = $this->epinKeys->markAsSold($reservation['token']);
                    $codes = [];
                    foreach ($keys as $key) {
                        if (!empty($key['code'])) {
                            $codes[] = $key['code'];
                        }
                    }
                    $deliveryData = ['keys' => $codes];
                } elseif ($reservation['type'] === 'account') {
                    $accounts = $this->accountStock->markAsSold($reservation['token']);
                    if (count($accounts) === 1) {
                        $account = $accounts[0];
                        $deliveryData = [
                            'username' => $account['username'] ?? '',
                            'password' => $account['password'] ?? '',
                            'extra' => !empty($account['extra']) ? $account['extra'] : new \stdClass(),
                        ];
                    } else {
                        $deliveryData = [
                            'accounts' => array_map(static function (array $account) {
                                return [
                                    'username' => $account['username'] ?? '',
                                    'password' => $account['password'] ?? '',
                                    'extra' => !empty($account['extra']) ? $account['extra'] : new \stdClass(),
                                ];
                            }, $accounts),
                        ];
                    }
                }

                if ($deliveryData !== null) {
                    $this->orders->saveDelivery((int)$item['id'], $deliveryData);
                }
            }

            if ($deliveryData !== null) {
                $item['delivered_data'] = $deliveryData;
            }
            $updatedItems[] = $item;
        }

        if (isset($reservations[$order['order_no']])) {
            unset($reservations[$order['order_no']]);
            $this->saveOrderReservations($reservations);
        }

        $this->orders->markPaid((int)$order['id'], $gateway, $paymentRef);

        $message = $this->composeDeliverySummary($order, $updatedItems);
        $email = $order['email'] ?? $order['customer_email'] ?? null;
        if ($email && class_exists('\\MailerStub')) {
            \MailerStub::send($email, 'Siparişiniz teslim edildi', $message);
        }

        return [
            'status' => 'paid',
            'items' => array_map(static function (array $item) {
                return [
                    'id' => $item['id'],
                    'product_name' => $item['product_name'] ?? '',
                    'delivered' => $item['delivered_data'] ?? null,
                ];
            }, $updatedItems),
        ];
    }

    protected function composeDeliverySummary(array $order, array $items): string
    {
        $lines = [
            'Siparişiniz teslim edildi.',
            'Sipariş No: ' . ($order['order_no'] ?? ''),
        ];

        $total = isset($order['total']) ? (float)$order['total'] : null;
        if ($total !== null) {
            $formatted = function_exists('money_fmt')
                ? money_fmt($total)
                : number_format($total, 2, ',', '.');
            $lines[] = 'Sipariş Tutarı: ' . $formatted . ' ₺';
        }

        $lines[] = '';
        foreach ($items as $item) {
            $line = ($item['product_name'] ?? 'Ürün') . ' x' . ($item['qty'] ?? 1);
            if (!empty($item['variant_name'])) {
                $line .= ' (' . $item['variant_name'] . ')';
            }
            $lines[] = $line;

            $delivery = $item['delivered_data'] ?? null;
            if (is_array($delivery) && isset($delivery['keys']) && is_array($delivery['keys'])) {
                foreach ($delivery['keys'] as $code) {
                    $lines[] = '  - ' . $code;
                }
            } elseif (is_array($delivery) && isset($delivery['accounts']) && is_array($delivery['accounts'])) {
                foreach ($delivery['accounts'] as $account) {
                    $lines[] = '  - Kullanıcı: ' . ($account['username'] ?? '') . ' / Parola: ' . ($account['password'] ?? '');
                }
            } elseif (is_array($delivery) && isset($delivery['username'])) {
                $lines[] = '  - Kullanıcı: ' . ($delivery['username'] ?? '') . ' / Parola: ' . ($delivery['password'] ?? '');
            } else {
                $lines[] = '  - Teslimat detayları panelinizde görüntülenebilir.';
            }
        }

        $lines[] = '';
        $lines[] = 'Bizi tercih ettiğiniz için teşekkür ederiz.';

        return implode("\n", $lines);
    }

    protected function releaseReservationQueue(array $queue): void
    {
        foreach ($queue as $reservation) {
            if (!is_array($reservation) || empty($reservation['token'])) {
                continue;
            }
            if (($reservation['type'] ?? '') === 'account') {
                $this->accountStock->releaseReservation($reservation['token']);
            } else {
                $this->epinKeys->releaseReservation($reservation['token']);
            }
        }
    }

    protected function releaseOrderReservations(string $orderNo): void
    {
        $reservations = $this->loadOrderReservations();
        if (!isset($reservations[$orderNo])) {
            return;
        }

        foreach ($reservations[$orderNo]['items'] as $reservation) {
            if (($reservation['type'] ?? '') === 'account') {
                $this->accountStock->releaseReservation($reservation['token'] ?? '');
            } else {
                $this->epinKeys->releaseReservation($reservation['token'] ?? '');
            }
        }

        unset($reservations[$orderNo]);
        $this->saveOrderReservations($reservations);
    }

    protected function loadOrderReservations(): array
    {
        $this->ensureReservationStorage();
        $contents = (string)@file_get_contents($this->reservationFile);
        if ($contents === '') {
            return [];
        }

        $data = json_decode($contents, true);
        return is_array($data) ? $data : [];
    }

    protected function saveOrderReservations(array $reservations): void
    {
        $this->ensureReservationStorage();
        file_put_contents(
            $this->reservationFile,
            json_encode($reservations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }

    protected function ensureReservationStorage(): void
    {
        $directory = dirname($this->reservationFile);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        if (!file_exists($this->reservationFile)) {
            file_put_contents($this->reservationFile, json_encode([], JSON_UNESCAPED_UNICODE), LOCK_EX);
        }
    }

    protected function buildReservationToken(string $orderNo, int $index): string
    {
        return $orderNo . '-' . ($index + 1) . '-' . substr(Security::generateToken(8), 0, 16);
    }
}
