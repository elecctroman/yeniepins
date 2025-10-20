<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
ob_start();
?>
<section class="card">
    <h2>İade Talepleri</h2>
    <p class="text-muted">Müşteri iade süreçlerini yönetin.</p>
    <ul class="list list--bordered">
        <li class="list__item">İade kaydı bulunmuyor.</li>
    </ul>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
