<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Refund;
use Core\CSRF;
use Core\Controller;
use Core\Logger;
use Core\Response;
use Core\Security;
use Core\Session;

class RefundController extends Controller
{
    protected Order $orders;
    protected Refund $refunds;

    public function __construct()
    {
        parent::__construct();
        $this->orders = new Order();
        $this->refunds = new Refund();
    }

    public function show(array $params = []): void
    {
        Session::start();
        $customerId = (int)Session::get('user_id', 0);
        $orderNo = isset($params['orderId']) ? Security::sanitize((string)$params['orderId']) : '';
        $order = $orderNo !== '' ? $this->orders->findWithItemsByNo($orderNo) : null;

        if (!$order || (int)($order['customer_id'] ?? 0) !== $customerId) {
            Session::flash('error', 'İade talep edilecek sipariş bulunamadı.');
            Response::redirect('/siparislerim');
        }

        $existing = $this->refunds->findByOrder((int)$order['id']);
        if ($existing && !in_array($existing['status'], ['rejected', 'processed'], true)) {
            Session::flash('warning', 'Bu sipariş için zaten bir iade talebi mevcut.');
            Response::redirect('/siparislerim');
        }

        $this->render('customer/refund', [
            'title' => 'İade Talebi',
            'topbarTitle' => 'İade Talebi',
            'order' => $order,
            'breadcrumbs' => [
                ['label' => 'Siparişlerim', 'url' => '/siparislerim'],
                ['label' => 'İade Talebi'],
            ],
        ]);
    }

    public function submit(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/siparislerim');
        }

        Session::start();
        $customerId = (int)Session::get('user_id', 0);
        $orderNo = Security::sanitize($params['orderId'] ?? '');
        if ($orderNo === '') {
            Session::flash('error', 'Sipariş numarası eksik.');
            Response::redirect('/siparislerim');
        }

        $order = $this->orders->findWithItemsByNo($orderNo);
        if (!$order || (int)($order['customer_id'] ?? 0) !== $customerId) {
            Session::flash('error', 'Bu sipariş size ait değil.');
            Response::redirect('/siparislerim');
        }

        if (($order['status'] ?? '') !== 'paid') {
            Session::flash('error', 'Yalnızca ödemesi tamamlanan siparişler için iade talep edebilirsiniz.');
            Response::redirect('/siparislerim');
        }

        $existing = $this->refunds->findByOrder((int)$order['id']);
        if ($existing && !in_array($existing['status'], ['rejected', 'processed'], true)) {
            Session::flash('warning', 'Mevcut bir iade talebiniz bulunuyor.');
            Response::redirect('/siparislerim');
        }

        $input = $this->input();
        $reason = trim($input['reason'] ?? '');
        if ($reason === '' || mb_strlen($reason) < 10) {
            Session::flash('error', 'Lütfen en az 10 karakterlik ayrıntılı bir açıklama girin.');
            Response::redirect('/iade-talebi/' . rawurlencode($orderNo));
        }

        $cleanReason = Security::sanitize($reason);
        $refundId = $this->refunds->createRequest((int)$order['id'], $customerId, $cleanReason);
        Logger::info('Müşteri iade talebi oluşturdu', [
            'order_no' => $orderNo,
            'refund_id' => $refundId,
            'customer_id' => $customerId,
        ]);

        Session::flash('success', 'İade talebiniz alındı. Destek ekibimiz kısa sürede inceleyecektir.');
        Response::redirect('/siparislerim');
    }
}
