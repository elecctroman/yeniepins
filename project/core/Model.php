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
            ]);
        } catch (PDOException $e) {
            throw new PDOException('Veritabanı bağlantı hatası: ' . $e->getMessage());
        }

        return $pdo;
    }
}
