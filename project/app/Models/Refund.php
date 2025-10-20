<?php

namespace App\Models;

use Core\Model;
use PDO;

class Refund extends Model
{
    public function createRequest(int $orderId, int $customerId, string $reason): int
    {
        $sql = 'INSERT INTO refunds (order_id, reason, status, created_at) VALUES (:order_id, :reason, :status, NOW())';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->bindValue(':reason', $reason);
        $stmt->bindValue(':status', 'requested');
        $stmt->execute();

        return (int)$this->db()->lastInsertId();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->query('SELECT r.*, o.order_no, o.customer_id, o.total, o.status AS order_status, u.email AS customer_email, u.name AS customer_name '
            . 'FROM refunds r '
            . 'INNER JOIN orders o ON o.id = r.order_id '
            . 'LEFT JOIN users u ON u.id = o.customer_id '
            . 'WHERE r.id = :id LIMIT 1', [
                'id' => $id,
            ]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function findByOrder(int $orderId): ?array
    {
        $stmt = $this->query('SELECT * FROM refunds WHERE order_id = :order_id ORDER BY id DESC LIMIT 1', [
            'order_id' => $orderId,
        ]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function forCustomer(int $customerId): array
    {
        $sql = 'SELECT r.*, o.order_no, o.total, o.created_at AS order_created_at '
            . 'FROM refunds r '
            . 'INNER JOIN orders o ON o.id = r.order_id '
            . 'WHERE o.customer_id = :customer_id '
            . 'ORDER BY r.created_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':customer_id', $customerId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function all(?string $status = null, int $limit = 200): array
    {
        $sql = 'SELECT r.*, o.order_no, o.total, o.payment_method, o.customer_id, u.email AS customer_email '
            . 'FROM refunds r '
            . 'INNER JOIN orders o ON o.id = r.order_id '
            . 'LEFT JOIN users u ON u.id = o.customer_id ';
        $params = [];
        if ($status !== null) {
            $sql .= 'WHERE r.status = :status ';
            $params['status'] = $status;
        }
        $sql .= 'ORDER BY r.created_at DESC LIMIT :limit';
        $stmt = $this->db()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function updateStatus(int $refundId, string $status): bool
    {
        $stmt = $this->db()->prepare("UPDATE refunds SET status = :status, processed_at = CASE WHEN :status IN ('approved', 'rejected', 'processed') THEN NOW() ELSE processed_at END WHERE id = :id");
        return $stmt->execute([
            ':status' => $status,
            ':id' => $refundId,
        ]);
    }
}
