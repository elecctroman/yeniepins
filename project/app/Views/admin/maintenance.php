<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
ob_start();
?>
<section class="card">
    <h2>Bakım Modu</h2>
    <form method="post" action="#" class="form-grid">
        <?= csrf_field(); ?>
        <label class="switch">
            <input type="checkbox" name="maintenance">
            <span>Bakım modunu etkinleştir</span>
        </label>
        <button type="submit" class="btn btn-primary">Güncelle</button>
    </form>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
