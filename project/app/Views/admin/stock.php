<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
ob_start();
?>
<section class="card">
    <h2>Stok Yönetimi</h2>
    <p class="text-muted">E-PIN anahtarları ve dijital hesap stoklarını takip edin.</p>
    <div class="grid grid--cols-2">
        <div class="card card--sub">
            <h3>E-PIN Havuzu</h3>
            <p>Toplam kullanılabilir anahtar: --</p>
            <button class="btn btn-secondary" type="button">Toplu Anahtar Yükle</button>
        </div>
        <div class="card card--sub">
            <h3>Dijital Hesaplar</h3>
            <p>Hazır hesap adedi: --</p>
            <button class="btn btn-secondary" type="button">CSV İçe Aktar</button>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
