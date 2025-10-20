<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
$settings = $settings ?? [];
$active = !empty($settings['bakim_modu']);
ob_start();
?>
<section class="card card--elevated">
    <div class="card__header">
        <h2>Bakım Modu Durumu</h2>
    </div>
    <p class="text-soft">Bakım modu ayarlarını <strong>Ayarlar</strong> ekranından yönetebilirsiniz. Bu sayfa mevcut durumu özetler.</p>
    <div class="badge <?= $active ? 'badge--warning' : 'badge--success'; ?>">
        <?= $active ? 'Bakım modu aktif — müşteriler bakım ekranını görüyor.' : 'Bakım modu kapalı — site müşterilere açık.'; ?>
    </div>
    <?php if (!empty($settings['bakim_mesaj'])): ?>
        <div class="card card--subtle mt-3">
            <h3 class="text-sm text-soft">Gösterilen Mesaj</h3>
            <p><?= $view->escape($settings['bakim_mesaj']); ?></p>
        </div>
    <?php endif; ?>
    <div class="text-soft text-xs mt-3">Bakım modu etkin olduğunda yalnızca yönetici/operasyon rolleri siteye erişebilir.</div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
