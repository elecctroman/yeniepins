<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
ob_start();
?>
<section class="card">
    <h2>Kategori: <?= htmlspecialchars(ucwords(str_replace('-', ' ', $slug ?? 'kategori')), ENT_QUOTES, 'UTF-8'); ?></h2>
    <p class="text-muted">Kategoriye ait ürünler burada listelenecek.</p>
    <div class="grid grid--cols-3">
        <div class="card card--sub">
            <h3>Demo Ürün</h3>
            <p>Kısa açıklama.</p>
            <a href="/urun/demo" class="btn btn-secondary">İncele</a>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
