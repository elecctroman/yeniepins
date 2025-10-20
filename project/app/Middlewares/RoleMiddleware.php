<?php

namespace App\Middlewares;

use Core\Response;
use Core\Session;

class RoleMiddleware
{
    public function handle(array $routeParams = [], array $options = []): void
    {
        Session::start();
        $roles = $options['roles'] ?? [];
        if ($roles === []) {
            return;
        }

        $role = Session::get('user_role');
        if (!in_array($role, $roles, true)) {
            Session::flash('error', 'Bu sayfaya erişim yetkiniz yok.');
            Response::redirect('/');
        }
    }
}
