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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $duration    = (int)($_POST['duration'] ?? 30);

    if (empty($title)) $errors[] = 'Sarlavha kiritilishi shart!';

    if (empty($errors)) {
        $db->update('tests', ['title' => $title, 'description' => $description, 'duration' => $duration], "id = $id");
        flash_message('success', 'Test yangilandi!');
        redirect('/admin/tests/index.php');
    }
    $test = array_merge($test, $_POST);
}

include __DIR__ . '/../../includes/admin_header.php';
?>

<div class="max-w-2xl mx-auto">
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

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
