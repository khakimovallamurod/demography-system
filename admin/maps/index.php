<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$page_title = 'Xaritalar';
$maps = $db->get_data_by_table_all('maps', 'ORDER BY created_at DESC');

include __DIR__ . '/../../includes/admin_header.php';
?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 border-b border-gray-100 gap-3">
        <h3 class="font-semibold text-gray-700 flex items-center gap-2">
            <i class="fas fa-map-marked-alt text-indigo-500"></i> Barcha xaritalar
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full"><?= count($maps) ?></span>
        </h3>
        <a href="<?= BASE_URL ?>/admin/maps/create.php"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
            <i class="fas fa-plus"></i> Xarita qo'shish
        </a>
    </div>

    <?php if (empty($maps)): ?>
    <div class="text-center py-16">
        <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-map text-indigo-400 text-2xl"></i>
        </div>
        <p class="text-gray-500 font-medium mb-1">Xaritalar mavjud emas</p>
        <p class="text-gray-400 text-sm">PDF formatdagi demografik xaritalarni yuklang</p>
        <a href="<?= BASE_URL ?>/admin/maps/create.php" class="inline-flex items-center gap-2 mt-4 bg-indigo-600 text-white text-sm px-4 py-2 rounded-xl hover:bg-indigo-700 transition">
            <i class="fas fa-plus"></i> Xarita qo'shish
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
                    <th class="text-left px-5 py-3">PDF fayl</th>
                    <th class="text-left px-5 py-3 hidden lg:table-cell">Sana</th>
                    <th class="text-right px-5 py-3">Amallar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($maps as $i => $m): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3.5 text-gray-400 text-sm"><?= $i + 1 ?></td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marked-alt text-indigo-600 text-xs"></i>
                            </div>
                            <span class="font-medium text-gray-800"><?= h($m['title']) ?></span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell text-gray-500 text-sm">
                        <?= h(mb_substr($m['description'] ?? '', 0, 55)) ?><?= mb_strlen($m['description'] ?? '') > 55 ? '...' : '' ?>
                    </td>
                    <td class="px-5 py-3.5">
                        <a href="<?= BASE_URL ?>/<?= h($m['file_path']) ?>" target="_blank"
                           class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-800 text-xs font-medium bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1.5 rounded-lg transition">
                            <i class="fas fa-file-pdf"></i> <?= h(mb_substr($m['file_name'], 0, 22)) ?>
                        </a>
                    </td>
                    <td class="px-5 py-3.5 hidden lg:table-cell text-gray-400 text-xs">
                        <?= date('d.m.Y', strtotime($m['created_at'])) ?>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="<?= BASE_URL ?>/admin/maps/edit.php?id=<?= $m['id'] ?>"
                               class="w-8 h-8 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center transition" title="Tahrirlash">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/maps/delete.php?id=<?= $m['id'] ?>"
                               onclick="return swalDelete(event, this, 'Xarita o\'chiriladi!')"
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
