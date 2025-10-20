<?php

namespace App\Models;

use Core\Cache;
use Core\Model;

class Setting extends Model
{
    private const CACHE_KEY = 'settings.all';
    private const CACHE_PATH = __DIR__ . '/../../storage/cache/settings/';

    protected static ?array $localCache = null;
    protected Cache $cache;

    public function __construct()
    {
        parent::__construct();
        $this->cache = new Cache(self::CACHE_PATH);
    }

    public function all(): array
    {
        if (self::$localCache !== null) {
            return self::$localCache;
        }

        $cached = $this->cache->get(self::CACHE_KEY);
        if (is_array($cached)) {
            self::$localCache = $cached;
            return $cached;
        }

        $stmt = $this->query('SELECT `key`, `value` FROM settings');
        $settings = [];
        foreach ($stmt->fetchAll() ?: [] as $row) {
            $settings[$row['key']] = $row['value'];
        }

        $this->cache->set(self::CACHE_KEY, $settings, 600);
        self::$localCache = $settings;
        return $settings;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();
        return $settings[$key] ?? $default;
    }

    public function updateMany(array $values): void
    {
        $pdo = $this->db();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('REPLACE INTO settings (`key`, `value`) VALUES (:key, :value)');
            foreach ($values as $key => $value) {
                $stmt->execute([
                    ':key' => (string)$key,
                    ':value' => (string)$value,
                ]);
            }
            $pdo->commit();
            $this->cache->delete(self::CACHE_KEY);
            self::$localCache = null;
        } catch (\Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }
    }

    public static function flushCache(): void
    {
        self::$localCache = null;
        (new Cache(self::CACHE_PATH))->delete(self::CACHE_KEY);
    }
}
