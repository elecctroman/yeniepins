<?php

namespace App\Controllers;

use Core\Controller;

class PaymentController extends Controller
{
    public function callback(array $params = []): void
    {
        $gateway = $params['gateway'] ?? 'bilinmeyen';
        $this->json([
            'status' => 'ok',
            'gateway' => $gateway,
            'message' => 'Sandbox callback alındı.',
        ]);
    }
}
