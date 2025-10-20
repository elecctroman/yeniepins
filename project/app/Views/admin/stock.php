<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
$breadcrumbs = $breadcrumbs ?? [
    ['label' => 'Yönetim', 'url' => '/admin'],
    ['label' => 'Stok Yönetimi'],
];
ob_start();
?>
<section class="card">
    <div class="card__header">
        <div>
            <h2 class="card__title">Stok Görünümü</h2>
            <p class="text-muted">E-PIN, lisans ve hesap stoklarını gerçek zamanlı olarak izleyin, kritik eksiklere hızlı aksiyon alın.</p>
        </div>
    </div>
    <div class="grid grid--cols-3">
        <?php foreach ($stockCards as $card): ?>
            <?php $product = $card['product']; $summary = $card['summary']; ?>
            <article class="card card--sub">
                <header class="card__header">
                    <h3 class="card__title"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="text-muted">Tip: <?= strtoupper(htmlspecialchars($product['type'], ENT_QUOTES, 'UTF-8')); ?> · #<?= (int)$product['id']; ?></p>
                </header>
                <div class="card__content">
                    <?php if ($product['stock_policy'] === 'unlimited'): ?>
                        <p class="text-muted">Bu ürün için stok takibi yapılmıyor.</p>
                    <?php else: ?>
                        <div class="stack">
                            <span class="badge badge--success">Hazır: <?= (int)($summary['available'] ?? 0); ?></span>
                            <span class="badge badge--warning">Rezerve: <?= (int)($summary['reserved'] ?? 0); ?></span>
                            <span class="badge">Satıldı: <?= (int)($summary['sold'] ?? 0); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <footer class="card__footer">
                    <a href="/admin/urunler/<?= (int)$product['id']; ?>/duzenle" class="btn btn-secondary btn-sm">Ürünü Yönet</a>
                    <a href="/urun/<?= htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-ghost btn-sm" target="_blank">Mağazada Gör</a>
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="grid grid--cols-2">
    <div class="card">
        <div class="card__header">
            <h3 class="card__title">E-PIN Toplu Yükleme</h3>
            <p class="text-muted">CSV dosyasından toplu kod ekleyin. Kodlar otomatik olarak tekil olarak saklanır.</p>
        </div>
        <form method="post" action="/admin/stok/epin-yukle" enctype="multipart/form-data" class="form-grid" data-validate>
            <?= csrf_field(); ?>
            <label class="form-field">
                <span>E-PIN Ürünü</span>
                <select name="product_id" required>
                    <option value="">Ürün Seçin</option>
                    <?php foreach ($stockCards as $card): ?>
                        <?php if (($card['product']['type'] ?? '') === 'epin'): ?>
                            <option value="<?= (int)$card['product']['id']; ?>">#<?= (int)$card['product']['id']; ?> · <?= htmlspecialchars($card['product']['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="form-field">
                <span>CSV Dosyası</span>
                <input type="file" name="csv_file" accept=".csv,text/csv" required>
                <small class="text-muted">Her satıra bir kod, UTF-8 formatında.</small>
            </label>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Yükle</button>
            </div>
        </form>
        <?php if (!empty($epinLists)): ?>
            <div class="table-responsive">
                <table class="table table--compact">
                    <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Kod</th>
                        <th>Durum</th>
                        <th>Batch</th>
                        <th>Eklenme</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($epinLists as $productId => $rows): ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td>#<?= (int)$productId; ?></td>
                                <td><code><?= htmlspecialchars($row['code'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                <td><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['batch_id'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['created_at'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card__header">
            <h3 class="card__title">Dijital Hesap Stoğu</h3>
            <p class="text-muted">Tekli hesap ekleyin, şifreler güvenli şekilde şifrelenerek saklanır.</p>
        </div>
        <form method="post" action="/admin/stok/hesap-ekle" class="form-grid" data-validate>
            <?= csrf_field(); ?>
            <label class="form-field">
                <span>Hesap Ürünü</span>
                <select name="product_id" required>
                    <option value="">Ürün Seçin</option>
                    <?php foreach ($stockCards as $card): ?>
                        <?php if (($card['product']['type'] ?? '') === 'account'): ?>
                            <option value="<?= (int)$card['product']['id']; ?>">#<?= (int)$card['product']['id']; ?> · <?= htmlspecialchars($card['product']['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </label>
            <div class="grid grid--cols-2">
                <label class="form-field">
                    <span>Kullanıcı Adı</span>
                    <input type="text" name="username" required>
                </label>
                <label class="form-field">
                    <span>Şifre</span>
                    <input type="text" name="password" required>
                </label>
            </div>
            <label class="form-field">
                <span>Ek Bilgi (JSON)</span>
                <textarea name="extra" rows="3" placeholder='{"not":"opsiyonel açıklama"}'></textarea>
            </label>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Stoğa Ekle</button>
            </div>
        </form>
        <?php if (!empty($accountLists)): ?>
            <div class="table-responsive">
                <table class="table table--compact">
                    <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Kullanıcı</th>
                        <th>Şifre</th>
                        <th>Durum</th>
                        <th>Eklenme</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($accountLists as $productId => $rows): ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td>#<?= (int)$productId; ?></td>
                                <td><?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><code><?= htmlspecialchars($row['password_masked'] ?? '••••', ENT_QUOTES, 'UTF-8'); ?></code></td>
                                <td><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['created_at'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
