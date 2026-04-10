<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$page_title = 'Test natijalari';
$search = trim($_GET['q'] ?? '');
$selectedTestId = (int) ($_GET['test_id'] ?? 0);
$escapedSearch = $db->escape($search);

$tests = $db->get_data_by_table_all('tests', 'ORDER BY title ASC');

$whereParts = ['tr.completed_at IS NOT NULL'];
if ($search !== '') {
    $whereParts[] = "(t.title LIKE '%{$escapedSearch}%' OR u.full_name LIKE '%{$escapedSearch}%' OR u.username LIKE '%{$escapedSearch}%')";
}
if ($selectedTestId > 0) {
    $whereParts[] = 'tr.test_id = ' . $selectedTestId;
}

$whereSql = implode(' AND ', $whereParts);
$results = [];
$query = $db->query("
    SELECT
        tr.id,
        tr.score,
        tr.total,
        tr.started_at,
        tr.completed_at,
        u.full_name,
        u.username,
        t.id AS test_id,
        t.title AS test_title,
        t.duration
    FROM test_results tr
    INNER JOIN users u ON u.id = tr.user_id
    INNER JOIN tests t ON t.id = tr.test_id
    WHERE {$whereSql}
    ORDER BY tr.completed_at DESC
");

if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $results[] = $row;
    }
}

include __DIR__ . '/../../includes/admin_header.php';
?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 p-5 border-b border-gray-100">
        <div>
            <h3 class="font-semibold text-gray-700 flex items-center gap-2">
                <i class="fas fa-square-poll-vertical text-orange-500"></i> Test natijalari
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full"><?= count($results) ?></span>
            </h3>
            <p class="text-sm text-gray-400 mt-1">Qidiruv va test mavzusi bo‘yicha filtrlash mumkin.</p>
        </div>

        <form method="GET" class="w-full xl:w-auto">
            <div class="grid sm:grid-cols-[minmax(240px,1fr)_220px_auto_auto] gap-3">
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input
                        type="text"
                        name="q"
                        value="<?= h($search) ?>"
                        placeholder="Foydalanuvchi yoki test nomi"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-300"
                    >
                </div>
                <select
                    name="test_id"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-300"
                >
                    <option value="0">Barcha mavzular</option>
                    <?php foreach ($tests as $test): ?>
                    <option value="<?= (int) $test['id'] ?>" <?= $selectedTestId === (int) $test['id'] ? 'selected' : '' ?>>
                        <?= h($test['title']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="inline-flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>
                <?php if ($search !== '' || $selectedTestId > 0): ?>
                <a href="<?= BASE_URL ?>/admin/test-results/index.php" class="inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-xl transition">
                    <i class="fas fa-rotate-left text-xs"></i> Tozalash
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($results)): ?>
    <div class="text-center py-16">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-square-poll-vertical text-gray-400 text-2xl"></i>
        </div>
        <p class="text-gray-500 font-medium">Natijalar topilmadi</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="text-left px-5 py-3">#</th>
                    <th class="text-left px-5 py-3">Foydalanuvchi</th>
                    <th class="text-left px-5 py-3">Mavzu</th>
                    <th class="text-left px-5 py-3">Ball</th>
                    <th class="text-left px-5 py-3">Foiz</th>
                    <th class="text-left px-5 py-3">Davomiylik</th>
                    <th class="text-left px-5 py-3">Boshlanish</th>
                    <th class="text-left px-5 py-3">Yakunlangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($results as $index => $result): ?>
                <?php $percent = (int) ($result['total'] > 0 ? round(($result['score'] / $result['total']) * 100) : 0); ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3.5 text-gray-400"><?= $index + 1 ?></td>
                    <td class="px-5 py-3.5">
                        <div class="font-medium text-gray-800"><?= h($result['full_name']) ?></div>
                        <div class="text-xs text-gray-400 mt-0.5">@<?= h($result['username']) ?></div>
                    </td>
                    <td class="px-5 py-3.5 text-gray-700 min-w-[240px]"><?= h($result['test_title']) ?></td>
                    <td class="px-5 py-3.5 text-gray-700 font-medium"><?= (int) $result['score'] ?> / <?= (int) $result['total'] ?></td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium <?= $percent >= 70 ? 'bg-emerald-50 text-emerald-700' : ($percent >= 50 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600') ?>">
                            <?= $percent ?>%
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-gray-500 text-xs"><?= (int) $result['duration'] ?> daqiqa</td>
                    <td class="px-5 py-3.5 text-gray-500 text-xs"><?= date('d.m.Y H:i', strtotime($result['started_at'])) ?></td>
                    <td class="px-5 py-3.5 text-gray-500 text-xs"><?= date('d.m.Y H:i', strtotime($result['completed_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
