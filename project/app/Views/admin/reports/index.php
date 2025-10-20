<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
$summary = $summary ?? [];
$couponUsage = $couponUsage ?? [];
$topProducts = $topProducts ?? [];
$lowStock = $lowStock ?? [];
ob_start();
?>
<section class="dashboard-grid">
    <div class="card card--elevated">
        <div class="card__header"><h2>Satış Özeti</h2></div>
        <div class="metrics-grid">
            <?php foreach (['daily' => 'Günlük', 'weekly' => 'Haftalık', 'monthly' => 'Aylık'] as $key => $label):
                $data = $summary[$key] ?? ['total' => 0, 'tax_total' => 0, 'discount_total' => 0, 'order_count' => 0];
            ?>
                <div class="metric-card">
                    <span class="metric-card__label"><?= $label; ?></span>
                    <span class="metric-card__value">₺<?= money_fmt((float)$data['total']); ?></span>
                    <span class="metric-card__meta text-soft text-xs">Sipariş: <?= (int)$data['order_count']; ?> · KDV: ₺<?= money_fmt((float)$data['tax_total']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card card--elevated">
        <div class="card__header card__header--between">
            <h2>Kupon Kullanımı</h2>
            <span class="text-soft text-xs">Son 30 gün</span>
        </div>
        <?php if (!$couponUsage): ?>
            <p class="text-soft">Henüz kupon kullanımı kaydı bulunmuyor.</p>
        <?php else: ?>
            <ul class="list list--bordered">
                <?php
                $maxUsage = max(array_column($couponUsage, 'usage_count')) ?: 1;
                foreach ($couponUsage as $coupon):
                    $percentage = ($coupon['usage_count'] / $maxUsage) * 100;
                ?>
                    <li class="list__item">
                        <div class="list__title"><?= $view->escape($coupon['coupon_code']); ?></div>
                        <div class="chart-bar" role="presentation">
                            <span style="width: <?= number_format($percentage, 2, '.', ''); ?>%"></span>
                        </div>
                        <div class="text-soft text-xs">Kullanım: <?= (int)$coupon['usage_count']; ?> · İndirim: ₺<?= money_fmt((float)$coupon['total_discount']); ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="card card--elevated">
        <div class="card__header"><h2>En Çok Satan Ürünler</h2></div>
        <?php if (!$topProducts): ?>
            <p class="text-soft">Satış kaydı bulunamadı.</p>
        <?php else: ?>
            <table class="table table--dense table--striped">
                <thead>
                <tr><th>Ürün</th><th>Adet</th><th>Ciro</th></tr>
                </thead>
                <tbody>
                <?php foreach ($topProducts as $product): ?>
                    <tr>
                        <td><?= $view->escape($product['name']); ?></td>
                        <td><?= (int)$product['total_qty']; ?></td>
                        <td>₺<?= money_fmt((float)$product['revenue']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="card card--elevated">
        <div class="card__header"><h2>Azalan Stok Uyarıları</h2></div>
        <?php if (!$lowStock): ?>
            <p class="text-soft">Kritik stok uyarısı bulunmuyor.</p>
        <?php else: ?>
            <ul class="list list--bordered">
                <?php foreach ($lowStock as $stock): ?>
                    <li class="list__item">
                        <div class="list__title"><?= $view->escape($stock['name']); ?></div>
                        <div class="text-soft text-xs">Tip: <?= strtoupper($stock['type']); ?> · Kalan: <?= (int)$stock['available_keys']; ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../partials/layout.php';
