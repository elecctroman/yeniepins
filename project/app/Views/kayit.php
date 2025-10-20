<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer app--auth';
ob_start();
?>
<section class="card card--narrow">
    <h2>Yeni Hesap Oluşturun</h2>
    <form method="post" action="/kayit" class="form-grid" data-validate>
        <?= csrf_field(); ?>
        <label class="form-field">
            <span>Ad Soyad</span>
            <input type="text" name="name" value="<?= htmlspecialchars(old('name', ''), ENT_QUOTES, 'UTF-8'); ?>" required>
        </label>
        <label class="form-field">
            <span>E-posta</span>
            <input type="email" name="email" value="<?= htmlspecialchars(old('email', ''), ENT_QUOTES, 'UTF-8'); ?>" required>
        </label>
        <label class="form-field">
            <span>Telefon</span>
            <input type="tel" name="phone" value="<?= htmlspecialchars(old('phone', ''), ENT_QUOTES, 'UTF-8'); ?>">
        </label>
        <label class="form-field">
            <span>Parola</span>
            <input type="password" name="password" required>
        </label>
        <label class="form-field">
            <span>Parola (Tekrar)</span>
            <input type="password" name="password_confirmation" required>
        </label>
        <button type="submit" class="btn btn-primary">Kayıt Ol</button>
    </form>
    <div class="form-meta">
        <span>Zaten hesabınız var mı? <a href="/giris">Giriş yapın</a>.</span>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/partials/layout.php';
