<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
ob_start();
?>
<section class="card">
    <h2><?= htmlspecialchars($title ?? 'Dijital Market', ENT_QUOTES, 'UTF-8'); ?></h2>
    <p class="text-muted">Premium e-pin, lisans ve dijital hesap çözümleri.</p>
    <div class="grid grid--cols-2">
        <div class="card card--sub">
            <h3>Öne Çıkan Ürünler</h3>
            <p>Güncel kampanyalar ve popüler ürünleri keşfedin.</p>
            <a href="/kategori/oyunlar" class="btn btn-primary">Koleksiyona göz at</a>
        </div>
        <div class="card card--sub">
            <h3>Hızlı Satın Alma</h3>
            <p>Sepetinize ürün ekleyin ve saniyeler içinde teslim alın.</p>
            <form method="post" action="/sepet/ekle" class="form-grid" data-validate>
                <?= csrf_field(); ?>
                <label class="form-field">
                    <span>Ürün Kodu</span>
                    <input type="text" name="product" required placeholder="ör. valorant-vp-125">
                </label>
                <label class="form-field">
                    <span>Adet</span>
                    <input type="number" name="qty" min="1" value="1" required>
                </label>
                <button type="submit" class="btn btn-secondary">Sepete ekle</button>
            </form>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
