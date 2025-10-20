<?php

namespace App\Controllers;

use Core\Controller;

class HomeController extends Controller
{
    public function index(array $params = []): void
    {
        $this->render('customer/home', [
            'title' => 'Dijital Market Ana Sayfa',
        ]);
    }
}
