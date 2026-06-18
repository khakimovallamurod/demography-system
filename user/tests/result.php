<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$result_id = (int)($_GET['id'] ?? 0);
$result = $db->get_data_by_table('test_results', ['id' => $result_id]);

if (!$result || $result['user_id'] != $_SESSION['user_id']) {
    flash_message('error', 'Natija topilmadi!');
    redirect('/user/tests/index.php');
}

$test      = $db->get_data_by_table('tests', ['id' => $result['test_id']]);
$questions = $db->get_data_by_table_all('questions', "WHERE test_id = {$result['test_id']} ORDER BY id ASC");
$answers   = $db->get_data_by_table_all('test_answers', "WHERE result_id = $result_id");

$answer_map = [];
foreach ($answers as $a) {
    $answer_map[$a['question_id']] = $a['selected_option_id'];
}

$percent = $result['total'] > 0 ? round($result['score'] / $result['total'] * 100) : 0;

$is_passed = $percent >= 60;

if ($percent >= 86)      { $grade = 'A'; $grade_text = 'Ajoyib!';      $color = 'green'; }
elseif ($percent >= 71)  { $grade = 'B'; $grade_text = 'Yaxshi!';      $color = 'blue'; }
elseif ($percent >= 56)  { $grade = 'C'; $grade_text = 'Qoniqarli';    $color = 'yellow'; }
else                     { $grade = 'D'; $grade_text = 'Qayta ishlang'; $color = 'red'; }

$page_title = 'Test natijasi';
include __DIR__ . '/../../includes/user_header.php';

$show_alert = isset($_SESSION['show_result_alert']);
if ($show_alert) unset($_SESSION['show_result_alert']);
?>

<div class="max-w-3xl mx-auto">
    <!-- Result Summary Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-5">
        <div class="bg-gradient-to-r
            <?= $color === 'green' ? 'from-green-500 to-emerald-600' :
               ($color === 'blue' ? 'from-blue-500 to-blue-700' :
               ($color === 'yellow' ? 'from-yellow-400 to-orange-500' :
               'from-red-500 to-red-700')) ?>
            p-8 text-white text-center">

            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 relative">
                <span class="text-4xl font-bold"><?= $grade ?></span>
                <?php if ($is_passed): ?>
                <div class="absolute -bottom-2 -right-2 bg-green-500 text-white w-8 h-8 rounded-full flex items-center justify-center border-2 border-white shadow-sm" title="O'tdi">
                    <i class="fas fa-check text-sm"></i>
                </div>
                <?php else: ?>
                <div class="absolute -bottom-2 -right-2 bg-red-500 text-white w-8 h-8 rounded-full flex items-center justify-center border-2 border-white shadow-sm" title="O'ta olmadi">
                    <i class="fas fa-times text-sm"></i>
                </div>
                <?php endif; ?>
            </div>
            <h2 class="text-2xl font-bold mb-1"><?= $grade_text ?></h2>
            <p class="text-white/80 text-sm mb-3"><?= h($test['title'] ?? '') ?></p>
            
            <span class="inline-block px-4 py-1.5 rounded-full text-sm font-bold bg-white/20 border border-white/30 <?= $is_passed ? 'text-green-50' : 'text-red-50' ?>">
                Holati: <?= $is_passed ? "Testdan o'tdingiz" : "Testdan o'ta olmadingiz" ?>
            </span>

            <div class="grid grid-cols-3 gap-4 mt-6">
                <div class="bg-white/20 rounded-xl p-3">
                    <p class="text-2xl font-bold"><?= $result['score'] ?></p>
                    <p class="text-xs text-white/70">To'g'ri</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <p class="text-2xl font-bold"><?= $result['total'] - $result['score'] ?></p>
                    <p class="text-xs text-white/70">Noto'g'ri</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <p class="text-2xl font-bold"><?= $percent ?>%</p>
                    <p class="text-xs text-white/70">Natija</p>
                </div>
            </div>
        </div>

        <div class="p-5">
            <div class="flex gap-3">
                <a href="<?= BASE_URL ?>/user/tests/take.php?id=<?= $result['test_id'] ?>"
                   class="flex-1 bg-gray-50 hover:bg-gray-100 text-gray-700 text-sm font-medium py-2.5 rounded-xl text-center transition">
                    <i class="fas fa-redo mr-1"></i> Qayta ishlash
                </a>
                <a href="<?= BASE_URL ?>/user/tests/index.php"
                   class="flex-1 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium py-2.5 rounded-xl text-center transition">
                    <i class="fas fa-list mr-1"></i> Testlar ro'yxati
                </a>
            </div>
        </div>
    </div>

    <!-- Detailed Review -->
    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-3">Batafsil ko'rish</h3>
    <div class="space-y-4">
        <?php foreach ($questions as $qi => $q):
            $options = $db->get_data_by_table_all('options', "WHERE question_id = {$q['id']} ORDER BY id ASC");
            $selected_id = $answer_map[$q['id']] ?? null;
            $is_correct = false;
            foreach ($options as $o) {
                if ($o['is_correct'] && $o['id'] == $selected_id) {
                    $is_correct = true;
                    break;
                }
            }
        ?>
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden
            <?= $is_correct ? 'border-green-200' : 'border-red-200' ?>">
            <div class="flex items-center gap-2 px-5 py-3
                <?= $is_correct ? 'bg-green-50' : 'bg-red-50' ?> border-b
                <?= $is_correct ? 'border-green-200' : 'border-red-200' ?>">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white
                    <?= $is_correct ? 'bg-green-500' : 'bg-red-500' ?>">
                    <?= $is_correct ? '✓' : '✗' ?>
                </span>
                <span class="text-xs font-medium <?= $is_correct ? 'text-green-700' : 'text-red-700' ?>">
                    <?= $qi + 1 ?>-savol · <?= $is_correct ? "To'g'ri javob" : "Noto'g'ri javob" ?>
                </span>
            </div>

            <div class="p-5">
                <p class="font-medium text-gray-800 mb-3"><?= h($q['question_text']) ?></p>
                <div class="space-y-2">
                    <?php foreach ($options as $oi => $o):
                        $is_selected = ($o['id'] == $selected_id);
                        $is_correct_opt = (bool)$o['is_correct'];
                        
                        if ($is_selected && $is_correct_opt) {
                            $classes = 'bg-green-50 border border-green-200 text-green-700';
                            $icon = '<i class="fas fa-check text-green-500 ml-auto text-xs"></i>';
                            $letter_classes = 'bg-green-500 text-white';
                        } elseif ($is_selected && !$is_correct_opt) {
                            $classes = 'bg-red-50 border border-red-200 text-red-600';
                            $icon = '<i class="fas fa-times text-red-400 ml-auto text-xs"></i>';
                            $letter_classes = 'bg-red-500 text-white';
                        } else {
                            $classes = 'bg-gray-50 border border-transparent text-gray-600';
                            $icon = '';
                            $letter_classes = 'bg-gray-200 text-gray-500';
                        }
                    ?>
                    <div class="flex items-center gap-2.5 p-2.5 rounded-xl text-sm <?= $classes ?>">
                        <span class="w-6 h-6 rounded-lg text-xs font-bold flex items-center justify-center flex-shrink-0 <?= $letter_classes ?>">
                            <?= chr(65 + $oi) ?>
                        </span>
                        <span class="font-medium">
                            <?= h($o['option_text']) ?>
                        </span>
                        <?= $icon ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Navigation Buttons -->
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <a href="<?= BASE_URL ?>/user/lectures/view.php?id=<?= $test['module_id'] ?>" class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-8 py-3.5 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
            <i class="fas fa-arrow-left"></i> Ma'ruzaga qaytish
        </a>
        <?php if ($is_passed): ?>
        <a href="<?= BASE_URL ?>/user/lectures/index.php" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3.5 rounded-xl transition shadow-md flex items-center justify-center gap-2">
            Keyingi ma'ruzaga o'tish <i class="fas fa-arrow-right"></i>
        </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($show_alert): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const isPassed = <?= $is_passed ? 'true' : 'false' ?>;
    const percent = <?= $percent ?>;
    
    if (isPassed) {
        Swal.fire({
            title: "Tabriklaymiz, o'tdingiz!",
            text: `Siz ${percent}% natija ko'rsatib, testdan muvaffaqiyatli o'tdingiz.`,
            icon: 'success',
            confirmButtonText: 'Davom etish',
            confirmButtonColor: '#10b981'
        });
    } else {
        Swal.fire({
            title: "Afsuski, o'ta olmadingiz!",
            text: `Siz ${percent}% natija ko'rsatdingiz. O'tish bali kamida 60%. Qayta urinib ko'ring!`,
            icon: 'error',
            confirmButtonText: 'Tushunarli',
            confirmButtonColor: '#ef4444'
        });
    }
});
</script>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
