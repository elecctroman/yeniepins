<?php

namespace App\Controllers;

use Core\CSRF;
use Core\Controller;
use Core\Response;
use Core\Session;

class CustomerController extends Controller
{
    public function account(array $params = []): void
    {
        $this->render('customer/account', ['title' => 'Hesabım']);
    }

    public function orders(array $params = []): void
    {
        $this->render('customer/orders', ['title' => 'Siparişlerim']);
    }

    public function keys(array $params = []): void
    {
        $this->render('customer/keys', ['title' => 'Anahtarlarım']);
    }

    public function wallet(array $params = []): void
    {
        $this->render('customer/wallet', ['title' => 'Cüzdan']);
    }

    public function showRefundForm(array $params = []): void
    {
        $orderId = $params['orderId'] ?? '';
        $this->render('customer/refund', [
            'title' => 'İade Talebi',
            'orderId' => $orderId,
        ]);
    }

    public function submitRefund(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/iade-talebi/' . ($params['orderId'] ?? '')); 
        }

        Session::flash('success', 'İade talebiniz alınmıştır.');
        Response::redirect('/siparislerim');
    }
}
