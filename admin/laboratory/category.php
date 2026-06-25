<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$categories = [
    1 => ['name' => "Yo'riqnoma", 'desc' => "Aholishunoslik laboratoriyasida ishlash boʻyicha yoʻriqnoma", 'color' => 'orange', 'icon' => 'fa-clipboard-list'],
    2 => ['name' => "Laboratoriya Imkoniyatlari", 'desc' => "Laboratoriya topshiriqlaridan na'munalar.", 'color' => 'emerald', 'icon' => 'fa-chart-bar'],
    3 => ['name' => "Ilmiy Resurslar", 'desc' => "Maqolalar va manbalar", 'color' => 'blue', 'icon' => 'fa-book-open'],
    4 => ['name' => "Maqola Yozish Qoidalari", 'desc' => "Ilmiy maqola tuzilishi (IMRAD va h.k.)", 'color' => 'amber', 'icon' => 'fa-pen-nib'],
    5 => ['name' => "Demografik Saytlar", 'desc' => "Milliy va xalqaro demografik platformalar", 'color' => 'slate', 'icon' => 'fa-globe'],
    6 => ['name' => "Demografiya Videolar", 'desc' => "Ta’limiy videolar.", 'color' => 'red', 'icon' => 'fa-play']
];

// Nomi bazadan olinadi (agar o'zgartirilsa, avtomatik moslashadi)
$db_cats = $db->get_data_by_table_all('laboratory_categories');
if ($db_cats) {
    foreach ($db_cats as $c) {
        if (isset($categories[$c['id']])) {
            $categories[$c['id']]['name'] = $c['name'];
        }
    }
}

if (!isset($categories[$id])) {
    redirect('/admin/laboratory/index.php');
}

$cat = $categories[$id];
$color = $cat['color'];

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    $upload_dirs = [
        1 => 'laboratory_materials/yoriqnoma',
        2 => 'laboratory_materials/imkoniyatlar',
        3 => 'laboratory_materials/resurslar',
        4 => 'laboratory_materials/maqola_yozish',
        5 => 'laboratory_materials/platformalar',
        6 => 'laboratory_materials/videolar'
    ];
    $base_upload_dir = isset($upload_dirs[$id]) ? $upload_dirs[$id] : 'laboratory_materials/other';
    $full_upload_path = __DIR__ . '/../../uploads/' . $base_upload_dir;
    
    if ($action === 'add') {
        $url = $_POST['url'] ?? '';
        $title = $_POST['title'] ?? '';
        $description = trim($_POST['description'] ?? '');

        if ($id === 6 && !empty($url)) {
            $html = fetch_remote_content($url);
            if ($html) {
                if (preg_match('/<meta property="og:title" content="(.*?)"/s', $html, $matches)) {
                    $title = html_entity_decode($matches[1], ENT_QUOTES);
                }
                if (preg_match('/<meta property="og:description" content="(.*?)"/s', $html, $matches)) {
                    $description = html_entity_decode($matches[1], ENT_QUOTES);
                }
            }
        }
        
        $description = preg_replace('/([^\r\n])\r?\n([^\r\n])/', '$1 $2', $description);
        $description = preg_replace('/(https?:\/\/[^\s]+?)([A-Z][a-z]+:)/', "$1\n$2", $description);
        
        if ($id === 5 && empty($title) && !empty($url)) {
            $parsed = parse_url($_POST['url'], PHP_URL_HOST);
            $title = $parsed ? $parsed : 'Platforma';
        }
        
        $file_path = '';
        $file_name = '';
        
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
            if ($_FILES['pdf']['size'] > 20 * 1024 * 1024) {
                die_with_swal("Katta hajm", "Fayl hajmi 20MB dan oshmasligi kerak!");
            }
            $ext = strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf' && $ext !== 'png' && $ext !== 'jpg' && $ext !== 'jpeg') {
                die_with_swal("Xato format", "Faqat PDF yoki rasm ruxsat etiladi!");
            }
            
            $filename = basename($_FILES['pdf']['name']);
            $target = $full_upload_path . '/' . $filename;
            
            if (!is_dir($full_upload_path)) {
                mkdir($full_upload_path, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['pdf']['tmp_name'], $target)) {
                $file_path = 'uploads/' . $base_upload_dir . '/' . $filename;
                $file_name = $_FILES['pdf']['name'];
            }
        }
        
        $db->insert('laboratory_materials', [
            'category_id' => $id,
            'title' => $title,
            'description' => $description,
            'file_path' => $file_path,
            'file_name' => $file_name,
            'url' => $url
        ]);
        redirect('/admin/laboratory/category.php?id=' . $id);
    }
    
    if ($action === 'edit') {
        $item_id = (int)$_POST['id'];
        $url = $_POST['url'] ?? '';
        $title = $_POST['title'] ?? '';
        $description = trim($_POST['description'] ?? '');
        
        if ($id === 6 && !empty($url)) {
            $html = fetch_remote_content($url);
            if ($html) {
                if (preg_match('/<meta property="og:title" content="(.*?)"/s', $html, $matches)) {
                    $title = html_entity_decode($matches[1], ENT_QUOTES);
                }
                if (preg_match('/<meta property="og:description" content="(.*?)"/s', $html, $matches)) {
                    $description = html_entity_decode($matches[1], ENT_QUOTES);
                }
            }
        }
        
        $description = preg_replace('/([^\r\n])\r?\n([^\r\n])/', '$1 $2', $description);
        $description = preg_replace('/(https?:\/\/[^\s]+?)([A-Z][a-z]+:)/', "$1\n$2", $description);
        
        if ($id === 5 && empty($title) && !empty($url)) {
            $parsed = parse_url($_POST['url'], PHP_URL_HOST);
            $title = $parsed ? $parsed : 'Platforma';
        }
        
        $update_data = [
            'title' => $title,
            'description' => $description,
            'url' => $url
        ];
        
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
            if ($_FILES['pdf']['size'] > 20 * 1024 * 1024) {
                die_with_swal("Katta hajm", "Fayl hajmi 20MB dan oshmasligi kerak!");
            }
            $ext = strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf' && $ext !== 'png' && $ext !== 'jpg' && $ext !== 'jpeg') {
                die_with_swal("Xato format", "Faqat PDF yoki rasm ruxsat etiladi!");
            }
            
            $filename = basename($_FILES['pdf']['name']);
            $target = $full_upload_path . '/' . $filename;
            
            if (!is_dir($full_upload_path)) {
                mkdir($full_upload_path, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['pdf']['tmp_name'], $target)) {
                // Delete old file
                $old_item = $db->get_data_by_table('laboratory_materials', ['id' => $item_id]);
                if ($old_item && !empty($old_item['file_path'])) {
                    $old_file = __DIR__ . '/../../' . $old_item['file_path'];
                    if (file_exists($old_file) && is_file($old_file)) {
                        unlink($old_file);
                    }
                }
                
                $update_data['file_path'] = 'uploads/' . $base_upload_dir . '/' . $filename;
                $update_data['file_name'] = $_FILES['pdf']['name'];
            }
        }
        
        $db->update('laboratory_materials', $update_data, "id = $item_id");
        redirect('/admin/laboratory/category.php?id=' . $id);
    }
    
    if ($action === 'delete') {
        $item_id = (int)$_POST['id'];
        
        // Delete the associated file
        $old_item = $db->get_data_by_table('laboratory_materials', ['id' => $item_id]);
        if ($old_item && !empty($old_item['file_path'])) {
            $old_file = __DIR__ . '/../../' . $old_item['file_path'];
            if (file_exists($old_file) && is_file($old_file)) {
                unlink($old_file);
            }
        }
        
        $db->delete('laboratory_materials', "id = $item_id");
        redirect('/admin/laboratory/category.php?id=' . $id);
    }
}

// Read items
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$where = "WHERE category_id = $id";
if ($search) {
    $s = $db->escape($search);
    $where .= " AND (title LIKE '%$s%' OR description LIKE '%$s%')";
}
if ($id === 5) {
    $order = "ORDER BY CASE WHEN url IS NULL OR url = '' THEN 0 ELSE 1 END ASC, CASE WHEN url IS NULL OR url = '' THEN title ELSE created_at END ASC, created_at DESC";
} else {
    $order = "ORDER BY created_at DESC";
}
$items = $db->get_data_by_table_all('laboratory_materials', "$where $order");

$page_title = $cat['name'];
include __DIR__ . '/../../includes/admin_header.php';
?>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 8px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="<?= BASE_URL ?>/admin/laboratory/index.php" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i> Orqaga</a>
            <span class="text-gray-300">/</span>
            <span class="text-<?= $color ?>-600 font-medium"><?= h($cat['name']) ?></span>
        </div>
        <h2 class="text-xl font-bold text-gray-800"><?= h($cat['name']) ?> bo'yicha materiallar</h2>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="openModal('addModal')" class="bg-<?= $color ?>-500 hover:bg-<?= $color ?>-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition shadow-sm flex items-center gap-2">
            <i class="fas fa-plus"></i> Yangi qo'shish
        </button>
    </div>
</div>

<div class="mb-6">
    <form method="GET" class="relative w-full max-w-md">
        <input type="hidden" name="id" value="<?= $id ?>">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input type="text" name="q" value="<?= h($search) ?>" placeholder="Sarlavha bo'yicha qidirish..." class="w-full rounded-xl border border-gray-200 bg-white shadow-sm pl-11 pr-4 py-3 text-sm focus:outline-none focus:border-<?= $color ?>-500 transition">
    </form>
</div>

<?php if (empty($items)): ?>
<div class="bg-white rounded-2xl border border-gray-100 p-10 text-center shadow-sm">
    <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas <?= $cat['icon'] ?> text-2xl"></i>
    </div>
    <h3 class="text-gray-800 font-bold mb-1">Materiallar topilmadi</h3>
    <p class="text-sm text-gray-500">Hozircha ushbu bo'limda hech narsa yo'q.</p>
</div>
<?php else: ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php foreach ($items as $item): ?>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col overflow-hidden group <?= empty($item['url']) ? 'h-full' : 'h-fit' ?>">
        <?php if (!empty($item['url'])): 
            if ($id === 6) {
                preg_match('/(?:v=|youtu\.be\/|embed\/)([^&?\/]+)/', $item['url'], $vid_match);
                $video_id = $vid_match[1] ?? '';
            }
        ?>
            <?php if ($id === 6 && !empty($video_id)): ?>
            <div class="aspect-video bg-black relative flex items-center justify-center border-b border-gray-100 overflow-hidden shrink-0 group">
                <img src="https://img.youtube.com/vi/<?= $video_id ?>/maxresdefault.jpg" alt="Video" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500" onerror="this.src='https://img.youtube.com/vi/<?= $video_id ?>/hqdefault.jpg';">
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-14 h-14 bg-red-600/90 backdrop-blur rounded-full flex items-center justify-center text-white shadow-lg group-hover:bg-red-600 transition-colors">
                        <i class="fas fa-play text-xl ml-1"></i>
                    </div>
                </div>
                <div class="absolute top-3 right-3 bg-black/60 backdrop-blur px-2.5 py-1 rounded-md text-xs font-bold text-white shadow-sm flex items-center gap-1.5 z-20">
                    <i class="fas fa-eye text-gray-300"></i> <?= $item['views'] ?>
                </div>
            </div>
            <div class="p-5 flex-1 flex flex-col">
                <h3 class="font-bold text-gray-800 text-sm mb-1 line-clamp-2"><?= h(stripslashes($item['title'])) ?></h3>
                <div class="text-[11px] text-gray-400 mb-3 flex items-center gap-1.5 font-medium">
                    <i class="far fa-calendar-alt"></i> <?= date('d.m.Y', strtotime($item['created_at'])) ?>
                </div>
                <?php if (!empty($item['description'])): ?>
                    <div class="text-xs leading-relaxed text-gray-600 mb-4 overflow-y-auto custom-scrollbar pr-2 flex-1 max-h-16 line-clamp-3">
                        <?= nl2br(h($item['description'])) ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
            <div class="p-6 flex-1 flex flex-col">
                <div class="flex items-center gap-4 mb-4 pb-4 border-b border-gray-100 relative">
                    <div class="absolute top-0 right-0 bg-gray-50 px-2 py-0.5 rounded-md text-[10px] font-bold text-gray-500 flex items-center gap-1">
                        <i class="fas fa-eye"></i> <?= $item['views'] ?>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0">
                        <img src="https://www.google.com/s2/favicons?domain=<?= h(parse_url($item['url'], PHP_URL_HOST)) ?>&sz=128" alt="favicon" class="w-10 h-10 object-contain" onerror="this.src=''; this.parentElement.innerHTML='<i class=\'fas fa-globe text-slate-400 text-2xl\'></i>';">
                    </div>
                    <div class="flex-1 min-w-0 pr-8">
                        <h3 class="font-bold text-gray-800 text-sm truncate"><?= h(stripslashes($item['title'])) ?></h3>
                        <p class="text-[11px] text-blue-500 font-mono truncate mt-0.5"><?= h(parse_url($item['url'], PHP_URL_HOST)) ?></p>
                        <div class="text-[10px] text-gray-400 mt-1.5 flex items-center gap-1.5 font-medium">
                            <i class="far fa-calendar-alt"></i> <?= date('d.m.Y', strtotime($item['created_at'])) ?>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($item['description'])): ?>
                    <div class="text-[13px] leading-relaxed text-gray-600 mb-6 flex-1">
                        <?= nl2br(h($item['description'])) ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
        <div class="aspect-[1/1.4] bg-gray-100 relative flex items-center justify-center border-b border-gray-100 overflow-hidden shrink-0">
            <canvas class="pdf-thumbnail absolute inset-0 w-full h-full object-contain group-hover:scale-105 transition-transform duration-500 z-10" data-pdf-url="<?= BASE_URL ?>/<?= h($item['file_path']) ?>"></canvas>
            <div class="pdf-thumbnail-loader absolute inset-0 flex items-center justify-center bg-gray-50 z-0">
                <i class="fas fa-file-pdf text-6xl text-gray-300"></i>
            </div>
            <div class="absolute top-3 right-3 bg-white/80 backdrop-blur px-2.5 py-1 rounded-full text-xs font-bold text-gray-700 shadow-sm flex items-center gap-1.5 z-20">
                <i class="fas fa-eye text-<?= $color ?>-500"></i> <?= $item['views'] ?>
            </div>
        </div>
        <div class="p-5 flex-1 flex flex-col">
            <h3 class="font-bold text-gray-800 text-sm mb-1 line-clamp-2"><?= h(stripslashes($item['title'])) ?></h3>
            <div class="text-[11px] text-gray-400 mb-3 flex items-center gap-1.5 font-medium">
                <i class="far fa-calendar-alt"></i> <?= date('d.m.Y', strtotime($item['created_at'])) ?>
            </div>
            
            <?php if (!empty($item['description'])): ?>
                <div class="text-xs leading-relaxed text-gray-600 mb-4 overflow-y-auto custom-scrollbar pr-2 flex-1 max-h-12 line-clamp-2">
                    <?= nl2br(h($item['description'])) ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
            
            <div class="mt-auto flex items-center gap-1.5 pt-4 border-t border-gray-50">
                <?php if (!empty($item['file_path'])): ?>
                    <a href="<?= BASE_URL ?>/admin/laboratory/view.php?id=<?= $item['id'] ?>" class="flex-1 text-center py-2 bg-<?= $color ?>-50 text-<?= $color ?>-600 hover:bg-<?= $color ?>-100 rounded-lg text-xs font-bold transition">Ko'rish</a>
                <?php elseif (!empty($item['url'])): ?>
                    <?php if ($id === 6): ?>
                        <a href="<?= BASE_URL ?>/admin/laboratory/view_url.php?id=<?= $item['id'] ?>" class="flex-1 text-center py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-bold transition"><i class="fas fa-play-circle mr-1"></i> Videoni ko'rish</a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/admin/laboratory/view_url.php?id=<?= $item['id'] ?>" target="_blank" class="flex-1 text-center py-2 bg-slate-50 text-slate-600 hover:bg-slate-100 rounded-lg text-xs font-bold transition">Platformaga o'tish</a>
                    <?php endif; ?>
                <?php endif; ?>
                
                <button onclick="editItem(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)" class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-600 flex items-center justify-center transition" title="Tahrirlash">
                    <i class="fas fa-edit text-xs"></i>
                </button>
                <form method="POST" id="deleteForm<?= $item['id'] ?>" class="inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                    <button type="button" onclick="confirmDelete(<?= $item['id'] ?>)" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition" title="O'chirish">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="addModalContent">
        <div class="flex justify-between items-center p-5 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Yangi qo'shish</h3>
            <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form method="POST" enctype="multipart/form-data" class="p-5">
            <input type="hidden" name="action" value="add">
            
            <?php if ($id === 5): ?>
            <div class="mb-5 flex gap-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                    <input type="radio" name="upload_type" value="resource" checked onchange="toggleForm('add')" class="text-<?= $color ?>-600 focus:ring-<?= $color ?>-500"> Resurs (PDF)
                </label>
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                    <input type="radio" name="upload_type" value="platform" onchange="toggleForm('add')" class="text-<?= $color ?>-600 focus:ring-<?= $color ?>-500"> Platforma (URL)
                </label>
            </div>
            <?php endif; ?>
            
            <div class="mb-4" id="add_title_div">
                <label class="block text-sm font-medium text-gray-700 mb-1">Sarlavha *</label>
                <input type="text" name="title" id="add_title_input" required class="w-full rounded-xl border border-gray-200 px-4 py-2 text-sm focus:border-<?= $color ?>-500 focus:ring-1 focus:ring-<?= $color ?>-500">
            </div>
            
            <?php if (!in_array($id, [1, 2, 3, 4])): ?>
            <div class="mb-4" id="add_desc_div" <?= $id === 5 ? 'style="display:none;"' : '' ?>>
                <label class="block text-sm font-medium text-gray-700 mb-1">Qisqa ta'rif (ixtiyoriy)</label>
                <textarea name="description" rows="2" class="w-full rounded-xl border border-gray-200 px-4 py-2 text-sm focus:border-<?= $color ?>-500 focus:ring-1 focus:ring-<?= $color ?>-500"></textarea>
            </div>
            <?php endif; ?>
            
            <div class="space-y-4 mb-4">
                <div id="add_pdf_div">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fayl (PDF/Rasm) <?= $id === 5 ? '*' : '' ?></label>
                    <input type="file" name="pdf" id="add_pdf_input" <?= in_array($id, [1, 2, 3, 4, 5]) ? 'required' : '' ?> class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-<?= $color ?>-50 file:text-<?= $color ?>-700 hover:file:bg-<?= $color ?>-100">
                </div>
                
                <?php if (!in_array($id, [1, 2, 3, 4])): ?>
                <div id="add_url_div" <?= $id === 5 ? 'style="display:none;"' : '' ?>>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL havola <?= $id === 5 ? '*' : '' ?></label>
                    <input type="url" name="url" id="add_url_input" placeholder="https://" class="w-full rounded-xl border border-gray-200 px-3 py-1.5 text-sm focus:border-<?= $color ?>-500 focus:ring-1 focus:ring-<?= $color ?>-500">
                </div>
                <?php endif; ?>
            </div>
            <?php if (!in_array($id, [1, 2, 3, 4, 5])): ?>
            <p class="text-xs text-gray-400 mb-4">* Fayl yuklash yoki URL havola kiritish ixtiyoriy, lekin ulardan biri kiritilgani ma'qul.</p>
            <?php endif; ?>
            
            <div class="flex justify-end gap-3 <?= in_array($id, [1, 2, 3, 4]) ? 'mt-6' : '' ?>">
                <button type="button" onclick="closeModal('addModal')" class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200">Bekor qilish</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-medium text-white bg-<?= $color ?>-500 hover:bg-<?= $color ?>-600 shadow-sm">Saqlash</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="editModalContent">
        <div class="flex justify-between items-center p-5 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Tahrirlash</h3>
            <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form method="POST" enctype="multipart/form-data" class="p-5">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            
            <?php if ($id === 5): ?>
            <div class="mb-5 flex gap-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                    <input type="radio" name="edit_upload_type" value="resource" checked onchange="toggleForm('edit')" class="text-<?= $color ?>-600 focus:ring-<?= $color ?>-500"> Resurs (PDF)
                </label>
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                    <input type="radio" name="edit_upload_type" value="platform" onchange="toggleForm('edit')" class="text-<?= $color ?>-600 focus:ring-<?= $color ?>-500"> Platforma (URL)
                </label>
            </div>
            <?php endif; ?>
            
            <div class="mb-4" id="edit_title_div">
                <label class="block text-sm font-medium text-gray-700 mb-1">Sarlavha *</label>
                <input type="text" name="title" id="edit_title_input" required class="w-full rounded-xl border border-gray-200 px-4 py-2 text-sm focus:border-<?= $color ?>-500 focus:ring-1 focus:ring-<?= $color ?>-500">
            </div>
            
            <?php if (!in_array($id, [1, 2, 3, 4])): ?>
            <div class="mb-4" id="edit_desc_div" <?= $id === 5 ? 'style="display:none;"' : '' ?>>
                <label class="block text-sm font-medium text-gray-700 mb-1">Qisqa ta'rif (ixtiyoriy)</label>
                <textarea name="description" id="edit_description" rows="2" class="w-full rounded-xl border border-gray-200 px-4 py-2 text-sm focus:border-<?= $color ?>-500 focus:ring-1 focus:ring-<?= $color ?>-500"></textarea>
            </div>
            <?php endif; ?>
            
            <div class="space-y-4 mb-4">
                <?php if (!in_array($id, [1, 2, 3, 4])): ?>
                <div id="edit_url_div" <?= $id === 5 ? 'style="display:none;"' : '' ?>>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL havola <?= $id === 5 ? '*' : '' ?></label>
                    <input type="url" name="url" id="edit_url_input" placeholder="https://" class="w-full rounded-xl border border-gray-200 px-3 py-1.5 text-sm focus:border-<?= $color ?>-500 focus:ring-1 focus:ring-<?= $color ?>-500">
                </div>
                <?php endif; ?>
            </div>
            
            <div class="flex justify-end gap-3 <?= in_array($id, [1, 2, 3, 4]) ? 'mt-6' : '' ?>">
                <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200">Bekor qilish</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-medium text-white bg-<?= $color ?>-500 hover:bg-<?= $color ?>-600 shadow-sm">Saqlash</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    const modal = document.getElementById(id);
    const content = document.getElementById(id + 'Content');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeModal(id) {
    const modal = document.getElementById(id);
    const content = document.getElementById(id + 'Content');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

function editItem(item) {
    document.getElementById('edit_id').value = item.id;
    const titleEl = document.getElementById('edit_title') || document.getElementById('edit_title_input');
    if (titleEl) titleEl.value = item.title;
    
    const descEl = document.getElementById('edit_description');
    if (descEl) descEl.value = item.description || '';
    
    const urlEl = document.getElementById('edit_url_input');
    if (urlEl) urlEl.value = item.url || '';
    
    <?php if ($id === 5): ?>
    if (item.url) {
        document.querySelector('input[name="edit_upload_type"][value="platform"]').checked = true;
        toggleForm('edit');
    } else {
        document.querySelector('input[name="edit_upload_type"][value="resource"]').checked = true;
        toggleForm('edit');
    }
    <?php endif; ?>
    
    openModal('editModal');
}

<?php if ($id === 5 || $id === 6): ?>
function toggleForm(prefix) {
    <?php if ($id === 5): ?>
    const type = document.querySelector(`input[name="${prefix === 'add' ? '' : 'edit_'}upload_type"]:checked`).value;
    <?php else: ?>
    const type = 'platform'; // Force platform mode for ID 6
    <?php endif; ?>
    
    const titleDiv = document.getElementById(prefix + '_title_div');
    const titleInput = document.getElementById(prefix + '_title_input');
    const pdfDiv = document.getElementById(prefix + '_pdf_div');
    const pdfInput = document.getElementById(prefix + '_pdf_input');
    const urlDiv = document.getElementById(prefix + '_url_div');
    const urlInput = document.getElementById(prefix + '_url_input');
    const descDiv = document.getElementById(prefix + '_desc_div');
    
    <?php if ($id === 6): ?>
    // For Category 6, ONLY show URL
    if (titleDiv) titleDiv.style.display = 'none';
    if (titleInput) titleInput.required = false;
    if (pdfDiv) pdfDiv.style.display = 'none';
    if (pdfInput) pdfInput.required = false;
    if (urlDiv) {
        urlDiv.style.display = 'block';
        const lbl = urlDiv.querySelector('label');
        if (lbl) lbl.textContent = 'YouTube Video URL *';
    }
    if (urlInput) {
        urlInput.required = true;
        urlInput.placeholder = 'https://www.youtube.com/watch?v=...';
    }
    if (descDiv) descDiv.style.display = 'none';
    <?php else: ?>
    if (type === 'resource') {
        if (titleDiv) titleDiv.style.display = 'block';
        if (titleInput) titleInput.required = true;
        if (pdfDiv) pdfDiv.style.display = 'block';
        if (prefix === 'add' && pdfInput) pdfInput.required = true;
        if (urlDiv) urlDiv.style.display = 'none';
        if (urlInput) urlInput.required = false;
        if (descDiv) descDiv.style.display = 'none';
    } else {
        if (titleDiv) titleDiv.style.display = 'none';
        if (titleInput) titleInput.required = false;
        if (pdfDiv) pdfDiv.style.display = 'none';
        if (pdfInput) pdfInput.required = false;
        if (urlDiv) urlDiv.style.display = 'block';
        if (urlInput) urlInput.required = true;
        if (descDiv) descDiv.style.display = 'block';
    }
    <?php endif; ?>
}

// Initial state setup for Add Modal
document.addEventListener('DOMContentLoaded', () => {
    toggleForm('add');
});
<?php endif; ?>

function confirmDelete(id) {
    Swal.fire({
        title: "Rostdan ham o'chirmoqchimisiz?",
        text: "Bu amalni ortga qaytarib bo'lmaydi!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: "Ha, o'chirish",
        cancelButtonText: "Bekor qilish"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm' + id).submit();
        }
    });
}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    
    document.addEventListener("DOMContentLoaded", function() {
        const canvases = document.querySelectorAll('.pdf-thumbnail');
        
        canvases.forEach(canvas => {
            const url = canvas.getAttribute('data-pdf-url');
            if(!url) return;
            
            const loadingTask = pdfjsLib.getDocument(url);
            loadingTask.promise.then(pdf => {
                return pdf.getPage(1);
            }).then(page => {
                const viewport = page.getViewport({scale: 1.5});
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                
                return page.render(renderContext).promise;
            }).then(() => {
                const loader = canvas.nextElementSibling;
                if(loader && loader.classList.contains('pdf-thumbnail-loader')) {
                    loader.style.opacity = '0';
                    setTimeout(() => loader.remove(), 300);
                }
            }).catch(err => {
                console.error("PDF preview error: ", err);
            });
        });
    });
</script>

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
