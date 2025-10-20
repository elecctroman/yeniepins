<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer app--auth';
ob_start();
?>
<section class="card card--narrow">
    <h2>Parola Sıfırlama</h2>
    <p class="text-muted">Kayıtlı e-posta adresinize sıfırlama bağlantısı gönderilecektir.</p>
    <form method="post" action="/sifre-sifirla" class="form-grid" data-validate>
        <?= csrf_field(); ?>
        <label class="form-field">
            <span>E-posta</span>
            <input type="email" name="email" value="<?= htmlspecialchars(old('email', ''), ENT_QUOTES, 'UTF-8'); ?>" required>
        </label>
        <button type="submit" class="btn btn-primary">Bağlantı Gönder</button>
    </form>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/partials/layout.php';
