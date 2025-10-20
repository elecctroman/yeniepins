<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
$logContent = $logContent ?? 'Log kaydı bulunamadı.';
ob_start();
?>
<section class="card card--elevated">
    <div class="card__header card__header--between">
        <div>
            <h2>Sistem Logları</h2>
            <p class="text-soft text-sm">Son 1000 satır gösteriliyor.</p>
        </div>
        <button class="btn btn-ghost" data-copy="#logViewer">Kopyala</button>
    </div>
    <pre id="logViewer" class="log-viewer log-viewer--scrollable"><?= $view->escape($logContent); ?></pre>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
