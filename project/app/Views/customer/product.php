<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
ob_start();
?>
<section class="card">
    <h2>Ürün: <?= htmlspecialchars(ucwords(str_replace('-', ' ', $slug ?? 'ürün')), ENT_QUOTES, 'UTF-8'); ?></h2>
    <p class="text-muted">Ürün açıklaması ve varyant seçenekleri bu alanda görüntülenir.</p>
    <form method="post" action="/sepet/ekle" class="form-grid" data-validate>
        <?= csrf_field(); ?>
        <input type="hidden" name="product" value="<?= htmlspecialchars($slug ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <label class="form-field">
            <span>Varyant</span>
            <select name="variant">
                <option>Varsayılan</option>
            </select>
        </label>
        <label class="form-field">
            <span>Adet</span>
            <input type="number" min="1" name="qty" value="1" required>
        </label>
        <button type="submit" class="btn btn-primary">Sepete Ekle</button>
    </form>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
