<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
$balance = isset($wallet['balance']) ? (float)$wallet['balance'] : 0.0;
$transactions = $transactions ?? [];
ob_start();
?>
<section class="card card--elevated">
    <div class="card__header flex items-center justify-between">
        <h2 class="text-lg">Cüzdan Bakiyesi</h2>
        <span class="badge badge--glow">Güvende</span>
    </div>
    <div class="wallet-balance-grid">
        <div class="wallet-balance">
            <span class="wallet-balance__label">Güncel Bakiye</span>
            <span class="wallet-balance__value">₺<?= money_fmt($balance); ?></span>
        </div>
        <div class="wallet-meta">
            <span class="wallet-meta__label">Cüzdan No</span>
            <span class="wallet-meta__value">#<?= $view->escape(str_pad((string)($wallet['id'] ?? 0), 6, '0', STR_PAD_LEFT)); ?></span>
        </div>
    </div>
</section>

<section class="card card--elevated">
    <div class="card__header flex items-center justify-between">
        <h3 class="text-md">Son İşlemler</h3>
        <span class="text-soft text-xs">Son <?= count($transactions); ?> kayıt</span>
    </div>
    <?php if (!$transactions): ?>
        <p class="text-soft">Henüz cüzdan hareketiniz bulunmuyor. Satın almalarınız ve iadeleriniz burada listelenecek.</p>
    <?php else: ?>
        <div class="table table--dense table--striped">
            <div class="table__head">
                <div>Tür</div>
                <div>Tutar</div>
                <div>Açıklama</div>
                <div>Tarih</div>
            </div>
            <div class="table__body">
                <?php foreach ($transactions as $tx): ?>
                    <div class="table__row">
                        <div>
                            <span class="badge badge--<?= $tx['type'] === 'credit' ? 'success' : 'danger'; ?>">
                                <?= $tx['type'] === 'credit' ? 'Yükleme' : 'Çıkış'; ?>
                            </span>
                        </div>
                        <div><?= $tx['type'] === 'credit' ? '+' : '-'; ?>₺<?= money_fmt((float)$tx['amount']); ?></div>
                        <div class="text-soft text-sm"><?= $view->escape($tx['reason'] ?? ''); ?></div>
                        <div class="text-soft text-sm"><?= $view->escape(date('d.m.Y H:i', strtotime($tx['created_at'] ?? 'now'))); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
