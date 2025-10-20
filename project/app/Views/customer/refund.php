<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
$order = $order ?? null;
$orderNo = $order['order_no'] ?? '';
$items = $order['items'] ?? [];
ob_start();
?>
<section class="card card--elevated">
    <div class="card__header">
        <h2 class="text-lg">İade Talebi</h2>
        <p class="text-soft">Sipariş No: <?= $view->escape($orderNo); ?> · <?= $view->escape(date('d.m.Y H:i', strtotime($order['created_at'] ?? 'now'))); ?></p>
    </div>
    <div class="order-summary-grid">
        <div class="order-summary">
            <h3 class="text-md">Sipariş Özeti</h3>
            <ul class="list list--bordered">
                <?php foreach ($items as $item): ?>
                    <li class="list__item">
                        <div class="list__title"><?= $view->escape($item['product_name'] ?? 'Ürün'); ?></div>
                        <div class="list__meta text-soft text-sm">
                            Adet: <?= (int)($item['qty'] ?? 1); ?> · <?= money_fmt((float)($item['unit_price'] ?? 0)); ?> ₺
                            <?php if (!empty($item['variant_name'])): ?>
                                · <?= $view->escape($item['variant_name']); ?>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="order-refund-form">
            <form method="post" action="/iade-talebi/<?= $view->escape($orderNo); ?>" class="form-grid" data-validate>
                <?= csrf_field(); ?>
                <label class="form-field">
                    <span>İade Nedeni</span>
                    <textarea name="reason" rows="6" minlength="10" required placeholder="Karşılaştığınız durumu detaylandırın"></textarea>
                    <small class="text-soft">İade sürecinin hızlanması için net bilgiler paylaşın.</small>
                </label>
                <button type="submit" class="btn btn-primary">Talebi Gönder</button>
            </form>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
