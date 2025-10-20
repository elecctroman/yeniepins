<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
ob_start();
?>
<section class="card">
    <h2>Kullanıcılar</h2>
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Ad</th>
                <th>E-posta</th>
                <th>Rol</th>
                <th>Durum</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="4" class="text-muted">Kullanıcı listesi boş.</td>
            </tr>
            </tbody>
        </table>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
