<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$test = $db->get_data_by_table('tests', ['id' => $id]);
if (!$test) {
    flash_message('error', 'Test topilmadi!');
    redirect('/admin/tests/index.php');
}

$page_title = "Testni tahrirlash";
$errors = [];

$lectures_res = $db->query("SELECT id, title FROM lectures ORDER BY order_num ASC, id ASC");
$lectures = [];
if ($lectures_res) {
    while($row = mysqli_fetch_assoc($lectures_res)) $lectures[] = $row;
}

$lectures_json = json_encode($lectures);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title           = trim($_POST['title'] ?? '');
    $description     = trim($_POST['description'] ?? '');
    $duration        = (int)($_POST['duration'] ?? 30);
    $attempts_limit  = (int)($_POST['attempts_limit'] ?? 3);
    $questions_limit = (int)($_POST['questions_limit'] ?? 10);
    $module_type     = 0;
    $module_id       = (int)($_POST['module_id'] ?? 0);

    if (empty($title)) $errors[] = 'Sarlavha kiritilishi shart!';
    if ($duration < 1) $errors[] = 'Vaqt 1 daqiqadan kam bo\'lmasligi kerak!';
    if ($attempts_limit < 1) $errors[] = 'Urinishlar soni kamida 1 marta bo\'lishi kerak!';
    if ($questions_limit < 1) $errors[] = 'Savollar soni kamida 1 ta bo\'lishi kerak!';
    if (empty($module_id)) $errors[] = 'Ma\'ruza tanlanishi shart!';

    if (empty($errors)) {
        $db->update('tests', [
            'title'           => $title, 
            'description'     => $description, 
            'duration'        => $duration,
            'attempts_limit'  => $attempts_limit,
            'questions_limit' => $questions_limit,
            'module_type'     => $module_type,
            'module_id'       => $module_id
        ], "id = $id");
        flash_message('success', 'Test yangilandi!');
        redirect('/admin/tests/index.php');
    }
    $test = array_merge($test, $_POST);
}

include __DIR__ . '/../../includes/admin_header.php';
?>

<!-- Include Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 42px !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 0.75rem !important;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #374151 !important;
        font-size: 0.875rem !important;
        padding-left: 1rem !important;
    }
    .select2-search__field {
        border-radius: 0.5rem !important;
    }
    .select2-dropdown {
        border: 1px solid #e5e7eb !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }
</style>

<div class="max-w-2xl mx-auto pb-10">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= BASE_URL ?>/admin/tests/index.php"
           class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-gray-500 text-sm"></i>
        </a>
        <h2 class="text-lg font-semibold text-gray-800">Testni tahrirlash</h2>
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
            
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 mb-2">
                <input type="hidden" name="module_type" value="0">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ma'ruzani tanlang <span class="text-red-500">*</span></label>
                    <select name="module_id" id="module_id" class="w-full" required>
                        <option value="">Tanlang...</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sarlavha <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="<?= h($test['title']) ?>"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tavsif</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"><?= h($test['description'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Davomiyligi (daqiqada)</label>
                <div class="flex items-center gap-3">
                    <input type="number" name="duration" value="<?= (int)$test['duration'] ?>"
                        min="1" max="180"
                        class="w-28 px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <span class="text-sm text-gray-500">daqiqa</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Urinishlar soni
                    </label>
                    <input type="number" name="attempts_limit" value="<?= (int)($test['attempts_limit'] ?? 3) ?>"
                        min="1" max="100"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Ishlanadigan savollar soni
                    </label>
                    <input type="number" name="questions_limit" value="<?= (int)($test['questions_limit'] ?? 10) ?>"
                        min="1" max="500"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="flex-1 sm:flex-none bg-orange-500 hover:bg-orange-600 text-white font-medium px-6 py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Yangilash
                </button>
                <a href="<?= BASE_URL ?>/admin/tests/questions/index.php?test_id=<?= $id ?>"
                   class="px-4 py-2.5 bg-purple-50 hover:bg-purple-100 text-purple-600 rounded-xl transition text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-question-circle"></i> Savollar
                </a>
                <a href="<?= BASE_URL ?>/admin/tests/index.php"
                   class="px-6 py-2.5 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl transition text-sm font-medium">
                    Bekor
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const lectures = <?= $lectures_json ?>;
    const selectedModuleId = <?= (int)($test['module_id'] ?? 0) ?>;

    function updateModules() {
        const select = $('#module_id');
        
        select.empty();
        select.append(new Option('Tanlang...', '', false, false));
        
        lectures.forEach(item => {
            const isSelected = (parseInt(item.id) === selectedModuleId);
            const newOption = new Option(item.title, item.id, false, isSelected);
            select.append(newOption);
        });
        
        select.trigger('change');
    }

    $(document).ready(function() {
        $('#module_id').select2({
            placeholder: "Mavzuni qidiring yoki tanlang",
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Ma'lumot topilmadi";
                }
            }
        });
        
        updateModules();
    });
</script>

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
