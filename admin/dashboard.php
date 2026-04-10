<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Dashboard';

$total_lectures   = count($db->get_data_by_table_all('lectures'));
$total_practicals = count($db->get_data_by_table_all('practicals'));
$total_tests      = count($db->get_data_by_table_all('tests'));
$total_users      = count($db->get_data_by_table_all('users', 'WHERE role = "user"'));

$recent_lectures   = $db->get_data_by_table_all('lectures', 'ORDER BY created_at DESC LIMIT 5');
$recent_practicals = $db->get_data_by_table_all('practicals', 'ORDER BY created_at DESC LIMIT 5');

include __DIR__ . '/../includes/admin_header.php';
?>

<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-book-open text-blue-600"></i>
            </div>
            <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full font-medium">Jami</span>
        </div>
        <p class="text-2xl font-bold text-gray-800"><?= $total_lectures ?></p>
        <p class="text-sm text-gray-500 mt-0.5">Ma'ruzalar</p>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-flask text-emerald-600"></i>
            </div>
            <span class="text-xs bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-full font-medium">Jami</span>
        </div>
        <p class="text-2xl font-bold text-gray-800"><?= $total_practicals ?></p>
        <p class="text-sm text-gray-500 mt-0.5">Amaliy mashg'ulotlar</p>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-clipboard-list text-orange-600"></i>
            </div>
            <span class="text-xs bg-orange-50 text-orange-600 px-2 py-0.5 rounded-full font-medium">Jami</span>
        </div>
        <p class="text-2xl font-bold text-gray-800"><?= $total_tests ?></p>
        <p class="text-sm text-gray-500 mt-0.5">Testlar</p>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-purple-600"></i>
            </div>
            <span class="text-xs bg-purple-50 text-purple-600 px-2 py-0.5 rounded-full font-medium">Jami</span>
        </div>
        <p class="text-2xl font-bold text-gray-800"><?= $total_users ?></p>
        <p class="text-sm text-gray-500 mt-0.5">Foydalanuvchilar</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
        <i class="fas fa-bolt text-yellow-500"></i> Tezkor amallar
    </h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        <a href="<?= BASE_URL ?>/admin/lectures/create.php" class="flex items-center gap-3 p-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition">
            <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-plus text-white text-sm"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-blue-700">Ma'ruza qo'sh</p>
                <p class="text-xs text-blue-500">Yangi mavzu</p>
            </div>
        </a>
        <a href="<?= BASE_URL ?>/admin/practicals/create.php" class="flex items-center gap-3 p-3 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition">
            <div class="w-9 h-9 bg-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-plus text-white text-sm"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-emerald-700">Amaliy qo'sh</p>
                <p class="text-xs text-emerald-500">Yangi mashg'ulot</p>
            </div>
        </a>
        <a href="<?= BASE_URL ?>/admin/tests/create.php" class="flex items-center gap-3 p-3 bg-orange-50 hover:bg-orange-100 rounded-xl transition">
            <div class="w-9 h-9 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-plus text-white text-sm"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-orange-700">Test qo'sh</p>
                <p class="text-xs text-orange-500">Yangi test</p>
            </div>
        </a>
    </div>
</div>

<!-- Recent Content -->
<div class="grid md:grid-cols-2 gap-4">
    <!-- Recent Lectures -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <i class="fas fa-book-open text-blue-500"></i> So'nggi ma'ruzalar
            </h3>
            <a href="<?= BASE_URL ?>/admin/lectures/index.php" class="text-xs text-blue-600 hover:underline">Hammasi</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php if (empty($recent_lectures)): ?>
            <p class="text-sm text-gray-400 text-center py-8">Ma'ruzalar mavjud emas</p>
            <?php else: ?>
            <?php foreach ($recent_lectures as $l): ?>
            <div class="flex items-start gap-3 p-4 hover:bg-gray-50 transition">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-alt text-blue-600 text-xs"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-700 truncate"><?= h($l['title']) ?></p>
                    <p class="text-xs text-gray-400 mt-0.5"><?= time_ago($l['created_at']) ?></p>
                </div>
                <a href="<?= BASE_URL ?>/admin/lectures/edit.php?id=<?= $l['id'] ?>" class="text-gray-400 hover:text-blue-500">
                    <i class="fas fa-edit text-xs"></i>
                </a>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Practicals -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <i class="fas fa-flask text-emerald-500"></i> So'nggi amaliy mashg'ulotlar
            </h3>
            <a href="<?= BASE_URL ?>/admin/practicals/index.php" class="text-xs text-emerald-600 hover:underline">Hammasi</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php if (empty($recent_practicals)): ?>
            <p class="text-sm text-gray-400 text-center py-8">Amaliy mashg'ulotlar mavjud emas</p>
            <?php else: ?>
            <?php foreach ($recent_practicals as $p): ?>
            <div class="flex items-start gap-3 p-4 hover:bg-gray-50 transition">
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-flask text-emerald-600 text-xs"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-700 truncate"><?= h($p['title']) ?></p>
                    <p class="text-xs text-gray-400 mt-0.5"><?= time_ago($p['created_at']) ?></p>
                </div>
                <a href="<?= BASE_URL ?>/admin/practicals/edit.php?id=<?= $p['id'] ?>" class="text-gray-400 hover:text-emerald-500">
                    <i class="fas fa-edit text-xs"></i>
                </a>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- External Resource Banners -->
<div class="grid sm:grid-cols-2 gap-4 mt-4">
    <a href="https://demografiya.uz" target="_blank" rel="noopener"
       class="bg-gradient-to-r from-blue-700 to-blue-800 rounded-2xl p-4 text-white flex items-center gap-3 hover:opacity-90 transition group">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-chart-line text-lg"></i>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-1.5">
                <h4 class="font-bold text-sm">demografiya.uz</h4>
                <i class="fas fa-external-link-alt text-xs text-blue-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition"></i>
            </div>
            <p class="text-blue-200 text-xs mt-0.5">Demografiya portali</p>
        </div>
    </a>
    <a href="https://stat.uz" target="_blank" rel="noopener"
       class="bg-gradient-to-r from-orange-600 to-red-600 rounded-2xl p-4 text-white flex items-center gap-3 hover:opacity-90 transition group">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-database text-lg"></i>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-1.5">
                <h4 class="font-bold text-sm">stat.uz</h4>
                <i class="fas fa-external-link-alt text-xs text-orange-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition"></i>
            </div>
            <p class="text-orange-100 text-xs mt-0.5">O'zbekiston statistika qo'mitasi</p>
        </div>
    </a>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
