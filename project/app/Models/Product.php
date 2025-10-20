<?php

namespace App\Models;

use Core\Model;
use PDO;

class Product extends Model
{
    public function getCatalog(?int $categoryId = null, int $limit = 0, ?string $status = 'active'): array
    {
        $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p '
            . 'LEFT JOIN categories c ON c.id = p.category_id WHERE 1=1';
        $params = [];

        if ($status !== null) {
            $sql .= ' AND p.status = :status';
            $params['status'] = $status;
        }

        if ($categoryId) {
            $sql .= ' AND p.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }

        $sql .= ' ORDER BY p.created_at DESC';
        if ($limit > 0) {
            $sql .= ' LIMIT :limit';
        }

        $stmt = $this->db()->prepare($sql);
        foreach ($params as $key => $value) {
            $param = ':' . $key;
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($param, $value, $type);
        }
        if ($limit > 0) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    public function getBySlug(string $slug): ?array
    {
        $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p '
            . 'LEFT JOIN categories c ON c.id = p.category_id '
            . 'WHERE p.slug = :slug LIMIT 1';
        $stmt = $this->query($sql, ['slug' => $slug]);
        $product = $stmt->fetch();
        if (!$product) {
            return null;
        }

        $product['variants'] = $this->getVariants((int)$product['id']);
        return $product;
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM products WHERE id = :id LIMIT 1';
        $stmt = $this->query($sql, ['id' => $id]);
        $product = $stmt->fetch();
        if (!$product) {
            return null;
        }

        $product['variants'] = $this->getVariants((int)$product['id']);
        return $product;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO products (category_id, type, name, slug, description, price, tax_rate, stock_policy, delivery, min_qty, max_qty, status, created_at) '
            . 'VALUES (:category_id, :type, :name, :slug, :description, :price, :tax_rate, :stock_policy, :delivery, :min_qty, :max_qty, :status, NOW())';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([
            ':category_id' => $data['category_id'] ?? null,
            ':type' => $data['type'],
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'] ?? null,
            ':price' => $data['price'],
            ':tax_rate' => $data['tax_rate'],
            ':stock_policy' => $data['stock_policy'],
            ':delivery' => $data['delivery'],
            ':min_qty' => $data['min_qty'],
            ':max_qty' => $data['max_qty'],
            ':status' => $data['status'],
        ]);

        return (int)$this->db()->lastInsertId();
    }

    public function updateProduct(int $id, array $data): bool
    {
        $sql = 'UPDATE products SET category_id = :category_id, type = :type, name = :name, slug = :slug, description = :description, '
            . 'price = :price, tax_rate = :tax_rate, stock_policy = :stock_policy, delivery = :delivery, '
            . 'min_qty = :min_qty, max_qty = :max_qty, status = :status WHERE id = :id';
        $stmt = $this->db()->prepare($sql);

        return $stmt->execute([
            ':category_id' => $data['category_id'] ?? null,
            ':type' => $data['type'],
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'] ?? null,
            ':price' => $data['price'],
            ':tax_rate' => $data['tax_rate'],
            ':stock_policy' => $data['stock_policy'],
            ':delivery' => $data['delivery'],
            ':min_qty' => $data['min_qty'],
            ':max_qty' => $data['max_qty'],
            ':status' => $data['status'],
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db()->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function getVariants(int $productId): array
    {
        $variantModel = new Variant();
        return $variantModel->forProduct($productId);
    }

    public function availableStock(int $productId, ?int $variantId = null): int
    {
        $product = $this->findById($productId);
        if (!$product) {
            return 0;
        }

        if ($product['stock_policy'] === 'unlimited') {
            return PHP_INT_MAX;
        }

        if ($variantId) {
            $variantModel = new Variant();
            $variant = $variantModel->find($variantId);
            if (!$variant || (int)$variant['product_id'] !== $productId) {
                return 0;
            }
            if ($variant['stock_override'] !== null) {
                return (int)$variant['stock_override'];
            }
        }

        if ($product['type'] === 'epin') {
            $stmt = $this->query('SELECT COUNT(*) AS total FROM epin_keys WHERE product_id = :id AND status = \"available\"', [
                'id' => $productId,
            ]);
            $row = $stmt->fetch();
            return (int)($row['total'] ?? 0);
        }

        if ($product['type'] === 'account') {
            $stmt = $this->query('SELECT COUNT(*) AS total FROM accounts WHERE product_id = :id AND status = \"available\"', [
                'id' => $productId,
            ]);
            $row = $stmt->fetch();
            return (int)($row['total'] ?? 0);
        }

        if ($variantId) {
            return PHP_INT_MAX;
        }

        $variantModel = new Variant();
        $variants = $variantModel->forProduct($productId);
        $total = 0;
        foreach ($variants as $variant) {
            if ($variant['stock_override'] !== null) {
                $total += (int)$variant['stock_override'];
            }
        }

        return $total;
    }

    public function getStockSummary(int $productId): array
    {
        $product = $this->findById($productId);
        if (!$product) {
            return ['available' => 0, 'reserved' => 0, 'sold' => 0];
        }

        if ($product['type'] === 'epin') {
            $stmt = $this->query('SELECT status, COUNT(*) AS total FROM epin_keys WHERE product_id = :id GROUP BY status', ['id' => $productId]);
        } elseif ($product['type'] === 'account') {
            $stmt = $this->query('SELECT status, COUNT(*) AS total FROM accounts WHERE product_id = :id GROUP BY status', ['id' => $productId]);
        } else {
            $summary = ['available' => 0, 'reserved' => 0, 'sold' => 0];
            $variants = $product['variants'] ?? [];
            foreach ($variants as $variant) {
                if ($variant['stock_override'] !== null) {
                    $summary['available'] += (int)$variant['stock_override'];
                } else {
                    $summary['available'] = PHP_INT_MAX;
                }
            }
            if ($summary['available'] === PHP_INT_MAX) {
                return ['available' => PHP_INT_MAX, 'reserved' => 0, 'sold' => 0];
            }
            return $summary;
        }

        $data = ['available' => 0, 'reserved' => 0, 'sold' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $status = $row['status'] ?? '';
            $data[$status] = (int)$row['total'];
        }

        return $data;
    }
}
