<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$page_title = "Test natijalari";
include __DIR__ . '/../../includes/user_header.php';

$user_id = (int)$_SESSION['user_id'];
$sql = "SELECT r.*, t.title as test_title 
        FROM test_results r 
        JOIN tests t ON r.test_id = t.id 
        WHERE r.user_id = $user_id
        ORDER BY r.completed_at DESC";
$results = $db->query($sql);
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Test natijalari</h2>
        <p class="text-sm text-gray-500 mt-1">Sizning barcha topshirgan testlaringiz va ularning natijalari</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?= BASE_URL ?>/user/tests/index.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-sm flex items-center gap-2">
            <i class="fas fa-play"></i> Yangi test ishlash
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sana</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Test nomi</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Natija</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Holat</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Amal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if ($results && mysqli_num_rows($results) > 0): ?>
                    <?php while ($r = mysqli_fetch_assoc($results)): 
                        $percent = $r['total'] > 0 ? round($r['score'] / $r['total'] * 100) : 0;
                        $is_passed = $percent >= 60;
                    ?>
                    <tr class="hover:bg-gray-50/50 transition group">
                        <td class="px-6 py-4 text-sm text-gray-500 font-medium">
                            <?= date('d.m.Y H:i', strtotime($r['completed_at'])) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800 font-medium max-w-[300px] truncate" title="<?= h($r['test_title']) ?>">
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
                        <td class="px-6 py-4 text-right">
                            <a href="<?= BASE_URL ?>/user/tests/result.php?id=<?= $r['id'] ?>" class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition opacity-0 group-hover:opacity-100">
                                <i class="fas fa-eye"></i> Ko'rish
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clipboard-check text-2xl"></i>
                                </div>
                                <p class="font-medium text-gray-800 text-lg">Natijalar hozircha yo'q</p>
                                <p class="text-sm max-w-sm">Siz hali birorta ham test ishlamagansiz. Test ishlaganingizdan so'ng natijalar shu yerda ko'rinadi.</p>
                                <a href="<?= BASE_URL ?>/user/tests/index.php" class="inline-flex items-center gap-2 mt-2 bg-blue-50 text-blue-600 px-5 py-2.5 rounded-xl font-semibold hover:bg-blue-100 transition">
                                    Testlarga o'tish <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
