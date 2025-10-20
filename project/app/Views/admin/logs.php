<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
ob_start();
?>
<section class="card">
    <h2>Sistem Logları</h2>
    <pre class="log-viewer">Log kaydı mevcut değil.</pre>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
