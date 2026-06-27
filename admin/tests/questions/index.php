<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_admin();

$test_id = (int)($_GET['test_id'] ?? 0);
$test = $db->get_data_by_table('tests', ['id' => $test_id]);
if (!$test) {
    flash_message('error', 'Test topilmadi!');
    redirect('/admin/tests/index.php');
}

$page_title = h($test['title']) . ' — Savollar';
$questions  = $db->get_data_by_table_all('questions', "WHERE test_id = $test_id ORDER BY id ASC");

include __DIR__ . '/../../../includes/admin_header.php';
?>

<div class="flex items-center gap-3 mb-6 flex-wrap">
    <a href="<?= BASE_URL ?>/admin/tests/index.php"
       class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-50 transition">
        <i class="fas fa-arrow-left text-gray-500 text-sm"></i>
    </a>
    <div class="flex-1 min-w-0">
        <h2 class="text-lg font-semibold text-gray-800 truncate"><?= h($test['title']) ?></h2>
        <p class="text-xs text-gray-400"><?= count($questions) ?> savol · <?= $test['duration'] ?> daqiqa</p>
    </div>
    <div class="flex gap-2">
        <button type="button" onclick="document.getElementById('excelModal').classList.remove('hidden')"
           class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
            <i class="fas fa-file-excel"></i> Excel import
        </button>
        <a href="<?= BASE_URL ?>/admin/tests/questions/create.php?test_id=<?= $test_id ?>"
           class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
            <i class="fas fa-plus"></i> Savol qo'shish
        </a>
    </div>
</div>

<?php if (empty($questions)): ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 text-center py-16">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-question-circle text-gray-400 text-2xl"></i>
    </div>
    <p class="text-gray-500 font-medium">Savollar mavjud emas</p>
    <a href="<?= BASE_URL ?>/admin/tests/questions/create.php?test_id=<?= $test_id ?>"
       class="inline-flex items-center gap-2 mt-4 bg-purple-600 text-white text-sm px-4 py-2 rounded-xl hover:bg-purple-700 transition">
        <i class="fas fa-plus"></i> Birinchi savolni qo'shing
    </a>
</div>
<?php else: ?>
<div class="space-y-4">
    <?php foreach ($questions as $qi => $q):
        $options = $db->get_data_by_table_all('options', "WHERE question_id = {$q['id']} ORDER BY id ASC");
    ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-start justify-between p-5">
            <div class="flex items-start gap-3 flex-1 min-w-0">
                <div class="w-7 h-7 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 text-purple-700 text-xs font-bold mt-0.5">
                    <?= $qi + 1 ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-800"><?= h($q['question_text']) ?></p>

                    <?php if (!empty($options)): ?>
                    <div class="mt-3 grid sm:grid-cols-2 gap-2">
                        <?php foreach ($options as $oi => $o): ?>
                        <div class="flex items-center gap-2 text-sm <?= $o['is_correct'] ? 'text-green-700 bg-green-50' : 'text-gray-500 bg-gray-50' ?> px-3 py-1.5 rounded-lg">
                            <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 text-xs font-bold
                                <?= $o['is_correct'] ? 'border-green-500 bg-green-500 text-white' : 'border-gray-300' ?>">
                                <?= chr(65 + $oi) ?>
                            </span>
                            <span class="truncate"><?= h($o['option_text']) ?></span>
                            <?php if ($o['is_correct']): ?>
                            <i class="fas fa-check text-green-500 text-xs ml-auto flex-shrink-0"></i>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="mt-2 text-xs text-orange-500 flex items-center gap-1">
                        <i class="fas fa-exclamation-triangle"></i> Javob variantlari qo'shilmagan
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex items-center gap-2 ml-3 flex-shrink-0">
                <a href="<?= BASE_URL ?>/admin/tests/questions/edit.php?id=<?= $q['id'] ?>&test_id=<?= $test_id ?>"
                   class="w-8 h-8 bg-purple-50 hover:bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center transition">
                    <i class="fas fa-edit text-xs"></i>
                </a>
                <a href="<?= BASE_URL ?>/admin/tests/questions/delete.php?id=<?= $q['id'] ?>&test_id=<?= $test_id ?>"
                   onclick="return swalDelete(event, this, 'Savol o\'chiriladi!')"
                   class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg flex items-center justify-center transition">
                    <i class="fas fa-trash text-xs"></i>
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Excel Import Modal -->
<div id="excelModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="document.getElementById('excelModal').classList.add('hidden')"></div>

        <div class="relative inline-block w-full max-w-lg p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-bold text-gray-900 leading-6">Excel fayldan savollarni yuklash</h3>
                <button type="button" onclick="document.getElementById('excelModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mb-5 bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-700">
                <p class="font-medium mb-2"><i class="fas fa-info-circle mr-1"></i> Muhim ko'rsatmalar:</p>
                <ul class="list-disc list-inside space-y-1 text-blue-600">
                    <li>Faqat <b>.xlsx</b> formatidagi fayllar qabul qilinadi.</li>
                    <li>Birinchi qator (sarlavhalar) o'qilmaydi.</li>
                    <li>Dastlab maxsus namunani yuklab oling va shunga mos to'ldiring.</li>
                </ul>
            </div>

            <div class="mb-5 text-center">
                <a href="<?= BASE_URL ?>/admin/tests/questions/download_template.php?test_id=<?= $test_id ?>" class="inline-flex items-center gap-2 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 font-semibold px-4 py-2 rounded-xl transition text-sm border border-emerald-200">
                    <i class="fas fa-download"></i> Namunani yuklab olish
                </a>
            </div>

            <form action="<?= BASE_URL ?>/admin/tests/questions/import_excel.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="test_id" value="<?= $test_id ?>">
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Excel faylni tanlang <span class="text-red-500">*</span></label>
                    <input type="file" name="excel_file" accept=".xlsx" required
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-gray-200 rounded-xl cursor-pointer">
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-cloud-upload-alt mr-1"></i> Faylni yuklash
                    </button>
                    <button type="button" onclick="document.getElementById('excelModal').classList.add('hidden')" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition text-sm font-medium">
                        Bekor qilish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../../includes/admin_footer.php'; ?>
