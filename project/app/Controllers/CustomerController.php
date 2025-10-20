<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Refund;
use Core\Controller;
use Core\Session;

class CustomerController extends Controller
{
    protected Order $orders;
    protected Refund $refunds;

    public function __construct()
    {
        parent::__construct();
        $this->orders = new Order();
        $this->refunds = new Refund();
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
        $refundMap = [];
        if ($userId) {
            foreach ($this->refunds->forCustomer($userId) as $refund) {
                $refundMap[$refund['order_id']] = $refund;
            }
        }

        $this->render('customer/orders', [
            'title' => 'Siparişlerim',
            'topbarTitle' => 'Siparişlerim',
            'orders' => $orders,
            'refunds' => $refundMap,
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

}
