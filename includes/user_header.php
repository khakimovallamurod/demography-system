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
        .nav-link {
            transition: all 0.18s;
            border-left: 3px solid transparent;
            color: #cbd5e1;
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.12);
            color: #fff;
            border-left-color: rgba(255,255,255,0.4);
        }
        .nav-link.active {
            background: rgba(255,255,255,0.18);
            color: #fff;
            border-left-color: #86efac;
            font-weight: 600;
        }
        .nav-link .nav-icon { color: #4ade80; transition: color 0.18s; }
        .nav-link:hover .nav-icon,
        .nav-link.active .nav-icon { color: #86efac; }

        .card-hover { transition: transform 0.18s, box-shadow 0.18s; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }

        .dropdown-menu { display: none; }
        .dropdown:hover .dropdown-menu { display: block; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #475569; border-radius: 4px; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen pb-16 md:pb-0">

<!-- Sidebar — dark, same as admin -->
<aside id="sidebar"
       class="fixed top-0 left-0 h-full w-64 z-50
              transform -translate-x-full md:translate-x-0 transition-transform duration-300 flex flex-col"
       style="background: linear-gradient(180deg, #052e16 0%, #166534 100%);">

    <!-- Logo -->
    <div class="p-4 border-b border-white/10 flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/15 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-globe text-white text-lg"></i>
            </div>
            <div class="min-w-0">
                <h1 class="font-bold text-white text-xs leading-tight break-words"><?= SITE_NAME ?></h1>
                <p class="text-green-300 text-xs mt-0.5">Talaba kabineti</p>
            </div>
        </div>
    </div>

    <!-- Nav -->
    <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">

        <a href="<?= BASE_URL ?>/user/dashboard.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-home w-4 text-center"></i>
            <span>Bosh sahifa</span>
        </a>

        <a href="<?= BASE_URL ?>/user/lectures/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/user/lectures/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-book-open w-4 text-center"></i>
            <span>Ma'ruza mavzulari</span>
        </a>

        <a href="<?= BASE_URL ?>/user/practicals/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/user/practicals/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-chalkboard-teacher w-4 text-center"></i>
            <span>Amaliy mashg'ulotlar</span>
        </a>

        <a href="<?= BASE_URL ?>/user/tests/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/user/tests/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-clipboard-list w-4 text-center"></i>
            <span>Testlar</span>
        </a>

        <a href="<?= BASE_URL ?>/user/maps/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/user/maps/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-map-marked-alt w-4 text-center"></i>
            <span>Xaritalar</span>
        </a>

        <a href="<?= BASE_URL ?>/user/glossary/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/user/glossary/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-book w-4 text-center"></i>
            <span>Glossary</span>
        </a>

        <a href="<?= BASE_URL ?>/user/library/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/user/library/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-layer-group w-4 text-center"></i>
            <span>Raqamli kutubxona</span>
        </a>

        <a href="<?= BASE_URL ?>/user/laboratory/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/user/laboratory/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-microscope w-4 text-center"></i>
            <span>Aholishunoslik lab.</span>
        </a>

        <a href="<?= BASE_URL ?>/user/results/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/user/results/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-clipboard-check w-4 text-center"></i>
            <span>Test natijalari</span>
        </a>

        <a href="<?= BASE_URL ?>/user/reports/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/user/reports/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-chart-bar w-4 text-center"></i>
            <span>Hisobot</span>
        </a>

        <a href="#"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm">
            <i class="nav-icon fas fa-info-circle w-4 text-center"></i>
            <span>Biz haqimizda</span>
        </a>

        <a href="#"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm">
            <i class="nav-icon fas fa-cog w-4 text-center"></i>
            <span>Sozlamalar</span>
        </a>

        <div class="pt-3 mt-2 border-t border-white/10">
            <a href="<?= BASE_URL ?>/logout.php"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm !text-red-400 hover:!bg-red-500/20 hover:!text-red-300">
                <i class="fas fa-sign-out-alt w-4 text-center !text-red-400"></i>
                <span>Chiqish</span>
            </a>
        </div>
    </nav>
</aside>

<div id="overlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

<div class="md:ml-64 min-h-screen flex flex-col">

    <!-- Top navbar — light, same style as admin -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="flex items-center justify-between px-4 py-3 gap-3">

            <!-- Left -->
            <div class="flex items-center gap-3 flex-shrink-0">
                <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="hidden md:flex items-center gap-2">
                    <i class="fas fa-globe text-blue-600 text-lg"></i>
                    <span class="font-semibold text-gray-700 text-sm"><?= SITE_NAME ?></span>
                </div>
            </div>

            <!-- Center: search -->
            <div class="flex-1 max-w-sm">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" placeholder="Qidirish..."
                        class="w-full pl-9 pr-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-700
                               placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
            </div>

            <!-- Right -->
            <div class="flex items-center gap-1 flex-shrink-0">
                <button class="relative p-2.5 hover:bg-gray-100 rounded-lg transition text-gray-600">
                    <i class="fas fa-bell text-sm"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </button>
                <button class="p-2.5 hover:bg-gray-100 rounded-lg transition text-gray-600">
                    <i class="fas fa-envelope text-sm"></i>
                </button>
                <div class="dropdown relative ml-1">
                    <button class="flex items-center gap-2 py-1.5 px-2 hover:bg-gray-100 rounded-lg transition">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-sm font-bold text-white">
                            <?= strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <span class="hidden sm:block text-sm font-medium text-gray-700"><?= h($_SESSION['full_name'] ?? '') ?></span>
                        <i class="fas fa-chevron-down text-xs text-gray-400 hidden sm:block"></i>
                    </button>
                    <div class="dropdown-menu absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50">
                        <div class="px-4 py-2.5 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800"><?= h($_SESSION['full_name'] ?? '') ?></p>
                            <p class="text-xs text-gray-400">Talaba</p>
                        </div>
                        <a href="<?= BASE_URL ?>/logout.php"
                           class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                            <i class="fas fa-sign-out-alt text-xs w-4"></i> Chiqish
                        </a>
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
