<?php
$styles = [asset('css/theme.css')];
$bodyClass = 'app--maintenance';
ob_start();
?>
<section class="page-maintenance">
    <div class="card card--elevated">
        <h1 class="text-xl">Bakımdayız</h1>
        <p class="text-muted"><?= $view->escape($message ?? 'Kısa bir bakım çalışması gerçekleştiriyoruz. Lütfen daha sonra tekrar deneyin.'); ?></p>
        <p class="text-xs text-soft">İşlemleriniz etkilenmeyecek şekilde en kısa sürede geri döneceğiz.</p>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/partials/layout.php';
