<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$page_title = "Hisobot";
include __DIR__ . '/../../includes/user_header.php';
?>

<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Hisobot va Statistika</h2>
    <p class="text-sm text-gray-500 mt-1">O'zlashtirish ko'rsatkichlaringiz va faolligingiz</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:shadow-md transition">
        <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl font-bold z-10 shadow-inner">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="z-10">
            <p class="text-sm text-gray-500 font-medium">O'qilgan ma'ruzalar</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">0</h3>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:shadow-md transition">
        <div class="absolute right-0 top-0 w-24 h-24 bg-green-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center text-xl font-bold z-10 shadow-inner">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="z-10">
            <p class="text-sm text-gray-500 font-medium">O'rtacha ball</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">0%</h3>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:shadow-md transition">
        <div class="absolute right-0 top-0 w-24 h-24 bg-purple-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
        <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center text-xl font-bold z-10 shadow-inner">
            <i class="fas fa-clock"></i>
        </div>
        <div class="z-10">
            <p class="text-sm text-gray-500 font-medium">Tizimda o'tkazilgan vaqt</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">0 soat</h3>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="text-center py-10">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-chart-pie text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-800 mb-2">Batafsil hisobot tayyorlanmoqda</h3>
        <p class="text-gray-500 text-sm max-w-md mx-auto">Sizning faolligingiz bo'yicha yetarli ma'lumot to'plangandan so'ng, bu yerda grafiklar va tahlillar paydo bo'ladi.</p>
    </div>
</div>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
