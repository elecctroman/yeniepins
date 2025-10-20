<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer app--auth';
ob_start();
?>
<section class="card card--narrow">
    <h2>OTP Doğrulama</h2>
    <p class="text-muted">E-posta adresinize gönderilen 6 haneli kodu giriniz.</p>
    <form method="post" action="/otp-dogrula" class="form-grid" data-validate>
        <?= csrf_field(); ?>
        <label class="form-field">
            <span>OTP Kod</span>
            <input type="text" name="otp" maxlength="6" pattern="[0-9]{6}" required>
        </label>
        <button type="submit" class="btn btn-primary">Doğrula</button>
    </form>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/partials/layout.php';
