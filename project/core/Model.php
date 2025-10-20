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
        $host = $config['host'] ?? '127.0.0.1';
        $dbName = $config['dbname'] ?? '';
        $charset = $config['charset'] ?? 'utf8mb4';
        $port = (int)($config['port'] ?? 3306);
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $dbName, $charset);

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        if (!empty($config['options']) && is_array($config['options'])) {
            $options = $config['options'] + $options;
        }

        try {
            $pdo = new PDO($dsn, $config['user'] ?? '', $config['pass'] ?? '', $options);
        } catch (PDOException $e) {
            Logger::error('Veritabanı bağlantı hatası: {message}', [
                'message' => $e->getMessage(),
                'host' => $host,
                'dbname' => $dbName,
            ]);
            throw new PDOException('Veritabanı bağlantı hatası: ' . $e->getMessage(), (int)$e->getCode(), $e);
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
