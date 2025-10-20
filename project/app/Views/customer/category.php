<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
$titleText = $category['name'] ?? 'Mağaza';
$selectedSlug = $category['slug'] ?? null;
ob_start();
?>
<section class="card">
    <header class="card__header">
        <div>
            <h1 class="card__title"><?= htmlspecialchars($titleText, ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="text-muted">Favori e-pin, lisans ve dijital hesaplarınızı tek tıkla satın alın.</p>
        </div>
        <form method="get" action="/urunler" role="search">
            <label class="form-field">
                <span class="sr-only">Kategori seçin</span>
                <select name="kategori" onchange="if(this.value){window.location.href=this.value;}" aria-label="Kategori seçimi">
                    <option value="/urunler">Tüm Ürünler</option>
                    <?php foreach ($categories as $cat): ?>
                        <?php $url = '/kategori/' . urlencode($cat['slug']); ?>
                        <option value="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" <?= $selectedSlug === $cat['slug'] ? 'selected' : ''; ?>><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </form>
    </header>
    <div class="grid grid--cols-3">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <article class="card card--sub">
                    <h2 class="card__title"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p class="text-muted">Tip: <?= strtoupper(htmlspecialchars($product['type'], ENT_QUOTES, 'UTF-8')); ?> · Kategori: <?= htmlspecialchars($product['category_name'] ?? 'Genel', ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><?= htmlspecialchars(mb_strimwidth($product['description'] ?? 'Hemen teslim dijital ürün.', 0, 140, '...'), ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong><?= money_fmt((float)$product['price']); ?> ₺</strong> · KDV %<?= number_format((float)$product['tax_rate'], 2, ',', '.'); ?></p>
                    <ul>
                        <li>Stok Politikası: <?= $product['stock_policy'] === 'track' ? 'Takipli' : 'Sınırsız'; ?></li>
                        <li>Teslimat: <?= $product['delivery'] === 'instant' ? 'Anında' : 'Manuel onay'; ?></li>
                        <li>Min/Max Adet: <?= (int)($product['min_qty'] ?? 1); ?> / <?= $product['max_qty'] ? (int)$product['max_qty'] : 'Sınırsız'; ?></li>
                    </ul>
                    <div class="form-actions">
                        <a href="/urun/<?= htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">Ürüne Git</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card card--sub">
                <h3>Henüz ürün bulunamadı</h3>
                <p>Bu kategoriye ürün eklenmesini bekleyebilirsiniz. Diğer kategorileri incelemeyi deneyin.</p>
                <a href="/urunler" class="btn btn-secondary">Tüm Ürünleri Gör</a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
