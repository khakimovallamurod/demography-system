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

$questions = $db->get_data_by_table_all('questions', "WHERE test_id = $test_id ORDER BY RAND() LIMIT " . $test['questions_limit']);
if (empty($questions)) {
    flash_message('error', 'Testda savollar mavjud emas!');
    redirect('/user/tests/index.php');
}

$user_id = (int)$_SESSION['user_id'];

// Check attempts limit
$attempts_res = $db->query("SELECT COUNT(*) as cnt FROM test_results WHERE user_id = $user_id AND test_id = $test_id");
$attempts_count = mysqli_fetch_assoc($attempts_res)['cnt'];
if ($attempts_count >= $test['attempts_limit']) {
    flash_message('error', 'Bu test uchun barcha urinishlar soni tugagan!');
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
            
            $db->insert('test_answers', [
                'result_id'          => $result_id,
                'question_id'        => $q['id'],
                'selected_option_id' => $selected
            ]);
        } else {
            $db->insert('test_answers', [
                'result_id'          => $result_id,
                'question_id'        => $q['id']
            ]);
        }
    }

    // Update score
    $db->update('test_results', ['score' => $score], "id = $result_id");

    // Pass session var to trigger alert
    $_SESSION['show_result_alert'] = true;

    redirect('/user/tests/result.php?id=' . $result_id);
}

$page_title = h($test['title']);
include __DIR__ . '/../../includes/user_header.php';
?>

<div class="max-w-6xl mx-auto" id="test-container">
    <!-- Header (Timer and Title) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-5 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4 sm:p-5 text-white flex items-center justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-base sm:text-lg font-bold mb-1 truncate"><?= h($test['title']) ?></h1>
                <p class="text-blue-100 text-xs sm:text-sm">Jami: <?= count($questions) ?> savol · Vaqt: <?= $test['duration'] ?> daqiqa</p>
            </div>
            <div class="text-center flex-shrink-0 bg-white/20 rounded-xl px-4 py-2 border border-white/30">
                <div id="timer" class="text-xl sm:text-2xl font-bold font-mono tracking-wider">
                    <?= str_pad($test['duration'], 2, '0', STR_PAD_LEFT) ?>:00
                </div>
                <p class="text-[10px] sm:text-xs text-blue-100 mt-0.5 uppercase tracking-wide">Qolgan vaqt</p>
            </div>
        </div>
    </div>

    <form method="POST" id="test-form">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            <!-- Left Panel (Questions) -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-8 flex flex-col min-h-[400px]">
                    
                    <!-- Questions Container -->
                    <div id="questions-wrapper" class="flex-1">
                        <?php foreach ($questions as $qi => $q): ?>
                        <div class="question-block hidden" data-index="<?= $qi ?>" id="q-block-<?= $qi ?>">
                            <div class="mb-6">
                                <span class="text-xs font-bold text-blue-500 tracking-wider uppercase mb-3 inline-block">
                                    SAVOL <?= $qi + 1 ?> / <?= count($questions) ?>
                                </span>
                                <p class="text-lg font-medium text-gray-800 leading-relaxed"><?= h($q['question_text']) ?></p>
                            </div>

                            <div class="space-y-3">
                                <?php foreach ($q['options'] as $oi => $o): ?>
                                <label class="option-label flex items-center gap-4 p-4 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition"
                                       data-q-idx="<?= $qi ?>">
                                    <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $o['id'] ?>"
                                           class="w-5 h-5 accent-blue-600 focus:ring-blue-500"
                                           onchange="answerSelected(<?= $qi ?>)">
                                    <span class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-600 text-sm font-bold flex items-center justify-center flex-shrink-0 shadow-sm">
                                        <?= chr(65 + $oi) ?>
                                    </span>
                                    <span class="text-base text-gray-700"><?= h($o['option_text']) ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Navigation Footer inside Card -->
                    <div class="mt-8 pt-5 border-t border-gray-100 flex items-center justify-between gap-4 flex-wrap">
                        <button type="button" id="btn-prev" onclick="goTo(currentIndex - 1)"
                                class="px-6 py-2.5 rounded-xl text-gray-600 font-semibold border border-gray-200 hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed bg-white">
                            Oldingi
                        </button>
                        
                        <span class="text-xs text-gray-400 hidden sm:inline-block">
                            Savol raqamini o'ngdagi navigator orqali ham tanlashingiz mumkin
                        </span>

                        <button type="button" id="btn-next" onclick="goTo(currentIndex + 1)"
                                class="px-8 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition shadow-sm">
                            Keyingi
                        </button>
                        
                        <button type="button" id="btn-finish" class="hidden px-8 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition shadow-sm items-center gap-2">
                            Tugatish <i class="fas fa-check-circle text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Panel (Navigator) -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-24">
                    <h3 class="font-bold text-gray-800 text-sm mb-4">Savollar navigatori</h3>
                    
                    <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-4 gap-2 mb-6" id="nav-grid">
                        <?php foreach ($questions as $qi => $q): ?>
                        <button type="button" onclick="goTo(<?= $qi ?>)" id="nav-btn-<?= $qi ?>"
                                class="nav-btn aspect-square rounded-lg text-sm font-semibold flex items-center justify-center transition bg-gray-100 text-gray-600 hover:bg-gray-200">
                            <?= $qi + 1 ?>
                        </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-xs text-gray-600">
                            <span class="w-3 h-3 rounded bg-gray-200 inline-block"></span> Ochilmagan
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-600">
                            <span class="w-3 h-3 rounded bg-blue-500 inline-block"></span> Hozirgi
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-600">
                            <span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span> Javob berilgan
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-600">
                            <span class="w-3 h-3 rounded bg-red-500 inline-block"></span> Bo'sh (Tashlab ketilgan)
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
const totalQuestions = <?= count($questions) ?>;
const duration = <?= (int)$test['duration'] ?> * 60;
let timeLeft = duration;
let currentIndex = 0;
let formSubmitted = false;

// Track status of questions: 'unopened', 'seen', 'answered'
const qStatus = Array(totalQuestions).fill('unopened');
qStatus[0] = 'seen';

function answerSelected(idx) {
    qStatus[idx] = 'answered';
    updateNavigator();
    
    // Highlight the selected option visually
    const block = document.getElementById('q-block-' + idx);
    block.querySelectorAll('.option-label').forEach(lbl => {
        lbl.classList.remove('border-blue-500', 'bg-blue-50');
        lbl.classList.add('border-gray-100');
    });
    const checked = block.querySelector('input[type=radio]:checked');
    if (checked) {
        checked.closest('.option-label').classList.remove('border-gray-100');
        checked.closest('.option-label').classList.add('border-blue-500', 'bg-blue-50');
    }
}

function updateNavigator() {
    for (let i = 0; i < totalQuestions; i++) {
        const btn = document.getElementById('nav-btn-' + i);
        // Reset classes
        btn.className = 'nav-btn aspect-square rounded-lg text-sm font-semibold flex items-center justify-center transition text-white';
        
        if (i === currentIndex) {
            btn.classList.add('bg-blue-500', 'ring-2', 'ring-blue-300', 'ring-offset-1');
        } else if (qStatus[i] === 'answered') {
            btn.classList.add('bg-emerald-500');
        } else if (qStatus[i] === 'seen') {
            btn.classList.add('bg-red-500'); // seen but not answered
        } else {
            // unopened
            btn.className = 'nav-btn aspect-square rounded-lg text-sm font-semibold flex items-center justify-center transition bg-gray-100 text-gray-600 hover:bg-gray-200';
        }
    }
}

function goTo(index) {
    if (index < 0 || index >= totalQuestions) return;
    
    // If leaving current, mark it as seen if not answered
    if (qStatus[currentIndex] !== 'answered') {
        qStatus[currentIndex] = 'seen';
    }
    
    // Hide current
    document.getElementById('q-block-' + currentIndex).classList.add('hidden');
    
    // Update index
    currentIndex = index;
    if (qStatus[currentIndex] === 'unopened') {
        qStatus[currentIndex] = 'seen';
    }
    
    // Show new
    document.getElementById('q-block-' + currentIndex).classList.remove('hidden');
    
    // Update buttons
    document.getElementById('btn-prev').disabled = (currentIndex === 0);
    
    if (currentIndex === totalQuestions - 1) {
        document.getElementById('btn-next').classList.add('hidden');
        document.getElementById('btn-finish').classList.remove('hidden');
        document.getElementById('btn-finish').classList.add('flex');
    } else {
        document.getElementById('btn-next').classList.remove('hidden');
        document.getElementById('btn-finish').classList.add('hidden');
        document.getElementById('btn-finish').classList.remove('flex');
    }
    
    updateNavigator();
}

// Timer Logic
const timerEl = document.getElementById('timer');
const timerInterval = setInterval(() => {
    timeLeft--;
    const m = Math.floor(timeLeft / 60);
    const s = timeLeft % 60;
    timerEl.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');

    if (timeLeft <= 60) timerEl.classList.add('text-red-300');
    if (timeLeft <= 0) {
        clearInterval(timerInterval);
        Swal.fire({
            title: 'Vaqt tugadi!',
            text: 'Test avtomatik ravishda yakunlanmoqda...',
            icon: 'info',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            formSubmitted = true;
            window.onbeforeunload = null;
            document.getElementById('test-form').submit();
        });
    }
}, 1000);

// Submission Logic with SweetAlert
document.getElementById('btn-finish').addEventListener('click', function() {
    const unanswered = qStatus.filter(s => s !== 'answered').length;
    if (unanswered > 0) {
        Swal.fire({
            title: 'Diqqat!',
            text: "Testni yakunlash uchun barcha savollarga javob belgilashingiz shart! Hali " + unanswered + " ta savolga javob berilmagan.",
            icon: 'warning',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Tushunarli'
        });
        return;
    }

    Swal.fire({
        title: 'Testni yakunlamoqchimisiz?',
        text: "Barcha belgilangan javoblar yuboriladi.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981', // emerald
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ha, yakunlash',
        cancelButtonText: 'Bekor qilish'
    }).then((result) => {
        if (result.isConfirmed) {
            formSubmitted = true;
            window.onbeforeunload = null;
            document.getElementById('test-form').submit();
        }
    });
});

// Intercept link clicks
document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', function(e) {
        if (!formSubmitted) {
            e.preventDefault();
            const targetUrl = this.href;
            if (!targetUrl || targetUrl.includes('javascript:') || targetUrl.includes('#')) return;

            Swal.fire({
                title: 'Ishonchingiz komilmi?',
                text: "Test hali yakunlanmadi! Agar sahifani tark etsangiz, barcha javoblaringiz o'chib ketadi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ha, chiqib ketish',
                cancelButtonText: 'Qolish'
            }).then((result) => {
                if (result.isConfirmed) {
                    formSubmitted = true;
                    window.onbeforeunload = null;
                    window.location.href = targetUrl;
                }
            });
        }
    });
});

// Native beforeunload
window.onbeforeunload = (e) => {
    if (!formSubmitted) {
        e.returnValue = "Test hali yakunlanmadi. Chiqib ketsangiz javoblar saqlanmaydi.";
        return "Test hali yakunlanmadi. Chiqib ketsangiz javoblar saqlanmaydi.";
    }
};

// Initialize First View
goTo(0);
</script>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
