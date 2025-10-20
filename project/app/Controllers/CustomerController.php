<?php

namespace App\Controllers;

use App\Models\Order;
use Core\CSRF;
use Core\Controller;
use Core\Response;
use Core\Session;

class CustomerController extends Controller
{
    protected Order $orders;

    public function __construct()
    {
        parent::__construct();
        $this->orders = new Order();
    }

    public function account(array $params = []): void
    {
        $this->render('customer/account', [
            'title' => 'Hesabım',
            'topbarTitle' => 'Hesabım',
        ]);
    }

    public function orders(array $params = []): void
    {
        Session::start();
        $userId = (int)Session::get('user_id', 0);
        $orders = $userId ? $this->orders->getOrdersForCustomer($userId) : [];

        $this->render('customer/orders', [
            'title' => 'Siparişlerim',
            'topbarTitle' => 'Siparişlerim',
            'orders' => $orders,
            'breadcrumbs' => [
                ['label' => 'Siparişlerim'],
            ],
        ]);
    }

    public function keys(array $params = []): void
    {
        Session::start();
        $userId = (int)Session::get('user_id', 0);
        $deliveries = $userId ? $this->orders->getDeliveredItemsForCustomer($userId) : [];

        $this->render('customer/keys', [
            'title' => 'Anahtarlarım',
            'topbarTitle' => 'Anahtarlarım',
            'deliveries' => $deliveries,
            'breadcrumbs' => [
                ['label' => 'Anahtarlarım'],
            ],
        ]);
    }

    public function wallet(array $params = []): void
    {
        $this->render('customer/wallet', [
            'title' => 'Cüzdan',
            'topbarTitle' => 'Cüzdan',
        ]);
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
