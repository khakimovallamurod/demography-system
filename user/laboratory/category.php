<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$categories = [
    1 => ['name' => "Yo'riqnoma", 'desc' => "Aholishunoslik laboratoriyasida ishlash boʻyicha yoʻriqnoma", 'color' => 'orange', 'icon' => 'fa-clipboard-list'],
    2 => ['name' => "Laboratoriya Imkoniyatlari", 'desc' => "Laboratoriya topshiriqlaridan na'munalar.", 'color' => 'emerald', 'icon' => 'fa-chart-bar'],
    3 => ['name' => "Ilmiy Resurslar", 'desc' => "Maqolalar va manbalar", 'color' => 'blue', 'icon' => 'fa-book-open'],
    4 => ['name' => "Maqola Yozish Qoidalari", 'desc' => "Ilmiy maqola tuzilishi (IMRAD va h.k.)", 'color' => 'amber', 'icon' => 'fa-pen-nib'],
    5 => ['name' => "Demografik Saytlar", 'desc' => "Milliy va xalqaro demografik platformalar", 'color' => 'slate', 'icon' => 'fa-globe'],
    6 => ['name' => "Demografiya Videolar", 'desc' => "Ta’limiy videolar.", 'color' => 'red', 'icon' => 'fa-play']
];

// Nomi bazadan olinadi
$db_cats = $db->get_data_by_table_all('laboratory_categories');
if ($db_cats) {
    foreach ($db_cats as $c) {
        if (isset($categories[$c['id']])) {
            $categories[$c['id']]['name'] = $c['name'];
        }
    }
}

if (!isset($categories[$id])) {
    redirect('/user/laboratory/index.php');
}

$cat = $categories[$id];
$color = $cat['color'];

// Read items
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$where = "WHERE category_id = $id";
if ($search) {
    $s = $db->escape($search);
    $where .= " AND (title LIKE '%$s%' OR description LIKE '%$s%')";
}
if ($id === 5) {
    // For Demografik Saytlar (category 5), ensure PDFs are always first, sorted by their titles (1, 2, 3...)
    $order = "ORDER BY CASE WHEN url IS NULL OR url = '' THEN 0 ELSE 1 END ASC, CASE WHEN url IS NULL OR url = '' THEN title ELSE created_at END ASC, created_at DESC";
} else {
    $order = "ORDER BY created_at DESC";
}
$items = $db->get_data_by_table_all('laboratory_materials', "$where $order");

$page_title = $cat['name'];
include __DIR__ . '/../../includes/user_header.php';
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
            <a href="<?= BASE_URL ?>/user/laboratory/index.php" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i> Orqaga</a>
            <span class="text-gray-300">/</span>
            <span class="text-<?= $color ?>-600 font-medium"><?= h($cat['name']) ?></span>
        </div>
        <h2 class="text-xl font-bold text-gray-800"><?= h($cat['name']) ?> bo'yicha materiallar</h2>
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
            <div class="mt-auto pt-4 border-t border-gray-50">
                <?php if (!empty($item['file_path'])): ?>
                    <a href="<?= BASE_URL ?>/user/laboratory/view.php?id=<?= $item['id'] ?>" class="block w-full text-center py-2 bg-<?= $color ?>-50 text-<?= $color ?>-600 hover:bg-<?= $color ?>-100 rounded-lg text-xs font-bold transition">Ko'rish</a>
                <?php elseif (!empty($item['url'])): ?>
                    <?php if ($id === 6): ?>
                        <a href="<?= BASE_URL ?>/user/laboratory/view_url.php?id=<?= $item['id'] ?>" class="block w-full text-center py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-bold transition"><i class="fas fa-play-circle mr-1"></i> Videoni ko'rish</a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/user/laboratory/view_url.php?id=<?= $item['id'] ?>" target="_blank" class="block w-full text-center py-2 bg-slate-50 text-slate-600 hover:bg-slate-100 rounded-lg text-xs font-bold transition">Platformaga o'tish</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

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

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
