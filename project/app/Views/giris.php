<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer app--auth';
ob_start();
?>
<section class="card card--narrow">
    <h2>Hesabınıza Giriş Yapın</h2>
    <form method="post" action="/giris" class="form-grid" data-validate>
        <?= csrf_field(); ?>
        <label class="form-field">
            <span>E-posta</span>
            <input type="email" name="email" value="<?= htmlspecialchars(old('email', ''), ENT_QUOTES, 'UTF-8'); ?>" required>
        </label>
        <label class="form-field">
            <span>Parola</span>
            <input type="password" name="password" required>
        </label>
        <button type="submit" class="btn btn-primary">Giriş Yap</button>
    </form>
    <div class="form-meta">
        <a href="/sifre-sifirla">Parolanızı mı unuttunuz?</a>
        <span>Hesabınız yok mu? <a href="/kayit">Hemen kayıt olun</a>.</span>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/partials/layout.php';
