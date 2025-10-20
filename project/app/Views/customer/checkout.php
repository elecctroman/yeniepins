<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
ob_start();
?>
<section class="card">
    <h2>Ödeme</h2>
    <p class="text-muted">Ödeme yöntemini seçerek siparişi tamamlayın.</p>
    <form method="post" action="/odeme/baslat" class="form-grid" data-validate>
        <?= csrf_field(); ?>
        <label class="form-field">
            <span>Ödeme Yöntemi</span>
            <select name="gateway" required>
                <option value="paytr">PayTR (Sandbox)</option>
                <option value="iyzico">iyzico (Sandbox)</option>
                <option value="papara">Papara</option>
                <option value="havale">Havale / FAST</option>
            </select>
        </label>
        <button type="submit" class="btn btn-primary">Ödemeyi Başlat</button>
    </form>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
