<?php
$current_page = basename($_SERVER['PHP_SELF']);
$nav_items = [
    ['href' => 'dashboard.php',         'icon' => 'fa-house',         'label' => 'Dashboard'],
    ['href' => 'pengantin.php',          'icon' => 'fa-heart',         'label' => 'Data Pengantin'],
    ['href' => 'tamu.php',              'icon' => 'fa-users',         'label' => 'Tamu Undangan'],
    ['href' => 'message.php',           'icon' => 'fa-envelope',      'label' => 'Pesan Pengantin'],
    ['href' => 'story.php',             'icon' => 'fa-book-open',     'label' => 'Love Story'],
    ['href' => 'gallery.php',           'icon' => 'fa-images',        'label' => 'Galeri Foto'],
    ['href' => 'undangan_url.php',      'icon' => 'fa-link',          'label' => 'Generate Invitation'],
    ['href' => 'rekening_pengantin.php','icon' => 'fa-credit-card',   'label' => 'Brides Account'],
    ['href' => 'log_akses_undangan.php','icon' => 'fa-clock-rotate-left','label' => 'Log Akses'],
    ['href' => 'logout.php',            'icon' => 'fa-right-from-bracket','label' => 'Logout'],
];
?>

<!-- Sidebar -->
<aside id="sidebar"
    class="fixed top-0 left-0 h-full w-64 z-40
           bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900
           shadow-2xl flex flex-col
           transition-transform duration-300 ease-in-out">

    <!-- Brand -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-pink-500 to-rose-400 flex items-center justify-center shadow-lg">
            <i class="fa-solid fa-heart text-white text-sm"></i>
        </div>
        <div>
            <span class="block text-white font-bold text-sm leading-tight">Wedding</span>
            <span class="block text-slate-400 text-xs">Dashboard Admin</span>
        </div>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        <?php foreach ($nav_items as $item):
            $active = ($current_page === $item['href']);
        ?>
        <a href="<?= $item['href'] ?>"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                  <?= $active
                        ? 'bg-gradient-to-r from-pink-500/20 to-rose-500/10 text-pink-400 border border-pink-500/30'
                        : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' ?>">
            <i class="fa-solid <?= $item['icon'] ?> w-4 text-center <?= $active ? 'text-pink-400' : 'text-slate-500' ?>"></i>
            <span><?= $item['label'] ?></span>
            <?php if ($active): ?>
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-pink-400"></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </nav>

    <!-- Footer -->
    <div class="px-6 py-4 border-t border-slate-700">
        <p class="text-slate-500 text-xs text-center">© 2025 Weddingku App</p>
    </div>
</aside>

<!-- Overlay mobile -->
<div id="sidebarOverlay"
     class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"
     onclick="toggleSidebar()"></div>
