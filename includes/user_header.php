<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? h($page_title) . ' | ' : '' ?><?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .nav-link { transition: all 0.2s; border-left: 3px solid transparent; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.15); border-left-color: rgb(31, 160, 52); }
        .card-hover { transition: transform 0.18s, box-shadow 0.18s; }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(0,0,0,0.12); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 h-full w-60 bg-gradient-to-b from-emerald-950 via-emerald-900 to-teal-900 text-white z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300">
    <!-- Logo -->
    <div class="p-4 border-b border-emerald-800">
        <div class="flex items-center gap-2.5">
            <img src="<?= SITE_LOGO ?>" alt="<?= SITE_NAME ?> logo"
                 class="h-14 w-auto object-contain flex-shrink-0"
                 onerror="this.style.display='none'">
            <div>
                <p class="font-bold text-sm leading-tight"><?= SITE_NAME ?></p>
                <p class="text-emerald-300 text-xs">Talaba kabineti</p>
            </div>
        </div>
    </div>

    <!-- User info -->
    <div class="px-4 py-3 border-b border-emerald-800">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">
                <?= strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium truncate"><?= h($_SESSION['full_name'] ?? '') ?></p>
                <p class="text-emerald-300 text-xs">Talaba</p>
            </div>
        </div>
    </div>

    <nav class="p-3 space-y-0.5 mt-1">
        <a href="<?= BASE_URL ?>/user/dashboard.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active bg-white/15' : '' ?>">
            <i class="fas fa-home w-4 text-center text-emerald-300"></i> Bosh sahifa
        </a>
        <a href="<?= BASE_URL ?>/user/lectures/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= strpos($_SERVER['REQUEST_URI'], '/user/lectures/') !== false ? 'active bg-white/15' : '' ?>">
            <i class="fas fa-book-open w-4 text-center text-blue-300"></i> Ma'ruzalar
        </a>
        <a href="<?= BASE_URL ?>/user/practicals/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= strpos($_SERVER['REQUEST_URI'], '/user/practicals/') !== false ? 'active bg-white/15' : '' ?>">
            <i class="fas fa-flask w-4 text-center text-amber-300"></i> Amaliy mashg'ulotlar
        </a>
        <a href="<?= BASE_URL ?>/user/tests/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= strpos($_SERVER['REQUEST_URI'], '/user/tests/') !== false ? 'active bg-white/15' : '' ?>">
            <i class="fas fa-clipboard-list w-4 text-center text-orange-300"></i> Testlar
        </a>
        <a href="<?= BASE_URL ?>/user/maps/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= strpos($_SERVER['REQUEST_URI'], '/user/maps/') !== false ? 'active bg-white/15' : '' ?>">
            <i class="fas fa-map-marked-alt w-4 text-center text-indigo-300"></i> Xaritalar
        </a>

        <div class="pt-3 mt-2 border-t border-emerald-800">
            <a href="<?= BASE_URL ?>/logout.php"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-red-300 hover:text-red-200">
                <i class="fas fa-sign-out-alt w-4 text-center"></i> Chiqish
            </a>
        </div>
    </nav>
</aside>

<div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

<div class="md:ml-60 min-h-screen flex flex-col">
    <!-- Top bar -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-30">
        <div class="flex items-center justify-between px-4 py-3">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-bars text-gray-600"></i>
                </button>
                <span class="text-gray-800 font-semibold text-sm"><?= isset($page_title) ? h($page_title) : 'Bosh sahifa' ?></span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-emerald-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                    <?= strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)) ?>
                </div>
                <span class="hidden sm:block text-sm font-medium text-gray-700"><?= h($_SESSION['full_name'] ?? '') ?></span>
            </div>
        </div>
    </header>

    <main class="flex-1 p-4 md:p-5">
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
