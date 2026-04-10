<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$id = (int)($_GET['id'] ?? 0);
$lecture = $db->get_data_by_table('lectures', ['id' => $id]);
if (!$lecture) {
    flash_message('error', 'Ma\'ruza topilmadi!');
    redirect('/user/lectures/index.php');
}

$page_title = h($lecture['title']);
include __DIR__ . '/../../includes/user_header.php';
?>

<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= BASE_URL ?>/user/lectures/index.php"
           class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-gray-500 text-sm"></i>
        </a>
        <nav class="text-sm text-gray-500 flex items-center gap-1">
            <a href="<?= BASE_URL ?>/user/lectures/index.php" class="hover:text-blue-600">Ma'ruzalar</a>
            <i class="fas fa-chevron-right text-xs text-gray-400 mx-1"></i>
            <span class="text-gray-700 font-medium truncate max-w-48"><?= h($lecture['title']) ?></span>
        </nav>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book-open text-xl"></i>
                </div>
                <div class="flex-1">
                    <h1 class="text-xl font-bold mb-1"><?= h($lecture['title']) ?></h1>
                    <?php if ($lecture['description']): ?>
                    <p class="text-blue-100 text-sm"><?= h($lecture['description']) ?></p>
                    <?php endif; ?>
                    <p class="text-blue-200 text-xs mt-2">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        <?= date('d F Y', strtotime($lecture['created_at'])) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <?php if ($lecture['content']): ?>
            <div class="prose max-w-none text-gray-700 leading-relaxed whitespace-pre-wrap text-sm">
                <?= h($lecture['content']) ?>
            </div>
            <?php else: ?>
            <p class="text-gray-400 text-center py-8">Kontent qo'shilmagan</p>
            <?php endif; ?>

            <?php if ($lecture['file_path']): ?>
            <div class="mt-6 pt-6 border-t border-gray-100">
                <a href="<?= BASE_URL ?>/<?= h($lecture['file_path']) ?>" target="_blank" download
                   class="inline-flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-600 font-medium px-5 py-3 rounded-xl transition">
                    <i class="fas fa-file-download text-lg"></i>
                    <div>
                        <p class="text-sm">Faylni yuklash</p>
                        <p class="text-xs text-blue-400"><?= h($lecture['file_name'] ?? '') ?></p>
                    </div>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-4 flex justify-between">
        <a href="<?= BASE_URL ?>/user/lectures/index.php"
           class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 transition">
            <i class="fas fa-arrow-left"></i> Barcha ma'ruzalar
        </a>
    </div>
</div>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
