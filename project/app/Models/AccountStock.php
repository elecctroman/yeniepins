<?php

namespace App\Models;

use Core\Model;
use PDO;

class AccountStock extends Model
{
    protected string $reservationFile;
    protected string $key;

    public function __construct()
    {
        parent::__construct();
        $directory = __DIR__ . '/../../storage/cache';
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $this->reservationFile = $directory . '/account_reservations.json';
        if (!file_exists($this->reservationFile)) {
            file_put_contents($this->reservationFile, json_encode([], JSON_UNESCAPED_UNICODE), LOCK_EX);
        }
        $this->key = (string)config('app_key', 'yeniepins');
    }

    public function listForProduct(int $productId, int $limit = 50): array
    {
        $stmt = $this->db()->prepare('SELECT * FROM accounts WHERE product_id = :product_id ORDER BY id DESC LIMIT :limit');
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll() ?: [];
        foreach ($rows as &$row) {
            $row['password_masked'] = $this->maskPassword($row['password_encrypted']);
        }
        return $rows;
    }

    public function addAccount(int $productId, string $username, string $password, array $extra = []): int
    {
        $sql = "INSERT INTO accounts (product_id, username, password_encrypted, extra_json, status, created_at) "
            . "VALUES (:product_id, :username, :password_encrypted, :extra_json, 'available', NOW())";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([
            ':product_id' => $productId,
            ':username' => $username,
            ':password_encrypted' => $this->encryptPassword($password),
            ':extra_json' => $extra ? json_encode($extra, JSON_UNESCAPED_UNICODE) : null,
        ]);

        return (int)$this->db()->lastInsertId();
    }

    public function reserveAccounts(int $productId, int $quantity, string $token, int $ttl = 900): array
    {
        $this->cleanExpiredReservations();
        $pdo = $this->db();
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT id, username, password_encrypted FROM accounts WHERE product_id = :product_id AND status = 'available' ORDER BY id ASC LIMIT :qty FOR UPDATE");
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':qty', $quantity, PDO::PARAM_INT);
        $stmt->execute();
        $accounts = $stmt->fetchAll();

        if (count($accounts) < $quantity) {
            $pdo->rollBack();
            return [];
        }

        $ids = array_column($accounts, 'id');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $update = $pdo->prepare("UPDATE accounts SET status = 'reserved' WHERE id IN ($placeholders)");
        $update->execute($ids);
        $pdo->commit();

        $reservations = $this->loadReservations();
        $reservations[$token] = [
            'account_ids' => array_map('intval', $ids),
            'expires_at' => time() + $ttl,
            'product_id' => $productId,
        ];
        $this->saveReservations($reservations);

        return $accounts;
    }

    public function releaseReservation(string $token): void
    {
        $reservations = $this->loadReservations();
        if (!isset($reservations[$token])) {
            return;
        }

        $payload = $reservations[$token];
        $ids = $payload['account_ids'] ?? [];
        if ($ids) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $this->db()->prepare("UPDATE accounts SET status = 'available' WHERE id IN ($placeholders) AND status = 'reserved'");
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
        $ids = $payload['account_ids'] ?? [];
        $accounts = [];
        if ($ids) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $select = $this->db()->prepare("SELECT id, username, password_encrypted, extra_json FROM accounts WHERE id IN ($placeholders)");
            $select->execute($ids);
            $rows = $select->fetchAll() ?: [];
            foreach ($rows as $row) {
                $accounts[] = [
                    'id' => (int)$row['id'],
                    'username' => $row['username'],
                    'password' => $this->decryptPassword($row['password_encrypted']),
                    'extra' => $row['extra_json'] ? json_decode($row['extra_json'], true) ?: [] : [],
                ];
            }

            $stmt = $this->db()->prepare("UPDATE accounts SET status = 'sold', delivered_at = NOW() WHERE id IN ($placeholders)");
            $stmt->execute($ids);
        }

        unset($reservations[$token]);
        $this->saveReservations($reservations);
        return $accounts;
    }

    public function cleanExpiredReservations(): void
    {
        $reservations = $this->loadReservations();
        $changed = false;
        foreach ($reservations as $token => $payload) {
            if (($payload['expires_at'] ?? 0) < time()) {
                $ids = $payload['account_ids'] ?? [];
                if ($ids) {
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $stmt = $this->db()->prepare("UPDATE accounts SET status = 'available' WHERE id IN ($placeholders) AND status = 'reserved'");
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

    public function decryptPassword(string $encrypted): string
    {
        $decoded = base64_decode($encrypted, true);
        if ($decoded === false) {
            return '';
        }

        return $this->xorString($decoded, $this->key);
    }

    public function maskPassword(string $encrypted): string
    {
        $plain = $this->decryptPassword($encrypted);
        $length = mb_strlen($plain, 'UTF-8');
        if ($length === 0) {
            return '••••';
        }

        if ($length <= 3) {
            return str_repeat('•', $length);
        }

        return mb_substr($plain, 0, 2, 'UTF-8')
            . str_repeat('•', max(1, $length - 3))
            . mb_substr($plain, -1, 1, 'UTF-8');
    }

    protected function encryptPassword(string $password): string
    {
        return base64_encode($this->xorString($password, $this->key));
    }

    protected function xorString(string $value, string $key): string
    {
        $keyLength = strlen($key) ?: 1;
        $output = '';
        $valueLength = strlen($value);
        for ($i = 0; $i < $valueLength; $i++) {
            $output .= chr(ord($value[$i]) ^ ord($key[$i % $keyLength]));
        }

        return $output;
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
