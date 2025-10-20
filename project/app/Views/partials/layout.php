<?php
\Core\Session::start();
$csrfToken = $csrfToken ?? \Core\CSRF::token();
$styles = $styles ?? [];
$scripts = $scripts ?? [];
$bodyClass = trim('app ' . ($bodyClass ?? ''));
$title = $title ?? config('site_name');
$layoutType = $layoutType ?? (str_contains($bodyClass, 'app--admin') ? 'admin' : (str_contains($bodyClass, 'app--customer') ? 'customer' : 'public'));
$hasSidebar = in_array($layoutType, ['admin', 'customer'], true);
$showHeader = $showHeader ?? !$hasSidebar;
$showFooter = $showFooter ?? true;
$breadcrumbs = $breadcrumbs ?? [];
$topbarTitle = $topbarTitle ?? $title;
$topbarActions = $topbarActions ?? [];
$userName = \Core\Session::get('user_name', '');
$userEmail = \Core\Session::get('user_email', '');
$userRole = \Core\Session::get('user_role', 'guest');
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
<body class="<?= htmlspecialchars(trim($bodyClass . ' app--' . $layoutType), ENT_QUOTES, 'UTF-8'); ?>">
<?php if ($showHeader): ?>
    <?php include __DIR__ . '/header.php'; ?>
<?php endif; ?>
<?php if ($hasSidebar): ?>
    <div class="app-shell app-shell--with-sidebar">
        <aside class="app-sidebar sidebar" data-sidebar>
            <?php include __DIR__ . '/sidebar.php'; ?>
        </aside>
        <div class="app-content">
            <?php include __DIR__ . '/topbar.php'; ?>
            <?php include __DIR__ . '/flash.php'; ?>
            <div class="page-content">
                <?= $content ?? ''; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <main class="app-main">
        <?php include __DIR__ . '/flash.php'; ?>
        <?= $content ?? ''; ?>
    </main>
<?php endif; ?>
<?php if ($showFooter): ?>
    <?php include __DIR__ . '/footer.php'; ?>
<?php endif; ?>
<?php include __DIR__ . '/modal.php'; ?>
<script src="<?= asset('js/app.js'); ?>" defer></script>
<?php foreach ($scripts as $script): ?>
    <script src="<?= htmlspecialchars($script, ENT_QUOTES, 'UTF-8'); ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
<?php \Core\Session::clearOldInput(); ?>
