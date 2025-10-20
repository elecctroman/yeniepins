<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
$orders = $orders ?? [];
$refunds = $refunds ?? [];
$lastOrderNo = \Core\Session::get('last_order_no');
$statusMap = [
    'pending' => ['label' => 'Beklemede', 'class' => 'badge badge--warning'],
    'paid' => ['label' => 'Ödendi', 'class' => 'badge badge--success'],
    'cancelled' => ['label' => 'İptal', 'class' => 'badge badge--muted'],
    'refunded' => ['label' => 'İade', 'class' => 'badge badge--info'],
    'failed' => ['label' => 'Başarısız', 'class' => 'badge badge--danger'],
];
$refundStatusMap = [
    'requested' => ['label' => 'İncelemede', 'class' => 'badge badge--warning'],
    'approved' => ['label' => 'Onaylandı', 'class' => 'badge badge--success'],
    'rejected' => ['label' => 'Reddedildi', 'class' => 'badge badge--danger'],
    'processed' => ['label' => 'Tamamlandı', 'class' => 'badge badge--info'],
];
ob_start();
?>
<section class="card card--elevated">
    <div class="card__header card__header--between">
        <div>
            <h2>Siparişlerim</h2>
            <?php if ($lastOrderNo): ?>
                <p class="text-muted small">Son sipariş numaranız: <span class="text-highlight"><?= htmlspecialchars($lastOrderNo, ENT_QUOTES, 'UTF-8'); ?></span></p>
            <?php else: ?>
                <p class="text-muted small">Mağazadan verdiğiniz siparişler burada listelenir.</p>
            <?php endif; ?>
        </div>
        <div class="card__actions">
            <a href="/anahtarlarim" class="btn btn-secondary">Teslimatlarım</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table--striped table--hover">
            <thead>
            <tr>
                <th>Sipariş No</th>
                <th>Tarih</th>
                <th>Ürün Sayısı</th>
                <th>Tutar</th>
                <th>Durum</th>
                <th>İade Durumu</th>
                <th class="text-right">İşlem</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$orders): ?>
                <tr>
                    <td colspan="7" class="text-muted">Henüz siparişiniz bulunmuyor.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order):
                    $status = $order['status'] ?? 'pending';
                    $statusInfo = $statusMap[$status] ?? ['label' => ucfirst($status), 'class' => 'badge badge--muted'];
                    $createdAt = $order['created_at'] ?? null;
                    $displayDate = $createdAt ? date('d.m.Y H:i', strtotime($createdAt)) : '-';
                    $itemCount = (int)($order['item_count'] ?? 0);
                    $total = isset($order['total']) ? money_fmt((float)$order['total']) : '0,00';
                    $refund = $refunds[$order['id']] ?? null;
                    $refundBadge = $refund ? ($refundStatusMap[$refund['status']] ?? ['label' => ucfirst($refund['status']), 'class' => 'badge']) : null;
                ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($order['order_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></td>
                        <td><?= htmlspecialchars($displayDate, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= $itemCount; ?></td>
                        <td><?= htmlspecialchars($total, ENT_QUOTES, 'UTF-8'); ?> ₺</td>
                        <td><span class="<?= htmlspecialchars($statusInfo['class'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($statusInfo['label'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td>
                            <?php if ($refundBadge): ?>
                                <span class="<?= htmlspecialchars($refundBadge['class'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($refundBadge['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php elseif ($status === 'paid'): ?>
                                <span class="badge badge--ghost">İade talebi yok</span>
                            <?php else: ?>
                                <span class="text-soft text-xs">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <a href="/anahtarlarim" class="btn btn-ghost">Anahtarlar</a>
                            <?php if ($status === 'paid' && !$refund): ?>
                                <a href="/iade-talebi/<?= htmlspecialchars($order['order_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary">İade Talebi</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
