<?php

namespace App\Controllers;

use App\Models\Wallet;
use Core\Controller;
use Core\Session;

class WalletController extends Controller
{
    protected Wallet $wallets;

    public function __construct()
    {
        parent::__construct();
        $this->wallets = new Wallet();
    }

    public function index(array $params = []): void
    {
        Session::start();
        $customerId = (int)Session::get('user_id', 0);
        $wallet = $this->wallets->findByCustomer($customerId);
        $transactions = $wallet['id'] ? $this->wallets->getTransactions((int)$wallet['id']) : [];

        $this->render('customer/wallet', [
            'title' => 'Cüzdanım',
            'topbarTitle' => 'Cüzdanım',
            'wallet' => $wallet,
            'transactions' => $transactions,
            'breadcrumbs' => [
                ['label' => 'Cüzdan'],
            ],
        ]);
    }
}
