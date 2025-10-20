<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
ob_start();
?>
<section class="card">
    <h2>Sepetiniz</h2>
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Ürün</th>
                <th>Adet</th>
                <th>Birim Fiyat</th>
                <th>Toplam</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="5" class="text-muted">Sepetiniz boş.</td>
            </tr>
            </tbody>
        </table>
    </div>
    <form method="post" action="/kupon" class="form-inline" data-validate>
        <?= csrf_field(); ?>
        <label class="form-field">
            <span>Kupon Kodu</span>
            <input type="text" name="coupon" placeholder="HOSGELDIN10" required>
        </label>
        <button type="submit" class="btn btn-secondary">Uygula</button>
    </form>
    <div class="form-actions">
        <a href="/" class="btn btn-secondary">Alışverişe Devam</a>
        <a href="/odeme" class="btn btn-primary">Ödemeye Geç</a>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
