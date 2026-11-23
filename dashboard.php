<?php
include 'auth_check.php';
include 'koneksi.php';

$jml_pengantin = $weddingku->query("SELECT COUNT(*) as total FROM pengantin")->fetch_assoc()['total'];
$total_ucapan  = $weddingku->query("SELECT COUNT(*) as total FROM message")->fetch_assoc()['total'];
$total_galeri  = $weddingku->query("SELECT COUNT(*) as total FROM gallery")->fetch_assoc()['total'];
$total_tamu    = $weddingku->query("SELECT COUNT(*) as total FROM tamu")->fetch_assoc()['total'];

$tamu_per_pengantin = $weddingku->query("
    SELECT p.id, CONCAT(p.nama_pria, ' & ', p.nama_wanita) as nama_pengantin, COUNT(t.id) as total_tamu
    FROM pengantin p LEFT JOIN tamu t ON p.id = t.pengantin_id GROUP BY p.id
");
$ucapan_per_pengantin = $weddingku->query("
    SELECT p.id, CONCAT(p.nama_pria, ' & ', p.nama_wanita) as nama_pengantin, COUNT(m.id) as total_ucapan
    FROM pengantin p LEFT JOIN message m ON p.id = m.pengantin_id GROUP BY p.id
");
$galeri_per_pengantin = $weddingku->query("
    SELECT p.id, CONCAT(p.nama_pria, ' & ', p.nama_wanita) as nama_pengantin, COUNT(g.id) as total_galeri
    FROM pengantin p LEFT JOIN gallery g ON p.id = g.pengantin_id GROUP BY p.id
");
$pengantinList = $weddingku->query("SELECT nama_pria, nama_wanita FROM pengantin");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Weddingku</title>
    <link rel="icon" href="images/amirulprojectsolutions.ico" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.5s ease forwards',
                        'count-up': 'countUp 1s ease forwards',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .stat-card { animation: fadeInUp 0.5s ease forwards; opacity: 0; }
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #1e293b; }
        ::-webkit-scrollbar-thumb { background: #475569; border-radius: 3px; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 font-sans min-h-screen">

<?php include 'sidebar.php'; ?>

<!-- Main wrapper -->
<div class="lg:ml-64 min-h-screen flex flex-col transition-all duration-300" id="mainContent">

    <!-- Topbar -->
    <header class="sticky top-0 z-20 bg-slate-900/80 backdrop-blur border-b border-slate-800 px-6 py-4 flex items-center gap-4">
        <button onclick="toggleSidebar()"
                class="lg:hidden w-9 h-9 rounded-lg bg-slate-800 hover:bg-slate-700 flex items-center justify-center transition">
            <i class="fa-solid fa-bars text-slate-300 text-sm"></i>
        </button>
        <div>
            <h1 class="text-lg font-semibold text-white">Dashboard</h1>
            <p class="text-xs text-slate-400">Selamat datang di Weddingku Admin Panel</p>
        </div>
        <div class="ml-auto flex items-center gap-2 text-xs text-slate-400 bg-slate-800 px-3 py-1.5 rounded-full">
            <i class="fa-solid fa-circle text-emerald-400 text-[6px]"></i>
            System Online
        </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1 p-6">

        <!-- Welcome Banner -->
        <div class="mb-6 rounded-2xl bg-gradient-to-r from-pink-600/20 via-rose-500/10 to-slate-900 border border-pink-500/20 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-pink-500 to-rose-400 flex items-center justify-center shadow-lg flex-shrink-0">
                <i class="fa-solid fa-heart text-white text-lg"></i>
            </div>
            <div>
                <h2 class="font-semibold text-white text-base">Selamat datang di Aplikasi Undangan Pernikahan Digital 🎊</h2>
                <p class="text-slate-400 text-sm mt-0.5">Kelola semua data pernikahan dari satu tempat dengan mudah.</p>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">

            <!-- Pengantin -->
            <div class="stat-card group bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-pink-500/40 hover:shadow-lg hover:shadow-pink-500/5 transition-all duration-300 cursor-pointer"
                 onclick="toggleDetail('detailPengantin')">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl bg-pink-500/10 flex items-center justify-center group-hover:bg-pink-500/20 transition">
                        <i class="fa-solid fa-heart text-pink-400 text-lg"></i>
                    </div>
                    <span class="text-xs text-slate-500 bg-slate-800 px-2 py-1 rounded-full">Total</span>
                </div>
                <p class="text-3xl font-bold text-white counter" data-target="<?= $jml_pengantin ?>">0</p>
                <p class="text-sm text-slate-400 mt-1">Pasangan Pengantin</p>
                <div class="mt-3 flex items-center gap-1 text-xs text-pink-400 opacity-0 group-hover:opacity-100 transition">
                    <i class="fa-solid fa-eye text-[10px]"></i> Lihat detail
                </div>
            </div>

            <!-- Tamu -->
            <div class="stat-card group bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-blue-500/40 hover:shadow-lg hover:shadow-blue-500/5 transition-all duration-300 cursor-pointer"
                 onclick="toggleDetail('detailTamu')">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl bg-blue-500/10 flex items-center justify-center group-hover:bg-blue-500/20 transition">
                        <i class="fa-solid fa-users text-blue-400 text-lg"></i>
                    </div>
                    <span class="text-xs text-slate-500 bg-slate-800 px-2 py-1 rounded-full">Total</span>
                </div>
                <p class="text-3xl font-bold text-white counter" data-target="<?= $total_tamu ?>">0</p>
                <p class="text-sm text-slate-400 mt-1">Tamu Undangan</p>
                <div class="mt-3 flex items-center gap-1 text-xs text-blue-400 opacity-0 group-hover:opacity-100 transition">
                    <i class="fa-solid fa-eye text-[10px]"></i> Lihat detail
                </div>
            </div>

            <!-- Ucapan -->
            <div class="stat-card group bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-amber-500/40 hover:shadow-lg hover:shadow-amber-500/5 transition-all duration-300 cursor-pointer"
                 onclick="toggleDetail('detailUcapan')">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl bg-amber-500/10 flex items-center justify-center group-hover:bg-amber-500/20 transition">
                        <i class="fa-solid fa-comments text-amber-400 text-lg"></i>
                    </div>
                    <span class="text-xs text-slate-500 bg-slate-800 px-2 py-1 rounded-full">Total</span>
                </div>
                <p class="text-3xl font-bold text-white counter" data-target="<?= $total_ucapan ?>">0</p>
                <p class="text-sm text-slate-400 mt-1">Ucapan & Doa</p>
                <div class="mt-3 flex items-center gap-1 text-xs text-amber-400 opacity-0 group-hover:opacity-100 transition">
                    <i class="fa-solid fa-eye text-[10px]"></i> Lihat detail
                </div>
            </div>

            <!-- Galeri -->
            <div class="stat-card group bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-emerald-500/40 hover:shadow-lg hover:shadow-emerald-500/5 transition-all duration-300 cursor-pointer"
                 onclick="toggleDetail('detailGaleri')">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-500/10 flex items-center justify-center group-hover:bg-emerald-500/20 transition">
                        <i class="fa-solid fa-images text-emerald-400 text-lg"></i>
                    </div>
                    <span class="text-xs text-slate-500 bg-slate-800 px-2 py-1 rounded-full">Total</span>
                </div>
                <p class="text-3xl font-bold text-white counter" data-target="<?= $total_galeri ?>">0</p>
                <p class="text-sm text-slate-400 mt-1">Foto Galeri</p>
                <div class="mt-3 flex items-center gap-1 text-xs text-emerald-400 opacity-0 group-hover:opacity-100 transition">
                    <i class="fa-solid fa-eye text-[10px]"></i> Lihat detail
                </div>
            </div>

        </div>

        <!-- Detail Panels -->
        <div class="space-y-4">

            <!-- Detail Pengantin -->
            <div id="detailPengantin" class="hidden bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-heart text-pink-400 text-sm"></i>
                        <h3 class="font-semibold text-sm text-white">Daftar Pengantin</h3>
                    </div>
                    <button onclick="toggleDetail('detailPengantin')" class="text-slate-500 hover:text-white transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-slate-800/60 text-slate-400 text-xs uppercase">
                            <th class="px-6 py-3 text-left">No</th>
                            <th class="px-6 py-3 text-left">Nama Pria</th>
                            <th class="px-6 py-3 text-left">Nama Wanita</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-800">
                        <?php $no = 1; while ($row = $pengantinList->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-3 text-slate-400"><?= $no++ ?></td>
                                <td class="px-6 py-3 text-white font-medium"><?= htmlspecialchars($row['nama_pria']) ?></td>
                                <td class="px-6 py-3 text-white font-medium"><?= htmlspecialchars($row['nama_wanita']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detail Tamu -->
            <div id="detailTamu" class="hidden bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-users text-blue-400 text-sm"></i>
                        <h3 class="font-semibold text-sm text-white">Tamu per Pengantin</h3>
                    </div>
                    <button onclick="toggleDetail('detailTamu')" class="text-slate-500 hover:text-white transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-slate-800/60 text-slate-400 text-xs uppercase">
                            <th class="px-6 py-3 text-left">No</th>
                            <th class="px-6 py-3 text-left">Nama Pengantin</th>
                            <th class="px-6 py-3 text-left">Jumlah Tamu</th>
                            <th class="px-6 py-3 text-left">Aksi</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-800">
                        <?php $no = 1; $tamu_per_pengantin->data_seek(0); while ($row = $tamu_per_pengantin->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-3 text-slate-400"><?= $no++ ?></td>
                                <td class="px-6 py-3 text-white font-medium"><?= htmlspecialchars($row['nama_pengantin']) ?></td>
                                <td class="px-6 py-3">
                                    <span class="bg-blue-500/10 text-blue-400 text-xs font-semibold px-2.5 py-1 rounded-full">
                                        <?= $row['total_tamu'] ?> tamu
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    <button onclick="showGuests(<?= $row['id'] ?>)"
                                        class="text-xs bg-blue-500/10 text-blue-400 border border-blue-500/20 px-3 py-1.5 rounded-lg hover:bg-blue-500/20 transition">
                                        <i class="fa-solid fa-eye mr-1"></i> Lihat Tamu
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detail Ucapan -->
            <div id="detailUcapan" class="hidden bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-comments text-amber-400 text-sm"></i>
                        <h3 class="font-semibold text-sm text-white">Ucapan per Pengantin</h3>
                    </div>
                    <button onclick="toggleDetail('detailUcapan')" class="text-slate-500 hover:text-white transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-slate-800/60 text-slate-400 text-xs uppercase">
                            <th class="px-6 py-3 text-left">No</th>
                            <th class="px-6 py-3 text-left">Nama Pengantin</th>
                            <th class="px-6 py-3 text-left">Total Ucapan</th>
                            <th class="px-6 py-3 text-left">Aksi</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-800">
                        <?php $no = 1; $ucapan_per_pengantin->data_seek(0); while ($row = $ucapan_per_pengantin->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-3 text-slate-400"><?= $no++ ?></td>
                                <td class="px-6 py-3 text-white font-medium"><?= htmlspecialchars($row['nama_pengantin']) ?></td>
                                <td class="px-6 py-3">
                                    <span class="bg-amber-500/10 text-amber-400 text-xs font-semibold px-2.5 py-1 rounded-full">
                                        <?= $row['total_ucapan'] ?> ucapan
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    <button onclick="showUcapan(<?= $row['id'] ?>)"
                                        class="text-xs bg-amber-500/10 text-amber-400 border border-amber-500/20 px-3 py-1.5 rounded-lg hover:bg-amber-500/20 transition">
                                        <i class="fa-solid fa-eye mr-1"></i> Lihat Ucapan
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detail Galeri -->
            <div id="detailGaleri" class="hidden bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-images text-emerald-400 text-sm"></i>
                        <h3 class="font-semibold text-sm text-white">Galeri per Pengantin</h3>
                    </div>
                    <button onclick="toggleDetail('detailGaleri')" class="text-slate-500 hover:text-white transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-slate-800/60 text-slate-400 text-xs uppercase">
                            <th class="px-6 py-3 text-left">No</th>
                            <th class="px-6 py-3 text-left">Nama Pengantin</th>
                            <th class="px-6 py-3 text-left">Total Galeri</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-800">
                        <?php $no = 1; while ($row = $galeri_per_pengantin->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-3 text-slate-400"><?= $no++ ?></td>
                                <td class="px-6 py-3 text-white font-medium"><?= htmlspecialchars($row['nama_pengantin']) ?></td>
                                <td class="px-6 py-3">
                                    <span class="bg-emerald-500/10 text-emerald-400 text-xs font-semibold px-2.5 py-1 rounded-full">
                                        <?= $row['total_galeri'] ?> foto
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- /Detail Panels -->

        <!-- Quick Links -->
        <div class="mt-8">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Menu Cepat</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <?php
                $quick = [
                    ['href'=>'pengantin.php',          'icon'=>'fa-heart',              'label'=>'Pengantin',   'color'=>'pink'],
                    ['href'=>'tamu.php',               'icon'=>'fa-users',              'label'=>'Tamu',        'color'=>'blue'],
                    ['href'=>'message.php',            'icon'=>'fa-envelope',           'label'=>'Pesan',       'color'=>'amber'],
                    ['href'=>'gallery.php',            'icon'=>'fa-images',             'label'=>'Galeri',      'color'=>'emerald'],
                    ['href'=>'story.php',              'icon'=>'fa-book-open',          'label'=>'Love Story',  'color'=>'violet'],
                    ['href'=>'undangan_url.php',       'icon'=>'fa-link',               'label'=>'Generate',    'color'=>'cyan'],
                ];
                $colorMap = [
                    'pink'    => 'bg-pink-500/10 text-pink-400 hover:bg-pink-500/20 border-pink-500/20',
                    'blue'    => 'bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border-blue-500/20',
                    'amber'   => 'bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border-amber-500/20',
                    'emerald' => 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border-emerald-500/20',
                    'violet'  => 'bg-violet-500/10 text-violet-400 hover:bg-violet-500/20 border-violet-500/20',
                    'cyan'    => 'bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 border-cyan-500/20',
                ];
                foreach ($quick as $q):
                    $cls = $colorMap[$q['color']];
                ?>
                <a href="<?= $q['href'] ?>"
                   class="flex flex-col items-center gap-2 p-4 rounded-xl border <?= $cls ?> transition-all duration-200 hover:scale-105">
                    <i class="fa-solid <?= $q['icon'] ?> text-xl"></i>
                    <span class="text-xs font-medium"><?= $q['label'] ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

    </main>
</div>

<!-- Modal Tamu -->
<div id="guestModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('guestModal')"></div>
    <div class="relative bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-2xl max-h-[80vh] flex flex-col shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
            <h3 class="font-semibold text-white flex items-center gap-2">
                <i class="fa-solid fa-users text-blue-400 text-sm"></i> Daftar Tamu
            </h3>
            <button onclick="closeModal('guestModal')" class="text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <div class="overflow-auto flex-1">
            <table class="w-full text-sm">
                <thead class="sticky top-0 bg-slate-800">
                    <tr class="text-slate-400 text-xs uppercase">
                        <th class="px-6 py-3 text-left">No</th>
                        <th class="px-6 py-3 text-left">Nama</th>
                        <th class="px-6 py-3 text-left">Alamat</th>
                    </tr>
                </thead>
                <tbody id="guestListBody" class="divide-y divide-slate-800"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Ucapan -->
<div id="ucapanModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('ucapanModal')"></div>
    <div class="relative bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-2xl max-h-[80vh] flex flex-col shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
            <h3 class="font-semibold text-white flex items-center gap-2">
                <i class="fa-solid fa-comments text-amber-400 text-sm"></i> Ucapan & Doa
            </h3>
            <button onclick="closeModal('ucapanModal')" class="text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <div class="overflow-auto flex-1">
            <table class="w-full text-sm">
                <thead class="sticky top-0 bg-slate-800">
                    <tr class="text-slate-400 text-xs uppercase">
                        <th class="px-6 py-3 text-left">No</th>
                        <th class="px-6 py-3 text-left">Nama</th>
                        <th class="px-6 py-3 text-left">Pesan</th>
                        <th class="px-6 py-3 text-left">Tanggal</th>
                    </tr>
                </thead>
                <tbody id="ucapanListBody" class="divide-y divide-slate-800"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
// --- Helpers ---
function esc(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

// --- Sidebar toggle ---
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}
// Desktop: sidebar always visible
document.getElementById('sidebar').classList.add('lg:translate-x-0');

// --- Detail panel toggle ---
function toggleDetail(id) {
    const el = document.getElementById(id);
    el.classList.toggle('hidden');
    if (!el.classList.contains('hidden')) {
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// --- Modal ---
function openModal(id) {
    const m = document.getElementById(id);
    m.classList.remove('hidden');
    m.classList.add('flex');
}
function closeModal(id) {
    const m = document.getElementById(id);
    m.classList.add('hidden');
    m.classList.remove('flex');
}

// --- Fetch tamu ---
function showGuests(pengantin_id) {
    fetch('get_tamu.php?pengantin_id=' + pengantin_id)
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('guestListBody');
            tbody.innerHTML = data.length
                ? data.map((g, i) => `<tr class="hover:bg-slate-800/40 transition">
                    <td class="px-6 py-3 text-slate-400">${i+1}</td>
                    <td class="px-6 py-3 text-white font-medium">${esc(g.nama)}</td>
                    <td class="px-6 py-3 text-slate-300">${esc(g.alamat)}</td>
                  </tr>`).join('')
                : `<tr><td colspan="3" class="px-6 py-8 text-center text-slate-500">Belum ada tamu</td></tr>`;
            openModal('guestModal');
        });
}

// --- Fetch ucapan ---
function showUcapan(pengantin_id) {
    fetch('get_ucapan_pengantin.php?pengantin_id=' + pengantin_id)
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('ucapanListBody');
            tbody.innerHTML = data.length
                ? data.map((u, i) => `<tr class="hover:bg-slate-800/40 transition">
                    <td class="px-6 py-3 text-slate-400">${i+1}</td>
                    <td class="px-6 py-3 text-white font-medium">${esc(u.nama)}</td>
                    <td class="px-6 py-3 text-slate-300">${esc(u.pesan)}</td>
                    <td class="px-6 py-3 text-slate-500 text-xs">${esc(u.created_at)}</td>
                  </tr>`).join('')
                : `<tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">Belum ada ucapan</td></tr>`;
            openModal('ucapanModal');
        });
}

// --- Animated counter ---
document.querySelectorAll('.counter').forEach(el => {
    const target = parseInt(el.dataset.target, 10);
    if (!target) { el.textContent = 0; return; }
    const duration = 1000;
    const step = Math.ceil(target / (duration / 16));
    let current = 0;
    const timer = setInterval(() => {
        current = Math.min(current + step, target);
        el.textContent = current;
        if (current >= target) clearInterval(timer);
    }, 16);
});
</script>
</body>
</html>
