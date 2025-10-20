<?php
\Core\Session::start();
$token = \Core\CSRF::token();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Dijital Market', ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="<?= asset('css/theme.css'); ?>">
    <link rel="stylesheet" href="<?= asset('css/customer.css'); ?>">
    <script defer src="<?= asset('js/app.js'); ?>"></script>
    <script defer src="<?= asset('js/customer.js'); ?>"></script>
</head>
<body class="app app--customer">
<header class="app-header">
    <h1 class="app-title"><?= config('site_name'); ?></h1>
</header>
<main class="app-main">
    <section class="card">
        <h2>Hoş geldiniz</h2>
        <p>Dijital ürünlerinizi yönetmek için kapsamlı platform.</p>
        <form method="post" class="form-inline">
            <input type="hidden" name="_csrf" value="<?= $token; ?>">
            <button class="btn btn-primary" type="submit">Başla</button>
        </form>
    </section>
</main>
<footer class="app-footer">
    <small>&copy; <?= date('Y'); ?> <?= config('site_name'); ?></small>
</footer>
</body>
</html>
