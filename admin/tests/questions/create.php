<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_admin();

$test_id = (int)($_GET['test_id'] ?? 0);
$test = $db->get_data_by_table('tests', ['id' => $test_id]);
if (!$test) {
    flash_message('error', 'Test topilmadi!');
    redirect('/admin/tests/index.php');
}

$page_title = 'Savol qo\'shish';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_text  = trim($_POST['question_text'] ?? '');
    $options        = $_POST['options'] ?? [];
    $correct_option = (int)($_POST['correct_option'] ?? -1);

    if (empty($question_text)) $errors[] = 'Savol matni kiritilishi shart!';
    if (count(array_filter($options)) < 2) $errors[] = 'Kamida 2 ta javob varianti kiriting!';
    if ($correct_option < 0 || $correct_option >= count($options)) $errors[] = 'To\'g\'ri javobni belgilang!';

    if (empty($errors)) {
        $q_id = $db->insert('questions', ['test_id' => $test_id, 'question_text' => $question_text]);
        if ($q_id) {
            foreach ($options as $i => $opt) {
                if (trim($opt) === '') continue;
                $db->insert('options', [
                    'question_id' => $q_id,
                    'option_text' => $opt,
                    'is_correct'  => ($i == $correct_option) ? '1' : '0'
                ]);
            }
            flash_message('success', "Savol qo'shildi!");
            if (isset($_POST['add_another'])) {
                redirect('/admin/tests/questions/create.php?test_id=' . $test_id);
            }
            redirect('/admin/tests/questions/index.php?test_id=' . $test_id);
        } else {
            $errors[] = 'Saqlashda xatolik!';
        }
    }
}

include __DIR__ . '/../../../includes/admin_header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= BASE_URL ?>/admin/tests/questions/index.php?test_id=<?= $test_id ?>"
           class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-gray-500 text-sm"></i>
        </a>
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Yangi savol qo'shish</h2>
            <p class="text-xs text-gray-400"><?= h($test['title']) ?></p>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
        <?php foreach ($errors as $e): ?>
        <p class="text-red-700 text-sm flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> <?= h($e) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Savol matni <span class="text-red-500">*</span>
                </label>
                <textarea name="question_text" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                    placeholder="Savol matnini kiriting..." required><?= h($_POST['question_text'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Javob variantlari <span class="text-red-500">*</span>
                    <span class="text-gray-400 text-xs font-normal ml-1">(to'g'ri javobni belgilang)</span>
                </label>
                <div class="space-y-3" id="options-container">
                    <?php
                    $saved_options  = $_POST['options'] ?? ['', '', '', ''];
                    $correct_option = (int)($_POST['correct_option'] ?? 0);
                    foreach ($saved_options as $i => $opt):
                    ?>
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="correct_option" value="<?= $i ?>"
                                <?= $correct_option === $i ? 'checked' : '' ?>
                                class="w-4 h-4 accent-purple-600">
                        </label>
                        <span class="w-7 h-7 bg-gray-100 rounded-lg flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">
                            <?= chr(65 + $i) ?>
                        </span>
                        <input type="text" name="options[]" value="<?= h($opt) ?>"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent"
                            placeholder="<?= chr(65 + $i) ?> varianti">
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" onclick="addOption()"
                    class="mt-3 inline-flex items-center gap-1.5 text-sm text-purple-600 hover:text-purple-700 font-medium">
                    <i class="fas fa-plus-circle"></i> Variant qo'shish
                </button>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" name="save"
                    class="flex-1 sm:flex-none bg-purple-600 hover:bg-purple-700 text-white font-medium px-5 py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Saqlash
                </button>
                <button type="submit" name="add_another"
                    class="flex-1 sm:flex-none bg-purple-50 hover:bg-purple-100 text-purple-600 font-medium px-5 py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i> Saqlash + Yana qo'shish
                </button>
                <a href="<?= BASE_URL ?>/admin/tests/questions/index.php?test_id=<?= $test_id ?>"
                   class="px-5 py-2.5 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl transition text-sm font-medium">
                    Bekor qilish
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let optCount = <?= count($saved_options ?? ['','','','']) ?>;
function addOption() {
    const container = document.getElementById('options-container');
    const label = String.fromCharCode(65 + optCount);
    const div = document.createElement('div');
    div.className = 'flex items-center gap-3';
    div.innerHTML = `
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="correct_option" value="${optCount}" class="w-4 h-4 accent-purple-600">
        </label>
        <span class="w-7 h-7 bg-gray-100 rounded-lg flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">${label}</span>
        <input type="text" name="options[]"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent"
            placeholder="${label} varianti">
    `;
    container.appendChild(div);
    optCount++;
}
</script>

<?php include __DIR__ . '/../../../includes/admin_footer.php'; ?>
