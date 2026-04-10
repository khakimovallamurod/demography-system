<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? h($page_title) . ' | ' : '' ?><?= SITE_NAME ?> — Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .nav-link { transition: all 0.2s; border-left: 3px solid transparent; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.13); border-left-color: #fff; }
        .card-hover { transition: transform 0.18s, box-shadow 0.18s; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-slate-800 to-slate-700 text-white z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300">
    <div class="p-5 border-b border-slate-600">
        <div class="flex items-center gap-3">
            <img src="<?= SITE_LOGO ?>" alt="<?= SITE_NAME ?> logo"
                 class="h-14 w-auto object-contain flex-shrink-0"
                 onerror="this.style.display='none'">
            <div>
                <h1 class="font-bold text-sm leading-tight"><?= SITE_NAME ?></h1>
                <p class="text-slate-400 text-xs">Admin Panel</p>
            </div>
        </div>
    </div>

    <nav class="p-3 space-y-0.5 mt-2 overflow-y-auto h-[calc(100%-80px)]">
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider px-3 py-2">Asosiy</p>
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt w-4 text-center text-slate-400"></i>
            <span>Dashboard</span>
        </a>
        <a href="<?= BASE_URL ?>/admin/analytics.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : '' ?>">
            <i class="fas fa-brain w-4 text-center text-cyan-300"></i>
            <span>AI Tahlil</span>
        </a>

        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider px-3 py-2 mt-2">Kontent</p>
        <a href="<?= BASE_URL ?>/admin/lectures/index.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= strpos($_SERVER['REQUEST_URI'], '/admin/lectures/') !== false ? 'active' : '' ?>">
            <i class="fas fa-book-open w-4 text-center text-blue-400"></i>
            <span>Ma'ruzalar</span>
        </a>
        <a href="<?= BASE_URL ?>/admin/practicals/index.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= strpos($_SERVER['REQUEST_URI'], '/admin/practicals/') !== false ? 'active' : '' ?>">
            <i class="fas fa-flask w-4 text-center text-emerald-400"></i>
            <span>Amaliy mashg'ulotlar</span>
        </a>
        <a href="<?= BASE_URL ?>/admin/tests/index.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= strpos($_SERVER['REQUEST_URI'], '/admin/tests/') !== false ? 'active' : '' ?>">
            <i class="fas fa-clipboard-list w-4 text-center text-orange-400"></i>
            <span>Testlar</span>
        </a>
        <a href="<?= BASE_URL ?>/admin/test-results/index.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= strpos($_SERVER['REQUEST_URI'], '/admin/test-results/') !== false ? 'active' : '' ?>">
            <i class="fas fa-square-poll-vertical w-4 text-center text-amber-300"></i>
            <span>Test natijalari</span>
        </a>
        <a href="<?= BASE_URL ?>/admin/users/index.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= strpos($_SERVER['REQUEST_URI'], '/admin/users/') !== false ? 'active' : '' ?>">
            <i class="fas fa-users w-4 text-center text-purple-300"></i>
            <span>Foydalanuvchilar</span>
        </a>
        <a href="<?= BASE_URL ?>/admin/maps/index.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= strpos($_SERVER['REQUEST_URI'], '/admin/maps/') !== false ? 'active' : '' ?>">
            <i class="fas fa-map-marked-alt w-4 text-center text-indigo-400"></i>
            <span>Xaritalar</span>
        </a>

        <div class="pt-3 mt-3 border-t border-slate-600">
            <a href="<?= BASE_URL ?>/logout.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-red-400 hover:text-red-300">
                <i class="fas fa-sign-out-alt w-4 text-center"></i>
                <span>Chiqish</span>
            </a>
        </div>
    </nav>
</aside>

<div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

<div class="md:ml-64 min-h-screen flex flex-col">
    <!-- Top navbar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="flex items-center justify-between px-4 py-3">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-bars text-gray-600"></i>
                </button>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <a href="<?= BASE_URL ?>/admin/dashboard.php" class="hover:text-blue-600">Admin</a>
                    <?php if (isset($page_title) && $page_title !== 'Dashboard'): ?>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span class="text-gray-800 font-medium"><?= h($page_title) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                        <?= strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-sm font-medium text-gray-700 leading-none"><?= h($_SESSION['full_name'] ?? 'Admin') ?></p>
                        <p class="text-xs text-gray-400 mt-0.5">Administrator</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 p-4 md:p-6">
<?php
$flash = get_flash();
if ($flash):
    $flashJson = json_encode(['type' => $flash['type'], 'message' => $flash['message']]);
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const f = <?= $flashJson ?>;
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: f.type === 'success' ? 'success' : 'error',
        title: f.message,
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        customClass: { popup: 'text-sm' }
    });
});
</script>
<?php endif; ?>
