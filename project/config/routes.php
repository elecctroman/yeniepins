<?php

use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;

return [
    // Public routes
    ['method' => 'GET', 'path' => '/', 'handler' => ['HomeController', 'index']],
    ['method' => 'GET', 'path' => '/urunler', 'handler' => ['ProductController', 'index']],
    ['method' => 'GET', 'path' => '/kategori/{slug}', 'handler' => ['CategoryController', 'show']],
    ['method' => 'GET', 'path' => '/urun/{slug}', 'handler' => ['ProductController', 'show']],
    ['method' => 'GET', 'path' => '/sepet', 'handler' => ['CartController', 'index']],
    ['method' => 'POST', 'path' => '/sepet/ekle', 'handler' => ['ProductController', 'addToCart']],
    ['method' => 'POST', 'path' => '/sepet/sil', 'handler' => ['CartController', 'remove']],
    ['method' => 'POST', 'path' => '/kupon', 'handler' => ['CouponController', 'apply']],
    ['method' => 'GET', 'path' => '/odeme', 'handler' => ['CheckoutController', 'index']],
    ['method' => 'POST', 'path' => '/odeme/baslat', 'handler' => ['CheckoutController', 'start']],
    ['method' => 'POST', 'path' => '/odeme/callback/{gateway}', 'handler' => ['PaymentController', 'callback']],

    // Auth routes
    ['method' => 'GET', 'path' => '/giris', 'handler' => ['AuthController', 'showLoginForm']],
    ['method' => 'POST', 'path' => '/giris', 'handler' => ['AuthController', 'login']],
    ['method' => 'GET', 'path' => '/kayit', 'handler' => ['AuthController', 'showRegisterForm']],
    ['method' => 'POST', 'path' => '/kayit', 'handler' => ['AuthController', 'register']],
    ['method' => 'POST', 'path' => '/cikis', 'handler' => ['AuthController', 'logout'], 'middleware' => [
        ['class' => AuthMiddleware::class],
    ]],
    ['method' => 'GET', 'path' => '/sifre-sifirla', 'handler' => ['AuthController', 'showResetForm']],
    ['method' => 'POST', 'path' => '/sifre-sifirla', 'handler' => ['AuthController', 'resetPassword']],
    ['method' => 'GET', 'path' => '/otp-dogrula', 'handler' => ['AuthController', 'showOtpForm']],
    ['method' => 'POST', 'path' => '/otp-dogrula', 'handler' => ['AuthController', 'verifyOtp']],

    // Customer routes
    ['method' => 'GET', 'path' => '/hesabim', 'handler' => ['CustomerController', 'account'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['customer']]],
    ]],
    ['method' => 'GET', 'path' => '/siparislerim', 'handler' => ['CustomerController', 'orders'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['customer']]],
    ]],
    ['method' => 'GET', 'path' => '/anahtarlarim', 'handler' => ['CustomerController', 'keys'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['customer']]],
    ]],
    ['method' => 'GET', 'path' => '/cuzdan', 'handler' => ['CustomerController', 'wallet'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['customer']]],
    ]],
    ['method' => 'GET', 'path' => '/iade-talebi/{orderId}', 'handler' => ['CustomerController', 'showRefundForm'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['customer']]],
    ]],
    ['method' => 'POST', 'path' => '/iade-talebi/{orderId}', 'handler' => ['CustomerController', 'submitRefund'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['customer']]],
    ]],

    // Admin routes
    ['method' => 'GET', 'path' => '/admin', 'handler' => ['AdminController', 'dashboard'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin', 'editor', 'support']]],
    ]],
    ['method' => 'GET', 'path' => '/admin/urunler', 'handler' => ['Admin\\ProductAdminController', 'index'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin', 'editor']]],
    ]],
    ['method' => 'GET', 'path' => '/admin/urunler/olustur', 'handler' => ['Admin\\ProductAdminController', 'create'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin', 'editor']]],
    ]],
    ['method' => 'POST', 'path' => '/admin/urunler', 'handler' => ['Admin\\ProductAdminController', 'store'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin', 'editor']]],
    ]],
    ['method' => 'GET', 'path' => '/admin/urunler/{id}/duzenle', 'handler' => ['Admin\\ProductAdminController', 'edit'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin', 'editor']]],
    ]],
    ['method' => 'POST', 'path' => '/admin/urunler/{id}/guncelle', 'handler' => ['Admin\\ProductAdminController', 'update'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin', 'editor']]],
    ]],
    ['method' => 'POST', 'path' => '/admin/urunler/{id}/sil', 'handler' => ['Admin\\ProductAdminController', 'destroy'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin']]],
    ]],
    ['method' => 'GET', 'path' => '/admin/stok', 'handler' => ['Admin\\ProductAdminController', 'stock'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin', 'editor', 'support']]],
    ]],
    ['method' => 'POST', 'path' => '/admin/stok/epin-yukle', 'handler' => ['Admin\\ProductAdminController', 'uploadEpinCsv'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin', 'editor']]],
    ]],
    ['method' => 'POST', 'path' => '/admin/stok/hesap-ekle', 'handler' => ['Admin\\ProductAdminController', 'addAccount'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin', 'editor', 'support']]],
    ]],
    ['method' => 'GET', 'path' => '/admin/kuponlar', 'handler' => ['AdminController', 'coupons'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin', 'editor']]],
    ]],
    ['method' => 'GET', 'path' => '/admin/siparisler', 'handler' => ['AdminController', 'orders'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin', 'support']]],
    ]],
    ['method' => 'GET', 'path' => '/admin/iadeler', 'handler' => ['AdminController', 'refunds'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin', 'support']]],
    ]],
    ['method' => 'GET', 'path' => '/admin/kullanicilar', 'handler' => ['AdminController', 'users'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin']]],
    ]],
    ['method' => 'GET', 'path' => '/admin/ayarlar', 'handler' => ['AdminController', 'settings'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin']]],
    ]],
    ['method' => 'GET', 'path' => '/admin/loglar', 'handler' => ['AdminController', 'logs'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin', 'support']]],
    ]],
    ['method' => 'GET', 'path' => '/admin/bakim', 'handler' => ['AdminController', 'maintenance'], 'middleware' => [
        ['class' => AuthMiddleware::class],
        ['class' => RoleMiddleware::class, 'params' => ['roles' => ['admin']]],
    ]],
];
