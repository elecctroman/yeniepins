<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
$layoutType = 'admin';
$title = 'Yönetim Paneli';
$topbarTitle = 'Gösterge Paneli';
$breadcrumbs = [
    ['label' => 'Yönetim', 'url' => '/admin'],
    ['label' => 'Gösterge Paneli'],
];
$topbarActions = [
    ['label' => 'Yeni Ürün', 'href' => '/admin/urunler/olustur'],
    ['label' => 'Rapor İndir', 'href' => '#'],
];
ob_start();
?>
<section class="grid grid--cols-3">
    <?php
    $stats = [
        ['label' => 'Aktif Ürün', 'value' => '48', 'trend' => '+5 bugün'],
        ['label' => 'Günlük Satış', 'value' => '₺7.430', 'trend' => '+12%'],
        ['label' => 'Bekleyen Talepler', 'value' => '3', 'trend' => '2 kritik'],
    ];
    foreach ($stats as $stat): ?>
        <div class="stat-card">
            <div class="stat-card__label"><?= htmlspecialchars($stat['label'], ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="stat-card__value"><?= htmlspecialchars($stat['value'], ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="small-text text-muted"><?= htmlspecialchars($stat['trend'], ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
    <?php endforeach; ?>
</section>

<section class="bento-grid">
    <article class="card card--highlight">
        <div class="card__header">
            <h3 class="card__title">Satış Özeti</h3>
            <span class="badge badge--info">Son 24 Saat</span>
        </div>
        <p class="text-muted">Ödeme sağlayıcılarına göre hacim dağılımı.</p>
        <table class="table">
            <thead>
                <tr>
                    <th>Ödeme Kanalı</th>
                    <th>İşlem</th>
                    <th>Tutar</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>PayTR</td>
                    <td>42</td>
                    <td>₺4.980</td>
                </tr>
                <tr>
                    <td>iyzico</td>
                    <td>18</td>
                    <td>₺1.860</td>
                </tr>
                <tr>
                    <td>Havale/FAST</td>
                    <td>5</td>
                    <td>₺590</td>
                </tr>
            </tbody>
        </table>
    </article>

    <article class="card">
        <div class="card__header">
            <h3 class="card__title">Stok Sağlığı</h3>
            <span class="badge badge--warning">Uyarı</span>
        </div>
        <div class="timeline">
            <div class="timeline__item">
                <strong>E-PIN · Steam Cüzdan</strong>
                <div class="small-text text-muted">Stok: 12 adet — Yenileme önerilir</div>
            </div>
            <div class="timeline__item">
                <strong>Lisans · Antivirüs Pro</strong>
                <div class="small-text text-muted">Stok: 4 adet — Toplu import bekleniyor</div>
            </div>
            <div class="timeline__item">
                <strong>Dijital Hesap · MMO Starter</strong>
                <div class="small-text text-muted">Stok: 26 adet — Sağlıklı</div>
            </div>
        </div>
    </article>

    <article class="card">
        <div class="card__header">
            <h3 class="card__title">Canlı Log Akışı</h3>
            <a href="/admin/loglar" class="btn btn-ghost btn--small">Tümü</a>
        </div>
        <ul class="stack small-text" style="margin:0; padding:0; list-style:none;">
            <li>· [INFO] 2 yeni müşteri kaydı</li>
            <li>· [WARNING] PayTR webhook gecikmesi</li>
            <li>· [SUCCESS] Otomatik E-PIN dağıtımı tamamlandı</li>
        </ul>
    </article>
</section>

<section class="card">
    <div class="card__header">
        <h3 class="card__title">Anlık Ciro</h3>
        <span class="small-text text-muted">Günlük hedef: ₺10.000</span>
    </div>
    <div class="progress" aria-hidden="true">
        <div class="progress__bar" style="width: 74%;"></div>
    </div>
    <div class="tabular-data">
        <div class="tabular-data__row">
            <span class="tabular-data__label">Bugünkü Sipariş</span>
            <span class="tabular-data__value">57</span>
        </div>
        <div class="tabular-data__row">
            <span class="tabular-data__label">İade Talepleri</span>
            <span class="tabular-data__value badge badge--warning">3 bekliyor</span>
        </div>
        <div class="tabular-data__row">
            <span class="tabular-data__label">Bakım Modu</span>
            <span class="tabular-data__value badge badge--success">Kapalı</span>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
