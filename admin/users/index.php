<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$page_title = 'Foydalanuvchilar';
$search = trim($_GET['q'] ?? '');
$escapedSearch = $db->escape($search);

$where = 'WHERE role = "user"';
if ($search !== '') {
    $where .= " AND (full_name LIKE '%{$escapedSearch}%' OR username LIKE '%{$escapedSearch}%')";
}

$users = $db->get_data_by_table_all('users', "{$where} ORDER BY created_at DESC");

$resultsByUser = [];
$statsQuery = $db->query("
    SELECT
        user_id,
        COUNT(*) AS attempts,
        MAX(completed_at) AS last_completed_at
    FROM test_results
    WHERE completed_at IS NOT NULL
    GROUP BY user_id
");

if ($statsQuery) {
    while ($row = mysqli_fetch_assoc($statsQuery)) {
        $resultsByUser[(int) $row['user_id']] = $row;
    }
}

include __DIR__ . '/../../includes/admin_header.php';
?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 p-5 border-b border-gray-100">
        <div>
            <h3 class="font-semibold text-gray-700 flex items-center gap-2">
                <i class="fas fa-users text-purple-500"></i> Foydalanuvchilar ro'yxati
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full"><?= count($users) ?></span>
            </h3>
            <p class="text-sm text-gray-400 mt-1">Qidiruv orqali ism yoki login bo‘yicha filtrlash mumkin.</p>
        </div>

        <form method="GET" class="w-full lg:w-auto">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative min-w-[280px]">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input
                        type="text"
                        name="q"
                        value="<?= h($search) ?>"
                        placeholder="Ism yoki username bo‘yicha qidirish"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-purple-300"
                    >
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>
                <?php if ($search !== ''): ?>
                <a href="<?= BASE_URL ?>/admin/users/index.php" class="inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-xl transition">
                    <i class="fas fa-rotate-left text-xs"></i> Tozalash
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($users)): ?>
    <div class="text-center py-16">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-users text-gray-400 text-2xl"></i>
        </div>
        <p class="text-gray-500 font-medium">Foydalanuvchilar topilmadi</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="text-left px-5 py-3">#</th>
                    <th class="text-left px-5 py-3">F.I.SH</th>
                    <th class="text-left px-5 py-3">Username</th>
                    <th class="text-left px-5 py-3">Rol</th>
                    <th class="text-left px-5 py-3">Test urinishlari</th>
                    <th class="text-left px-5 py-3">Oxirgi natija</th>
                    <th class="text-left px-5 py-3">Ro'yxatdan o'tgan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($users as $index => $user): ?>
                <?php $userStats = $resultsByUser[(int) $user['id']] ?? null; ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3.5 text-gray-400"><?= $index + 1 ?></td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-semibold text-sm flex-shrink-0">
                                <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <div class="font-medium text-gray-800"><?= h($user['full_name']) ?></div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-gray-600">@<?= h($user['username']) ?></td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center rounded-full bg-purple-50 text-purple-600 px-2.5 py-1 text-xs font-medium">user</span>
                    </td>
                    <td class="px-5 py-3.5 text-gray-700"><?= (int) ($userStats['attempts'] ?? 0) ?></td>
                    <td class="px-5 py-3.5 text-gray-500 text-xs">
                        <?= !empty($userStats['last_completed_at']) ? date('d.m.Y H:i', strtotime($userStats['last_completed_at'])) : '—' ?>
                    </td>
                    <td class="px-5 py-3.5 text-gray-500 text-xs"><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
