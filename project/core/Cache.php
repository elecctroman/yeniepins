<?php

namespace Core;

class Cache
{
    protected string $path;

    public function __construct(string $path = null)
    {
        $this->path = $path ?? __DIR__ . '/../storage/cache/';
        if (!is_dir($this->path)) {
            mkdir($this->path, 0775, true);
        }
    }

    public function set(string $key, mixed $value, int $ttl = 300): void
    {
        $data = ['expires' => time() + $ttl, 'value' => $value];
        file_put_contents($this->path . md5($key) . '.cache', serialize($data));
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $file = $this->path . md5($key) . '.cache';
        if (!file_exists($file)) {
            return $default;
        }
        $data = unserialize(file_get_contents($file));
        if (!is_array($data) || ($data['expires'] ?? 0) < time()) {
            @unlink($file);
            return $default;
        }
        return $data['value'];
    }

    public function delete(string $key): void
    {
        $file = $this->path . md5($key) . '.cache';
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
