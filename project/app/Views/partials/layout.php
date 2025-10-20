<?php
\Core\Session::start();
$csrfToken = $csrfToken ?? \Core\CSRF::token();
$styles = $styles ?? [];
$scripts = $scripts ?? [];
$bodyClass = $bodyClass ?? '';
$title = $title ?? config('site_name');
$flashTypes = ['success', 'error', 'info', 'warning'];
$flashMessages = [];
foreach ($flashTypes as $type) {
    $message = \Core\Session::flash($type);
    if ($message) {
        $flashMessages[$type] = $message;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="<?= asset('css/theme.css'); ?>">
    <?php foreach ($styles as $style): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($style, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endforeach; ?>
</head>
<body class="app <?= htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8'); ?>">
<?php include __DIR__ . '/header.php'; ?>
<main class="app-main">
    <?php if (!empty($flashMessages)): ?>
        <div class="alert-stack">
            <?php foreach ($flashMessages as $variant => $message): ?>
                <div class="alert alert--<?= htmlspecialchars($variant, ENT_QUOTES, 'UTF-8'); ?>">
                    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?= $content ?? ''; ?>
</main>
<?php include __DIR__ . '/footer.php'; ?>
<script src="<?= asset('js/app.js'); ?>" defer></script>
<?php foreach ($scripts as $script): ?>
    <script src="<?= htmlspecialchars($script, ENT_QUOTES, 'UTF-8'); ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
<?php \Core\Session::clearOldInput(); ?>
