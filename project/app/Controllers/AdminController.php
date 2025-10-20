<?php

namespace App\Controllers;

use App\Models\Order;
use Core\Controller;
use Core\Security;

class AdminController extends Controller
{
    protected Order $orders;

    public function __construct()
    {
        parent::__construct();
        $this->orders = new Order();
    }

    public function dashboard(array $params = []): void
    {
        $this->render('admin/dashboard', ['title' => 'Yönetim Paneli']);
    }

    public function products(array $params = []): void
    {
        $this->render('admin/products', ['title' => 'Ürün Yönetimi']);
    }

    public function stock(array $params = []): void
    {
        $this->render('admin/stock', ['title' => 'Stok Yönetimi']);
    }

    public function coupons(array $params = []): void
    {
        $this->render('admin/coupons', ['title' => 'Kuponlar']);
    }

    public function orders(array $params = []): void
    {
        $orderList = $this->orders->all();
        $selectedNo = isset($_GET['no']) ? Security::sanitize((string)$_GET['no']) : '';
        $selectedOrder = $selectedNo ? $this->orders->findWithItemsByNo($selectedNo) : null;

        $this->render('admin/orders', [
            'title' => 'Siparişler',
            'topbarTitle' => 'Sipariş Yönetimi',
            'orders' => $orderList,
            'selectedOrder' => $selectedOrder,
            'selectedNo' => $selectedNo ?: null,
        ]);
    }

    public function refunds(array $params = []): void
    {
        $this->render('admin/refunds', ['title' => 'İade Talepleri']);
    }

    public function users(array $params = []): void
    {
        $this->render('admin/users', ['title' => 'Kullanıcılar']);
    }

    public function settings(array $params = []): void
    {
        $this->render('admin/settings', ['title' => 'Genel Ayarlar']);
    }

    public function logs(array $params = []): void
    {
        $this->render('admin/logs', ['title' => 'Sistem Logları']);
    }

    public function maintenance(array $params = []): void
    {
        $this->render('admin/maintenance', ['title' => 'Bakım Modu']);
    }
}
