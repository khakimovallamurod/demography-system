<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Dashboard';

$total_lectures   = count($db->get_data_by_table_all('lectures'));
$total_practicals = count($db->get_data_by_table_all('practicals'));
$total_tests      = count($db->get_data_by_table_all('tests'));
$total_users      = count($db->get_data_by_table_all('users', 'WHERE role = "user"'));
$external_resources = get_dashboard_external_resources();

$recent_lectures   = $db->get_data_by_table_all('lectures', 'ORDER BY created_at DESC LIMIT 5');
$recent_practicals = $db->get_data_by_table_all('practicals', 'ORDER BY created_at DESC LIMIT 5');
$all_users = $db->get_data_by_table_all('users', 'WHERE role = "user" ORDER BY created_at DESC');

$all_test_results = [];
$resultsQuery = $db->query("
    SELECT
        tr.id,
        tr.score,
        tr.total,
        tr.completed_at,
        u.full_name,
        u.username,
        t.title AS test_title
    FROM test_results tr
    INNER JOIN users u ON u.id = tr.user_id
    INNER JOIN tests t ON t.id = tr.test_id
    WHERE tr.completed_at IS NOT NULL
    ORDER BY tr.completed_at DESC
");

if ($resultsQuery) {
    while ($row = mysqli_fetch_assoc($resultsQuery)) {
        $all_test_results[] = $row;
    }
}

$recent_users = array_slice($all_users, 0, 5);
$recent_test_results = array_slice($all_test_results, 0, 5);

include __DIR__ . '/../includes/admin_header.php';
?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <a href="<?= BASE_URL ?>/admin/lectures/create.php" class="flex items-center gap-3 p-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition min-w-0">
            <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-plus text-white text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-blue-700 break-words">Ma'ruza qo'sh</p>
                <p class="text-xs text-blue-500">Yangi mavzu</p>
            </div>
        </a>
        <a href="<?= BASE_URL ?>/admin/practicals/create.php" class="flex items-center gap-3 p-3 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition min-w-0">
            <div class="w-9 h-9 bg-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-plus text-white text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-emerald-700 break-words">Amaliy qo'sh</p>
                <p class="text-xs text-emerald-500">Yangi mashg'ulot</p>
            </div>
        </a>
        <a href="<?= BASE_URL ?>/admin/tests/create.php" class="flex items-center gap-3 p-3 bg-orange-50 hover:bg-orange-100 rounded-xl transition min-w-0">
            <div class="w-9 h-9 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-plus text-white text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-orange-700 break-words">Test qo'sh</p>
                <p class="text-xs text-orange-500">Yangi test</p>
            </div>
        </a>
    </div>
</div>

<!-- Recent Content -->
<div class="grid md:grid-cols-2 gap-4">
    <!-- Recent Lectures -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-wrap items-center justify-between gap-2 p-5 border-b border-gray-100">
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
        <div class="flex flex-wrap items-center justify-between gap-2 p-5 border-b border-gray-100">
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

<div class="grid xl:grid-cols-2 gap-4 mt-4">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-2 p-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <i class="fas fa-users text-purple-500"></i> Foydalanuvchilar
            </h3>
            <div class="flex items-center gap-2">
                <span class="text-xs bg-purple-50 text-purple-600 px-2.5 py-1 rounded-full font-medium"><?= count($all_users) ?> ta</span>
                <a href="<?= BASE_URL ?>/admin/users/index.php" class="text-xs text-purple-600 hover:underline">Hammasi</a>
            </div>
        </div>
        <div class="max-h-[430px] overflow-y-auto divide-y divide-gray-50">
            <?php if (empty($recent_users)): ?>
            <p class="text-sm text-gray-400 text-center py-8">Foydalanuvchilar mavjud emas</p>
            <?php else: ?>
            <?php foreach ($recent_users as $user): ?>
            <div class="flex items-center gap-3 p-4 hover:bg-gray-50 transition">
                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-semibold text-sm flex-shrink-0">
                    <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-800 truncate"><?= h($user['full_name']) ?></p>
                    <p class="text-xs text-gray-400 truncate">@<?= h($user['username']) ?></p>
                </div>
                <div class="text-left sm:text-right flex-shrink-0">
                    <p class="text-xs text-gray-400">Qo‘shilgan</p>
                    <p class="text-xs font-medium text-gray-600"><?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-2 p-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <i class="fas fa-square-poll-vertical text-orange-500"></i> Test natijalari
            </h3>
            <div class="flex items-center gap-2">
                <span class="text-xs bg-orange-50 text-orange-600 px-2.5 py-1 rounded-full font-medium"><?= count($all_test_results) ?> ta</span>
                <a href="<?= BASE_URL ?>/admin/test-results/index.php" class="text-xs text-orange-600 hover:underline">Hammasi</a>
            </div>
        </div>
        <div class="max-h-[430px] overflow-y-auto divide-y divide-gray-50">
            <?php if (empty($recent_test_results)): ?>
            <p class="text-sm text-gray-400 text-center py-8">Test natijalari hali yo‘q</p>
            <?php else: ?>
            <?php foreach ($recent_test_results as $result): ?>
            <?php $percent = (int) ($result['total'] > 0 ? round(($result['score'] / $result['total']) * 100) : 0); ?>
            <div class="p-4 hover:bg-gray-50 transition">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0 <?= $percent >= 70 ? 'bg-emerald-100 text-emerald-700' : ($percent >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') ?>">
                        <?= $percent ?>%
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate"><?= h($result['full_name']) ?></p>
                                <p class="text-xs text-gray-400 truncate">@<?= h($result['username']) ?></p>
                            </div>
                            <p class="text-xs text-gray-400 whitespace-nowrap"><?= time_ago($result['completed_at']) ?></p>
                        </div>
                        <p class="text-sm text-gray-600 mt-2 break-words"><?= h($result['test_title']) ?></p>
                        <div class="flex flex-col items-start justify-between gap-1 sm:flex-row sm:items-center sm:gap-3 mt-2">
                            <p class="text-xs text-gray-400"><?= (int) $result['score'] ?>/<?= (int) $result['total'] ?> to‘g‘ri javob</p>
                            <p class="text-xs font-medium text-gray-500"><?= date('d.m.Y H:i', strtotime($result['completed_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="mt-4">
    <?php include __DIR__ . '/../includes/external_resource_cards.php'; ?>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
