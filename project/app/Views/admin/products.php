<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
ob_start();
?>
<section class="card">
    <h2>Ürün Yönetimi</h2>
    <p class="text-muted">E-PIN, lisans ve hesap ürünlerini yönetin.</p>
    <div class="toolbar">
        <a href="#" class="btn btn-primary">Yeni Ürün</a>
        <button type="button" class="btn btn-secondary">CSV İçe Aktar</button>
    </div>
    <table class="table">
        <thead>
        <tr>
            <th>Ürün</th>
            <th>Kategori</th>
            <th>Tip</th>
            <th>Fiyat</th>
            <th>Durum</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="5" class="text-muted">Kayıt bulunamadı.</td>
        </tr>
        </tbody>
    </table>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
