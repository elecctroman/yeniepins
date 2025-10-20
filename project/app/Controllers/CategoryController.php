<?php

namespace App\Controllers;

use Core\Controller;

class CategoryController extends Controller
{
    public function show(array $params = []): void
    {
        $slug = $params['slug'] ?? '';
        $this->render('customer/category', [
            'title' => 'Kategori: ' . ucfirst(str_replace('-', ' ', $slug)),
            'slug' => $slug,
        ]);
    }
}
