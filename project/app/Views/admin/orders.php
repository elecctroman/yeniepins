<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
$orders = $orders ?? [];
$selectedOrder = $selectedOrder ?? null;
$statusMap = [
    'pending' => ['label' => 'Beklemede', 'class' => 'badge badge--warning'],
    'paid' => ['label' => 'Ödendi', 'class' => 'badge badge--success'],
    'cancelled' => ['label' => 'İptal', 'class' => 'badge badge--muted'],
    'refunded' => ['label' => 'İade', 'class' => 'badge badge--info'],
    'failed' => ['label' => 'Başarısız', 'class' => 'badge badge--danger'],
];
ob_start();
?>
<section class="card">
    <div class="card__header card__header--between">
        <div>
            <h2>Siparişler</h2>
            <p class="text-muted small">Toplam <?= count($orders); ?> kayıt listeleniyor.</p>
        </div>
        <?php if (!empty($selectedNo)): ?>
            <a href="/admin/siparisler" class="btn btn-ghost">Filtreyi Temizle</a>
        <?php endif; ?>
    </div>
    <div class="table-responsive">
        <table class="table table--striped table--hover">
            <thead>
            <tr>
                <th>No</th>
                <th>Müşteri</th>
                <th>Tutar</th>
                <th>Durum</th>
                <th>Ödeme</th>
                <th>Tarih</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$orders): ?>
                <tr>
                    <td colspan="6" class="text-muted">Henüz sipariş bulunmuyor.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order):
                    $status = $order['status'] ?? 'pending';
                    $statusInfo = $statusMap[$status] ?? ['label' => ucfirst($status), 'class' => 'badge'];
                    $total = isset($order['total']) ? money_fmt((float)$order['total']) : '0,00';
                    $createdAt = $order['created_at'] ?? null;
                    $displayDate = $createdAt ? date('d.m.Y H:i', strtotime($createdAt)) : '-';
                    $customer = trim(($order['customer_name'] ?? '') . ' ' . ($order['customer_email'] ?? ''));
                ?>
                    <tr class="table__row--selectable <?= (!empty($selectedNo) && $selectedNo === ($order['order_no'] ?? '')) ? 'is-active' : ''; ?>">
                        <td><a href="/admin/siparisler?no=<?= rawurlencode($order['order_no'] ?? ''); ?>" class="link-muted"><?= htmlspecialchars($order['order_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?></a></td>
                        <td><?= htmlspecialchars($customer ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($total, ENT_QUOTES, 'UTF-8'); ?> ₺</td>
                        <td><span class="<?= htmlspecialchars($statusInfo['class'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($statusInfo['label'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td><?= htmlspecialchars($order['payment_method'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($displayDate, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php if ($selectedOrder):
    $items = $selectedOrder['items'] ?? [];
    $hasDelivered = false;
    foreach ($items as $item) {
        if (!empty($item['delivered_data'])) {
            $hasDelivered = true;
            break;
        }
    }
    $status = $selectedOrder['status'] ?? 'pending';
    $statusInfo = $statusMap[$status] ?? ['label' => ucfirst($status), 'class' => 'badge'];
    $total = isset($selectedOrder['total']) ? money_fmt((float)$selectedOrder['total']) : '0,00';
    $paidAt = $selectedOrder['paid_at'] ?? null;
    $createdAt = $selectedOrder['created_at'] ?? null;
?>
<section class="card card--elevated">
    <header class="card__header card__header--between">
        <div>
            <h3>Sipariş Detayı · <?= htmlspecialchars($selectedOrder['order_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
            <p class="text-muted small">Oluşturma: <?= htmlspecialchars($createdAt ? date('d.m.Y H:i', strtotime($createdAt)) : '-', ENT_QUOTES, 'UTF-8'); ?> · Ödeme: <?= htmlspecialchars($paidAt ? date('d.m.Y H:i', strtotime($paidAt)) : 'Bekliyor', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="card__actions">
            <form method="post" action="/admin/siparisler/<?= rawurlencode($selectedOrder['order_no'] ?? ''); ?>/tekrar-teslim" class="inline-form">
                <?= csrf_field(); ?>
                <button type="submit" class="btn btn-secondary" <?= $hasDelivered ? '' : 'disabled'; ?>>Teslimatı Yenile</button>
            </form>
            <form method="post" action="/admin/siparisler/<?= rawurlencode($selectedOrder['order_no'] ?? ''); ?>/iade" class="inline-form">
                <?= csrf_field(); ?>
                <button type="submit" class="btn btn-ghost">İade Başlat</button>
            </form>
        </div>
    </header>
    <div class="card__body">
        <div class="data-grid">
            <div>
                <span class="data-grid__label">Müşteri</span>
                <span class="data-grid__value"><?= htmlspecialchars($selectedOrder['customer_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div>
                <span class="data-grid__label">E-posta</span>
                <span class="data-grid__value"><?= htmlspecialchars($selectedOrder['email'] ?? $selectedOrder['customer_email'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div>
                <span class="data-grid__label">Durum</span>
                <span class="data-grid__value"><span class="<?= htmlspecialchars($statusInfo['class'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($statusInfo['label'], ENT_QUOTES, 'UTF-8'); ?></span></span>
            </div>
            <div>
                <span class="data-grid__label">Ödeme Yöntemi</span>
                <span class="data-grid__value"><?= htmlspecialchars($selectedOrder['payment_method'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div>
                <span class="data-grid__label">Ödeme Referansı</span>
                <span class="data-grid__value"><?= htmlspecialchars($selectedOrder['payment_ref'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div>
                <span class="data-grid__label">Toplam</span>
                <span class="data-grid__value"><?= htmlspecialchars($total, ENT_QUOTES, 'UTF-8'); ?> ₺</span>
            </div>
        </div>
        <div class="stack stack--lg mt-lg">
            <h4>Ürünler</h4>
            <?php if (!$items): ?>
                <p class="text-muted">Siparişe ait ürün bulunamadı.</p>
            <?php else: ?>
                <?php foreach ($items as $item):
                    $delivery = $item['delivered_data'] ?? null;
                    $lineTotal = money_fmt(((float)($item['unit_price'] ?? 0)) * (int)($item['qty'] ?? 1));
                ?>
                    <article class="card card--ghost">
                        <div class="card__header card__header--between">
                            <div>
                                <h5><?= htmlspecialchars($item['product_name'] ?? 'Ürün', ENT_QUOTES, 'UTF-8'); ?></h5>
                                <p class="text-muted small">Adet: <?= (int)($item['qty'] ?? 0); ?> · Varyant: <?= htmlspecialchars($item['variant_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <span class="badge badge--outline">Satır Tutarı: <?= htmlspecialchars($lineTotal, ENT_QUOTES, 'UTF-8'); ?> ₺</span>
                        </div>
                        <div class="card__body">
                            <?php if (is_array($delivery) && isset($delivery['keys'])): ?>
                                <ul class="list list--bordered">
                                    <?php foreach ($delivery['keys'] as $code): ?>
                                        <li class="list__item list__item--with-action">
                                            <span class="code-chip"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></span>
                                            <button type="button" class="btn btn-ghost btn--small" data-copy="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>">Kopyala</button>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php elseif (is_array($delivery) && isset($delivery['accounts'])): ?>
                                <div class="stack stack--sm">
                                    <?php foreach ($delivery['accounts'] as $account): ?>
                                        <div class="key-value">
                                            <span class="key-label">Kullanıcı</span>
                                            <div class="key-actions">
                                                <span class="code-chip"><?= htmlspecialchars($account['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                                <button type="button" class="btn btn-ghost btn--small" data-copy="<?= htmlspecialchars($account['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">Kopyala</button>
                                            </div>
                                        </div>
                                        <div class="key-value">
                                            <span class="key-label">Parola</span>
                                            <div class="key-actions">
                                                <span class="code-chip"><?= htmlspecialchars($account['password'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                                <button type="button" class="btn btn-ghost btn--small" data-copy="<?= htmlspecialchars($account['password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">Kopyala</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif (is_array($delivery) && isset($delivery['username'])): ?>
                                <div class="stack stack--sm">
                                    <div class="key-value">
                                        <span class="key-label">Kullanıcı</span>
                                        <div class="key-actions">
                                            <span class="code-chip"><?= htmlspecialchars($delivery['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                            <button type="button" class="btn btn-ghost btn--small" data-copy="<?= htmlspecialchars($delivery['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">Kopyala</button>
                                        </div>
                                    </div>
                                    <div class="key-value">
                                        <span class="key-label">Parola</span>
                                        <div class="key-actions">
                                            <span class="code-chip"><?= htmlspecialchars($delivery['password'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                            <button type="button" class="btn btn-ghost btn--small" data-copy="<?= htmlspecialchars($delivery['password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">Kopyala</button>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Bu ürün için teslimat bekleniyor.</p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
