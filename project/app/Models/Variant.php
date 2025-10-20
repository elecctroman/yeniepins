<?php

namespace App\Models;

use Core\Model;

class Variant extends Model
{
    public function forProduct(int $productId): array
    {
        $stmt = $this->query('SELECT * FROM product_variants WHERE product_id = :product_id ORDER BY id ASC', [
            'product_id' => $productId,
        ]);

        return $stmt->fetchAll() ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->query('SELECT * FROM product_variants WHERE id = :id LIMIT 1', ['id' => $id]);
        $variant = $stmt->fetch();
        return $variant ?: null;
    }

    public function create(int $productId, array $data): int
    {
        $sql = 'INSERT INTO product_variants (product_id, name, price_override, stock_override) '
            . 'VALUES (:product_id, :name, :price_override, :stock_override)';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([
            ':product_id' => $productId,
            ':name' => $data['name'],
            ':price_override' => $data['price_override'],
            ':stock_override' => $data['stock_override'],
        ]);

        return (int)$this->db()->lastInsertId();
    }

    public function updateVariant(int $id, array $data): bool
    {
        $sql = 'UPDATE product_variants SET name = :name, price_override = :price_override, stock_override = :stock_override '
            . 'WHERE id = :id';
        $stmt = $this->db()->prepare($sql);

        return $stmt->execute([
            ':name' => $data['name'],
            ':price_override' => $data['price_override'],
            ':stock_override' => $data['stock_override'],
            ':id' => $id,
        ]);
    }

    public function deleteVariant(int $id): bool
    {
        $stmt = $this->db()->prepare('DELETE FROM product_variants WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function sync(int $productId, array $variants): void
    {
        $existing = $this->forProduct($productId);
        $existingIds = array_column($existing, 'id');
        $keepIds = [];

        foreach ($variants as $variant) {
            $name = trim($variant['name'] ?? '');
            if ($name === '') {
                continue;
            }

            $payload = [
                'name' => $name,
                'price_override' => $variant['price_override'] !== '' ? (float)$variant['price_override'] : null,
                'stock_override' => $variant['stock_override'] !== '' ? (int)$variant['stock_override'] : null,
            ];

            if (!empty($variant['id']) && in_array((int)$variant['id'], $existingIds, true)) {
                $this->updateVariant((int)$variant['id'], $payload);
                $keepIds[] = (int)$variant['id'];
            } else {
                $newId = $this->create($productId, $payload);
                $keepIds[] = $newId;
            }
        }

        $toDelete = array_diff($existingIds, $keepIds);
        foreach ($toDelete as $deleteId) {
            $this->deleteVariant((int)$deleteId);
        }
    }
}
