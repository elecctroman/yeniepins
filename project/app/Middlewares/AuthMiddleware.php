<?php

namespace App\Middlewares;

use Core\Response;
use Core\Session;

class AuthMiddleware
{
    public function handle(array $routeParams = [], array $options = []): void
    {
        Session::start();
        if (!Session::has('user_id')) {
            Session::flash('error', 'Lütfen giriş yapın.');
            Response::redirect('/giris');
        }
    }
}
