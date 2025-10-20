<?php
\Core\Session::start();
$userId = \Core\Session::get('user_id');
$userRole = \Core\Session::get('user_role');
?>
<header class="app-header">
    <div class="app-header__inner" style="display:flex;align-items:center;justify-content:space-between;gap:var(--spacing-4);">
        <div class="app-title"><a href="/" class="focus-ring"><?= htmlspecialchars(config('site_name'), ENT_QUOTES, 'UTF-8'); ?></a></div>
        <nav class="app-nav" aria-label="Ana menü">
            <a href="/" class="focus-ring">Ana Sayfa</a>
            <a href="/kategori/epin" class="focus-ring">Kategoriler</a>
            <a href="/sepet" class="focus-ring">Sepet</a>
            <?php if ($userId): ?>
                <a href="/hesabim" class="focus-ring">Hesabım</a>
                <?php if (in_array($userRole, ['admin', 'editor', 'support'], true)): ?>
                    <a href="/admin" class="focus-ring">Yönetim</a>
                <?php endif; ?>
                <form method="post" action="/cikis" class="focus-ring" style="display:inline-flex;">
                    <?= csrf_field(); ?>
                    <button type="submit" class="btn btn-secondary btn--small">Çıkış</button>
                </form>
            <?php else: ?>
                <a href="/giris" class="btn btn-secondary btn--small focus-ring">Giriş</a>
                <a href="/kayit" class="btn btn-primary btn--small focus-ring">Kayıt</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
