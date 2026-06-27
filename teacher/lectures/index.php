<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$page_title = "Ma'ruzalar";
$lectures = $db->get_data_by_table_all('lectures', 'ORDER BY order_num ASC, id ASC');

$user_id = (int)($_SESSION['user_id'] ?? 0);
$progress_sql = "SELECT module_id FROM user_module_progress WHERE user_id = $user_id AND module_type = 0";
$progress_res = $db->query($progress_sql);
$read_lectures = [];
if ($progress_res) {
    while ($row = mysqli_fetch_assoc($progress_res)) {
        $read_lectures[] = $row['module_id'];
    }
}

$completed_lectures = [];
foreach ($read_lectures as $lid) {
    $test_res = $db->query("SELECT id FROM tests WHERE module_type = 0 AND module_id = $lid");
    $test = mysqli_fetch_assoc($test_res);
    if ($test) {
        $pass_res = $db->query("SELECT id FROM test_results WHERE test_id = {$test['id']} AND user_id = $user_id AND (score / total) >= 0.6");
        if (mysqli_num_rows($pass_res) > 0) {
            $completed_lectures[] = $lid;
        }
    } else {
        $completed_lectures[] = $lid;
    }
}

foreach ($lectures as &$l) {
    $l['is_read'] = true;
    $l['is_completed'] = true;
    $l['is_unlocked'] = true;
}
unset($l);

include __DIR__ . '/../../includes/teacher_header.php';
?>

<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Ma'ruza mavzulari</h2>
    <p class="text-sm text-gray-500 mt-1">Geodemografiya bo'yicha barcha ma'ruzalar to'plami</p>
</div>

<?php if (empty($lectures)): ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 text-center py-16">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-book-open text-gray-400 text-2xl"></i>
    </div>
    <p class="text-gray-500 font-medium">Ma'ruzalar hali qo'shilmagan</p>
    <p class="text-gray-400 text-sm mt-1">O'qituvchi ma'ruzalarni yuklagandan so'ng ko'rinadi</p>
</div>
<?php else: ?>
<div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php foreach ($lectures as $i => $l): 
        $locked_class = !$l['is_unlocked'] ? 'opacity-60 cursor-not-allowed grayscale-[0.3]' : 'hover:-translate-y-1 hover:shadow-md cursor-pointer';
    ?>
    <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col min-h-[300px] transition-transform transform <?= $locked_class ?> relative">
        <?php if (!$l['is_unlocked']): ?>
        <div class="absolute top-4 right-4 z-10 w-8 h-8 bg-gray-100/90 rounded-full flex items-center justify-center text-gray-500 shadow-sm backdrop-blur-sm" title="Qulflangan">
            <i class="fas fa-lock text-sm"></i>
        </div>
        <?php endif; ?>
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2"></div>
        <div class="p-6 flex-1 flex flex-col relative">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-xl text-xs font-bold flex items-center gap-2 w-max">
                    <i class="fas fa-layer-group text-sm"></i>
                    <?= $l['order_num'] > 0 ? h($l['order_num']) . "-ma'ruza" : "Ma'ruza" ?>
                </div>
                <?php if ($l['is_completed']): ?>
                <div class="text-green-500 text-xs font-bold flex items-center gap-1 bg-green-50 px-2 py-1 rounded-lg">
                    <i class="fas fa-check-circle"></i> O'qilgan
                </div>
                <?php elseif ($l['is_read']): ?>
                <div class="text-orange-500 text-xs font-bold flex items-center gap-1 bg-orange-50 px-2 py-1 rounded-lg">
                    <i class="fas fa-clock"></i> Test kutilmoqda
                </div>
                <?php endif; ?>
            </div>
            
            <h3 class="font-bold text-gray-800 text-base leading-snug mb-3 line-clamp-3"><?= h($l['title']) ?></h3>
            
            <?php if ($l['description']): ?>
            <p class="text-sm text-gray-500 mb-6 flex-1 line-clamp-4 leading-relaxed">
                <?= h($l['description']) ?>
            </p>
            <?php else: ?>
            <div class="flex-1"></div>
            <?php endif; ?>

            <div class="mt-auto pt-4 border-t border-gray-50">
                <?php if ($l['is_unlocked']): ?>
                    <?php if ($l['is_read'] && !$l['is_completed']): ?>
                    <a href="<?= BASE_URL ?>/teacher/lectures/view.php?id=<?= $l['id'] ?>"
                       class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-2.5 rounded-xl text-center transition-colors flex items-center justify-center gap-2 shadow-sm">
                        <i class="fas fa-tasks"></i> Testni ishlash
                    </a>
                    <?php else: ?>
                    <a href="<?= BASE_URL ?>/teacher/lectures/view.php?id=<?= $l['id'] ?>"
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-xl text-center transition-colors flex items-center justify-center gap-2 shadow-sm">
                        <i class="fas fa-book-reader"></i> O'qishni boshlash
                    </a>
                    <?php endif; ?>
                <?php else: ?>
                <button disabled
                   class="w-full bg-gray-100 text-gray-400 font-medium py-2.5 rounded-xl text-center flex items-center justify-center gap-2">
                    <i class="fas fa-lock"></i> Qulflangan
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/teacher_footer.php'; ?>
