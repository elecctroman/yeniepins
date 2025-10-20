<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
$breadcrumbs = $breadcrumbs ?? [
    ['label' => 'Yönetim', 'url' => '/admin'],
    ['label' => 'Ürünler'],
];
ob_start();
?>
<section class="card">
    <div class="card__header">
        <div>
            <h2 class="card__title">Ürün Kataloğu</h2>
            <p class="text-muted">Mağazada yayınlanan tüm ürünleri yönetin, stok durumlarını izleyin.</p>
        </div>
        <a href="/admin/urunler/olustur" class="btn btn-primary">Yeni Ürün</a>
    </div>
    <div class="table-responsive">
        <table class="table table--dense">
            <thead>
            <tr>
                <th>Ürün</th>
                <th>Kategori</th>
                <th>Tip</th>
                <th>Fiyat</th>
                <th>Stok</th>
                <th>Durum</th>
                <th class="text-end">İşlemler</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <?php $summary = $stockSummaries[$product['id']] ?? ['available' => 0, 'reserved' => 0, 'sold' => 0]; ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
                            <small class="text-muted">#<?= (int)$product['id']; ?> · <?= htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8'); ?></small>
                        </td>
                        <td><?= htmlspecialchars($product['category_name'] ?? 'Kategori yok', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <span class="badge badge--ghost"><?= strtoupper(htmlspecialchars($product['type'], ENT_QUOTES, 'UTF-8')); ?></span>
                        </td>
                        <td>
                            <div><?= money_fmt((float)$product['price']); ?> ₺</div>
                            <small class="text-muted">KDV %<?= number_format((float)$product['tax_rate'], 2, ',', '.'); ?></small>
                        </td>
                        <td>
                            <?php if ($product['stock_policy'] === 'unlimited'): ?>
                                <span class="badge badge--success">Sınırsız</span>
                            <?php else: ?>
                                <div class="stack">
                                    <span class="badge badge--success">Hazır: <?= (int)($summary['available'] ?? 0); ?></span>
                                    <span class="badge badge--warning">Rezerve: <?= (int)($summary['reserved'] ?? 0); ?></span>
                                    <span class="badge">Satıldı: <?= (int)($summary['sold'] ?? 0); ?></span>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (($product['status'] ?? '') === 'active'): ?>
                                <span class="badge badge--success">Aktif</span>
                            <?php elseif (($product['status'] ?? '') === 'draft'): ?>
                                <span class="badge badge--warning">Taslak</span>
                            <?php else: ?>
                                <span class="badge badge--danger">Pasif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="button-group">
                                <a href="/urun/<?= htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-ghost" target="_blank">Görüntüle</a>
                                <a href="/admin/urunler/<?= (int)$product['id']; ?>/duzenle" class="btn btn-secondary">Düzenle</a>
                                <form method="post" action="/admin/urunler/<?= (int)$product['id']; ?>/sil" class="inline-form" onsubmit="return confirm('Ürün kalıcı olarak silinecek. Devam edilsin mi?');">
                                    <?= csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger">Sil</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-muted text-center">Henüz ürün oluşturulmadı.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
