<?php
require_once __DIR__ . '/../../includes/functions.php';
require_teacher();

$page_title = 'Talabalar';

// Get current teacher's university_id
$teacher_id = (int)$_SESSION['user_id'];
$teacher = $db->get_data_by_table('users', ['id' => $teacher_id]);
$university_id = (int)($teacher['university_id'] ?? 0);

$search = trim($_GET['q'] ?? '');
$escapedSearch = $db->escape($search);

$where = "WHERE role = 'user' AND university_id = $university_id";
if ($search !== '') {
    $where .= " AND (full_name LIKE '%{$escapedSearch}%' OR phone LIKE '%{$escapedSearch}%')";
}

$students = $db->get_data_by_table_all('users', "{$where} ORDER BY created_at DESC");

// Get test stats for these students
$resultsByUser = [];
if (!empty($students)) {
    $student_ids = array_column($students, 'id');
    $in_clause = implode(',', $student_ids);
    $statsQuery = $db->query("
        SELECT
            user_id,
            COUNT(*) AS attempts,
            MAX(completed_at) AS last_completed_at
        FROM test_results
        WHERE completed_at IS NOT NULL AND user_id IN ($in_clause)
        GROUP BY user_id
    ");

    if ($statsQuery) {
        while ($row = mysqli_fetch_assoc($statsQuery)) {
            $resultsByUser[(int) $row['user_id']] = $row;
        }
    }
}

include __DIR__ . '/../../includes/teacher_header.php';
?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 p-5 border-b border-gray-100">
        <div>
            <h3 class="font-semibold text-gray-700 flex items-center gap-2">
                <i class="fas fa-user-graduate text-blue-500"></i> O'z guruhim talabalari
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full"><?= count($students) ?></span>
            </h3>
            <p class="text-sm text-gray-400 mt-1">Siz bilan bir xil OTM ga biriktirilgan talabalar ro'yxati</p>
        </div>

        <form method="GET" class="w-full lg:w-auto">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative min-w-[280px]">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input
                        type="text"
                        name="q"
                        value="<?= h($search) ?>"
                        placeholder="Ism yoki telefon raqam orqali qidirish"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-300"
                    >
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition">
                    <i class="fas fa-search text-xs"></i> Izlash
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="text-left px-5 py-3">#</th>
                    <th class="text-left px-5 py-3">F.I.SH</th>
                    <th class="text-left px-5 py-3">Telefon raqam</th>
                    <th class="text-left px-5 py-3">Test urinishlari</th>
                    <th class="text-left px-5 py-3">Oxirgi natija</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (empty($students)): ?>
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                        Ma'lumot topilmadi.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($students as $index => $student): 
                        $stats = $resultsByUser[$student['id']] ?? null;
                    ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3.5 text-gray-400"><?= $index + 1 ?></td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                                    <?= strtoupper(substr($student['full_name'], 0, 1)) ?>
                                </div>
                                <div class="font-medium text-gray-800"><?= h($student['full_name']) ?></div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-gray-600"><?= h($student['phone']) ?></td>
                        <td class="px-5 py-3.5 text-gray-700">
                            <?= $stats ? (int)$stats['attempts'] . ' ta' : '<span class="text-gray-400">—</span>' ?>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs">
                            <?= $stats ? date('d.m.Y H:i', strtotime($stats['last_completed_at'])) : '<span class="text-gray-400">—</span>' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/teacher_footer.php'; ?>
