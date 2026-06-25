<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();

$page_title = "Aholishunoslik laboratoriyasi";
include __DIR__ . '/../../includes/user_header.php';

// Fetch Yo'riqnoma file (Category 1)
$yoriqnoma_item = $db->get_data_by_table('laboratory_materials', ['category_id' => 1]);
$yoriqnoma_link = $yoriqnoma_item && !empty($yoriqnoma_item['file_path']) ? BASE_URL . '/user/laboratory/view.php?id=' . $yoriqnoma_item['id'] : '#';
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Aholishunoslik laboratoriyasi</h2>
        <p class="text-sm text-gray-500 mt-1">Ilmiy resurslar, yo'riqnomalar va demografik vositalar markazi</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- 1. Yo'riqnoma -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 flex flex-col group">
        <div class="h-32 bg-gradient-to-br from-orange-400 to-orange-600 relative overflow-hidden flex items-center justify-center">
            <div class="absolute inset-0 bg-white/10 mix-blend-overlay"></div>
            <div class="absolute -right-4 -bottom-4 text-white/20 text-7xl transform group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-lightbulb"></i>
            </div>
            <div class="relative z-10 flex items-center gap-3 text-white">
                <i class="fas fa-clipboard-list text-3xl"></i>
                <h3 class="text-xl font-bold tracking-wide">Yo'riqnoma</h3>
            </div>
        </div>
        <div class="p-6 flex flex-col flex-grow text-center">
            <p class="text-sm text-gray-500 mb-6 flex-grow">Aholishunoslik laboratoriyasida ishlash boʻyicha yoʻriqnoma</p>
            <hr class="border-gray-100 mb-5">
            <a href="<?= $yoriqnoma_link ?>" class="bg-gradient-to-r from-orange-400 to-orange-500 hover:from-orange-500 hover:to-orange-600 text-white font-bold py-2.5 px-6 rounded-xl shadow-md hover:shadow-lg transition-all text-sm mx-auto min-w-[140px]">
                Ko'rsatma
            </a>
        </div>
    </div>

    <!-- 2. Laboratoriya Imkoniyatlari -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 flex flex-col group">
        <div class="h-32 bg-gradient-to-br from-emerald-500 to-green-600 relative overflow-hidden flex items-center justify-center">
            <div class="absolute inset-0 bg-white/10 mix-blend-overlay"></div>
            <div class="absolute -right-4 -bottom-4 text-white/20 text-7xl transform group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-chart-area"></i>
            </div>
            <div class="relative z-10 flex flex-col items-center gap-1 text-white">
                <div class="flex items-center gap-3">
                    <i class="fas fa-chart-bar text-2xl"></i>
                    <h3 class="text-xl font-bold tracking-wide">Laboratoriya</h3>
                </div>
                <h3 class="text-xl font-bold tracking-wide">imkoniyatlari</h3>
            </div>
        </div>
        <div class="p-6 flex flex-col flex-grow text-center">
            <p class="text-sm text-gray-500 mb-6 flex-grow">Laboratoriya topshiriqlaridan na'munalar.</p>
            <hr class="border-gray-100 mb-5">
            <a href="<?= BASE_URL ?>/user/laboratory/category.php?id=2" class="bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md hover:shadow-lg transition-all text-sm mx-auto min-w-[140px]">
                Ko'rish
            </a>
        </div>
    </div>

    <!-- 3. Ilmiy Resurslar -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 flex flex-col group">
        <div class="h-32 bg-gradient-to-br from-blue-500 to-blue-700 relative overflow-hidden flex items-center justify-center">
            <div class="absolute inset-0 bg-white/10 mix-blend-overlay"></div>
            <div class="absolute -right-4 -bottom-4 text-white/20 text-7xl transform group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-book"></i>
            </div>
            <div class="relative z-10 flex items-center gap-3 text-white">
                <i class="fas fa-book-open text-3xl"></i>
                <h3 class="text-xl font-bold tracking-wide">Ilmiy resurslar</h3>
            </div>
        </div>
        <div class="p-6 flex flex-col flex-grow text-center">
            <p class="text-sm text-gray-500 mb-6 flex-grow">Maqolalar va manbalar</p>
            <hr class="border-gray-100 mb-5">
            <a href="<?= BASE_URL ?>/user/laboratory/category.php?id=3" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md hover:shadow-lg transition-all text-sm mx-auto min-w-[140px]">
                O'qish
            </a>
        </div>
    </div>

    <!-- 4. Maqola Yozish Qoidalari -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 flex flex-col group">
        <div class="h-32 bg-gradient-to-br from-amber-400 to-orange-500 relative overflow-hidden flex items-center justify-center">
            <div class="absolute inset-0 bg-white/10 mix-blend-overlay"></div>
            <div class="absolute -right-4 -bottom-4 text-white/20 text-7xl transform group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="relative z-10 flex items-center gap-3 text-white">
                <i class="fas fa-pen-nib text-3xl"></i>
                <h3 class="text-xl font-bold tracking-wide">Maqola yozish qoidalari</h3>
            </div>
        </div>
        <div class="p-6 flex flex-col flex-grow text-center">
            <p class="text-sm text-gray-500 mb-6 flex-grow">Ilmiy maqola tuzilishi (IMRAD va h.k.)</p>
            <hr class="border-gray-100 mb-5">
            <a href="<?= BASE_URL ?>/user/laboratory/category.php?id=4" class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-orange-500 hover:to-orange-600 text-white font-bold py-2.5 px-6 rounded-xl shadow-md hover:shadow-lg transition-all text-sm mx-auto min-w-[140px]">
                Bilish
            </a>
        </div>
    </div>

    <!-- 5. Demografik Saytlar -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 flex flex-col group">
        <div class="h-32 bg-gradient-to-br from-slate-500 to-slate-700 relative overflow-hidden flex items-center justify-center">
            <div class="absolute inset-0 bg-white/10 mix-blend-overlay"></div>
            <div class="absolute -right-4 -bottom-4 text-white/20 text-7xl transform group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-globe"></i>
            </div>
            <div class="relative z-10 flex items-center gap-3 text-white">
                <i class="fas fa-sitemap text-3xl"></i>
                <h3 class="text-xl font-bold tracking-wide">Demografik saytlar</h3>
            </div>
        </div>
        <div class="p-6 flex flex-col flex-grow text-center">
            <p class="text-sm text-gray-500 mb-4 flex-grow">Milliy va xalqaro demografik platformalar</p>
            <div class="flex-grow flex items-center justify-center flex-wrap gap-2 mb-6">
                <span class="text-[10px] font-bold bg-blue-600 text-white px-2 py-1 rounded">STAT.UZ</span>
                <span class="text-[10px] font-bold bg-slate-700 text-white px-2 py-1 rounded">SIAT.STAT.UZ</span>
                <span class="text-[10px] font-bold bg-green-600 text-white px-2 py-1 rounded">DEMOKALKULYATOR</span>
                <span class="text-[10px] font-bold bg-orange-500 text-white px-2 py-1 rounded">DEMOGRAFIYA.UZ</span>
            </div>
            <hr class="border-gray-100 mb-5">
            <a href="<?= BASE_URL ?>/user/laboratory/category.php?id=5" class="bg-gradient-to-r from-slate-600 to-slate-700 hover:from-slate-700 hover:to-slate-800 text-white font-bold py-2.5 px-6 rounded-xl shadow-md hover:shadow-lg transition-all text-sm mx-auto min-w-[140px]">
                Integratsiya
            </a>
        </div>
    </div>

    <!-- 6. Demografiya Videolar -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 flex flex-col group">
        <div class="h-32 bg-gradient-to-br from-red-500 to-red-700 relative overflow-hidden flex items-center justify-center">
            <div class="absolute inset-0 bg-white/10 mix-blend-overlay"></div>
            <div class="absolute -right-4 -bottom-4 text-white/20 text-7xl transform group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-play"></i>
            </div>
            <div class="relative z-10 flex items-center gap-3 text-white">
                <i class="fab fa-youtube text-4xl"></i>
                <h3 class="text-xl font-bold tracking-wide">Demografiya videolar</h3>
            </div>
        </div>
        <div class="p-6 flex flex-col flex-grow text-center">
            <p class="text-sm text-gray-500 mb-6 flex-grow">Ta’limiy videolar.</p>
            <hr class="border-gray-100 mb-5">
            <a href="<?= BASE_URL ?>/user/laboratory/category.php?id=6" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md hover:shadow-lg transition-all text-sm mx-auto min-w-[140px]">
                Tomosha qilish
            </a>
        </div>
    </div>

</div>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
