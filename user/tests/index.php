<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$page_title = 'Testlar';
$tests = $db->get_data_by_table_all('tests', 'ORDER BY created_at DESC');

// Fetch user completed modules to check locks
$user_id = (int)$_SESSION['user_id'];
$progress_res = $db->query("SELECT module_type, module_id FROM user_module_progress WHERE user_id = $user_id");
$completed_modules = [];
if ($progress_res) {
    while($row = mysqli_fetch_assoc($progress_res)) {
        $completed_modules[$row['module_type'] . '_' . $row['module_id']] = true;
    }
}

// Fetch lecture/practical names for badges
$lectures_res = $db->query("SELECT id, title FROM lectures");
$lectures_map = [];
if ($lectures_res) {
    while($r = mysqli_fetch_assoc($lectures_res)) $lectures_map[$r['id']] = $r['title'];
}

$practicals_res = $db->query("SELECT id, title FROM practicals");
$practicals_map = [];
if ($practicals_res) {
    while($r = mysqli_fetch_assoc($practicals_res)) $practicals_map[$r['id']] = $r['title'];
}

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
$attempts_map = [];
foreach ($my_results as $r) {
    if (!isset($my_result_map[$r['test_id']])) {
        $my_result_map[$r['test_id']] = $r;
    }
}
$all_attempts = $db->get_data_by_table_all('test_results', "WHERE user_id = {$_SESSION['user_id']}");
foreach ($all_attempts as $a) {
    if (!isset($attempts_map[$a['test_id']])) $attempts_map[$a['test_id']] = 0;
    $attempts_map[$a['test_id']]++;
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
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php foreach ($tests as $t):
        $q_res = $db->query("SELECT COUNT(*) as cnt FROM questions WHERE test_id = {$t['id']}");
        $q_count = mysqli_fetch_assoc($q_res)['cnt'];
        $my_result = $my_result_map[$t['id']] ?? null;
        $percent = $my_result && $my_result['total'] > 0 ? round($my_result['score'] / $my_result['total'] * 100) : null;
        $attempts_made = $attempts_map[$t['id']] ?? 0;
        $can_take = $attempts_made < $t['attempts_limit'];
        
        // Lock logic
        $is_locked = false;
        $lock_reason = '';
        $module_name = '';
        if (!empty($t['module_id'])) {
            $key = $t['module_type'] . '_' . $t['module_id'];
            
            if ($t['module_type'] == 0) {
                $module_name = $lectures_map[$t['module_id']] ?? '';
                if (!isset($completed_modules[$key])) {
                    $is_locked = true;
                    $lock_reason = "Testni ishlash uchun avval Ma'ruzani to'liq o'qib chiqishingiz kerak.";
                }
            } else {
                $module_name = $practicals_map[$t['module_id']] ?? '';
                if (!isset($completed_modules[$key])) {
                    $is_locked = true;
                    $lock_reason = "Testni ishlash uchun avval Amaliyotni yakunlashingiz kerak.";
                }
            }
        }
    ?>
    <div class="card-hover bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col h-full">
        <div class="bg-gradient-to-br from-orange-400 to-red-500 h-2"></div>
        <div class="p-6 flex-1 flex flex-col relative">
            <?php if (!empty($t['module_id'])): ?>
            <div class="mb-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?= $t['module_type'] == 0 ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' ?>">
                    <i class="<?= $t['module_type'] == 0 ? 'fas fa-book-open' : 'fas fa-chalkboard-teacher' ?>"></i>
                    <span class="truncate max-w-[180px]"><?= h($module_name) ?></span>
                </span>
            </div>
            <?php endif; ?>

            <div class="flex items-start gap-4 mb-4">
                <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clipboard-check text-orange-500 text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-gray-800 text-lg leading-tight mb-1"><?= h($t['title']) ?></h3>
                    <?php if ($t['description']): ?>
                    <p class="text-sm text-gray-500 line-clamp-2"><?= h($t['description']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-5 mt-auto">
                <div class="bg-gray-50 rounded-xl p-3 flex flex-col items-center justify-center">
                    <i class="fas fa-question-circle text-orange-400 mb-1 text-lg"></i>
                    <span class="text-xs text-gray-500 font-medium"><?= $q_count ?> savol</span>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 flex flex-col items-center justify-center">
                    <i class="fas fa-clock text-orange-400 mb-1 text-lg"></i>
                    <span class="text-xs text-gray-500 font-medium"><?= $t['duration'] ?> daqiqa</span>
                </div>
            </div>

            <?php if ($my_result): ?>
            <div class="mb-5 bg-gray-50 rounded-xl p-3.5 border border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">So'nggi natija</span>
                    <span class="text-sm font-bold <?= $percent >= 70 ? 'text-green-600' : ($percent >= 50 ? 'text-yellow-600' : 'text-red-500') ?>">
                        <?= $my_result['score'] ?> ta to'g'ri / <?= $my_result['total'] ?> tadan (<?= $percent ?>%)
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full <?= $percent >= 70 ? 'bg-green-500' : ($percent >= 50 ? 'bg-yellow-400' : 'bg-red-400') ?> transition-all duration-1000"
                         style="width: <?= $percent ?>%"></div>
                </div>
            </div>
            <?php endif; ?>

            <div class="mb-5 flex items-center justify-between text-xs font-medium px-1">
                <span class="text-gray-500">Urinishlar soni:</span>
                <span class="<?= $can_take ? 'text-blue-600' : 'text-red-500' ?>"><?= $attempts_made ?> / <?= $t['attempts_limit'] ?></span>
            </div>

            <div class="flex gap-2">
                <?php if ($is_locked): ?>
                <div class="flex-1 bg-gray-100 text-gray-400 text-sm font-bold py-3 rounded-xl text-center flex items-center justify-center gap-2" title="<?= h($lock_reason) ?>">
                    <i class="fas fa-lock text-sm"></i> Qulflangan
                </div>
                <?php elseif ($q_count > 0): ?>
                    <?php if ($can_take): ?>
                    <a href="<?= BASE_URL ?>/user/tests/take.php?id=<?= $t['id'] ?>"
                       class="flex-1 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold py-3 rounded-xl text-center transition shadow-sm flex items-center justify-center gap-2">
                        <?= $my_result ? 'Qayta ishlash' : 'Testni boshlash' ?> <i class="fas fa-play text-xs"></i>
                    </a>
                    <?php else: ?>
                    <div class="flex-1 bg-gray-100 text-gray-500 text-sm font-bold py-3 rounded-xl text-center cursor-not-allowed flex items-center justify-center gap-2" title="Urinishlar soni tugagan">
                        Urinishlar tugagan
                    </div>
                    <?php endif; ?>
                <?php if ($my_result): ?>
                <a href="<?= BASE_URL ?>/user/tests/result.php?id=<?= $my_result['id'] ?>"
                   class="w-12 bg-gray-50 hover:bg-gray-100 text-gray-500 border border-gray-200 rounded-xl flex items-center justify-center transition flex-shrink-0" title="Natijani ko'rish">
                    <i class="fas fa-chart-bar text-sm"></i>
                </a>
                <?php endif; ?>
                <?php else: ?>
                <div class="flex-1 bg-gray-100 text-gray-400 text-sm font-bold py-3 rounded-xl text-center cursor-not-allowed">
                    Savollar kiritilmagan
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($is_locked): ?>
            <p class="text-[10px] text-red-500 mt-2 text-center font-medium"><?= h($lock_reason) ?></p>
            <?php endif; ?>
            
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
