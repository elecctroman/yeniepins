<?php

namespace App\Controllers;

use Core\Controller;

class ProductController extends Controller
{
    public function show(array $params = []): void
    {
        $slug = $params['slug'] ?? '';
        $this->render('customer/product', [
            'title' => 'Ürün Detayı',
            'slug' => $slug,
        ]);
    }
}
