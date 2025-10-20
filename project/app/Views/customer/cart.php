<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
ob_start();
?>
<section class="card">
    <div class="card__header">
        <div>
            <h1 class="card__title">Sepetiniz</h1>
            <p class="text-muted">Satın alımınızı tamamlamak için adım adım ilerleyin. Ürünleri düzenleyebilir veya kupon kullanabilirsiniz.</p>
        </div>
        <a href="/" class="btn btn-ghost">Alışverişe Devam</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Ürün</th>
                <th>Varyant</th>
                <th>Adet</th>
                <th>Birim Fiyat</th>
                <th>Satır Toplamı</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($cartItems)): ?>
                <?php foreach ($cartItems as $key => $item): ?>
                    <?php $lineTotal = $item['qty'] * $item['unit_price']; ?>
                    <tr>
                        <td>
                            <div class="table-product">
                                <a href="/urun/<?= htmlspecialchars($item['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="table-product__name">
                                    <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                                <span class="table-product__meta">Tip: <?= strtoupper(htmlspecialchars($item['type'], ENT_QUOTES, 'UTF-8')); ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($item['variant_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= (int)$item['qty']; ?></td>
                        <td><?= money_fmt((float)$item['unit_price']); ?> ₺</td>
                        <td><?= money_fmt((float)$lineTotal); ?> ₺</td>
                        <td class="text-end">
                            <form method="post" action="/sepet/sil" class="inline-form" data-validate>
                                <?= csrf_field(); ?>
                                <input type="hidden" name="item_key" value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="btn btn-ghost">Kaldır</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-muted text-center">Sepetinizde ürün bulunmuyor.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card card--sub">
        <p><strong>Ara Toplam:</strong> <?= money_fmt((float)$subtotal); ?> ₺</p>
        <p><strong>Vergi:</strong> <?= money_fmt((float)$taxTotal); ?> ₺</p>
        <p><strong>Genel Toplam:</strong> <?= money_fmt((float)$total); ?> ₺</p>
        <div class="form-actions">
            <a href="/odeme" class="btn btn-primary" <?= empty($cartItems) ? 'disabled' : ''; ?>>Ödemeye Devam Et</a>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
