<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$page_title = 'Xaritalar';
$maps = $db->get_data_by_table_all('maps', 'ORDER BY created_at DESC');

include __DIR__ . '/../../includes/user_header.php';
?>

<div class="mb-5">
    <h2 class="text-lg font-bold text-gray-800">Demografik xaritalar</h2>
    <p class="text-sm text-gray-500 mt-0.5">PDF ko'rinishidagi xaritalarni oching va yuklab oling</p>
</div>

<?php if (empty($maps)): ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 text-center py-16">
    <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-map text-indigo-400 text-2xl"></i>
    </div>
    <p class="text-gray-500 font-medium">Xaritalar hali qo'shilmagan</p>
    <p class="text-gray-400 text-sm mt-1">Admin xaritalarni qo'shgandan so'ng ko'rinadi</p>
</div>
<?php else: ?>
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($maps as $m): ?>
    <div class="card-hover bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
        <!-- PDF preview area -->
        <div class="bg-gradient-to-br from-indigo-600 to-purple-700 p-6 flex items-center justify-center">
            <div class="text-center text-white">
                <div class="w-16 h-20 bg-white/20 rounded-lg flex flex-col items-center justify-center mx-auto mb-2 backdrop-blur">
                    <i class="fas fa-file-pdf text-red-400 text-3xl"></i>
                    <span class="text-xs mt-1 text-white/80 font-medium">PDF</span>
                </div>
                <p class="text-xs text-indigo-200 mt-1"><?= h($m['file_name']) ?></p>
            </div>
        </div>

        <div class="p-4 flex-1 flex flex-col">
            <h3 class="font-semibold text-gray-800 mb-1"><?= h($m['title']) ?></h3>
            <?php if ($m['description']): ?>
            <p class="text-xs text-gray-500 mb-3 flex-1 line-clamp-2"><?= h($m['description']) ?></p>
            <?php else: ?>
            <div class="flex-1"></div>
            <?php endif; ?>

            <p class="text-xs text-gray-400 mb-3 flex items-center gap-1">
                <i class="fas fa-calendar-alt"></i>
                <?= date('d F Y', strtotime($m['created_at'])) ?>
            </p>

            <div class="flex gap-2">
                <!-- View PDF in browser -->
                <a href="<?= BASE_URL ?>/user/maps/view.php?id=<?= $m['id'] ?>"
                   class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium py-2.5 rounded-xl text-center transition flex items-center justify-center gap-1.5">
                    <i class="fas fa-eye"></i> Ko'rish
                </a>
                <!-- Download -->
                <a href="<?= BASE_URL ?>/<?= h($m['file_path']) ?>" target="_blank" download
                   class="w-10 h-10 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl flex items-center justify-center transition" title="Yuklash">
                    <i class="fas fa-download text-sm"></i>
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
