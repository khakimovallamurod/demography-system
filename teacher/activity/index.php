<?php
require_once __DIR__ . '/../../includes/functions.php';
require_teacher();

$page_title = 'Talaba faolligi';

// Get current teacher's university_id
$teacher_id = (int)$_SESSION['user_id'];
$teacher = $db->get_data_by_table('users', ['id' => $teacher_id]);
$university_id = (int)($teacher['university_id'] ?? 0);

$search_date = trim($_GET['date'] ?? date('Y-m-d'));

$sql = "
    SELECT a.*, u.full_name, u.phone 
    FROM user_activity a
    JOIN users u ON a.user_id = u.id
    WHERE u.role = 'user' 
      AND u.university_id = $university_id
      AND a.date = '" . $db->escape($search_date) . "'
    ORDER BY a.total_minutes DESC, a.visits_count DESC
";
$activities_res = $db->query($sql);
$activities = [];
if ($activities_res) {
    while ($row = mysqli_fetch_assoc($activities_res)) {
        $activities[] = $row;
    }
}

include __DIR__ . '/../../includes/teacher_header.php';
?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 p-5 border-b border-gray-100">
        <div>
            <h3 class="font-semibold text-gray-700 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-500"></i> Talaba faolligi
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full"><?= count($activities) ?> ta yozuv</span>
            </h3>
            <p class="text-sm text-gray-400 mt-1">Platformadan kunlik foydalanish vaqti va kirishlar soni</p>
        </div>

        <form method="GET" class="w-full lg:w-auto">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative min-w-[200px]">
                    <i class="fas fa-calendar absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input
                        type="date"
                        name="date"
                        value="<?= h($search_date) ?>"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-300"
                    >
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition">
                    <i class="fas fa-filter text-xs"></i> Filtrlash
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
                    <th class="text-left px-5 py-3">Sana</th>
                    <th class="text-left px-5 py-3">Tizimga kirishlar</th>
                    <th class="text-left px-5 py-3">Jami vaqt (daqiqa)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (empty($activities)): ?>
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                        Tanlangan sanada faollik ko'rsatkichlari topilmadi.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($activities as $index => $act): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3.5 text-gray-400"><?= $index + 1 ?></td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                                    <?= strtoupper(substr($act['full_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-800"><?= h($act['full_name']) ?></div>
                                    <div class="text-xs text-gray-400"><?= h($act['phone']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-gray-600"><?= date('d.m.Y', strtotime($act['date'])) ?></td>
                        <td class="px-5 py-3.5 text-gray-700">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-600">
                                <?= (int)$act['visits_count'] ?> marta
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-gray-700">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold <?= $act['total_minutes'] > 60 ? 'bg-green-50 text-green-600' : 'bg-orange-50 text-orange-600' ?>">
                                <i class="fas fa-stopwatch text-[10px]"></i> <?= (int)$act['total_minutes'] ?> daqiqa
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/teacher_footer.php'; ?>
