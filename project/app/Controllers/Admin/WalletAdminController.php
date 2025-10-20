<?php

namespace App\Controllers\Admin;

use App\Models\User;
use App\Models\Wallet;
use Core\CSRF;
use Core\Controller;
use Core\Logger;
use Core\Response;
use Core\Security;
use Core\Session;

class WalletAdminController extends Controller
{
    protected Wallet $wallets;
    protected User $users;

    public function __construct()
    {
        parent::__construct();
        $this->wallets = new Wallet();
        $this->users = new User();
    }

    public function index(array $params = []): void
    {
        $wallets = $this->wallets->all();

        $this->render('admin/wallets', [
            'title' => 'Cüzdan Yönetimi',
            'topbarTitle' => 'Cüzdanlar',
            'wallets' => $wallets,
            'breadcrumbs' => [
                ['label' => 'Cüzdanlar'],
            ],
        ]);
    }

    public function adjust(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/admin/cuzdanlar');
        }

        $input = $this->input();
        $userId = isset($input['user_id']) ? (int)$input['user_id'] : 0;
        $type = strtolower(Security::sanitize($input['type'] ?? ''));
        $amount = isset($input['amount']) ? (float)$input['amount'] : 0.0;
        $reason = Security::sanitize($input['reason'] ?? 'Manuel düzeltme');

        if ($userId <= 0 || !in_array($type, ['credit', 'debit'], true) || $amount <= 0) {
            Session::flash('error', 'Form alanlarını kontrol edin.');
            Response::redirect('/admin/cuzdanlar');
        }

        $user = $this->users->findById($userId);
        if (!$user) {
            Session::flash('error', 'Kullanıcı bulunamadı.');
            Response::redirect('/admin/cuzdanlar');
        }

        $success = $this->wallets->adjustBalance($userId, $amount, $type, $reason, 'manual');
        if ($success) {
            Logger::info('Admin cüzdan hareketi oluşturdu', [
                'admin_id' => Session::get('user_id'),
                'user_id' => $userId,
                'type' => $type,
                'amount' => $amount,
            ]);
            Session::flash('success', 'Cüzdan güncellendi.');
        } else {
            Session::flash('error', 'Cüzdan güncellenemedi.');
        }

        Response::redirect('/admin/cuzdanlar');
    }
}
