<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
$breadcrumbs = $breadcrumbs ?? [
    ['label' => 'Yönetim', 'url' => '/admin'],
    ['label' => 'Ürünler', 'url' => '/admin/urunler'],
    ['label' => 'Ürün Düzenle'],
];
$oldInput = \Core\Session::get('_old_input', []);
$values = [
    'name' => $oldInput['name'] ?? ($product['name'] ?? ''),
    'slug' => $oldInput['slug'] ?? ($product['slug'] ?? ''),
    'description' => $oldInput['description'] ?? ($product['description'] ?? ''),
    'category_id' => $oldInput['category_id'] ?? ($product['category_id'] ?? ''),
    'type' => $oldInput['type'] ?? ($product['type'] ?? 'epin'),
    'delivery' => $oldInput['delivery'] ?? ($product['delivery'] ?? 'instant'),
    'price' => $oldInput['price'] ?? ($product['price'] ?? '0'),
    'tax_rate' => $oldInput['tax_rate'] ?? ($product['tax_rate'] ?? '20'),
    'min_qty' => $oldInput['min_qty'] ?? ($product['min_qty'] ?? '1'),
    'max_qty' => $oldInput['max_qty'] ?? ($product['max_qty'] ?? ''),
    'stock_policy' => $oldInput['stock_policy'] ?? ($product['stock_policy'] ?? 'track'),
    'status' => $oldInput['status'] ?? ($product['status'] ?? 'active'),
];
$variantInput = $oldInput['variants'] ?? ($product['variants'] ?? []);
ob_start();
?>
<section class="card">
    <div class="card__header">
        <h2 class="card__title">Ürün Düzenle</h2>
        <p class="text-muted">Ürün detaylarını güncelleyin, stok ve varyantları yönetmeye devam edin.</p>
    </div>
    <form method="post" action="/admin/urunler/<?= (int)$product['id']; ?>/guncelle" class="form-grid" data-validate>
        <?= csrf_field(); ?>
        <div class="grid grid--cols-2">
            <label class="form-field">
                <span>Ürün Adı</span>
                <input type="text" name="name" value="<?= htmlspecialchars($values['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </label>
            <label class="form-field">
                <span>Slug</span>
                <input type="text" name="slug" value="<?= htmlspecialchars($values['slug'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </label>
        </div>
        <label class="form-field">
            <span>Açıklama</span>
            <textarea name="description" rows="4"><?= htmlspecialchars($values['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        </label>
        <div class="grid grid--cols-3">
            <label class="form-field">
                <span>Kategori</span>
                <select name="category_id">
                    <option value="">Kategori Seç</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int)$category['id']; ?>" <?= (string)$values['category_id'] === (string)$category['id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="form-field">
                <span>Ürün Tipi</span>
                <select name="type" required>
                    <option value="epin" <?= $values['type'] === 'epin' ? 'selected' : ''; ?>>E-PIN</option>
                    <option value="license" <?= $values['type'] === 'license' ? 'selected' : ''; ?>>Lisans</option>
                    <option value="account" <?= $values['type'] === 'account' ? 'selected' : ''; ?>>Dijital Hesap</option>
                </select>
            </label>
            <label class="form-field">
                <span>Teslimat</span>
                <select name="delivery">
                    <option value="instant" <?= $values['delivery'] === 'instant' ? 'selected' : ''; ?>>Anında</option>
                    <option value="manual" <?= $values['delivery'] === 'manual' ? 'selected' : ''; ?>>Manuel</option>
                </select>
            </label>
        </div>
        <div class="grid grid--cols-4">
            <label class="form-field">
                <span>Fiyat (₺)</span>
                <input type="number" step="0.01" min="0" name="price" value="<?= htmlspecialchars($values['price'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </label>
            <label class="form-field">
                <span>KDV (%)</span>
                <input type="number" step="0.01" min="0" name="tax_rate" value="<?= htmlspecialchars($values['tax_rate'], ENT_QUOTES, 'UTF-8'); ?>">
            </label>
            <label class="form-field">
                <span>Min. Adet</span>
                <input type="number" min="1" name="min_qty" value="<?= htmlspecialchars($values['min_qty'], ENT_QUOTES, 'UTF-8'); ?>">
            </label>
            <label class="form-field">
                <span>Maks. Adet</span>
                <input type="number" min="0" name="max_qty" value="<?= htmlspecialchars((string)$values['max_qty'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="sınırsız">
            </label>
        </div>
        <div class="grid grid--cols-2">
            <label class="form-field">
                <span>Stok Politikası</span>
                <select name="stock_policy">
                    <option value="track" <?= $values['stock_policy'] === 'track' ? 'selected' : ''; ?>>Stok Takipli</option>
                    <option value="unlimited" <?= $values['stock_policy'] === 'unlimited' ? 'selected' : ''; ?>>Sınırsız</option>
                </select>
            </label>
            <label class="form-field">
                <span>Durum</span>
                <select name="status">
                    <option value="draft" <?= $values['status'] === 'draft' ? 'selected' : ''; ?>>Taslak</option>
                    <option value="active" <?= $values['status'] === 'active' ? 'selected' : ''; ?>>Aktif</option>
                    <option value="inactive" <?= $values['status'] === 'inactive' ? 'selected' : ''; ?>>Pasif</option>
                </select>
            </label>
        </div>
        <div class="card card--sub">
            <div class="card__header">
                <div>
                    <h3 class="card__title">Varyantlar</h3>
                    <p class="text-muted">Mevcut varyantları güncelleyin veya yeni satırlar ekleyin.</p>
                </div>
                <button type="button" class="btn btn-ghost" onclick="addVariantRow(this)">Varyant Satırı Ekle</button>
            </div>
            <div class="stack" data-variants>
                <?php $index = 0; ?>
                <?php foreach ($variantInput as $variant): ?>
                    <div class="grid grid--cols-4">
                        <input type="hidden" name="variants[<?= $index; ?>][id]" value="<?= htmlspecialchars($variant['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <label class="form-field">
                            <span>Varyant Adı</span>
                            <input type="text" name="variants[<?= $index; ?>][name]" value="<?= htmlspecialchars($variant['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="form-field">
                            <span>Fiyat Override</span>
                            <input type="number" step="0.01" name="variants[<?= $index; ?>][price_override]" value="<?= htmlspecialchars($variant['price_override'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="form-field">
                            <span>Stok</span>
                            <input type="number" min="0" name="variants[<?= $index; ?>][stock_override]" value="<?= htmlspecialchars($variant['stock_override'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <div class="form-field">
                            <span>&nbsp;</span>
                            <button type="button" class="btn btn-ghost" onclick="removeVariantRow(this)">Sil</button>
                        </div>
                    </div>
                    <?php $index++; ?>
                <?php endforeach; ?>
                <?php for ($i = 0; $i < 2; $i++): ?>
                    <div class="grid grid--cols-4">
                        <input type="hidden" name="variants[<?= $index; ?>][id]" value="">
                        <label class="form-field">
                            <span>Varyant Adı</span>
                            <input type="text" name="variants[<?= $index; ?>][name]">
                        </label>
                        <label class="form-field">
                            <span>Fiyat Override</span>
                            <input type="number" step="0.01" name="variants[<?= $index; ?>][price_override]">
                        </label>
                        <label class="form-field">
                            <span>Stok</span>
                            <input type="number" min="0" name="variants[<?= $index; ?>][stock_override]">
                        </label>
                        <div class="form-field">
                            <span>&nbsp;</span>
                            <button type="button" class="btn btn-ghost" onclick="removeVariantRow(this)">Sil</button>
                        </div>
                    </div>
                    <?php $index++; ?>
                <?php endfor; ?>
            </div>
        </div>
        <div class="form-actions">
            <a href="/admin/urunler" class="btn btn-ghost">Geri</a>
            <button type="submit" class="btn btn-primary">Güncelle</button>
        </div>
    </form>
</section>
<script>
    function addVariantRow(button) {
        const container = button.closest('.card').querySelector('[data-variants]');
        const index = container.querySelectorAll('.grid.grid--cols-4').length;
        const row = document.createElement('div');
        row.className = 'grid grid--cols-4';
        row.innerHTML = `
            <input type="hidden" name="variants[${index}][id]" value="">
            <label class="form-field">
                <span>Varyant Adı</span>
                <input type="text" name="variants[${index}][name]">
            </label>
            <label class="form-field">
                <span>Fiyat Override</span>
                <input type="number" step="0.01" name="variants[${index}][price_override]">
            </label>
            <label class="form-field">
                <span>Stok</span>
                <input type="number" min="0" name="variants[${index}][stock_override]">
            </label>
            <div class="form-field">
                <span>&nbsp;</span>
                <button type="button" class="btn btn-ghost" onclick="removeVariantRow(this)">Sil</button>
            </div>
        `;
        container.appendChild(row);
    }
    function removeVariantRow(button) {
        const row = button.closest('.variant-row');
        if (row) {
            row.remove();
        }
    }
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
