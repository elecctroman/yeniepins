<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
$settings = $settings ?? [];
ob_start();
?>
<section class="card card--elevated">
    <div class="card__header card__header--between">
        <h2>Genel Ayarlar</h2>
        <span class="text-soft text-xs">Ayarlar settings tablosuna kaydedilir.</span>
    </div>
    <form method="post" action="/admin/ayarlar" class="form-grid" data-validate>
        <?= csrf_field(); ?>
        <label class="form-field">
            <span>Site Adı</span>
            <input type="text" name="site_adi" value="<?= $view->escape($settings['site_adi'] ?? config('site_name')); ?>" required>
        </label>
        <label class="form-field form-field--inline">
            <span>Bakım Modu</span>
            <label class="switch">
                <input type="checkbox" name="bakim_modu" <?= !empty($settings['bakim_modu']) ? 'checked' : ''; ?>>
                <span class="switch__slider"></span>
            </label>
        </label>
        <label class="form-field">
            <span>Bakım Mesajı</span>
            <textarea name="bakim_mesaj" rows="3" placeholder="Planlı bakım mesajı"><?= $view->escape($settings['bakim_mesaj'] ?? ''); ?></textarea>
        </label>
        <label class="form-field">
            <span>Logo Yolu</span>
            <input type="text" name="logo_yol" value="<?= $view->escape($settings['logo_yol'] ?? ''); ?>" placeholder="/public/assets/img/logo.svg">
        </label>
        <div class="form-grid form-grid--2">
            <label class="form-field">
                <span>Genel KDV (%)</span>
                <input type="number" step="0.01" name="genel_kdv" value="<?= $view->escape($settings['genel_kdv'] ?? '18'); ?>">
            </label>
            <label class="form-field">
                <span>Minimum Sipariş Tutarı</span>
                <input type="number" step="0.01" name="min_siparis" value="<?= $view->escape($settings['min_siparis'] ?? '0'); ?>">
            </label>
            <label class="form-field">
                <span>Maksimum Sipariş Tutarı</span>
                <input type="number" step="0.01" name="max_siparis" value="<?= $view->escape($settings['max_siparis'] ?? '0'); ?>">
            </label>
        </div>
        <h3 class="text-md">Ödeme Anahtar Placeholder</h3>
        <div class="form-grid form-grid--3">
            <label class="form-field">
                <span>PayTR</span>
                <input type="text" name="paytr_anahtar" value="<?= $view->escape($settings['paytr_anahtar'] ?? ''); ?>" placeholder="PAYTR_SANDBOX_KEY">
            </label>
            <label class="form-field">
                <span>iyzico</span>
                <input type="text" name="iyzico_anahtar" value="<?= $view->escape($settings['iyzico_anahtar'] ?? ''); ?>" placeholder="IYZICO_SANDBOX_KEY">
            </label>
            <label class="form-field">
                <span>Papara</span>
                <input type="text" name="papara_anahtar" value="<?= $view->escape($settings['papara_anahtar'] ?? ''); ?>" placeholder="PAPARA_SANDBOX_KEY">
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Ayarları Kaydet</button>
        </div>
    </form>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
