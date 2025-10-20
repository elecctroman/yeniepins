<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->query('SELECT * FROM users WHERE email = :email LIMIT 1', ['email' => $email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->query('SELECT * FROM users WHERE id = :id LIMIT 1', ['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO users (email, password_hash, name, phone, role, status, twofa_enabled, created_at) VALUES (:email, :password_hash, :name, :phone, :role, :status, :twofa_enabled, NOW())';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([
            ':email' => $data['email'],
            ':password_hash' => $data['password_hash'],
            ':name' => $data['name'],
            ':phone' => $data['phone'] ?? null,
            ':role' => $data['role'] ?? 'customer',
            ':status' => $data['status'] ?? 'active',
            ':twofa_enabled' => $data['twofa_enabled'] ?? 0,
        ]);

        return (int)$this->db()->lastInsertId();
    }
}
