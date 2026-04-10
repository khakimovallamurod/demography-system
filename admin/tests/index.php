<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$page_title = 'Testlar';
$tests = $db->get_data_by_table_all('tests', 'ORDER BY created_at DESC');

include __DIR__ . '/../../includes/admin_header.php';
?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 border-b border-gray-100 gap-3">
        <h3 class="font-semibold text-gray-700 flex items-center gap-2">
            <i class="fas fa-clipboard-list text-orange-500"></i> Barcha testlar
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full"><?= count($tests) ?></span>
        </h3>
        <a href="<?= BASE_URL ?>/admin/tests/create.php"
           class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
            <i class="fas fa-plus"></i> Yangi test
        </a>
    </div>

    <?php if (empty($tests)): ?>
    <div class="text-center py-16">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
        </div>
        <p class="text-gray-500 font-medium">Testlar mavjud emas</p>
        <a href="<?= BASE_URL ?>/admin/tests/create.php" class="inline-flex items-center gap-2 mt-4 bg-orange-500 text-white text-sm px-4 py-2 rounded-xl hover:bg-orange-600 transition">
            <i class="fas fa-plus"></i> Qo'shish
        </a>
    </div>
    <?php else: ?>
    <div class="divide-y divide-gray-50">
        <?php foreach ($tests as $t):
            $q_count_res = $db->query("SELECT COUNT(*) as cnt FROM questions WHERE test_id = {$t['id']}");
            $q_count = mysqli_fetch_assoc($q_count_res)['cnt'];
        ?>
        <div class="p-5 hover:bg-gray-50 transition">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="w-9 h-9 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clipboard-list text-orange-500 text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800"><?= h($t['title']) ?></h4>
                            <p class="text-xs text-gray-400 mt-0.5"><?= h(mb_substr($t['description'] ?? '', 0, 80)) ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 ml-12 mt-1">
                        <span class="text-xs text-gray-500 flex items-center gap-1">
                            <i class="fas fa-question-circle text-gray-400"></i> <?= $q_count ?> savol
                        </span>
                        <span class="text-xs text-gray-500 flex items-center gap-1">
                            <i class="fas fa-clock text-gray-400"></i> <?= $t['duration'] ?> daqiqa
                        </span>
                        <span class="text-xs text-gray-400">
                            <?= date('d.m.Y', strtotime($t['created_at'])) ?>
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="<?= BASE_URL ?>/admin/tests/questions/index.php?test_id=<?= $t['id'] ?>"
                       class="inline-flex items-center gap-1.5 bg-purple-50 hover:bg-purple-100 text-purple-600 text-xs font-medium px-3 py-2 rounded-xl transition">
                        <i class="fas fa-question-circle"></i> Savollar
                    </a>
                    <a href="<?= BASE_URL ?>/admin/tests/edit.php?id=<?= $t['id'] ?>"
                       class="w-8 h-8 bg-orange-50 hover:bg-orange-100 text-orange-500 rounded-lg flex items-center justify-center transition">
                        <i class="fas fa-edit text-xs"></i>
                    </a>
                    <a href="<?= BASE_URL ?>/admin/tests/delete.php?id=<?= $t['id'] ?>"
                       onclick="return swalDelete(event, this, 'Test va barcha savollari o\'chiriladi!')"
                       class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg flex items-center justify-center transition">
                        <i class="fas fa-trash text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
