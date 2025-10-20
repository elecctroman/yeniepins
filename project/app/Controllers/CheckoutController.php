<?php

namespace App\Controllers;

use Core\CSRF;
use Core\Controller;
use Core\Response;
use Core\Session;

class CheckoutController extends Controller
{
    public function index(array $params = []): void
    {
        $this->render('customer/checkout', [
            'title' => 'Ödeme',
        ]);
    }

    public function start(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/odeme');
        }

        Session::flash('success', 'Ödeme süreci başlatıldı (sandbox).');
        Response::redirect('/odeme');
    }
}
