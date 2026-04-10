<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$page_title = "Ma'ruzalar";
$lectures = $db->get_data_by_table_all('lectures', 'ORDER BY created_at DESC');

include __DIR__ . '/../../includes/user_header.php';
?>

<div class="mb-5">
    <h2 class="text-lg font-bold text-gray-800">Ma'ruza mavzulari</h2>
    <p class="text-sm text-gray-500 mt-0.5">Demografiya bo'yicha barcha ma'ruzalar</p>
</div>

<?php if (empty($lectures)): ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 text-center py-16">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-book-open text-gray-400 text-2xl"></i>
    </div>
    <p class="text-gray-500 font-medium">Ma'ruzalar hali qo'shilmagan</p>
    <p class="text-gray-400 text-sm mt-1">Admin ma'ruzalarni qo'shgandan keyin ko'rinadi</p>
</div>
<?php else: ?>
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($lectures as $i => $l): ?>
    <div class="card-hover bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
        <div class="bg-gradient-to-br from-blue-500 to-blue-700 h-2"></div>
        <div class="p-5 flex-1 flex flex-col">
            <div class="flex items-start gap-3 mb-3">
                <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-alt text-blue-600 text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-800 leading-tight"><?= h($l['title']) ?></h3>
                    <p class="text-xs text-gray-400 mt-0.5"><?= date('d.m.Y', strtotime($l['created_at'])) ?></p>
                </div>
            </div>

            <?php if ($l['description']): ?>
            <p class="text-sm text-gray-600 mb-4 flex-1">
                <?= h(mb_substr($l['description'], 0, 100)) ?><?= strlen($l['description']) > 100 ? '...' : '' ?>
            </p>
            <?php endif; ?>

            <div class="flex items-center gap-2 mt-auto">
                <a href="<?= BASE_URL ?>/user/lectures/view.php?id=<?= $l['id'] ?>"
                   class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-600 text-sm font-medium py-2 rounded-xl text-center transition">
                    O'qish <i class="fas fa-arrow-right text-xs ml-1"></i>
                </a>
                <?php if ($l['file_path']): ?>
                <a href="<?= BASE_URL ?>/<?= h($l['file_path']) ?>" target="_blank" download
                   class="w-9 h-9 bg-gray-50 hover:bg-gray-100 text-gray-500 rounded-xl flex items-center justify-center transition" title="Yuklash">
                    <i class="fas fa-download text-sm"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
