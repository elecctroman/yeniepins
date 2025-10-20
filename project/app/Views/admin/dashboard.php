<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
ob_start();
?>
<section class="card">
    <h2><?= htmlspecialchars($title ?? 'Yönetim Paneli', ENT_QUOTES, 'UTF-8'); ?></h2>
    <p class="text-muted">Sistem durumu ve kritik metrikler için kontrol paneli.</p>
    <div class="grid grid--cols-3">
        <div class="stat">
            <span class="stat__label">Aktif Ürün</span>
            <span class="stat__value">--</span>
        </div>
        <div class="stat">
            <span class="stat__label">Günlük Satış</span>
            <span class="stat__value">--</span>
        </div>
        <div class="stat">
            <span class="stat__label">Bekleyen Talepler</span>
            <span class="stat__value">--</span>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
