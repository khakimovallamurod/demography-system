<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$page_title = 'Testlar';
$tests = $db->get_data_by_table_all('tests', 'ORDER BY created_at DESC');

include __DIR__ . '/../../includes/user_header.php';
?>

<div class="mb-5">
    <h2 class="text-lg font-bold text-gray-800">Testlar</h2>
    <p class="text-sm text-gray-500 mt-0.5">Bilimingizni sinab ko'ring</p>
</div>

<!-- My Results -->
<?php
$my_results = $db->get_data_by_table_all('test_results',
    "WHERE user_id = {$_SESSION['user_id']} AND completed_at IS NOT NULL ORDER BY completed_at DESC");
$my_result_map = [];
foreach ($my_results as $r) {
    if (!isset($my_result_map[$r['test_id']])) {
        $my_result_map[$r['test_id']] = $r;
    }
}
?>

<?php if (empty($tests)): ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 text-center py-16">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
    </div>
    <p class="text-gray-500 font-medium">Testlar hali qo'shilmagan</p>
</div>
<?php else: ?>
<div class="grid sm:grid-cols-2 gap-4">
    <?php foreach ($tests as $t):
        $q_res = $db->query("SELECT COUNT(*) as cnt FROM questions WHERE test_id = {$t['id']}");
        $q_count = mysqli_fetch_assoc($q_res)['cnt'];
        $my_result = $my_result_map[$t['id']] ?? null;
        $percent = $my_result && $my_result['total'] > 0 ? round($my_result['score'] / $my_result['total'] * 100) : null;
    ?>
    <div class="card-hover bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-br from-orange-400 to-red-500 h-2"></div>
        <div class="p-5">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clipboard-list text-orange-500"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-800"><?= h($t['title']) ?></h3>
                    <?php if ($t['description']): ?>
                    <p class="text-xs text-gray-500 mt-0.5"><?= h(mb_substr($t['description'], 0, 70)) ?><?= strlen($t['description']) > 70 ? '...' : '' ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex items-center gap-4 mb-4">
                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                    <i class="fas fa-question-circle text-orange-400"></i>
                    <span><?= $q_count ?> savol</span>
                </div>
                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                    <i class="fas fa-clock text-orange-400"></i>
                    <span><?= $t['duration'] ?> daqiqa</span>
                </div>
            </div>

            <?php if ($my_result): ?>
            <div class="mb-4 bg-gray-50 rounded-xl p-3">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-gray-500">So'nggi natija</span>
                    <span class="text-xs font-bold <?= $percent >= 70 ? 'text-green-600' : ($percent >= 50 ? 'text-yellow-600' : 'text-red-500') ?>">
                        <?= $my_result['score'] ?>/<?= $my_result['total'] ?> (<?= $percent ?>%)
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full <?= $percent >= 70 ? 'bg-green-500' : ($percent >= 50 ? 'bg-yellow-400' : 'bg-red-400') ?>"
                         style="width: <?= $percent ?>%"></div>
                </div>
            </div>
            <?php endif; ?>

            <div class="flex gap-2">
                <?php if ($q_count > 0): ?>
                <a href="<?= BASE_URL ?>/user/tests/take.php?id=<?= $t['id'] ?>"
                   class="flex-1 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium py-2.5 rounded-xl text-center transition">
                    <?= $my_result ? 'Qayta ishlash' : 'Testni boshlash' ?> <i class="fas fa-play text-xs ml-1"></i>
                </a>
                <?php if ($my_result): ?>
                <a href="<?= BASE_URL ?>/user/tests/result.php?id=<?= $my_result['id'] ?>"
                   class="w-10 h-10 bg-gray-50 hover:bg-gray-100 text-gray-500 rounded-xl flex items-center justify-center transition" title="Natijani ko'rish">
                    <i class="fas fa-chart-bar text-sm"></i>
                </a>
                <?php endif; ?>
                <?php else: ?>
                <div class="flex-1 bg-gray-100 text-gray-400 text-sm py-2.5 rounded-xl text-center cursor-not-allowed">
                    Savollar qo'shilmagan
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
