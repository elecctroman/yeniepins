<?php

namespace App\Controllers\Admin;

use App\Models\Order;
use App\Models\Refund;
use App\Models\Wallet;
use Core\CSRF;
use Core\Controller;
use Core\Logger;
use Core\Response;
use Core\Security;
use Core\Session;

class RefundController extends Controller
{
    protected Refund $refunds;
    protected Wallet $wallets;
    protected Order $orders;

    public function __construct()
    {
        parent::__construct();
        $this->refunds = new Refund();
        $this->wallets = new Wallet();
        $this->orders = new Order();
    }

    public function index(array $params = []): void
    {
        $status = isset($_GET['durum']) ? Security::sanitize((string)$_GET['durum']) : null;
        $status = $status !== '' ? $status : null;
        $refunds = $this->refunds->all($status);

        $this->render('admin/refunds', [
            'title' => 'İade Talepleri',
            'topbarTitle' => 'İade Talepleri',
            'refunds' => $refunds,
            'selectedStatus' => $status,
            'breadcrumbs' => [
                ['label' => 'İade Talepleri'],
            ],
        ]);
    }

    public function update(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/admin/iadeler');
        }

        $refundId = isset($params['id']) ? (int)$params['id'] : 0;
        if ($refundId <= 0) {
            Session::flash('error', 'Geçersiz iade kaydı.');
            Response::redirect('/admin/iadeler');
        }

        $refund = $this->refunds->find($refundId);
        if (!$refund) {
            Session::flash('error', 'İade kaydı bulunamadı.');
            Response::redirect('/admin/iadeler');
        }

        $input = $this->input();
        $action = strtolower(Security::sanitize($input['action'] ?? ''));
        $amount = isset($input['amount']) ? (float)$input['amount'] : (float)($refund['total'] ?? 0);

        switch ($action) {
            case 'approve_wallet':
                $this->refunds->updateStatus($refundId, 'approved');
                $credited = $this->wallets->credit((int)$refund['customer_id'], $amount, 'İade kredisi', 'refund:' . $refundId);
                if ($credited) {
                    $this->refunds->updateStatus($refundId, 'processed');
                    $this->orders->updateStatus((int)$refund['order_id'], 'refunded');
                    Logger::info('İade cüzdana aktarıldı', [
                        'refund_id' => $refundId,
                        'order_no' => $refund['order_no'] ?? null,
                        'amount' => $amount,
                    ]);
                    Session::flash('success', 'İade tutarı müşteri cüzdanına aktarıldı.');
                } else {
                    Logger::warning('İade cüzdan aktarımı başarısız', [
                        'refund_id' => $refundId,
                        'order_no' => $refund['order_no'] ?? null,
                    ]);
                    Session::flash('error', 'Cüzdana aktarım başarısız oldu.');
                }
                break;
            case 'approve_original':
                $this->refunds->updateStatus($refundId, 'processed');
                $this->orders->updateStatus((int)$refund['order_id'], 'refunded');
                Logger::info('İade orijinal ödeme yöntemiyle işaretlendi', [
                    'refund_id' => $refundId,
                    'order_no' => $refund['order_no'] ?? null,
                    'amount' => $amount,
                ]);
                Session::flash('success', 'İade orijinal ödeme yöntemiyle tamamlandı olarak işaretlendi.');
                break;
            case 'reject':
                $this->refunds->updateStatus($refundId, 'rejected');
                Logger::info('İade talebi reddedildi', [
                    'refund_id' => $refundId,
                    'order_no' => $refund['order_no'] ?? null,
                ]);
                Session::flash('info', 'İade talebi reddedildi.');
                break;
            default:
                Session::flash('error', 'Geçersiz işlem seçildi.');
                break;
        }

        Response::redirect('/admin/iadeler');
    }
}
