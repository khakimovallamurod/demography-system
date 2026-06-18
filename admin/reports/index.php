<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$page_title = 'Hisobot';
include __DIR__ . '/../../includes/admin_header.php';
?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="font-semibold text-gray-800 text-xl flex items-center gap-2">
                <i class="fas fa-chart-pie text-blue-500"></i> Hisobot
            </h3>
            <p class="text-sm text-gray-500 mt-1">Tizimdagi barcha hisobotlarni ko'rish va tahlil qilish.</p>
        </div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2">
            <i class="fas fa-download"></i> Yuklab olish
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 border border-blue-100 rounded-xl p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-white text-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                    <i class="fas fa-users"></i>
                </div>
                <h4 class="text-blue-900 font-medium">Jami foydalanuvchilar</h4>
            </div>
            <p class="text-3xl font-bold text-blue-900 mt-3">1,245</p>
        </div>
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 border border-emerald-100 rounded-xl p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-white text-emerald-600 rounded-lg flex items-center justify-center shadow-sm">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h4 class="text-emerald-900 font-medium">Tugatilgan testlar</h4>
            </div>
            <p class="text-3xl font-bold text-emerald-900 mt-3">8,532</p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100/50 border border-purple-100 rounded-xl p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-white text-purple-600 rounded-lg flex items-center justify-center shadow-sm">
                    <i class="fas fa-book-open"></i>
                </div>
                <h4 class="text-purple-900 font-medium">Faol materiallar</h4>
            </div>
            <p class="text-3xl font-bold text-purple-900 mt-3">342</p>
        </div>
    </div>

    <div class="text-center py-20 bg-gray-50/50 rounded-xl border border-dashed border-gray-200">
        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-5 shadow-sm border border-gray-100">
            <i class="fas fa-chart-line text-blue-400 text-3xl"></i>
        </div>
        <h4 class="text-gray-800 font-semibold text-lg">Tez kunda</h4>
        <p class="text-gray-500 mt-2 max-w-sm mx-auto">Batafsil hisobotlar va tizim faoliyati bo'yicha analitik grafiklar tez orada qo'shiladi.</p>
    </div>
</div>

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
