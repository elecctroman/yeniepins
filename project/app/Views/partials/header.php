<?php
\Core\Session::start();
$userId = \Core\Session::get('user_id');
$userRole = \Core\Session::get('user_role');
?>
<header class="app-header">
    <h1 class="app-title"><a href="/"><?= config('site_name'); ?></a></h1>
    <nav class="app-nav">
        <a href="/">Ana Sayfa</a>
        <a href="/sepet">Sepet</a>
        <?php if ($userId): ?>
            <a href="/hesabim">Hesabım</a>
            <?php if (in_array($userRole, ['admin', 'editor', 'support'], true)): ?>
                <a href="/admin">Yönetim</a>
            <?php endif; ?>
            <form method="post" action="/cikis">
                <?= csrf_field(); ?>
                <button type="submit" class="btn btn-secondary btn--small">Çıkış</button>
            </form>
        <?php else: ?>
            <a href="/giris">Giriş</a>
            <a href="/kayit">Kayıt</a>
        <?php endif; ?>
    </nav>
</header>
