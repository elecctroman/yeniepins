<?php
$styles = [asset('css/customer.css')];
$scripts = [asset('js/customer.js')];
$bodyClass = 'app--customer';
ob_start();
?>
<section class="card">
    <h2>Hesap Bilgileri</h2>
    <p class="text-muted">Profil detaylarınızı görüntüleyin ve güncelleyin.</p>
    <dl class="definition">
        <div>
            <dt>E-posta</dt>
            <dd><?= htmlspecialchars(\Core\Session::get('user_email', 'bilgi yok'), ENT_QUOTES, 'UTF-8'); ?></dd>
        </div>
        <div>
            <dt>Rol</dt>
            <dd><?= htmlspecialchars(\Core\Session::get('user_role', 'customer'), ENT_QUOTES, 'UTF-8'); ?></dd>
        </div>
    </dl>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
