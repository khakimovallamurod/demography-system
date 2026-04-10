<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$id = (int)($_GET['id'] ?? 0);
$map = $db->get_data_by_table('maps', ['id' => $id]);
if (!$map) {
    flash_message('error', 'Xarita topilmadi!');
    redirect('/user/maps/index.php');
}

$page_title = h($map['title']);
$pdf_url = BASE_URL . '/' . $map['file_path'];
include __DIR__ . '/../../includes/user_header.php';
?>

<div class="max-w-5xl mx-auto">
    <div class="flex items-center gap-3 mb-4">
        <a href="<?= BASE_URL ?>/user/maps/index.php"
           class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-gray-500 text-sm"></i>
        </a>
        <div class="flex-1 min-w-0">
            <h2 class="text-base font-bold text-gray-800 truncate"><?= h($map['title']) ?></h2>
            <?php if ($map['description']): ?>
            <p class="text-xs text-gray-400 mt-0.5"><?= h($map['description']) ?></p>
            <?php endif; ?>
        </div>
        <a href="<?= BASE_URL ?>/<?= h($map['file_path']) ?>" download
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-4 py-2.5 rounded-xl transition flex-shrink-0">
            <i class="fas fa-download"></i> Yuklash
        </a>
    </div>

    <!-- PDF Viewer -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-indigo-50 border-b border-indigo-100 px-4 py-2.5 flex items-center gap-2">
            <i class="fas fa-file-pdf text-red-500"></i>
            <span class="text-sm text-indigo-700 font-medium"><?= h($map['file_name']) ?></span>
        </div>

        <!-- Embed PDF -->
        <div class="relative" style="height: 80vh; min-height: 500px;">
            <iframe src="<?= $pdf_url ?>#toolbar=1&navpanes=0&scrollbar=1"
                    class="w-full h-full border-0"
                    type="application/pdf"
                    title="<?= h($map['title']) ?>">
                <!-- Fallback for browsers that can't embed PDF -->
                <div class="flex flex-col items-center justify-center h-full p-8 text-center">
                    <i class="fas fa-file-pdf text-red-400 text-6xl mb-4"></i>
                    <p class="text-gray-600 font-medium mb-2">Brauzeringiz PDF ko'rsata olmadi</p>
                    <p class="text-gray-400 text-sm mb-4">Faylni yuklab ko'ring</p>
                    <a href="<?= BASE_URL ?>/<?= h($map['file_path']) ?>" target="_blank"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition">
                        <i class="fas fa-external-link-alt mr-1"></i> Yangi oynada ochish
                    </a>
                </div>
            </iframe>
        </div>
    </div>

    <div class="mt-4 flex items-center justify-between flex-wrap gap-2">
        <a href="<?= BASE_URL ?>/user/maps/index.php" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-1 transition">
            <i class="fas fa-arrow-left text-xs"></i> Barcha xaritalar
        </a>
        <a href="<?= BASE_URL ?>/<?= h($map['file_path']) ?>" target="_blank"
           class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
            <i class="fas fa-external-link-alt text-xs"></i> Yangi oynada ochish
        </a>
    </div>
</div>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
