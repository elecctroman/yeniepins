<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
$refunds = $refunds ?? [];
$selectedStatus = $selectedStatus ?? null;
$statusLabels = [
    'requested' => 'İncelemede',
    'approved' => 'Onaylandı',
    'rejected' => 'Reddedildi',
    'processed' => 'Tamamlandı',
];
ob_start();
?>
<section class="card card--elevated">
    <div class="card__header card__header--between">
        <div>
            <h2>İade Talepleri</h2>
            <p class="text-soft text-sm">Müşteri iadelerini yönetin, cüzdana aktarın veya orijinal ödeme yöntemini simüle edin.</p>
        </div>
        <form method="get" class="form-inline">
            <label class="form-field form-field--inline">
                <span class="text-soft text-xs">Durum</span>
                <select name="durum" onchange="this.form.submit()">
                    <option value="">Tümü</option>
                    <?php foreach ($statusLabels as $key => $label): ?>
                        <option value="<?= $view->escape($key); ?>" <?= $selectedStatus === $key ? 'selected' : ''; ?>><?= $view->escape($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table--striped table--hover">
            <thead>
            <tr>
                <th>Sipariş</th>
                <th>Müşteri</th>
                <th>Tutar</th>
                <th>Durum</th>
                <th>Talep Tarihi</th>
                <th>İşlem</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$refunds): ?>
                <tr>
                    <td colspan="6" class="text-soft">Kriterlere uygun iade bulunamadı.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($refunds as $refund):
                    $status = $refund['status'] ?? 'requested';
                    $label = $statusLabels[$status] ?? ucfirst($status);
                    $badgeClass = match ($status) {
                        'approved', 'processed' => 'badge badge--success',
                        'rejected' => 'badge badge--danger',
                        'requested' => 'badge badge--warning',
                        default => 'badge badge--muted',
                    };
                    $createdAt = $refund['created_at'] ?? '';
                    $formattedDate = $createdAt ? date('d.m.Y H:i', strtotime($createdAt)) : '-';
                    $orderNo = $refund['order_no'] ?? '';
                ?>
                    <tr>
                        <td>
                            <div class="text-sm font-semibold">#<?= $view->escape($orderNo); ?></div>
                            <div class="text-soft text-xs"><?= $view->escape($refund['reason'] ?? ''); ?></div>
                        </td>
                        <td>
                            <div><?= $view->escape($refund['customer_email'] ?? ''); ?></div>
                            <div class="text-soft text-xs">Ödeme: <?= $view->escape($refund['payment_method'] ?? '-'); ?></div>
                        </td>
                        <td>₺<?= money_fmt((float)($refund['total'] ?? 0)); ?></td>
                        <td><span class="<?= $badgeClass; ?>"><?= $view->escape($label); ?></span></td>
                        <td><?= $view->escape($formattedDate); ?></td>
                        <td>
                            <form method="post" action="/admin/iadeler/<?= (int)$refund['id']; ?>/guncelle" class="form-inline">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="amount" value="<?= $view->escape($refund['total'] ?? 0); ?>">
                                <div class="btn-group">
                                    <button name="action" value="approve_wallet" class="btn btn-secondary" <?= $status === 'processed' ? 'disabled' : ''; ?>>Cüzdana Yatır</button>
                                    <button name="action" value="approve_original" class="btn btn-ghost" <?= $status === 'processed' ? 'disabled' : ''; ?>>Orijinal Yöntem</button>
                                    <button name="action" value="reject" class="btn btn-danger" <?= $status === 'rejected' ? 'disabled' : ''; ?>>Reddet</button>
                                </div>
                            </form>
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
