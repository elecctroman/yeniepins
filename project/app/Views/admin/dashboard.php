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
    ['label' => 'Raporlar', 'href' => '/admin/raporlar'],
];
$daily = $summary['daily'] ?? ['total' => 0, 'order_count' => 0];
$weekly = $summary['weekly'] ?? ['total' => 0, 'order_count' => 0];
$monthly = $summary['monthly'] ?? ['total' => 0, 'order_count' => 0];
ob_start();
?>
<section class="grid grid--cols-3">
    <?php
    $stats = [
        ['label' => 'Günlük Ciro', 'value' => '₺' . money_fmt((float)$daily['total']), 'trend' => $daily['order_count'] . ' sipariş'],
        ['label' => 'Haftalık Ciro', 'value' => '₺' . money_fmt((float)$weekly['total']), 'trend' => $weekly['order_count'] . ' sipariş'],
        ['label' => 'Aylık Ciro', 'value' => '₺' . money_fmt((float)$monthly['total']), 'trend' => $monthly['order_count'] . ' sipariş'],
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
            <h3 class="card__title">En Çok Satanlar</h3>
            <span class="badge badge--info">Son siparişler</span>
        </div>
        <?php if (!$topProducts): ?>
            <p class="text-soft">Henüz satış kaydı bulunmuyor.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Adet</th>
                        <th>Ciro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topProducts as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= (int)$product['total_qty']; ?></td>
                            <td>₺<?= money_fmt((float)$product['revenue']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
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
            <li>· [INFO] Günlük ciro: ₺<?= money_fmt((float)$daily['total']); ?></li>
            <li>· [INFO] Haftalık ciro: ₺<?= money_fmt((float)$weekly['total']); ?></li>
            <li>· [INFO] Aylık sipariş: <?= (int)$monthly['order_count']; ?></li>
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
