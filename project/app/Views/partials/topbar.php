<?php
$userDisplay = $userName ?: ($userEmail ?: 'Misafir');
?>
<div class="topbar">
    <div class="topbar__left" style="display:flex;align-items:center;gap:var(--spacing-3);">
        <button type="button" class="btn btn-ghost app-sidebar-toggle" data-sidebar-toggle aria-label="Menüyü aç/kapat">
            ☰
        </button>
        <div>
            <?php if (!empty($breadcrumbs)): ?>
                <div class="breadcrumbs__wrapper">
                    <?php include __DIR__ . '/breadcrumbs.php'; ?>
                </div>
            <?php endif; ?>
            <div class="topbar__title"><?= htmlspecialchars($topbarTitle, ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
    </div>
    <div class="topbar__actions">
        <?php foreach ($topbarActions as $action): ?>
            <a href="<?= htmlspecialchars($action['href'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-ghost btn--small">
                <?= htmlspecialchars($action['label'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
        <?php endforeach; ?>
        <div class="pill" aria-label="Aktif kullanıcı">
            <span class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars(strtoupper($userRole), ENT_QUOTES, 'UTF-8'); ?></span>
            <strong><?= htmlspecialchars($userDisplay, ENT_QUOTES, 'UTF-8'); ?></strong>
        </div>
    </div>
</div>
