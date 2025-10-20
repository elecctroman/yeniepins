<?php

namespace App\Controllers;

use Core\Controller;

class AdminController extends Controller
{
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
        $this->render('admin/orders', ['title' => 'Siparişler']);
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
