<?php
$styles = [asset('css/admin.css')];
$scripts = [asset('js/admin.js')];
$bodyClass = 'app--admin';
$wallets = $wallets ?? [];
ob_start();
?>
<section class="card card--elevated">
    <div class="card__header card__header--between">
        <div>
            <h2>Cüzdanlar</h2>
            <p class="text-soft text-sm">Müşteri bakiyelerini görüntüleyin ve manuel düzenleyin.</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table--striped table--hover">
            <thead>
            <tr>
                <th>Müşteri</th>
                <th>E-posta</th>
                <th>Bakiye</th>
                <th>Güncelleme</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$wallets): ?>
                <tr><td colspan="4" class="text-soft">Kayıtlı cüzdan bulunamadı.</td></tr>
            <?php else: ?>
                <?php foreach ($wallets as $wallet): ?>
                    <tr>
                        <td><?= $view->escape($wallet['customer_name'] ?? 'Bilinmiyor'); ?></td>
                        <td><?= $view->escape($wallet['customer_email'] ?? '-'); ?></td>
                        <td>₺<?= money_fmt((float)($wallet['balance'] ?? 0)); ?></td>
                        <td>
                            <form method="post" action="/admin/cuzdanlar/duzelt" class="form-inline">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="user_id" value="<?= (int)($wallet['customer_id'] ?? 0); ?>">
                                <select name="type" class="input--sm">
                                    <option value="credit">Kredi</option>
                                    <option value="debit">Borç</option>
                                </select>
                                <input type="number" name="amount" step="0.01" min="0" class="input--sm" placeholder="Tutar" required>
                                <input type="text" name="reason" class="input--sm" placeholder="Açıklama" value="Manuel düzeltme">
                                <button type="submit" class="btn btn-secondary btn--sm">Uygula</button>
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
