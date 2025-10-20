<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
ob_start();
?>
<section class="card">
    <h2>Cüzdanım</h2>
    <div class="stat">
        <span class="stat__label">Mevcut Bakiye</span>
        <span class="stat__value">₺0,00</span>
    </div>
    <ul class="list list--bordered">
        <li class="list__item">Henüz işlem bulunmuyor.</li>
    </ul>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
