<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
$breadcrumbs = $breadcrumbs ?? [
    ['label' => 'Yönetim', 'url' => '/admin'],
    ['label' => 'Ürünler', 'url' => '/admin/urunler'],
    ['label' => 'Yeni Ürün'],
];
$oldInput = \Core\Session::get('_old_input', []);
ob_start();
?>
<section class="card">
    <div class="card__header">
        <h2 class="card__title">Yeni Ürün Oluştur</h2>
        <p class="text-muted">Temel ürün bilgilerini doldurun, fiyat, stok ve teslimat seçeneklerini belirleyin.</p>
    </div>
    <form method="post" action="/admin/urunler" class="form-grid" data-validate>
        <?= csrf_field(); ?>
        <div class="grid grid--cols-2">
            <label class="form-field">
                <span>Ürün Adı</span>
                <input type="text" name="name" value="<?= htmlspecialchars($oldInput['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </label>
            <label class="form-field">
                <span>Slug</span>
                <input type="text" name="slug" value="<?= htmlspecialchars($oldInput['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="otomatik">
            </label>
        </div>
        <label class="form-field">
            <span>Açıklama</span>
            <textarea name="description" rows="4" placeholder="Ürün hakkında kısa açıklama"><?= htmlspecialchars($oldInput['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </label>
        <div class="grid grid--cols-3">
            <label class="form-field">
                <span>Kategori</span>
                <select name="category_id">
                    <option value="">Kategori Seç</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int)$category['id']; ?>" <?= (string)($oldInput['category_id'] ?? '') === (string)$category['id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="form-field">
                <span>Ürün Tipi</span>
                <?php $typeOld = $oldInput['type'] ?? 'epin'; ?>
                <select name="type" required>
                    <option value="epin" <?= $typeOld === 'epin' ? 'selected' : ''; ?>>E-PIN</option>
                    <option value="license" <?= $typeOld === 'license' ? 'selected' : ''; ?>>Lisans</option>
                    <option value="account" <?= $typeOld === 'account' ? 'selected' : ''; ?>>Dijital Hesap</option>
                </select>
            </label>
            <label class="form-field">
                <span>Teslimat</span>
                <?php $deliveryOld = $oldInput['delivery'] ?? 'instant'; ?>
                <select name="delivery">
                    <option value="instant" <?= $deliveryOld === 'instant' ? 'selected' : ''; ?>>Anında</option>
                    <option value="manual" <?= $deliveryOld === 'manual' ? 'selected' : ''; ?>>Manuel</option>
                </select>
            </label>
        </div>
        <div class="grid grid--cols-4">
            <label class="form-field">
                <span>Fiyat (₺)</span>
                <input type="number" step="0.01" min="0" name="price" value="<?= htmlspecialchars($oldInput['price'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" required>
            </label>
            <label class="form-field">
                <span>KDV (%)</span>
                <input type="number" step="0.01" min="0" name="tax_rate" value="<?= htmlspecialchars($oldInput['tax_rate'] ?? '20', ENT_QUOTES, 'UTF-8'); ?>">
            </label>
            <label class="form-field">
                <span>Min. Adet</span>
                <input type="number" min="1" name="min_qty" value="<?= htmlspecialchars($oldInput['min_qty'] ?? '1', ENT_QUOTES, 'UTF-8'); ?>">
            </label>
            <label class="form-field">
                <span>Maks. Adet</span>
                <input type="number" min="0" name="max_qty" value="<?= htmlspecialchars($oldInput['max_qty'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="sınırsız">
            </label>
        </div>
        <div class="grid grid--cols-2">
            <label class="form-field">
                <span>Stok Politikası</span>
                <?php $policyOld = $oldInput['stock_policy'] ?? 'track'; ?>
                <select name="stock_policy">
                    <option value="track" <?= $policyOld === 'track' ? 'selected' : ''; ?>>Stok Takipli</option>
                    <option value="unlimited" <?= $policyOld === 'unlimited' ? 'selected' : ''; ?>>Sınırsız</option>
                </select>
            </label>
            <label class="form-field">
                <span>Durum</span>
                <?php $statusOld = $oldInput['status'] ?? 'active'; ?>
                <select name="status">
                    <option value="draft" <?= $statusOld === 'draft' ? 'selected' : ''; ?>>Taslak</option>
                    <option value="active" <?= $statusOld === 'active' ? 'selected' : ''; ?>>Aktif</option>
                    <option value="inactive" <?= $statusOld === 'inactive' ? 'selected' : ''; ?>>Pasif</option>
                </select>
            </label>
        </div>
        <div class="card card--sub">
            <div class="card__header">
                <div>
                    <h3 class="card__title">Varyantlar</h3>
                    <p class="text-muted">Fiyat ya da stok farkı olan varyantları ekleyin (opsiyonel).</p>
                </div>
            </div>
            <div class="stack">
                <?php $oldVariants = $oldInput['variants'] ?? []; ?>
                <?php for ($i = 0; $i < 3; $i++): ?>
                    <?php $variantOld = $oldVariants[$i] ?? []; ?>
                    <div class="grid grid--cols-3">
                        <label class="form-field">
                            <span>Varyant Adı</span>
                            <input type="text" name="variants[<?= $i; ?>][name]" value="<?= htmlspecialchars($variantOld['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Örn. 12 Aylık">
                        </label>
                        <label class="form-field">
                            <span>Fiyat Override</span>
                            <input type="number" step="0.01" name="variants[<?= $i; ?>][price_override]" value="<?= htmlspecialchars($variantOld['price_override'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="boş bırak">
                        </label>
                        <label class="form-field">
                            <span>Stok</span>
                            <input type="number" min="0" name="variants[<?= $i; ?>][stock_override]" value="<?= htmlspecialchars($variantOld['stock_override'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="boş bırak">
                        </label>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
        <div class="form-actions">
            <a href="/admin/urunler" class="btn btn-ghost">İptal</a>
            <button type="submit" class="btn btn-primary">Kaydet</button>
        </div>
    </form>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
