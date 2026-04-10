<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$page_title = "Xarita qo'shish";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($title)) $errors[] = 'Sarlavha kiritilishi shart!';
    $file_ok = isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK;
    if (!$file_ok) $errors[] = 'PDF fayl tanlanishi shart!';

    if (empty($errors)) {
        $upload = upload_file($_FILES['file'], 'maps');
        if (!$upload['success']) {
            $errors[] = $upload['message'];
        } else {
            $id = $db->insert('maps', [
                'title'       => $title,
                'description' => $description,
                'file_path'   => $upload['file_path'],
                'file_name'   => $upload['file_name']
            ]);
            if ($id) {
                flash_message('success', "Xarita muvaffaqiyatli qo'shildi!");
                redirect('/admin/maps/index.php');
            } else {
                $errors[] = 'Saqlashda xatolik!';
            }
        }
    }
}

include __DIR__ . '/../../includes/admin_header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= BASE_URL ?>/admin/maps/index.php"
           class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-gray-500 text-sm"></i>
        </a>
        <h2 class="text-lg font-semibold text-gray-800">Yangi xarita qo'shish</h2>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4 space-y-1">
        <?php foreach ($errors as $e): ?>
        <p class="text-red-700 text-sm flex items-center gap-2"><i class="fas fa-exclamation-circle flex-shrink-0"></i> <?= h($e) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" enctype="multipart/form-data" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sarlavha <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="<?= h($_POST['title'] ?? '') ?>"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Xarita nomi" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tavsif</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                    placeholder="Xarita haqida qisqacha..."><?= h($_POST['description'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    PDF xarita <span class="text-red-500">*</span>
                    <span class="text-gray-400 text-xs font-normal ml-1">(faqat PDF, max 2MB)</span>
                </label>
                <div class="border-2 border-dashed border-indigo-200 rounded-xl p-8 text-center hover:border-indigo-400 transition cursor-pointer"
                     onclick="document.getElementById('pdfFile').click()">
                    <div id="upload-placeholder">
                        <i class="fas fa-file-pdf text-indigo-400 text-4xl mb-3"></i>
                        <p class="text-sm text-gray-600 font-medium">PDF faylni bosing yoki shu yerga tashlang</p>
                        <p class="text-xs text-gray-400 mt-1">Faqat PDF · Maksimum 2MB</p>
                    </div>
                    <div id="upload-preview" class="hidden">
                        <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                        <p class="text-sm font-medium text-green-600" id="file-name-display"></p>
                        <p class="text-xs text-gray-400 mt-1" id="file-size-display"></p>
                    </div>
                    <input type="file" name="file" id="pdfFile" class="hidden" accept=".pdf"
                           onchange="showPreview(this)" required>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="flex-1 sm:flex-none bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Saqlash
                </button>
                <a href="<?= BASE_URL ?>/admin/maps/index.php"
                   class="px-6 py-2.5 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl transition text-sm font-medium">
                    Bekor qilish
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function showPreview(input) {
    if (!input.files[0]) return;
    const file = input.files[0];
    if (file.type !== 'application/pdf') {
        Swal.fire({ icon: 'error', title: 'Xatolik!', text: 'Faqat PDF fayl yuklash mumkin!', confirmButtonColor: '#4f46e5' });
        input.value = '';
        return;
    }
    if (file.size > 2 * 1024 * 1024) {
        Swal.fire({ icon: 'error', title: 'Fayl katta!', text: 'Fayl hajmi 2MB dan oshmasligi kerak!', confirmButtonColor: '#4f46e5' });
        input.value = '';
        return;
    }
    document.getElementById('upload-placeholder').classList.add('hidden');
    document.getElementById('upload-preview').classList.remove('hidden');
    document.getElementById('file-name-display').textContent = file.name;
    document.getElementById('file-size-display').textContent = (file.size / 1024).toFixed(1) + ' KB';
}
</script>

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
