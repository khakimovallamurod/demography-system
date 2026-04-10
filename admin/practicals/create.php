<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$page_title = "Amaliy mashg'ulot qo'shish";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $content     = trim($_POST['content'] ?? '');

    if (empty($title)) $errors[] = 'Sarlavha kiritilishi shart!';

    if (empty($errors)) {
        $data = ['title' => $title, 'description' => $description, 'content' => $content];

        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $upload = upload_file($_FILES['file'], 'practicals');
            if (!$upload['success']) {
                $errors[] = $upload['message'];
            } else {
                $data['file_path'] = $upload['file_path'];
                $data['file_name'] = $upload['file_name'];
            }
        }

        if (empty($errors)) {
            $id = $db->insert('practicals', $data);
            if ($id) {
                flash_message('success', "Amaliy mashg'ulot muvaffaqiyatli qo'shildi!");
                redirect('/admin/practicals/index.php');
            } else {
                $errors[] = 'Saqlashda xatolik!';
            }
        }
    }
}

include __DIR__ . '/../../includes/admin_header.php';
?>

<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= BASE_URL ?>/admin/practicals/index.php"
           class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-gray-500 text-sm"></i>
        </a>
        <h2 class="text-lg font-semibold text-gray-800">Yangi amaliy mashg'ulot</h2>
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
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sarlavha <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="<?= h($_POST['title'] ?? '') ?>"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    placeholder="Mashg'ulot mavzusi" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Qisqacha tavsif</label>
                <textarea name="description" rows="2"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none"
                    placeholder="Qisqacha tavsif..."><?= h($_POST['description'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mazmun</label>
                <textarea name="content" rows="10"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent font-mono"
                    placeholder="Mashg'ulot matni..."><?= h($_POST['content'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Fayl yuklash <span class="text-gray-400 text-xs">(faqat PDF — max 2MB)</span>
                </label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-emerald-300 transition">
                    <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-500 mb-2">Faylni shu yerga tashlang yoki</p>
                    <label class="cursor-pointer bg-emerald-50 hover:bg-emerald-100 text-emerald-600 text-sm font-medium px-4 py-2 rounded-lg transition">
                        <span>Fayl tanlang</span>
                        <input type="file" name="file" class="hidden" accept=".pdf"
                               onchange="document.getElementById('fn').textContent=this.files[0].name">
                    </label>
                    <p id="fn" class="text-xs text-gray-400 mt-2"></p>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="flex-1 sm:flex-none bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-6 py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Saqlash
                </button>
                <a href="<?= BASE_URL ?>/admin/practicals/index.php"
                   class="px-6 py-2.5 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl transition text-sm font-medium">
                    Bekor qilish
                </a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
