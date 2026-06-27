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
            border-left-color: #93c5fd;
            font-weight: 600;
        }
        .nav-link .nav-icon { color: #64748b; transition: color 0.18s; }
        .nav-link:hover .nav-icon,
        .nav-link.active .nav-icon { color: #93c5fd; }

        .dropdown-menu { display: none; }
        .dropdown:hover .dropdown-menu { display: block; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #475569; border-radius: 4px; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Sidebar — dark mode -->
<aside id="sidebar"
       class="fixed top-0 left-0 h-full w-64 z-50
              transform -translate-x-full md:translate-x-0 transition-transform duration-300 flex flex-col"
       style="background: linear-gradient(180deg, #0f172a 0%, #1e3a5f 100%);">


    <!-- Logo -->
    <div class="p-4 border-b border-white/10 flex-shrink-0">
        <div class="flex items-center gap-3">
            <img src="<?= SITE_LOGO ?>" alt="Logo" class="w-14 h-14 object-contain flex-shrink-0" onerror="this.style.display='none'">
            <div class="min-w-0">
                <h1 class="font-bold text-white text-xs leading-tight break-words"><?= SITE_NAME ?></h1>
                <p class="text-slate-400 text-xs mt-0.5">Admin Panel</p>
            </div>
        </div>
    </div>

    <!-- Nav -->
    <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">

        <a href="<?= BASE_URL ?>/admin/dashboard.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php' && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? 'active' : '' ?>">
            <i class="nav-icon fas fa-home w-4 text-center"></i>
            <span>Bosh sahifa</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/lectures/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/admin/lectures/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-book-open w-4 text-center"></i>
            <span>Ma'ruza mavzulari</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/practicals/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/admin/practicals/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-chalkboard-teacher w-4 text-center"></i>
            <span>Amaliy mashg'ulotlar</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/tests/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= (strpos($_SERVER['REQUEST_URI'], '/admin/tests/') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/test-results/') === false) ? 'active' : '' ?>">
            <i class="nav-icon fas fa-clipboard-list w-4 text-center"></i>
            <span>Testlar</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/maps/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/admin/maps/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-map-marked-alt w-4 text-center"></i>
            <span>Xaritalar</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/glossary/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/admin/glossary/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-book w-4 text-center"></i>
            <span>Glossary</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/library/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/admin/library/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-layer-group w-4 text-center"></i>
            <span>Raqamli kutubxona</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/laboratory/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/admin/laboratory/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-microscope w-4 text-center"></i>
            <span>Aholishunoslik lab.</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/test-results/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/admin/test-results/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-clipboard-check w-4 text-center"></i>
            <span>Test natijalari</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/reports/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/admin/reports/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-chart-bar w-4 text-center"></i>
            <span>Hisobot</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/users/index.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= strpos($_SERVER['REQUEST_URI'], '/admin/users/') !== false ? 'active' : '' ?>">
            <i class="nav-icon fas fa-users w-4 text-center"></i>
            <span>Foydalanuvchilar</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/analytics.php"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                  <?= basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-brain w-4 text-center"></i>
            <span>Biz haqimizda</span>
        </a>

        <!-- Sozlamalar menu with submenu -->
        <div>
            <button type="button" onclick="document.getElementById('settings-submenu').classList.toggle('hidden')"
               class="nav-link w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-sm <?= (strpos($_SERVER['REQUEST_URI'], '/admin/settings/') !== false) ? 'active' : '' ?>">
                <div class="flex items-center gap-3">
                    <i class="nav-icon fas fa-cog w-4 text-center"></i>
                    <span>Sozlamalar</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
            </button>
            <div id="settings-submenu" class="pl-10 pr-3 py-2 space-y-1 <?= (strpos($_SERVER['REQUEST_URI'], '/admin/settings/') !== false) ? '' : 'hidden' ?>">
                <a href="<?= BASE_URL ?>/admin/settings/universities/index.php" class="block py-1.5 text-sm text-gray-300 hover:text-white transition-colors <?= (strpos($_SERVER['REQUEST_URI'], '/admin/settings/universities/') !== false) ? 'text-white font-semibold' : '' ?>">
                    OTMlar
                </a>
                <a href="<?= BASE_URL ?>/admin/settings/teachers/index.php" class="block py-1.5 text-sm text-gray-300 hover:text-white transition-colors <?= (strpos($_SERVER['REQUEST_URI'], '/admin/settings/teachers/') !== false) ? 'text-white font-semibold' : '' ?>">
                    O'qituvchilar
                </a>
            </div>
        </div>

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

    <!-- Top navbar — light mode -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="flex items-center justify-between px-4 py-3 gap-3">

            <!-- Left -->
            <div class="flex items-center gap-3 flex-shrink-0">
                <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="hidden md:flex items-center gap-2">
                    <img src="<?= SITE_LOGO ?>" alt="Logo" class="w-6 h-6 object-contain" onerror="this.style.display='none'">
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
                            <?= strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 1)) ?>
                        </div>
                        <span class="hidden sm:block text-sm font-medium text-gray-700"><?= h($_SESSION['full_name'] ?? 'Admin') ?></span>
                        <i class="fas fa-chevron-down text-xs text-gray-400 hidden sm:block"></i>
                    </button>
                    <div class="dropdown-menu absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50">
                        <div class="px-4 py-2.5 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800"><?= h($_SESSION['full_name'] ?? 'Admin') ?></p>
                            <p class="text-xs text-gray-400">Administrator</p>
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
