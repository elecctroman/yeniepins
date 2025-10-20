<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
ob_start();
?>
<section class="card">
    <h2>Genel Ayarlar</h2>
    <form method="post" action="#" class="form-grid" data-validate>
        <?= csrf_field(); ?>
        <label class="form-field">
            <span>Site Başlığı</span>
            <input type="text" name="site_name" value="<?= htmlspecialchars(config('site_name'), ENT_QUOTES, 'UTF-8'); ?>" required>
        </label>
        <label class="form-field">
            <span>Destek E-postası</span>
            <input type="email" name="support_email" value="destek@example.com" required>
        </label>
        <button type="submit" class="btn btn-primary">Kaydet</button>
    </form>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
