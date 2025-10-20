<?php
$navItems = [];
if ($layoutType === 'admin') {
    $navItems = [
        ['label' => 'Gösterge Paneli', 'href' => '/admin', 'icon' => '📊'],
        ['label' => 'Ürünler', 'href' => '/admin/urunler', 'icon' => '🛒'],
        ['label' => 'Stok Yönetimi', 'href' => '/admin/stok', 'icon' => '🔐'],
        ['label' => 'Kuponlar', 'href' => '/admin/kuponlar', 'icon' => '🎟️'],
        ['label' => 'Siparişler', 'href' => '/admin/siparisler', 'icon' => '📦'],
        ['label' => 'İadeler', 'href' => '/admin/iadeler', 'icon' => '♻️'],
        ['label' => 'Cüzdanlar', 'href' => '/admin/cuzdanlar', 'icon' => '💳'],
        ['label' => 'Raporlar', 'href' => '/admin/raporlar', 'icon' => '📈'],
        ['label' => 'Kullanıcılar', 'href' => '/admin/kullanicilar', 'icon' => '👤'],
        ['label' => 'Ayarlar', 'href' => '/admin/ayarlar', 'icon' => '⚙️'],
        ['label' => 'Log Kayıtları', 'href' => '/admin/loglar', 'icon' => '🗂️'],
        ['label' => 'Bakım Modu', 'href' => '/admin/bakim', 'icon' => '🛠️'],
    ];
} elseif ($layoutType === 'customer') {
    $navItems = [
        ['label' => 'Hesap Özeti', 'href' => '/hesabim', 'icon' => '🌙'],
        ['label' => 'Siparişlerim', 'href' => '/siparislerim', 'icon' => '📦'],
        ['label' => 'Anahtarlarım', 'href' => '/anahtarlarim', 'icon' => '🔑'],
        ['label' => 'Cüzdanım', 'href' => '/cuzdan', 'icon' => '💳'],
    ];
}
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
?>
<div class="sidebar__brand">
    <span><?= htmlspecialchars(config('site_name'), ENT_QUOTES, 'UTF-8'); ?></span>
</div>
<nav class="sidebar__nav" aria-label="Yan menü">
    <?php foreach ($navItems as $item):
        $isActive = $currentPath === $item['href'] || ($item['href'] !== '/' && str_starts_with($currentPath, $item['href']));
    ?>
        <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>" class="sidebar__link<?= $isActive ? ' is-active' : ''; ?>">
            <span aria-hidden="true"><?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?></span>
            <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></span>
        </a>
    <?php endforeach; ?>
</nav>
