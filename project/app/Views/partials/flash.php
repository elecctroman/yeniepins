<?php if (!empty($flashMessages)): ?>
    <div class="flash-stack" role="alert" aria-live="assertive">
        <?php foreach ($flashMessages as $variant => $message): ?>
            <div class="alert alert--<?= htmlspecialchars($variant, ENT_QUOTES, 'UTF-8'); ?>">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
