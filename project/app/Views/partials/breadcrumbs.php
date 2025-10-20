<?php if (!empty($breadcrumbs)): ?>
    <nav class="breadcrumbs" aria-label="Sayfa izi">
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
            <?php if (!empty($crumb['url']) && $index < count($breadcrumbs) - 1): ?>
                <a href="<?= htmlspecialchars($crumb['url'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?= htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
                <span class="breadcrumbs__separator">›</span>
            <?php else: ?>
                <span><?= htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8'); ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
<?php endif; ?>
