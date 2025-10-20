<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Report;
use App\Models\Setting;
use Core\Controller;
use Core\Logger;
use Core\Response;
use Core\Security;

class AdminController extends Controller
{
    protected Order $orders;
    protected Setting $settings;
    protected Report $reports;

    public function __construct()
    {
        parent::__construct();
        $this->orders = new Order();
        $this->settings = new Setting();
        $this->reports = new Report();
    }

    public function dashboard(array $params = []): void
    {
        $summary = $this->reports->salesSummary();
        $topProducts = $this->reports->topProducts(3);

        $this->render('admin/dashboard', [
            'title' => 'Yönetim Paneli',
            'topbarTitle' => 'Yönetim Paneli',
            'summary' => $summary,
            'topProducts' => $topProducts,
        ]);
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
        Response::redirect('/admin/iadeler');
    }

    public function users(array $params = []): void
    {
        $this->render('admin/users', ['title' => 'Kullanıcılar']);
    }

    public function settings(array $params = []): void
    {
        $current = $this->settings->all();
        $this->render('admin/settings', [
            'title' => 'Genel Ayarlar',
            'topbarTitle' => 'Ayarlar',
            'settings' => $current,
        ]);
    }

    public function logs(array $params = []): void
    {
        $path = __DIR__ . '/../../storage/logs/app.log';
        $logs = $this->tailLogs($path, 1000);

        $this->render('admin/logs', [
            'title' => 'Sistem Logları',
            'topbarTitle' => 'Log Görüntüleyici',
            'logContent' => $logs,
        ]);
    }

    public function maintenance(array $params = []): void
    {
        $this->render('admin/maintenance', [
            'title' => 'Bakım Modu',
            'topbarTitle' => 'Bakım Modu',
            'settings' => $this->settings->all(),
        ]);
    }

    public function saveSettings(array $params = []): void
    {
        if (!\Core\CSRF::validate()) {
            \Core\Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/admin/ayarlar');
        }

        $input = $this->input();
        $values = [
            'site_adi' => Security::sanitize($input['site_adi'] ?? config('site_name')), 
            'bakim_modu' => isset($input['bakim_modu']) ? '1' : '0',
            'bakim_mesaj' => Security::sanitize($input['bakim_mesaj'] ?? ''),
            'logo_yol' => Security::sanitize($input['logo_yol'] ?? ''),
            'genel_kdv' => number_format((float)($input['genel_kdv'] ?? 18), 2, '.', ''),
            'min_siparis' => number_format((float)($input['min_siparis'] ?? 0), 2, '.', ''),
            'max_siparis' => number_format((float)($input['max_siparis'] ?? 0), 2, '.', ''),
            'paytr_anahtar' => Security::sanitize($input['paytr_anahtar'] ?? ''),
            'iyzico_anahtar' => Security::sanitize($input['iyzico_anahtar'] ?? ''),
            'papara_anahtar' => Security::sanitize($input['papara_anahtar'] ?? ''),
        ];

        try {
            $this->settings->updateMany($values);
            Setting::flushCache();
            \Core\Session::flash('success', 'Ayarlar başarıyla güncellendi.');
            Logger::info('Ayarlar güncellendi', [
                'admin_id' => \Core\Session::get('user_id'),
            ]);
        } catch (\Throwable $exception) {
            \Core\Session::flash('error', 'Ayarlar kaydedilirken hata oluştu.');
            Logger::error('Ayar kaydetme hatası: {message}', ['message' => $exception->getMessage()]);
        }

        Response::redirect('/admin/ayarlar');
    }

    protected function tailLogs(string $path, int $lines = 1000): string
    {
        if (!file_exists($path)) {
            return 'Log kaydı bulunamadı.';
        }

        $buffer = '';
        $file = new \SplFileObject($path, 'r');
        $file->seek(PHP_INT_MAX);
        $lineCount = $file->key();
        $target = max(0, $lineCount - $lines);
        $file->seek($target);
        while (!$file->eof()) {
            $buffer .= $file->fgets();
        }

        return trim($buffer);
    }
}
