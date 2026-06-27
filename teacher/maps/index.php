<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$search = trim($_GET['q'] ?? '');
$where = '';
if ($search !== '') {
    $escaped = $db->escape($search);
    $where = "WHERE title LIKE '%{$escaped}%' OR description LIKE '%{$escaped}%'";
}
$maps = $db->get_data_by_table_all('maps', "{$where} ORDER BY created_at DESC");

$page_title = "Demografik xaritalar";
include __DIR__ . '/../../includes/teacher_header.php';
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Demografik xaritalar</h2>
        <p class="text-sm text-gray-500 mt-1">Hududlar bo'yicha turli ko'rsatkichlarni o'z ichiga olgan xaritalarni ko'rish</p>
    </div>
</div>

<div class="mb-6">
    <form method="GET" class="relative w-full max-w-md" id="searchForm">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input type="text" name="q" id="searchInput" value="<?= h($search) ?>" placeholder="Xarita sarlavhasi bo'yicha qidiring..." class="w-full rounded-xl border border-gray-200 bg-white shadow-sm pl-11 pr-4 py-3 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
    </form>
</div>

<?php if (empty($maps)): ?>
<div class="bg-white rounded-2xl border border-gray-100 p-10 text-center shadow-sm">
    <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-map-marked-alt text-2xl"></i>
    </div>
    <h3 class="text-gray-800 font-bold mb-1">Xaritalar topilmadi</h3>
    <p class="text-sm text-gray-500">Hozircha tizimda hech qanday xarita mavjud emas.</p>
</div>
<?php else: ?>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">
    <?php foreach ($maps as $m): ?>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition group flex flex-col h-full">
        <div class="aspect-[1/1.4] bg-indigo-50 relative flex items-center justify-center border-b border-gray-100 overflow-hidden">
            <?php if (!empty($m['thumbnail']) && file_exists(__DIR__ . '/../../' . $m['thumbnail'])): ?>
                <img src="<?= BASE_URL ?>/<?= h($m['thumbnail']) ?>" alt="Cover" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500 z-10 relative">
            <?php else: ?>
                <canvas class="pdf-thumbnail absolute inset-0 w-full h-full object-contain group-hover:scale-105 transition-transform duration-500 z-10" data-pdf-url="<?= BASE_URL ?>/<?= h($m['file_path']) ?>"></canvas>
                <div class="pdf-thumbnail-loader absolute inset-0 flex items-center justify-center bg-indigo-50 z-0">
                    <i class="fas fa-file-pdf text-5xl text-indigo-200"></i>
                </div>
            <?php endif; ?>
            <div class="absolute top-3 right-3 bg-white/80 backdrop-blur px-2.5 py-1 rounded-full text-xs font-bold text-gray-700 shadow-sm flex items-center gap-1.5 z-20">
                <i class="fas fa-eye text-indigo-500"></i> <?= $m['views'] ?>
            </div>
        </div>
        <div class="p-4 flex-1 flex flex-col">
            <h3 class="font-bold text-gray-800 text-sm leading-snug mb-1 flex-1 line-clamp-2" title="<?= h($m['title']) ?>">
                <?= h($m['title']) ?>
            </h3>
            <?php if (!empty($m['description'])): ?>
            <p class="text-xs text-gray-500 mb-2 line-clamp-2" title="<?= h($m['description']) ?>">
                <?= h($m['description']) ?>
            </p>
            <?php else: ?>
            <p class="text-xs text-gray-500 mb-2 line-clamp-2">Izoh kiritilmagan</p>
            <?php endif; ?>
            
            <div class="flex items-center justify-between text-xs text-gray-400 mb-3">
                <span class="flex items-center gap-1.5 truncate" title="<?= h($m['file_name']) ?>">
                    <i class="fas fa-file"></i> <?= h(truncate_text($m['file_name'], 15)) ?>
                </span>
                <span class="flex items-center gap-1.5 text-gray-400" title="Yuklangan sana">
                    <i class="far fa-clock"></i> <?= date('d.m.Y', strtotime($m['created_at'])) ?>
                </span>
            </div>
            
            <div class="flex items-center gap-1.5 pt-3 border-t border-gray-50 mt-auto">
                <a href="<?= BASE_URL ?>/teacher/maps/view.php?id=<?= $m['id'] ?>" class="flex-1 text-center py-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg text-xs font-bold transition">
                    Ko'rish
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    let searchTimeout;

    if (searchInput && searchForm) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchForm.submit();
            }, 800);
        });

        searchInput.addEventListener('mouseleave', function() {
            if (this.value !== this.defaultValue) {
                searchForm.submit();
            }
        });
    }

    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    document.querySelectorAll('canvas.pdf-thumbnail').forEach(canvas => {
        const url = canvas.getAttribute('data-pdf-url');
        if (!url) return;
        
        pdfjsLib.getDocument(url).promise.then(pdf => {
            return pdf.getPage(1);
        }).then(page => {
            const viewport = page.getViewport({ scale: 1.5 });
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            const ctx = canvas.getContext('2d');
            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            return page.render(renderContext).promise;
        }).then(() => {
            const loader = canvas.parentElement.querySelector('.pdf-thumbnail-loader');
            if(loader) loader.style.display = 'none';
        }).catch(err => {
            console.error('Error rendering thumbnail:', err);
        });
    });
</script>

<?php include __DIR__ . '/../../includes/teacher_footer.php'; ?>
