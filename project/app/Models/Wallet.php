<?php

namespace App\Models;

use Core\Model;
use PDO;

class Wallet extends Model
{
    public function findByCustomer(int $customerId): array
    {
        $stmt = $this->db()->prepare('SELECT * FROM wallets WHERE customer_id = :customer_id LIMIT 1');
        $stmt->bindValue(':customer_id', $customerId, PDO::PARAM_INT);
        $stmt->execute();
        $wallet = $stmt->fetch();

        if (!$wallet) {
            $walletId = $this->createWallet($customerId);
            $wallet = $this->getById($walletId);
        }

        return $wallet ?: ['id' => 0, 'customer_id' => $customerId, 'balance' => 0.0];
    }

    public function getTransactions(int $walletId, int $limit = 50): array
    {
        $stmt = $this->db()->prepare('SELECT * FROM wallet_tx WHERE wallet_id = :wallet_id ORDER BY created_at DESC LIMIT :limit');
        $stmt->bindValue(':wallet_id', $walletId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function all(int $limit = 100): array
    {
        $sql = 'SELECT w.*, u.name AS customer_name, u.email AS customer_email '
            . 'FROM wallets w '
            . 'LEFT JOIN users u ON u.id = w.customer_id '
            . 'ORDER BY w.updated_at DESC '
            . 'LIMIT :limit';
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function adjustBalance(int $customerId, float $amount, string $type, string $reason, ?string $refId = null): bool
    {
        $type = $type === 'debit' ? 'debit' : 'credit';
        return $this->applyTransaction($customerId, $type, $amount, $reason, $refId);
    }

    public function credit(int $customerId, float $amount, string $reason, ?string $refId = null): bool
    {
        return $this->applyTransaction($customerId, 'credit', $amount, $reason, $refId);
    }

    public function debit(int $customerId, float $amount, string $reason, ?string $refId = null): bool
    {
        return $this->applyTransaction($customerId, 'debit', $amount, $reason, $refId);
    }

    protected function applyTransaction(int $customerId, string $type, float $amount, string $reason, ?string $refId = null): bool
    {
        $pdo = $this->db();
        $wallet = $this->findByCustomer($customerId);
        $walletId = (int)($wallet['id'] ?? 0);
        if ($walletId <= 0) {
            $walletId = $this->createWallet($customerId);
        }

        $amount = round($amount, 2);
        if ($amount <= 0) {
            return false;
        }

        $pdo->beginTransaction();
        try {
            $balance = (float)($wallet['balance'] ?? 0);
            $newBalance = $type === 'credit' ? $balance + $amount : $balance - $amount;
            if ($type === 'debit' && $newBalance < 0) {
                $pdo->rollBack();
                return false;
            }
            $stmt = $pdo->prepare('UPDATE wallets SET balance = :balance, updated_at = NOW() WHERE id = :id');
            $stmt->execute([
                ':balance' => number_format($newBalance, 2, '.', ''),
                ':id' => $walletId,
            ]);

            $insert = $pdo->prepare('INSERT INTO wallet_tx (wallet_id, type, amount, reason, ref_id, created_at) VALUES (:wallet_id, :type, :amount, :reason, :ref_id, NOW())');
            $insert->execute([
                ':wallet_id' => $walletId,
                ':type' => $type,
                ':amount' => number_format($amount, 2, '.', ''),
                ':reason' => $reason,
                ':ref_id' => $refId,
            ]);

            $pdo->commit();
            return true;
        } catch (\Throwable $exception) {
            $pdo->rollBack();
            return false;
        }
    }

    protected function createWallet(int $customerId): int
    {
        $stmt = $this->db()->prepare('INSERT INTO wallets (customer_id, balance, updated_at) VALUES (:customer_id, 0, NOW())');
        $stmt->bindValue(':customer_id', $customerId, PDO::PARAM_INT);
        $stmt->execute();

        return (int)$this->db()->lastInsertId();
    }

    protected function getById(int $walletId): ?array
    {
        $stmt = $this->query('SELECT * FROM wallets WHERE id = :id LIMIT 1', ['id' => $walletId]);
        $wallet = $stmt->fetch();

        return $wallet ?: null;
    }
}
