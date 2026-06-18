<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$page_title = 'Test natijalari';
include __DIR__ . '/../../includes/admin_header.php';

// Fetch all results
$sql = "SELECT r.*, u.full_name, t.title as test_title 
        FROM test_results r 
        JOIN users u ON r.user_id = u.id 
        JOIN tests t ON r.test_id = t.id 
        ORDER BY r.completed_at DESC";
$results = $db->query($sql);
?>

<div class="mb-5 flex items-center justify-between">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Test Natijalari</h2>
        <p class="text-sm text-gray-500 mt-1">Barcha foydalanuvchilarning test ko'rsatkichlari</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/tests/index.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2">
        <i class="fas fa-arrow-left text-xs"></i> Orqaga
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">O'quvchi</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Test nomi</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Natija</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Holat</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sana</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if ($results && mysqli_num_rows($results) > 0): ?>
                    <?php while ($r = mysqli_fetch_assoc($results)): 
                        $percent = $r['total'] > 0 ? round($r['score'] / $r['total'] * 100) : 0;
                        $is_passed = $percent >= 60;
                    ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs shadow-sm">
                                    <?= mb_substr(trim($r['full_name']), 0, 1) ?>
                                </div>
                                <span class="font-medium text-gray-800"><?= h($r['full_name']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 font-medium max-w-[250px] truncate" title="<?= h($r['test_title']) ?>">
                            <?= h($r['test_title']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold <?= $is_passed ? 'text-green-600' : 'text-red-500' ?>"><?= $percent ?>%</span>
                                <span class="text-xs text-gray-500 mt-0.5"><?= $r['score'] ?> / <?= $r['total'] ?> to'g'ri</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold <?= $is_passed ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100' ?>">
                                <i class="<?= $is_passed ? 'fas fa-check' : 'fas fa-times' ?>"></i>
                                <?= $is_passed ? "O'tdi" : "O'tolmadi" ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 font-medium">
                            <?= date('d.m.Y H:i', strtotime($r['completed_at'])) ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-inbox text-2xl text-gray-400"></i>
                                </div>
                                <p class="font-medium">Hali hech qanday natija mavjud emas</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
