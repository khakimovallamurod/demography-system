<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$map = $db->get_data_by_table('maps', ['id' => $id]);
if (!$map) {
    flash_message('error', 'Xarita topilmadi!');
    redirect('/admin/maps/index.php');
}

$page_title = 'Xaritani tahrirlash';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($title)) $errors[] = 'Sarlavha kiritilishi shart!';

    if (empty($errors)) {
        $data = ['title' => $title, 'description' => $description];

        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $upload = upload_file($_FILES['file'], 'maps');
            if (!$upload['success']) {
                $errors[] = $upload['message'];
            } else {
                if (file_exists(__DIR__ . '/../../' . $map['file_path'])) {
                    unlink(__DIR__ . '/../../' . $map['file_path']);
                }
                $data['file_path'] = $upload['file_path'];
                $data['file_name'] = $upload['file_name'];
            }
        }

        if (empty($errors)) {
            $db->update('maps', $data, "id = $id");
            flash_message('success', 'Xarita yangilandi!');
            redirect('/admin/maps/index.php');
        }
    }
    $map = array_merge($map, $_POST);
}

include __DIR__ . '/../../includes/admin_header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= BASE_URL ?>/admin/maps/index.php"
           class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-gray-500 text-sm"></i>
        </a>
        <h2 class="text-lg font-semibold text-gray-800">Xaritani tahrirlash</h2>
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
                <input type="text" name="title" value="<?= h($map['title']) ?>"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tavsif</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"><?= h($map['description'] ?? '') ?></textarea>
            </div>

            <!-- Current file -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mavjud fayl</label>
                <div class="flex items-center gap-3 bg-indigo-50 rounded-xl px-4 py-3 mb-3">
                    <i class="fas fa-file-pdf text-indigo-500 text-xl"></i>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-indigo-700 truncate"><?= h($map['file_name']) ?></p>
                    </div>
                    <a href="<?= BASE_URL ?>/<?= h($map['file_path']) ?>" target="_blank"
                       class="text-xs text-indigo-600 bg-white px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition font-medium flex-shrink-0">
                        <i class="fas fa-eye mr-1"></i> Ko'rish
                    </a>
                </div>

                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Yangi PDF yuklash <span class="text-gray-400 text-xs font-normal">(ixtiyoriy, faqat PDF, max 2MB)</span>
                </label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center hover:border-indigo-300 transition cursor-pointer"
                     onclick="document.getElementById('newFile').click()">
                    <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-500">PDF fayl tanlang</p>
                    <p id="new-file-name" class="text-xs text-indigo-600 mt-1 font-medium"></p>
                    <input type="file" name="file" id="newFile" class="hidden" accept=".pdf"
                           onchange="checkFile(this)">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="flex-1 sm:flex-none bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Yangilash
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
function checkFile(input) {
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
    document.getElementById('new-file-name').textContent = file.name + ' (' + (file.size/1024).toFixed(1) + ' KB)';
}
</script>

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
