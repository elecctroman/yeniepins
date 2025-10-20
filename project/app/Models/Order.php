<?php

namespace App\Models;

use Core\Model;
use PDO;

class Order extends Model
{
    public function nextOrderNo(): string
    {
        do {
            $orderNo = 'Y' . date('Ymd') . random_int(100000, 999999);
            $stmt = $this->query('SELECT 1 FROM orders WHERE order_no = :order_no LIMIT 1', [
                'order_no' => $orderNo,
            ]);
        } while ($stmt->fetch());

        return $orderNo;
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function createOrder(array $data, array $items): array
    {
        $pdo = $this->db();
        $orderNo = $data['order_no'] ?? $this->nextOrderNo();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare('INSERT INTO orders (order_no, customer_id, email, subtotal, tax_total, discount_total, total, coupon_code, status, payment_method, payment_ref, created_at) '
                . 'VALUES (:order_no, :customer_id, :email, :subtotal, :tax_total, :discount_total, :total, :coupon_code, :status, :payment_method, :payment_ref, NOW())');
            $stmt->execute([
                ':order_no' => $orderNo,
                ':customer_id' => $data['customer_id'],
                ':email' => $data['email'],
                ':subtotal' => $this->formatAmount($data['subtotal'] ?? 0.0),
                ':tax_total' => $this->formatAmount($data['tax_total'] ?? 0.0),
                ':discount_total' => $this->formatAmount($data['discount_total'] ?? 0.0),
                ':total' => $this->formatAmount($data['total'] ?? 0.0),
                ':coupon_code' => $data['coupon_code'] ?? null,
                ':status' => $data['status'] ?? 'pending',
                ':payment_method' => $data['payment_method'] ?? 'sandbox',
                ':payment_ref' => $data['payment_ref'] ?? null,
            ]);

            $orderId = (int)$pdo->lastInsertId();
            $itemIds = [];
            $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, variant_id, qty, unit_price, tax_rate, delivered_json) '
                . 'VALUES (:order_id, :product_id, :variant_id, :qty, :unit_price, :tax_rate, NULL)');

            foreach ($items as $item) {
                $itemStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':variant_id' => $item['variant_id'] ?? null,
                    ':qty' => $item['qty'],
                    ':unit_price' => $this->formatAmount($item['unit_price']),
                    ':tax_rate' => $this->formatAmount($item['tax_rate'] ?? 0.0),
                ]);
                $itemIds[] = (int)$pdo->lastInsertId();
            }

            $pdo->commit();

            return [
                'order_id' => $orderId,
                'order_no' => $orderNo,
                'item_ids' => $itemIds,
            ];
        } catch (\Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }
    }

    public function getOrdersForCustomer(int $customerId): array
    {
        $sql = 'SELECT o.*, COUNT(oi.id) AS item_count FROM orders o '
            . 'LEFT JOIN order_items oi ON oi.order_id = o.id '
            . 'WHERE o.customer_id = :customer_id '
            . 'GROUP BY o.id '
            . 'ORDER BY o.created_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':customer_id', $customerId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function getDeliveredItemsForCustomer(int $customerId): array
    {
        $sql = 'SELECT oi.*, o.order_no, o.created_at AS order_created_at, p.name AS product_name, p.type, p.delivery '
            . 'FROM order_items oi '
            . 'INNER JOIN orders o ON o.id = oi.order_id '
            . 'INNER JOIN products p ON p.id = oi.product_id '
            . 'WHERE o.customer_id = :customer_id AND oi.delivered_json IS NOT NULL '
            . 'ORDER BY o.created_at DESC, oi.id DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':customer_id', $customerId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll() ?: [];

        foreach ($rows as &$row) {
            $row['delivered_data'] = $this->decodePayload($row['delivered_json'] ?? null);
        }

        return $rows;
    }

    public function findWithItemsByNo(string $orderNo): ?array
    {
        $stmt = $this->query('SELECT o.*, u.name AS customer_name, u.email AS customer_email '
            . 'FROM orders o LEFT JOIN users u ON u.id = o.customer_id '
            . 'WHERE o.order_no = :order_no LIMIT 1', [
                'order_no' => $orderNo,
            ]);
        $order = $stmt->fetch();
        if (!$order) {
            return null;
        }

        $order['items'] = $this->getItems((int)$order['id']);
        return $order;
    }

    public function all(int $limit = 50): array
    {
        $sql = 'SELECT o.*, u.name AS customer_name, u.email AS customer_email, COUNT(oi.id) AS item_count '
            . 'FROM orders o '
            . 'LEFT JOIN users u ON u.id = o.customer_id '
            . 'LEFT JOIN order_items oi ON oi.order_id = o.id '
            . 'GROUP BY o.id '
            . 'ORDER BY o.created_at DESC '
            . 'LIMIT :limit';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function markPaid(int $orderId, string $paymentMethod, string $paymentRef): bool
    {
        $stmt = $this->db()->prepare('UPDATE orders SET status = "paid", payment_method = :payment_method, payment_ref = :payment_ref, paid_at = NOW() '
            . 'WHERE id = :id');

        return $stmt->execute([
            ':payment_method' => $paymentMethod,
            ':payment_ref' => $paymentRef,
            ':id' => $orderId,
        ]);
    }

    public function markFailed(int $orderId, string $paymentMethod, string $paymentRef, string $status = 'failed'): bool
    {
        $stmt = $this->db()->prepare('UPDATE orders SET status = :status, payment_method = :payment_method, payment_ref = :payment_ref '
            . 'WHERE id = :id');

        return $stmt->execute([
            ':status' => $status,
            ':payment_method' => $paymentMethod,
            ':payment_ref' => $paymentRef,
            ':id' => $orderId,
        ]);
    }

    public function updateStatus(int $orderId, string $status): bool
    {
        $stmt = $this->db()->prepare('UPDATE orders SET status = :status WHERE id = :id');

        return $stmt->execute([
            ':status' => $status,
            ':id' => $orderId,
        ]);
    }

    public function saveDelivery(int $orderItemId, array $payload): bool
    {
        $stmt = $this->db()->prepare('UPDATE order_items SET delivered_json = :payload WHERE id = :id');

        return $stmt->execute([
            ':payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ':id' => $orderItemId,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getItems(int $orderId): array
    {
        $sql = 'SELECT oi.*, p.name AS product_name, p.type, p.delivery, pv.name AS variant_name '
            . 'FROM order_items oi '
            . 'INNER JOIN products p ON p.id = oi.product_id '
            . 'LEFT JOIN product_variants pv ON pv.id = oi.variant_id '
            . 'WHERE oi.order_id = :order_id '
            . 'ORDER BY oi.id ASC';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll() ?: [];

        foreach ($items as &$item) {
            $item['delivered_data'] = $this->decodePayload($item['delivered_json'] ?? null);
        }

        return $items;
    }

    protected function decodePayload(?string $payload): ?array
    {
        if ($payload === null || $payload === '') {
            return null;
        }

        $decoded = json_decode($payload, true);
        return is_array($decoded) ? $decoded : null;
    }

    protected function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }
}
