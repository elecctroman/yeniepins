<?php

namespace App\Controllers;

use Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('customer/home', [
            'title' => 'Dijital Market Ana Sayfa',
        ]);
    }
}
