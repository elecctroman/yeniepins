<?php

namespace App\Controllers;

use Core\CSRF;
use Core\Controller;
use Core\Response;
use Core\Session;

class CartController extends Controller
{
    public function index(array $params = []): void
    {
        $this->render('customer/cart', [
            'title' => 'Sepetiniz',
        ]);
    }

    public function add(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/sepet');
        }

        $data = $this->input();
        Session::flash('success', 'Ürün sepetinize eklendi.');
        Session::pushOldInput($data);
        Response::redirect('/sepet');
    }

    public function remove(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/sepet');
        }

        Session::flash('success', 'Ürün sepetten kaldırıldı.');
        Response::redirect('/sepet');
    }
}
