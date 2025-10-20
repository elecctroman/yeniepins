<?php

namespace App\Controllers;

use Core\CSRF;
use Core\Controller;
use Core\Response;
use Core\Session;

class CartController extends Controller
{
    public function index(array $params = []): void
    {
        Session::start();
        $cart = Session::get('cart', ['items' => []]);
        $items = $cart['items'] ?? [];

        $subtotal = 0.0;
        $taxTotal = 0.0;
        foreach ($items as $item) {
            $lineTotal = $item['qty'] * $item['unit_price'];
            $subtotal += $lineTotal;
            $taxTotal += $lineTotal * ($item['tax_rate'] / 100);
        }
        $total = $subtotal + $taxTotal;

        $this->render('customer/cart', [
            'title' => 'Sepetiniz',
            'cartItems' => $items,
            'subtotal' => $subtotal,
            'taxTotal' => $taxTotal,
            'total' => $total,
            'topbarTitle' => 'Sepet',
        ]);
    }

    public function add(array $params = []): void
    {
        Response::redirect('/urunler');
    }

    public function remove(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/sepet');
        }

        $input = $this->input();
        $itemKey = $input['item_key'] ?? '';

        Session::start();
        $cart = Session::get('cart', ['items' => []]);
        if (isset($cart['items'][$itemKey])) {
            unset($cart['items'][$itemKey]);
            Session::set('cart', $cart);
            Session::flash('success', 'Ürün sepetten kaldırıldı.');
        } else {
            Session::flash('error', 'Ürün bulunamadı.');
        }

        Response::redirect('/sepet');
    }
}
