<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_admin();

$test_id = (int)($_GET['test_id'] ?? 0);
$test = $db->get_data_by_table('tests', ['id' => $test_id]);
if (!$test) {
    flash_message('error', 'Test topilmadi!');
    redirect('/admin/tests/index.php');
}

$page_title = 'Savollar qo\'shish';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questions = $_POST['questions'] ?? [];
    
    if (empty($questions) || !is_array($questions)) {
        $errors[] = 'Kamida bitta savol kiritishingiz kerak!';
    } else {
        $valid_questions = [];
        foreach ($questions as $index => $q) {
            $question_text = trim($q['question_text'] ?? '');
            $options = $q['options'] ?? [];
            $correct_option = (int)($q['correct_option'] ?? -1);

            if (empty($question_text)) {
                $errors[] = ($index + 1) . "-savol matni kiritilishi shart!";
                continue;
            }
            
            $filtered_options = [];
            $new_correct_index = -1;
            $opt_idx = 0;
            foreach ($options as $oi => $opt) {
                if (trim($opt) !== '') {
                    $filtered_options[] = trim($opt);
                    if ((int)$oi === $correct_option) {
                        $new_correct_index = $opt_idx;
                    }
                    $opt_idx++;
                }
            }

            if (count($filtered_options) < 2) {
                $errors[] = ($index + 1) . "-savol uchun kamida 2 ta javob varianti kiriting!";
                continue;
            }
            if ($new_correct_index < 0 || $new_correct_index >= count($filtered_options)) {
                $errors[] = ($index + 1) . "-savolda to'g'ri javobni belgilang!";
                continue;
            }
            
            $valid_questions[] = [
                'text' => $question_text,
                'options' => $filtered_options,
                'correct' => $new_correct_index
            ];
        }

        if (empty($errors)) {
            $success_count = 0;
            foreach ($valid_questions as $vq) {
                $q_id = $db->insert('questions', ['test_id' => $test_id, 'question_text' => $vq['text']]);
                if ($q_id) {
                    foreach ($vq['options'] as $i => $opt) {
                        $db->insert('options', [
                            'question_id' => $q_id,
                            'option_text' => $opt,
                            'is_correct'  => ($i == $vq['correct']) ? '1' : '0'
                        ]);
                    }
                    $success_count++;
                }
            }
            
            if ($success_count > 0) {
                flash_message('success', "$success_count ta savol muvaffaqiyatli qo'shildi!");
                redirect('/admin/tests/questions/index.php?test_id=' . $test_id);
            } else {
                $errors[] = 'Saqlashda xatolik yuz berdi!';
            }
        }
    }
}

$saved_questions = $_POST['questions'] ?? [
    ['question_text' => '', 'options' => ['', '', '', ''], 'correct_option' => 0]
];

include __DIR__ . '/../../../includes/admin_header.php';
?>

<div class="max-w-4xl mx-auto pb-10">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= BASE_URL ?>/admin/tests/questions/index.php?test_id=<?= $test_id ?>"
           class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-gray-500 text-sm"></i>
        </a>
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Yangi savollar qo'shish</h2>
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

    <form method="POST" id="questions-form">
        <div id="questions-container" class="space-y-6">
            <?php foreach ($saved_questions as $qIndex => $sq): ?>
            <div class="question-block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative" data-index="<?= $qIndex ?>">
                <?php if ($qIndex > 0): ?>
                <button type="button" onclick="removeQuestionBlock(this)" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition" title="Savolni o'chirish">
                    <i class="fas fa-times text-lg"></i>
                </button>
                <?php endif; ?>
                
                <h3 class="font-bold text-gray-700 mb-4 pb-2 border-b border-gray-50 question-number"><?= $qIndex + 1 ?>-savol</h3>
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Savol matni <span class="text-red-500">*</span>
                        </label>
                        <textarea name="questions[<?= $qIndex ?>][question_text]" rows="2"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                            placeholder="Savol matnini kiriting..." required><?= h($sq['question_text'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Javob variantlari <span class="text-red-500">*</span>
                            <span class="text-gray-400 text-xs font-normal ml-1">(to'g'ri javobni belgilang)</span>
                        </label>
                        <div class="options-container space-y-3">
                            <?php 
                            $correct_opt = (int)($sq['correct_option'] ?? 0);
                            $opts = $sq['options'] ?? ['', '', '', ''];
                            foreach ($opts as $oIndex => $opt): 
                            ?>
                            <div class="option-item flex items-center gap-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="questions[<?= $qIndex ?>][correct_option]" value="<?= $oIndex ?>"
                                        <?= $correct_opt === $oIndex ? 'checked' : '' ?>
                                        class="w-4 h-4 accent-purple-600">
                                </label>
                                <span class="option-label w-7 h-7 bg-gray-100 rounded-lg flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">
                                    <?= chr(65 + $oIndex) ?>
                                </span>
                                <input type="text" name="questions[<?= $qIndex ?>][options][]" value="<?= h($opt) ?>"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent"
                                    placeholder="<?= chr(65 + $oIndex) ?> varianti">
                                <button type="button" onclick="removeOption(this)" class="text-gray-400 hover:text-red-500 transition" title="Variantni o'chirish">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" onclick="addOption(this)"
                            class="mt-3 inline-flex items-center gap-1.5 text-sm text-purple-600 hover:text-purple-700 font-medium">
                            <i class="fas fa-plus-circle"></i> Variant qo'shish
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row items-center gap-4">
            <button type="button" onclick="addQuestionBlock()"
                class="w-full sm:w-auto px-6 py-3 border-2 border-dashed border-purple-300 text-purple-600 hover:bg-purple-50 hover:border-purple-400 rounded-2xl font-bold transition flex items-center justify-center gap-2">
                <i class="fas fa-plus"></i> Yangi savol qutisi qo'shish
            </button>
            <div class="flex-1"></div>
            <a href="<?= BASE_URL ?>/admin/tests/questions/index.php?test_id=<?= $test_id ?>"
               class="px-6 py-3 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl transition text-sm font-medium w-full sm:w-auto text-center">
                Bekor qilish
            </a>
            <button type="submit" name="save"
                class="bg-purple-600 hover:bg-purple-700 text-white font-medium px-8 py-3 rounded-xl transition flex items-center justify-center gap-2 w-full sm:w-auto shadow-sm">
                <i class="fas fa-save"></i> Barchasini saqlash
            </button>
        </div>
    </form>
</div>

<script>
function updateLabelsAndValues(block) {
    const qIndex = block.dataset.index;
    
    // Update options indexing within this block
    const optionsContainer = block.querySelector('.options-container');
    const optionItems = optionsContainer.querySelectorAll('.option-item');
    
    optionItems.forEach((item, index) => {
        const label = String.fromCharCode(65 + index);
        item.querySelector('.option-label').innerText = label;
        item.querySelector('input[type="text"]').placeholder = label + " varianti";
        item.querySelector('input[type="radio"]').value = index;
    });
}

function updateQuestionNumbers() {
    const blocks = document.querySelectorAll('.question-block');
    blocks.forEach((block, index) => {
        block.dataset.index = index;
        block.querySelector('.question-number').innerText = (index + 1) + "-savol";
        
        // Update name attributes for inputs
        block.querySelector('textarea').name = `questions[${index}][question_text]`;
        
        const optionItems = block.querySelectorAll('.option-item');
        optionItems.forEach(item => {
            item.querySelector('input[type="radio"]').name = `questions[${index}][correct_option]`;
            item.querySelector('input[type="text"]').name = `questions[${index}][options][]`;
        });
        
        updateLabelsAndValues(block);
    });
}

function addOption(btn) {
    const block = btn.closest('.question-block');
    const optionsContainer = block.querySelector('.options-container');
    const qIndex = block.dataset.index;
    const optionCount = optionsContainer.querySelectorAll('.option-item').length;
    const label = String.fromCharCode(65 + optionCount);
    
    const div = document.createElement('div');
    div.className = 'option-item flex items-center gap-3';
    div.innerHTML = `
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="questions[${qIndex}][correct_option]" value="${optionCount}" class="w-4 h-4 accent-purple-600">
        </label>
        <span class="option-label w-7 h-7 bg-gray-100 rounded-lg flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">
            ${label}
        </span>
        <input type="text" name="questions[${qIndex}][options][]" 
            class="flex-1 px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent"
            placeholder="${label} varianti">
        <button type="button" onclick="removeOption(this)" class="text-gray-400 hover:text-red-500 transition" title="Variantni o'chirish">
            <i class="fas fa-times"></i>
        </button>
    `;
    optionsContainer.appendChild(div);
}

function removeOption(btn) {
    const block = btn.closest('.question-block');
    const optionsContainer = block.querySelector('.options-container');
    
    if (optionsContainer.querySelectorAll('.option-item').length <= 2) {
        alert("Kamida 2 ta variant qolishi kerak!");
        return;
    }
    
    btn.closest('.option-item').remove();
    updateLabelsAndValues(block);
}

function addQuestionBlock() {
    const container = document.getElementById('questions-container');
    const blocks = container.querySelectorAll('.question-block');
    const newIndex = blocks.length;
    
    const div = document.createElement('div');
    div.className = 'question-block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative';
    div.dataset.index = newIndex;
    
    // Default 4 options html
    let optionsHtml = '';
    for(let i=0; i<4; i++) {
        let label = String.fromCharCode(65 + i);
        optionsHtml += `
        <div class="option-item flex items-center gap-3">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="questions[${newIndex}][correct_option]" value="${i}" ${i===0 ? 'checked' : ''} class="w-4 h-4 accent-purple-600">
            </label>
            <span class="option-label w-7 h-7 bg-gray-100 rounded-lg flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">
                ${label}
            </span>
            <input type="text" name="questions[${newIndex}][options][]" 
                class="flex-1 px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent"
                placeholder="${label} varianti">
            <button type="button" onclick="removeOption(this)" class="text-gray-400 hover:text-red-500 transition" title="Variantni o'chirish">
                <i class="fas fa-times"></i>
            </button>
        </div>`;
    }

    div.innerHTML = `
        <button type="button" onclick="removeQuestionBlock(this)" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition" title="Savolni o'chirish">
            <i class="fas fa-times text-lg"></i>
        </button>
        <h3 class="font-bold text-gray-700 mb-4 pb-2 border-b border-gray-50 question-number">${newIndex + 1}-savol</h3>
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Savol matni <span class="text-red-500">*</span>
                </label>
                <textarea name="questions[${newIndex}][question_text]" rows="2"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                    placeholder="Savol matnini kiriting..." required></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Javob variantlari <span class="text-red-500">*</span>
                    <span class="text-gray-400 text-xs font-normal ml-1">(to'g'ri javobni belgilang)</span>
                </label>
                <div class="options-container space-y-3">
                    ${optionsHtml}
                </div>
                <button type="button" onclick="addOption(this)"
                    class="mt-3 inline-flex items-center gap-1.5 text-sm text-purple-600 hover:text-purple-700 font-medium">
                    <i class="fas fa-plus-circle"></i> Variant qo'shish
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(div);
    updateQuestionNumbers();
}

function removeQuestionBlock(btn) {
    const block = btn.closest('.question-block');
    const container = document.getElementById('questions-container');
    
    if (container.querySelectorAll('.question-block').length <= 1) {
        alert("Kamida 1 ta savol qolishi kerak!");
        return;
    }
    
    block.remove();
    updateQuestionNumbers();
}
</script>

<?php include __DIR__ . '/../../../includes/admin_footer.php'; ?>
