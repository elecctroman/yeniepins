<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
ob_start();
?>
<section class="card">
    <h2>Kupon Yönetimi</h2>
    <form method="post" action="#" class="form-grid" data-validate>
        <?= csrf_field(); ?>
        <label class="form-field">
            <span>Kupon Kodu</span>
            <input type="text" name="code" required>
        </label>
        <label class="form-field">
            <span>Tip</span>
            <select name="type" required>
                <option value="percent">Yüzde</option>
                <option value="fixed">Sabit</option>
            </select>
        </label>
        <label class="form-field">
            <span>Değer</span>
            <input type="number" step="0.01" name="value" required>
        </label>
        <button type="submit" class="btn btn-primary">Kupon Oluştur</button>
    </form>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
