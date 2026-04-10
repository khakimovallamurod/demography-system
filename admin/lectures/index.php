<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$page_title = "Ma'ruzalar";
$lectures = $db->get_data_by_table_all('lectures', 'ORDER BY created_at DESC');

include __DIR__ . '/../../includes/admin_header.php';
?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 border-b border-gray-100 gap-3">
        <h3 class="font-semibold text-gray-700 flex items-center gap-2">
            <i class="fas fa-book-open text-blue-500"></i> Barcha ma'ruzalar
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full"><?= count($lectures) ?></span>
        </h3>
        <a href="<?= BASE_URL ?>/admin/lectures/create.php"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
            <i class="fas fa-plus"></i> Yangi ma'ruza
        </a>
    </div>

    <?php if (empty($lectures)): ?>
    <div class="text-center py-16">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-book-open text-gray-400 text-2xl"></i>
        </div>
        <p class="text-gray-500 font-medium">Ma'ruzalar mavjud emas</p>
        <p class="text-gray-400 text-sm mt-1">Birinchi ma'ruzani qo'shing</p>
        <a href="<?= BASE_URL ?>/admin/lectures/create.php" class="inline-flex items-center gap-2 mt-4 bg-blue-600 text-white text-sm px-4 py-2 rounded-xl hover:bg-blue-700 transition">
            <i class="fas fa-plus"></i> Qo'shish
        </a>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="text-left px-5 py-3">#</th>
                    <th class="text-left px-5 py-3">Sarlavha</th>
                    <th class="text-left px-5 py-3 hidden md:table-cell">Tavsif</th>
                    <th class="text-left px-5 py-3 hidden sm:table-cell">Fayl</th>
                    <th class="text-left px-5 py-3 hidden lg:table-cell">Sana</th>
                    <th class="text-right px-5 py-3">Amallar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($lectures as $i => $l): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3.5 text-gray-400"><?= $i + 1 ?></td>
                    <td class="px-5 py-3.5">
                        <div class="font-medium text-gray-800"><?= h($l['title']) ?></div>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell text-gray-500">
                        <?= h(mb_substr($l['description'] ?? '', 0, 50)) ?><?= strlen($l['description'] ?? '') > 50 ? '...' : '' ?>
                    </td>
                    <td class="px-5 py-3.5 hidden sm:table-cell">
                        <?php if ($l['file_path']): ?>
                        <a href="<?= BASE_URL ?>/<?= h($l['file_path']) ?>" target="_blank"
                           class="inline-flex items-center gap-1 text-blue-600 hover:underline text-xs">
                            <i class="fas fa-paperclip"></i> <?= h(mb_substr($l['file_name'] ?? 'Fayl', 0, 20)) ?>
                        </a>
                        <?php else: ?>
                        <span class="text-gray-400 text-xs">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3.5 hidden lg:table-cell text-gray-400 text-xs">
                        <?= date('d.m.Y', strtotime($l['created_at'])) ?>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="<?= BASE_URL ?>/admin/lectures/edit.php?id=<?= $l['id'] ?>"
                               class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center transition" title="Tahrirlash">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/lectures/delete.php?id=<?= $l['id'] ?>"
                               onclick="return swalDelete(event, this, 'Ma\'ruza o\'chiriladi!')"
                               class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg flex items-center justify-center transition" title="O'chirish">
                                <i class="fas fa-trash text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
