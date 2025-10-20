<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
ob_start();
?>
<section class="card">
    <h2>İade Talebi</h2>
    <p class="text-muted">Sipariş #<?= htmlspecialchars($orderId ?? '', ENT_QUOTES, 'UTF-8'); ?> için talep oluşturun.</p>
    <form method="post" action="/iade-talebi/<?= htmlspecialchars($orderId ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-grid" data-validate>
        <?= csrf_field(); ?>
        <label class="form-field">
            <span>İade Nedeni</span>
            <textarea name="reason" rows="4" required placeholder="Sorunu kısaca anlatın"></textarea>
        </label>
        <button type="submit" class="btn btn-primary">Talebi Gönder</button>
    </form>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
