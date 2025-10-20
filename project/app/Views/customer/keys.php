<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
$deliveries = $deliveries ?? [];
$typeMap = [
    'epin' => ['label' => 'E-PIN', 'class' => 'badge badge--info'],
    'license' => ['label' => 'Lisans', 'class' => 'badge badge--primary'],
    'account' => ['label' => 'Dijital Hesap', 'class' => 'badge badge--success'],
];
ob_start();
?>
<section class="card card--elevated">
    <div class="card__header card__header--between">
        <div>
            <h2>Anahtarlarım</h2>
            <p class="text-muted small">Teslim edilen kodlar, lisanslar ve dijital hesap bilgileri.</p>
        </div>
        <div class="card__actions">
            <button type="button" class="btn btn-ghost" data-copy="<?= htmlspecialchars(\Core\Session::get('user_email', ''), ENT_QUOTES, 'UTF-8'); ?>">E-postamı Kopyala</button>
        </div>
    </div>
    <?php if (!$deliveries): ?>
        <div class="empty-state">
            <p class="text-muted">Henüz teslim edilmiş anahtarınız bulunmuyor. Sipariş verdikten sonra anahtarlarınız burada listelenir.</p>
            <a href="/urunler" class="btn btn-primary">Mağazaya Dön</a>
        </div>
    <?php else: ?>
        <div class="grid grid--cols-1 grid--md-2 grid--gap-lg">
            <?php foreach ($deliveries as $item):
                $delivery = $item['delivered_data'] ?? [];
                $type = $item['type'] ?? 'epin';
                $typeInfo = $typeMap[$type] ?? ['label' => ucfirst($type), 'class' => 'badge'];
                $orderNo = $item['order_no'] ?? '';
                $orderedAt = $item['order_created_at'] ?? null;
                $orderedAtDisplay = $orderedAt ? date('d.m.Y H:i', strtotime($orderedAt)) : '-';
            ?>
                <article class="card card--subtle">
                    <header class="card__header">
                        <div>
                            <h3><?= htmlspecialchars($item['product_name'] ?? 'Ürün', ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="text-muted small">Sipariş No: <?= htmlspecialchars($orderNo, ENT_QUOTES, 'UTF-8'); ?> · <?= htmlspecialchars($orderedAtDisplay, ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <span class="<?= htmlspecialchars($typeInfo['class'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($typeInfo['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </header>
                    <div class="card__body">
                        <?php if (is_array($delivery) && isset($delivery['keys']) && is_array($delivery['keys'])): ?>
                            <ul class="list list--bordered">
                                <?php foreach ($delivery['keys'] as $code): ?>
                                    <li class="list__item list__item--with-action">
                                        <span class="code-chip"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <button type="button" class="btn btn-secondary btn--small" data-copy="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>">Kopyala</button>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php elseif (is_array($delivery) && isset($delivery['accounts']) && is_array($delivery['accounts'])): ?>
                            <div class="stack stack--md">
                                <?php foreach ($delivery['accounts'] as $account): ?>
                                    <div class="card card--ghost">
                                        <div class="stack stack--xs">
                                            <div class="key-value">
                                                <span class="key-label">Kullanıcı Adı</span>
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
                                            <?php if (!empty($account['extra']) && is_array($account['extra'])): ?>
                                                <div class="key-extra">
                                                    <?php foreach ($account['extra'] as $key => $value): ?>
                                                        <p class="text-muted small"><strong><?= htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8'); ?>:</strong> <?= htmlspecialchars(is_scalar($value) ? (string)$value : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?></p>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif (is_array($delivery) && isset($delivery['username'])): ?>
                            <div class="stack stack--sm">
                                <div class="key-value">
                                    <span class="key-label">Kullanıcı Adı</span>
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
                                <?php if (!empty($delivery['extra']) && is_array($delivery['extra'])): ?>
                                    <div class="key-extra">
                                        <?php foreach ($delivery['extra'] as $key => $value): ?>
                                            <p class="text-muted small"><strong><?= htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8'); ?>:</strong> <?= htmlspecialchars(is_scalar($value) ? (string)$value : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?></p>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Bu sipariş için teslimat bilgileri yönetici tarafından sağlanacaktır.</p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
