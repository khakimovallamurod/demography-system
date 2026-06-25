<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$page_title = "Amaliy mashg'ulotlar";
$practicals = $db->get_data_by_table_all('practicals', 'ORDER BY order_num ASC, id ASC');

$user_id = (int)($_SESSION['user_id'] ?? 0);

// Get all completed lectures order_nums
$progress_sql = "SELECT l.id, l.order_num 
                 FROM user_module_progress p
                 JOIN lectures l ON p.module_id = l.id
                 WHERE p.user_id = $user_id AND p.module_type = 0";
$progress_res = $db->query($progress_sql);
$completed_lecture_orders = [];
if ($progress_res) {
    while ($row = mysqli_fetch_assoc($progress_res)) {
        if ($row['order_num'] > 0) {
            $lid = $row['id'];
            $t_res = $db->query("SELECT id FROM tests WHERE module_type = 0 AND module_id = $lid");
            $t = mysqli_fetch_assoc($t_res);
            if ($t) {
                $p_res = $db->query("SELECT id FROM test_results WHERE test_id = {$t['id']} AND user_id = $user_id AND (score / total) >= 0.6");
                if (mysqli_num_rows($p_res) > 0) {
                    $completed_lecture_orders[] = (int)$row['order_num'];
                }
            } else {
                $completed_lecture_orders[] = (int)$row['order_num'];
            }
        }
    }
}

// Get completed practicals
$p_progress_sql = "SELECT module_id FROM user_module_progress WHERE user_id = $user_id AND module_type = 1";
$p_progress_res = $db->query($p_progress_sql);
$completed_practicals = [];
if ($p_progress_res) {
    while ($row = mysqli_fetch_assoc($p_progress_res)) {
        $completed_practicals[] = $row['module_id'];
    }
}

foreach ($practicals as &$p) {
    $p['is_completed'] = in_array($p['id'], $completed_practicals);
    // Dastlab barchasi qulf bo'ladi. Agar shu tartib raqamidagi ma'ruza tugatilgan bo'lsa ochiladi.
    if ((int)$p['order_num'] === 0) {
        $p['is_unlocked'] = true;
    } else {
        $p['is_unlocked'] = in_array((int)$p['order_num'], $completed_lecture_orders);
    }
}
unset($p);

include __DIR__ . '/../../includes/user_header.php';
?>

<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Amaliy mashg'ulotlar</h2>
    <p class="text-sm text-gray-500 mt-1">Demografiya bo'yicha barcha amaliyotlar to'plami</p>
</div>

<?php if (empty($practicals)): ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 text-center py-16">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-chalkboard-teacher text-gray-400 text-2xl"></i>
    </div>
    <p class="text-gray-500 font-medium">Amaliyotlar hali qo'shilmagan</p>
    <p class="text-gray-400 text-sm mt-1">O'qituvchi amaliyotlarni yuklagandan so'ng ko'rinadi</p>
</div>
<?php else: ?>
<div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php foreach ($practicals as $i => $l): 
        $locked_class = !$l['is_unlocked'] ? 'opacity-60 cursor-not-allowed grayscale-[0.3]' : 'hover:-translate-y-1 hover:shadow-md cursor-pointer';
    ?>
    <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col min-h-[300px] transition-transform transform <?= $locked_class ?> relative">
        <?php if (!$l['is_unlocked']): ?>
        <div class="absolute top-4 right-4 z-10 w-8 h-8 bg-gray-100/90 rounded-full flex items-center justify-center text-gray-500 shadow-sm backdrop-blur-sm" title="Qulflangan">
            <i class="fas fa-lock text-sm"></i>
        </div>
        <?php endif; ?>
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 h-2"></div>
        <div class="p-6 flex-1 flex flex-col relative">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-xl text-xs font-bold flex items-center gap-2 w-max">
                    <i class="fas fa-tasks text-sm"></i>
                    <?= $l['order_num'] > 0 ? h($l['order_num']) . "-amaliyot" : 'Amaliyot' ?>
                </div>
                <?php if ($l['is_completed']): ?>
                <div class="text-green-500 text-xs font-bold flex items-center gap-1 bg-green-50 px-2 py-1 rounded-lg">
                    <i class="fas fa-check-circle"></i> O'qilgan
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
                <a href="<?= BASE_URL ?>/user/practicals/view.php?id=<?= $l['id'] ?>"
                   class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 rounded-xl text-center transition-colors flex items-center justify-center gap-2 shadow-sm">
                    <i class="fas fa-book-reader"></i> O'qishni boshlash
                </a>
                <?php else: ?>
                <button disabled
                   class="w-full bg-gray-100 text-gray-400 font-medium py-2.5 rounded-xl text-center flex items-center justify-center gap-2">
                    <i class="fas fa-lock"></i> Ma'ruza o'qilmagan
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
