<?php

namespace Core;

use PDO;
use PDOException;

abstract class Model
{
    protected static ?PDO $db = null;

    public function __construct()
    {
        if (static::$db === null) {
            static::$db = $this->connect();
        }
    }

    protected function connect(): PDO
    {
        $config = require __DIR__ . '/../config/database.php';
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $config['host'], $config['dbname']);

        try {
            $pdo = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new PDOException('Veritabanı bağlantı hatası: ' . $e->getMessage());
        }

        return $pdo;
    }

    protected function db(): PDO
    {
        if (static::$db === null) {
            static::$db = $this->connect();
        }

        return static::$db;
    }

    protected function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->db()->prepare($sql);
        foreach ($params as $key => $value) {
            $param = is_int($key) ? $key + 1 : ':' . ltrim((string)$key, ':');
            $stmt->bindValue($param, $value);
        }
        $stmt->execute();
        return $stmt;
    }
}
