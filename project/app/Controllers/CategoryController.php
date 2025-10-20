<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use Core\Controller;
use Core\Response;
use Core\Session;

class CategoryController extends Controller
{
    protected Category $categories;
    protected Product $products;

    public function __construct()
    {
        parent::__construct();
        $this->categories = new Category();
        $this->products = new Product();
    }

    public function show(array $params = []): void
    {
        $slug = $params['slug'] ?? '';
        $category = $this->categories->findBySlug($slug);
        if (!$category) {
            Session::flash('error', 'Kategori bulunamadı.');
            Response::redirect('/urunler');
        }

        $products = $this->products->getCatalog((int)$category['id']);
        $categories = $this->categories->getVisibleCategories();

        $this->render('customer/category', [
            'title' => $category['name'] . ' Ürünleri',
            'topbarTitle' => $category['name'],
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
            'breadcrumbs' => [
                ['label' => 'Mağaza', 'url' => '/urunler'],
                ['label' => $category['name']],
            ],
        ]);
    }
}
