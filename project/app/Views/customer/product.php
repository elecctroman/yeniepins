<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
$productVariants = $product['variants'] ?? [];
$selectedSlug = $product['category_slug'] ?? null;
ob_start();
?>
<section class="card">
    <div class="grid grid--cols-2">
        <div>
            <h1 class="card__title"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="text-muted">Kategori: <?= htmlspecialchars($product['category_name'] ?? 'Genel', ENT_QUOTES, 'UTF-8'); ?> · Tip: <?= strtoupper(htmlspecialchars($product['type'], ENT_QUOTES, 'UTF-8')); ?></p>
            <p><?= nl2br(htmlspecialchars($product['description'] ?? 'Hızlı teslimat, güvenli alışveriş.', ENT_QUOTES, 'UTF-8')); ?></p>
            <ul>
                <li>Min. Adet: <?= (int)($product['min_qty'] ?? 1); ?></li>
                <li>Maks. Adet: <?= $product['max_qty'] ? (int)$product['max_qty'] : 'Sınırsız'; ?></li>
                <li>Stok Politikası: <?= $product['stock_policy'] === 'track' ? 'Takipli' : 'Sınırsız'; ?></li>
                <li>Teslimat: <?= $product['delivery'] === 'instant' ? 'Anında Dijital Teslimat' : 'Manuel Onay'; ?></li>
            </ul>
        </div>
        <div class="card card--sub">
            <p><strong><?= money_fmt((float)$product['price']); ?> ₺</strong> · KDV %<?= number_format((float)$product['tax_rate'], 2, ',', '.'); ?></p>
            <?php if ($product['stock_policy'] === 'track'): ?>
                <p><span class="badge badge--<?= $availableStock > 0 ? 'success' : 'danger'; ?>">Stok: <?= $availableStock; ?></span></p>
            <?php else: ?>
                <p><span class="badge badge--success">Sınırsız stok</span></p>
            <?php endif; ?>
            <form method="post" action="/sepet/ekle" class="form-grid" data-validate>
                <?= csrf_field(); ?>
                <input type="hidden" name="product_id" value="<?= (int)$product['id']; ?>">
                <?php if (!empty($productVariants)): ?>
                    <label class="form-field">
                        <span>Varyant Seçimi</span>
                        <select name="variant_id">
                            <option value="">Varsayılan Fiyat</option>
                            <?php foreach ($productVariants as $variant): ?>
                                <?php $label = $variant['name'];
                                if ($variant['price_override'] !== null) {
                                    $label .= ' · ' . money_fmt((float)$variant['price_override']) . ' ₺';
                                }
                                if ($variant['stock_override'] !== null) {
                                    $label .= ' · Stok ' . (int)$variant['stock_override'];
                                }
                                ?>
                                <option value="<?= (int)$variant['id']; ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                <?php endif; ?>
                <label class="form-field">
                    <span>Adet</span>
                    <input type="number" name="qty" min="<?= (int)($product['min_qty'] ?? 1); ?>" value="<?= (int)($product['min_qty'] ?? 1); ?>" <?= $product['max_qty'] ? 'max="' . (int)$product['max_qty'] . '"' : ''; ?> required>
                </label>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" <?= $product['stock_policy'] === 'track' && $availableStock <= 0 ? 'disabled' : ''; ?>>Sepete Ekle</button>
                    <a href="/sepet" class="btn btn-secondary">Sepeti Görüntüle</a>
                </div>
            </form>
        </div>
    </div>
</section>
<section class="card card--sub">
    <h2 class="card__title">Benzer Ürünler</h2>
    <p class="text-muted">Diğer kategorileri keşfederek yeni fırsatlar yakalayın.</p>
    <div class="grid grid--cols-3">
        <?php foreach ($categories as $cat): ?>
            <a href="/kategori/<?= htmlspecialchars($cat['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="btn <?= $selectedSlug === $cat['slug'] ? 'btn-primary' : 'btn-ghost'; ?>"><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?></a>
        <?php endforeach; ?>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
