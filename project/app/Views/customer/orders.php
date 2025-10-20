<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
ob_start();
?>
<section class="card">
    <h2>Siparişlerim</h2>
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>No</th>
                <th>Tarih</th>
                <th>Tutar</th>
                <th>Durum</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="4" class="text-muted">Sipariş kaydı bulunamadı.</td>
            </tr>
            </tbody>
        </table>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
