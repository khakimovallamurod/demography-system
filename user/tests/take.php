<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$test_id = (int)($_GET['id'] ?? 0);
$test = $db->get_data_by_table('tests', ['id' => $test_id]);
if (!$test) {
    flash_message('error', 'Test topilmadi!');
    redirect('/user/tests/index.php');
}

$questions = $db->get_data_by_table_all('questions', "WHERE test_id = $test_id ORDER BY id ASC");
if (empty($questions)) {
    flash_message('error', 'Testda savollar mavjud emas!');
    redirect('/user/tests/index.php');
}

// Load options for all questions
foreach ($questions as &$q) {
    $q['options'] = $db->get_data_by_table_all('options', "WHERE question_id = {$q['id']} ORDER BY id ASC");
}
unset($q);

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = $_POST['answers'] ?? [];
    $score = 0;
    $total = count($questions);

    // Create result record
    $result_id = $db->insert('test_results', [
        'user_id'      => $_SESSION['user_id'],
        'test_id'      => $test_id,
        'score'        => 0,
        'total'        => $total,
        'completed_at' => date('Y-m-d H:i:s')
    ]);

    foreach ($questions as $q) {
        $selected = isset($answers[$q['id']]) ? (int)$answers[$q['id']] : null;
        if ($selected) {
            $option = $db->get_data_by_table('options', ['id' => $selected]);
            if ($option && $option['is_correct']) $score++;
        }
        $db->insert('test_answers', [
            'result_id'          => $result_id,
            'question_id'        => $q['id'],
            'selected_option_id' => $selected ?? ''
        ]);
    }

    // Update score
    $db->update('test_results', ['score' => $score], "id = $result_id");

    redirect('/user/tests/result.php?id=' . $result_id);
}

$page_title = h($test['title']);
include __DIR__ . '/../../includes/user_header.php';
?>

<div class="max-w-3xl mx-auto" id="test-container">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-5 overflow-hidden">
        <div class="bg-gradient-to-r from-orange-500 to-red-500 p-5 text-white">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-lg font-bold mb-1"><?= h($test['title']) ?></h1>
                    <p class="text-orange-100 text-sm"><?= count($questions) ?> savol · <?= $test['duration'] ?> daqiqa</p>
                </div>
                <div class="text-center flex-shrink-0">
                    <div id="timer" class="text-2xl font-bold font-mono bg-white/20 px-4 py-2 rounded-xl">
                        <?= str_pad($test['duration'], 2, '0', STR_PAD_LEFT) ?>:00
                    </div>
                    <p class="text-xs text-orange-200 mt-1">Qolgan vaqt</p>
                </div>
            </div>
        </div>

        <!-- Progress -->
        <div class="px-5 py-3 bg-orange-50">
            <div class="flex items-center justify-between text-xs text-gray-600 mb-1.5">
                <span>Javob berilgan: <span id="answered-count">0</span>/<?= count($questions) ?></span>
                <span id="progress-pct">0%</span>
            </div>
            <div class="w-full bg-orange-100 rounded-full h-1.5">
                <div id="progress-bar" class="bg-orange-500 h-1.5 rounded-full transition-all" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <!-- Questions Form -->
    <form method="POST" id="test-form">
        <div class="space-y-4">
            <?php foreach ($questions as $qi => $q): ?>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5" id="q-<?= $q['id'] ?>">
                <div class="flex items-start gap-3 mb-4">
                    <span class="w-7 h-7 bg-orange-100 rounded-lg text-orange-600 text-xs font-bold flex items-center justify-center flex-shrink-0">
                        <?= $qi + 1 ?>
                    </span>
                    <p class="font-medium text-gray-800"><?= h($q['question_text']) ?></p>
                </div>

                <div class="space-y-2 ml-10">
                    <?php foreach ($q['options'] as $oi => $o): ?>
                    <label class="option-label flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-orange-300 hover:bg-orange-50 transition"
                           data-q="<?= $q['id'] ?>">
                        <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $o['id'] ?>"
                               class="w-4 h-4 accent-orange-500"
                               onchange="updateProgress()">
                        <span class="w-6 h-6 rounded-lg bg-gray-100 text-gray-500 text-xs font-bold flex items-center justify-center flex-shrink-0">
                            <?= chr(65 + $oi) ?>
                        </span>
                        <span class="text-sm text-gray-700"><?= h($o['option_text']) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-5 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle text-orange-400 mr-1"></i>
                    Barcha savollarga javob berilganidan so'ng "Yakunlash" tugmasini bosing.
                </div>
                <button type="submit"
                    onclick="return confirm('Testni yakunlamoqchimisiz? Yuborganidan keyin o\'zgartirib bo\'lmaydi.')"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-8 py-3 rounded-xl transition flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> Testni yakunlash
                </button>
            </div>
        </div>
    </form>
</div>

<script>
const totalQuestions = <?= count($questions) ?>;
const duration = <?= (int)$test['duration'] ?> * 60;
let timeLeft = duration;

function updateProgress() {
    const answered = document.querySelectorAll('input[type=radio]:checked').length;
    const pct = Math.round(answered / totalQuestions * 100);
    document.getElementById('answered-count').textContent = answered;
    document.getElementById('progress-pct').textContent = pct + '%';
    document.getElementById('progress-bar').style.width = pct + '%';

    // Highlight answered questions
    document.querySelectorAll('input[type=radio]:checked').forEach(radio => {
        const name = radio.name;
        document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
            r.closest('.option-label').classList.remove('border-orange-400', 'bg-orange-50');
        });
        radio.closest('.option-label').classList.add('border-orange-400', 'bg-orange-50');
    });
}

// Timer
const timerEl = document.getElementById('timer');
const timerInterval = setInterval(() => {
    timeLeft--;
    const m = Math.floor(timeLeft / 60);
    const s = timeLeft % 60;
    timerEl.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');

    if (timeLeft <= 60) timerEl.classList.add('text-red-300');
    if (timeLeft <= 0) {
        clearInterval(timerInterval);
        document.getElementById('test-form').submit();
    }
}, 1000);

// Warn on page leave
window.onbeforeunload = () => 'Test hali yakunlanmagan!';
document.getElementById('test-form').onsubmit = () => { window.onbeforeunload = null; };
</script>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
