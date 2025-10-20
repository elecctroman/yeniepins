<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
ob_start();
?>
<section class="card">
    <h2>Anahtarlarım</h2>
    <ul class="list list--bordered">
        <li class="list__item">
            Henüz teslim edilmiş anahtar bulunmuyor.
        </li>
        <li class="list__item list__item--with-action">
            <span class="key-value">VALO-XXXX-XXXX-XXXX</span>
            <button type="button" class="btn btn-secondary" data-copy="VALO-XXXX-XXXX-XXXX">Kopyala</button>
        </li>
    </ul>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
