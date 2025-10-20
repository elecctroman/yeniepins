<?php

namespace App\Models;

use Core\Model;
use PDO;

class Report extends Model
{
    public function salesSummary(): array
    {
        return [
            'daily' => $this->aggregateForPeriod('1 DAY'),
            'weekly' => $this->aggregateForPeriod('7 DAY'),
            'monthly' => $this->aggregateForPeriod('30 DAY'),
        ];
    }

    public function couponUsage(string $period = '30 DAY'): array
    {
        $sql = 'SELECT coupon_code, COUNT(*) AS usage_count, SUM(discount_total) AS total_discount '
            . 'FROM orders '
            . 'WHERE coupon_code IS NOT NULL AND coupon_code != "" '
            . 'AND created_at >= (NOW() - INTERVAL ' . $period . ') '
            . 'GROUP BY coupon_code '
            . 'ORDER BY usage_count DESC';
        $stmt = $this->db()->query($sql);

        return $stmt->fetchAll() ?: [];
    }

    public function topProducts(int $limit = 5): array
    {
        $sql = 'SELECT p.name, SUM(oi.qty) AS total_qty, SUM(oi.qty * oi.unit_price) AS revenue '
            . 'FROM order_items oi '
            . 'INNER JOIN orders o ON o.id = oi.order_id '
            . 'INNER JOIN products p ON p.id = oi.product_id '
            . 'WHERE o.status IN ("paid", "processed", "completed") '
            . 'GROUP BY p.id '
            . 'ORDER BY total_qty DESC '
            . 'LIMIT :limit';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function lowStockAlerts(int $threshold = 5): array
    {
        $sql = 'SELECT p.id, p.name, p.type, '
            . '((SELECT COUNT(*) FROM epin_keys ek WHERE ek.product_id = p.id AND ek.status = "available") '
            . '+ (SELECT COUNT(*) FROM accounts acc WHERE acc.product_id = p.id AND acc.status = "available")) AS available_keys '
            . 'FROM products p '
            . 'WHERE p.stock_policy = "track" '
            . 'HAVING available_keys <= :threshold '
            . 'ORDER BY available_keys ASC';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    protected function aggregateForPeriod(string $interval): array
    {
        $sql = 'SELECT COALESCE(SUM(total), 0) AS total, COALESCE(SUM(tax_total), 0) AS tax_total, '
            . 'COALESCE(SUM(discount_total), 0) AS discount_total, COUNT(*) AS order_count '
            . 'FROM orders '
            . 'WHERE status IN ("paid", "processed", "completed") '
            . 'AND created_at >= (NOW() - INTERVAL ' . $interval . ')';
        $stmt = $this->db()->query($sql);
        $row = $stmt->fetch();

        return [
            'total' => (float)($row['total'] ?? 0),
            'tax_total' => (float)($row['tax_total'] ?? 0),
            'discount_total' => (float)($row['discount_total'] ?? 0),
            'order_count' => (int)($row['order_count'] ?? 0),
        ];
    }
}
