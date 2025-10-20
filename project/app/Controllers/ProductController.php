<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Variant;
use Core\CSRF;
use Core\Controller;
use Core\Response;
use Core\Session;

class ProductController extends Controller
{
    protected Product $products;
    protected Category $categories;
    protected Variant $variants;

    public function __construct()
    {
        parent::__construct();
        $this->products = new Product();
        $this->categories = new Category();
        $this->variants = new Variant();
    }

    public function index(array $params = []): void
    {
        $catalog = $this->products->getCatalog();
        $categories = $this->categories->getVisibleCategories();

        $this->render('customer/category', [
            'title' => 'Mağaza',
            'topbarTitle' => 'Mağaza Vitrini',
            'category' => null,
            'products' => $catalog,
            'categories' => $categories,
            'breadcrumbs' => [
                ['label' => 'Mağaza'],
            ],
        ]);
    }

    public function show(array $params = []): void
    {
        $slug = $params['slug'] ?? '';
        $product = $this->products->getBySlug($slug);
        if (!$product || ($product['status'] ?? '') !== 'active') {
            Session::flash('error', 'Ürün bulunamadı.');
            Response::redirect('/urunler');
        }

        $categories = $this->categories->getVisibleCategories();
        $availableStock = $this->products->availableStock((int)$product['id']);

        $categorySlug = $product['category_slug'] ?? null;
        $categoryBreadcrumb = ['label' => $product['category_name'] ?? 'Kategori'];
        if ($categorySlug) {
            $categoryBreadcrumb['url'] = '/kategori/' . rawurlencode($categorySlug);
        }

        $this->render('customer/product', [
            'title' => $product['name'],
            'topbarTitle' => $product['name'],
            'product' => $product,
            'categories' => $categories,
            'availableStock' => $availableStock,
            'breadcrumbs' => [
                ['label' => 'Mağaza', 'url' => '/urunler'],
                $categoryBreadcrumb,
                ['label' => $product['name']],
            ],
        ]);
    }

    public function addToCart(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz istek.');
            Response::redirect('/sepet');
        }

        $input = $this->input();
        $productId = (int)($input['product_id'] ?? 0);
        $variantId = isset($input['variant_id']) && $input['variant_id'] !== '' ? (int)$input['variant_id'] : null;
        $quantity = max(1, (int)($input['qty'] ?? 1));

        $product = $this->products->findById($productId);
        if (!$product || ($product['status'] ?? '') !== 'active') {
            Session::flash('error', 'Ürün bulunamadı veya pasif.');
            Response::redirect('/urunler');
        }

        $minQty = max(1, (int)($product['min_qty'] ?? 1));
        $maxQty = (int)($product['max_qty'] ?? 0);
        if ($quantity < $minQty) {
            Session::flash('error', 'Minimum sipariş adedi ' . $minQty . ' olmalıdır.');
            Response::redirect('/urun/' . $product['slug']);
        }
        if ($maxQty > 0 && $quantity > $maxQty) {
            Session::flash('error', 'Maksimum sipariş adedi ' . $maxQty . ' olarak sınırlandırılmıştır.');
            Response::redirect('/urun/' . $product['slug']);
        }

        $variant = null;
        if ($variantId) {
            $variant = $this->variants->find($variantId);
            if (!$variant || (int)$variant['product_id'] !== $productId) {
                Session::flash('error', 'Seçilen varyant geçersiz.');
                Response::redirect('/urun/' . $product['slug']);
            }
        }

        $available = $this->products->availableStock($productId, $variantId);
        Session::start();
        $cart = Session::get('cart', ['items' => []]);
        $key = $productId . ':' . ($variantId ?? 0);
        $existingQty = $cart['items'][$key]['qty'] ?? 0;

        if ($product['stock_policy'] === 'track' && $available !== PHP_INT_MAX && ($existingQty + $quantity) > $available) {
            Session::flash('error', 'Yeterli stok bulunmuyor.');
            Response::redirect('/urun/' . $product['slug']);
        }

        $unitPrice = (float)$product['price'];
        if ($variant && $variant['price_override'] !== null) {
            $unitPrice = (float)$variant['price_override'];
        }

        if (!isset($cart['items'][$key])) {
            $cart['items'][$key] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'name' => $product['name'],
                'variant_name' => $variant['name'] ?? null,
                'qty' => 0,
                'unit_price' => $unitPrice,
                'tax_rate' => (float)$product['tax_rate'],
                'type' => $product['type'],
                'slug' => $product['slug'],
            ];
        }

        $cart['items'][$key]['qty'] += $quantity;
        $cart['items'][$key]['unit_price'] = $unitPrice;
        if ($variant) {
            $cart['items'][$key]['variant_name'] = $variant['name'];
        }

        Session::set('cart', $cart);
        Session::flash('success', 'Ürün sepetinize eklendi.');
        Response::redirect('/sepet');
    }
}
