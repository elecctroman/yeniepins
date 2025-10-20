<?php

namespace App\Controllers;

use Core\CSRF;
use Core\Controller;
use Core\Response;
use Core\Session;

class CouponController extends Controller
{
    public function apply(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/sepet');
        }

        $data = $this->input();
        Session::flash('success', 'Kupon uygulandı.');
        Session::pushOldInput($data);
        Response::redirect('/sepet');
    }
}
