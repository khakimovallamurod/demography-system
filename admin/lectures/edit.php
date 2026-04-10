<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$lecture = $db->get_data_by_table('lectures', ['id' => $id]);
if (!$lecture) {
    flash_message('error', 'Ma\'ruza topilmadi!');
    redirect('/admin/lectures/index.php');
}

$page_title = "Ma'ruzani tahrirlash";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $content     = trim($_POST['content'] ?? '');

    if (empty($title)) $errors[] = 'Sarlavha kiritilishi shart!';

    if (empty($errors)) {
        $data = [
            'title'       => $title,
            'description' => $description,
            'content'     => $content,
        ];

        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $upload = upload_file($_FILES['file'], 'lectures');
            if (!$upload['success']) {
                $errors[] = $upload['message'];
            } else {
                // Remove old file
                if ($lecture['file_path'] && file_exists(__DIR__ . '/../../' . $lecture['file_path'])) {
                    unlink(__DIR__ . '/../../' . $lecture['file_path']);
                }
                $data['file_path'] = $upload['file_path'];
                $data['file_name'] = $upload['file_name'];
            }
        }

        if (empty($errors)) {
            $db->update('lectures', $data, "id = $id");
            flash_message('success', "Ma'ruza muvaffaqiyatli yangilandi!");
            redirect('/admin/lectures/index.php');
        }
    }
    $lecture = array_merge($lecture, $_POST);
}

include __DIR__ . '/../../includes/admin_header.php';
?>

<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= BASE_URL ?>/admin/lectures/index.php"
           class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-gray-500 text-sm"></i>
        </a>
        <h2 class="text-lg font-semibold text-gray-800">Ma'ruzani tahrirlash</h2>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
        <?php foreach ($errors as $e): ?>
        <p class="text-red-700 text-sm flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> <?= h($e) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" enctype="multipart/form-data" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Sarlavha <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title"
                    value="<?= h($lecture['title']) ?>"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Qisqacha tavsif</label>
                <textarea name="description" rows="2"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"><?= h($lecture['description'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mazmun</label>
                <textarea name="content" rows="10"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono"><?= h($lecture['content'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Yangi fayl yuklash <span class="text-gray-400 text-xs">(bo'sh qoldirsangiz — mavjud fayl saqlanadi)</span>
                </label>
                <?php if ($lecture['file_path']): ?>
                <div class="mb-2 flex items-center gap-2 text-sm text-blue-600 bg-blue-50 px-3 py-2 rounded-lg">
                    <i class="fas fa-paperclip"></i>
                    <a href="<?= BASE_URL ?>/<?= h($lecture['file_path']) ?>" target="_blank" class="hover:underline">
                        <?= h($lecture['file_name'] ?? 'Mavjud fayl') ?>
                    </a>
                </div>
                <?php endif; ?>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center hover:border-blue-300 transition">
                    <label class="cursor-pointer bg-blue-50 hover:bg-blue-100 text-blue-600 text-sm font-medium px-4 py-2 rounded-lg transition">
                        <span>Yangi fayl tanlang (PDF)</span>
                        <input type="file" name="file" class="hidden" accept=".pdf"
                               onchange="showFileName(this)">
                    </label>
                    <p id="file-name" class="text-xs text-gray-400 mt-2"></p>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Yangilash
                </button>
                <a href="<?= BASE_URL ?>/admin/lectures/index.php"
                   class="px-6 py-2.5 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl transition text-sm font-medium">
                    Bekor qilish
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function showFileName(input) {
    document.getElementById('file-name').textContent = input.files[0] ? input.files[0].name : '';
}
</script>

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
