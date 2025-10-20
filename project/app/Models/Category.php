<?php

namespace App\Models;

use Core\Cache;
use Core\Model;

class Category extends Model
{
    protected Cache $cache;

    public function __construct()
    {
        parent::__construct();
        $this->cache = new Cache(__DIR__ . '/../../storage/cache/');
    }

    public function getVisibleCategories(bool $useCache = true): array
    {
        $cacheKey = 'categories_visible';
        if ($useCache) {
            $cached = $this->cache->get($cacheKey);
            if (is_array($cached)) {
                return $cached;
            }
        }

        $stmt = $this->query('SELECT * FROM categories WHERE hidden = 0 ORDER BY position ASC, name ASC');
        $categories = $stmt->fetchAll() ?: [];

        if ($useCache) {
            $this->cache->set($cacheKey, $categories, 3600);
        }

        return $categories;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->query('SELECT * FROM categories WHERE slug = :slug LIMIT 1', ['slug' => $slug]);
        $category = $stmt->fetch();
        return $category ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->query('SELECT * FROM categories WHERE id = :id LIMIT 1', ['id' => $id]);
        $category = $stmt->fetch();
        return $category ?: null;
    }

    public function getChildren(int $parentId): array
    {
        $stmt = $this->query('SELECT * FROM categories WHERE parent_id = :parent_id AND hidden = 0 ORDER BY position ASC', [
            'parent_id' => $parentId,
        ]);

        return $stmt->fetchAll() ?: [];
    }
}
