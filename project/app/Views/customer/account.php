<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
$layoutType = 'customer';
$title = 'Hesap Pano';
$topbarTitle = 'Hesap Özeti';
$breadcrumbs = [
    ['label' => 'Müşteri Paneli', 'url' => '/hesabim'],
    ['label' => 'Hesap Özeti'],
];
$topbarActions = [
    ['label' => 'Yeni Sipariş', 'href' => '/'],
    ['label' => 'Destek Talebi', 'href' => '#destek'],
];
$userEmail = \Core\Session::get('user_email', 'musteri@example.com');
$userName = \Core\Session::get('user_name', 'Demo Kullanıcı');
ob_start();
?>
<section class="grid grid--cols-2">
    <article class="card card--highlight">
        <div class="card__header">
            <h2 class="card__title">Profil Durumu</h2>
            <span class="badge badge--success">Aktif</span>
        </div>
        <div class="tabular-data">
            <div class="tabular-data__row">
                <span class="tabular-data__label">İsim</span>
                <span class="tabular-data__value"><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="tabular-data__row">
                <span class="tabular-data__label">E-posta</span>
                <span class="tabular-data__value"><?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="tabular-data__row">
                <span class="tabular-data__label">İki Aşamalı Giriş</span>
                <span class="tabular-data__value badge badge--info">E-posta OTP</span>
            </div>
        </div>
    </article>

    <article class="card">
        <div class="card__header">
            <h2 class="card__title">Cüzdan Bakiyesi</h2>
            <button type="button" class="btn btn-primary btn--small" onclick="modalOpen({title: 'Cüzdan Yükle', body: '<p>Yakında...</p>'});">Yükle</button>
        </div>
        <div class="stat-card__value" style="font-size:2.4rem;">₺420,00</div>
        <p class="text-muted">Son işlem: 2 gün önce PayTR ile yükleme</p>
        <div class="progress" aria-hidden="true">
            <div class="progress__bar" style="width: 58%;"></div>
        </div>
        <div class="small-text text-muted">Aylık hedefin %58'i tamamlandı.</div>
    </article>
</section>

<section class="card">
    <div class="card__header">
        <h2 class="card__title">Son Siparişler</h2>
        <a href="/siparislerim" class="btn btn-ghost btn--small">Tümü</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tarih</th>
                <th>Ürün</th>
                <th>Durum</th>
                <th>Tutar</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>#EP-2045</td>
                <td>12.05.2024</td>
                <td>Steam Cüzdan 100 TL</td>
                <td><span class="badge badge--success">Teslim Edildi</span></td>
                <td>₺118,00</td>
            </tr>
            <tr>
                <td>#LC-1320</td>
                <td>04.05.2024</td>
                <td>Antivirüs Pro Lisans</td>
                <td><span class="badge badge--info">Devam Ediyor</span></td>
                <td>₺249,00</td>
            </tr>
            <tr>
                <td>#AC-981</td>
                <td>28.04.2024</td>
                <td>MMORPG Premium Hesap</td>
                <td><span class="badge badge--warning">İade İncelemesi</span></td>
                <td>₺349,00</td>
            </tr>
        </tbody>
    </table>
</section>

<section class="card">
    <div class="card__header">
        <h2 class="card__title">Anahtarlarım</h2>
        <button type="button" class="btn btn-secondary btn--small" data-copy="XXXX-XXXX-XXXX">Kodu Kopyala</button>
    </div>
    <div class="stack">
        <div class="pill">Steam · 100 TL · Teslim: 12.05.2024</div>
        <div class="pill">Antivirüs Pro · 2 yıl · Teslim: 04.05.2024</div>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
