<?php

namespace App\Models;

use Core\Model;
use PDO;

class EpinKey extends Model
{
    protected string $reservationFile;

    public function __construct()
    {
        parent::__construct();
        $directory = __DIR__ . '/../../storage/cache';
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $this->reservationFile = $directory . '/epin_reservations.json';
        if (!file_exists($this->reservationFile)) {
            file_put_contents($this->reservationFile, json_encode([], JSON_UNESCAPED_UNICODE), LOCK_EX);
        }
    }

    public function listForProduct(int $productId, int $limit = 50): array
    {
        $stmt = $this->db()->prepare('SELECT * FROM epin_keys WHERE product_id = :product_id ORDER BY id DESC LIMIT :limit');
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function bulkInsert(int $productId, array $codes, string $batchId): array
    {
        $inserted = 0;
        $skipped = 0;

        $this->db()->beginTransaction();
        $sql = "INSERT IGNORE INTO epin_keys (product_id, code, batch_id, status, created_at) VALUES (:product_id, :code, :batch_id, 'available', NOW())";
        $stmt = $this->db()->prepare($sql);

        foreach ($codes as $code) {
            $cleanCode = trim($code);
            if ($cleanCode === '') {
                continue;
            }

            $result = $stmt->execute([
                ':product_id' => $productId,
                ':code' => $cleanCode,
                ':batch_id' => $batchId,
            ]);

            if ($result && $stmt->rowCount() > 0) {
                $inserted++;
            } else {
                $skipped++;
            }
        }

        $this->db()->commit();

        return ['inserted' => $inserted, 'skipped' => $skipped];
    }

    public function reserveKeys(int $productId, int $quantity, string $token, int $ttl = 900): array
    {
        $this->cleanExpiredReservations();
        $pdo = $this->db();
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT id, code FROM epin_keys WHERE product_id = :product_id AND status = 'available' ORDER BY id ASC LIMIT :qty FOR UPDATE");
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':qty', $quantity, PDO::PARAM_INT);
        $stmt->execute();
        $keys = $stmt->fetchAll();

        if (count($keys) < $quantity) {
            $pdo->rollBack();
            return [];
        }

        $ids = array_column($keys, 'id');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $update = $pdo->prepare("UPDATE epin_keys SET status = 'reserved' WHERE id IN ($placeholders)");
        $update->execute($ids);
        $pdo->commit();

        $reservations = $this->loadReservations();
        $reservations[$token] = [
            'key_ids' => array_map('intval', $ids),
            'expires_at' => time() + $ttl,
            'product_id' => $productId,
        ];
        $this->saveReservations($reservations);

        return $keys;
    }

    public function releaseReservation(string $token): void
    {
        $reservations = $this->loadReservations();
        if (!isset($reservations[$token])) {
            return;
        }

        $payload = $reservations[$token];
        $ids = $payload['key_ids'] ?? [];
        if ($ids) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $this->db()->prepare("UPDATE epin_keys SET status = 'available' WHERE id IN ($placeholders) AND status = 'reserved'");
            $stmt->execute($ids);
        }

        unset($reservations[$token]);
        $this->saveReservations($reservations);
    }

    public function markAsSold(string $token): array
    {
        $reservations = $this->loadReservations();
        if (!isset($reservations[$token])) {
            return [];
        }

        $payload = $reservations[$token];
        $ids = $payload['key_ids'] ?? [];
        $keys = [];
        if ($ids) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $select = $this->db()->prepare("SELECT id, code FROM epin_keys WHERE id IN ($placeholders)");
            $select->execute($ids);
            $keys = $select->fetchAll() ?: [];

            $stmt = $this->db()->prepare("UPDATE epin_keys SET status = 'sold', delivered_at = NOW() WHERE id IN ($placeholders)");
            $stmt->execute($ids);
        }

        unset($reservations[$token]);
        $this->saveReservations($reservations);
        return $keys;
    }

    public function cleanExpiredReservations(): void
    {
        $reservations = $this->loadReservations();
        $changed = false;
        foreach ($reservations as $token => $payload) {
            if (($payload['expires_at'] ?? 0) < time()) {
                $ids = $payload['key_ids'] ?? [];
                if ($ids) {
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $stmt = $this->db()->prepare("UPDATE epin_keys SET status = 'available' WHERE id IN ($placeholders) AND status = 'reserved'");
                    $stmt->execute($ids);
                }
                unset($reservations[$token]);
                $changed = true;
            }
        }

        if ($changed) {
            $this->saveReservations($reservations);
        }
    }

    protected function loadReservations(): array
    {
        $contents = (string)@file_get_contents($this->reservationFile);
        if ($contents === '') {
            return [];
        }

        $data = json_decode($contents, true);
        return is_array($data) ? $data : [];
    }

    protected function saveReservations(array $reservations): void
    {
        file_put_contents($this->reservationFile, json_encode($reservations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
    }
}
