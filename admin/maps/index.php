<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if ($title && isset($_FILES['pdf']) && $_FILES['pdf']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = upload_file($_FILES['pdf'], 'maps');
            if ($upload['success']) {
                $db->insert('maps', [
                    'title' => $title,
                    'description' => $description,
                    'file_path' => $upload['file_path'],
                    'file_name' => $upload['file_name'],
                    'thumbnail' => $upload['thumbnail'] ?? null
                ]);
                flash_message('success', 'Xarita muvaffaqiyatli yuklandi!');
            } else {
                flash_message('error', $upload['message']);
            }
        } else {
            flash_message('error', 'Sarlavha va PDF fayl kiritish shart!');
        }
        redirect('/admin/maps/index.php');
        
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if ($id > 0 && $title) {
            $updateData = ['title' => $title, 'description' => $description];
            
            // Check if new file is uploaded
            if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upload = upload_file($_FILES['pdf'], 'maps');
                if ($upload['success']) {
                    $updateData['file_path'] = $upload['file_path'];
                    $updateData['file_name'] = $upload['file_name'];
                    $updateData['thumbnail'] = $upload['thumbnail'] ?? null;
                    
                    // Delete old file
                    $old = $db->get_data_by_table('maps', ['id' => $id]);
                    if ($old && $old['file_path'] && file_exists(__DIR__ . '/../../' . $old['file_path'])) {
                        @unlink(__DIR__ . '/../../' . $old['file_path']);
                    }
                    if ($old && !empty($old['thumbnail']) && file_exists(__DIR__ . '/../../' . $old['thumbnail'])) {
                        @unlink(__DIR__ . '/../../' . $old['thumbnail']);
                    }
                } else {
                    flash_message('error', $upload['message']);
                    redirect('/admin/maps/index.php');
                }
            }
            
            $db->update('maps', $updateData, "id = {$id}");
            flash_message('success', 'Xarita muvaffaqiyatli yangilandi!');
        } else {
            flash_message('error', 'Sarlavha majburiy!');
        }
        redirect('/admin/maps/index.php');
        
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $old = $db->get_data_by_table('maps', ['id' => $id]);
            if ($old && $old['file_path'] && file_exists(__DIR__ . '/../../' . $old['file_path'])) {
                @unlink(__DIR__ . '/../../' . $old['file_path']);
            }
            if ($old && !empty($old['thumbnail']) && file_exists(__DIR__ . '/../../' . $old['thumbnail'])) {
                @unlink(__DIR__ . '/../../' . $old['thumbnail']);
            }
            $db->delete('maps', "id = {$id}");
            flash_message('success', 'Xarita o\'chirildi!');
            redirect('/admin/maps/index.php');
        }
    }
}

// Fetch maps
$search = trim($_GET['q'] ?? '');
$where = '';
if ($search !== '') {
    $escaped = $db->escape($search);
    $where = "WHERE title LIKE '%{$escaped}%' OR description LIKE '%{$escaped}%'";
}
$maps = $db->get_data_by_table_all('maps', "{$where} ORDER BY created_at DESC");

$page_title = "Demografik xaritalar";
include __DIR__ . '/../../includes/admin_header.php';
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Demografik xaritalar</h2>
        <p class="text-sm text-gray-500 mt-1">Hududlar bo'yicha turli ko'rsatkichlarni o'z ichiga olgan xaritalarni boshqarish</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="openModal('addModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition shadow-sm flex items-center gap-2">
            <i class="fas fa-upload"></i> Xarita yuklash
        </button>
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
                <a href="<?= BASE_URL ?>/admin/maps/view.php?id=<?= $m['id'] ?>" class="flex-1 text-center py-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg text-xs font-bold transition">
                    Ko'rish
                </a>
                <button onclick="editMap(<?= htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8') ?>)" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition" title="Tahrirlash">
                    <i class="fas fa-edit text-xs"></i>
                </button>
                <form method="POST" id="deleteForm_<?= $m['id'] ?>" class="inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                    <button type="button" onclick="confirmDelete(<?= $m['id'] ?>)" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition" title="O'chirish">
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
        <div class="flex justify-between items-center p-5 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-800">Yangi xarita yuklash</h3>
            <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form method="POST" enctype="multipart/form-data" class="p-5">
            <input type="hidden" name="action" value="add">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sarlavha</label>
                <input type="text" name="title" required placeholder="Xarita nomini kiriting" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tavsif (ixtiyoriy)</label>
                <textarea name="description" rows="2" placeholder="Xarita haqida qisqacha izoh..." class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"></textarea>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">PDF Fayl</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-indigo-400 transition bg-gray-50 relative overflow-hidden">
                    <div class="space-y-1 text-center">
                        <i class="fas fa-file-pdf text-3xl text-gray-400 mb-2"></i>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                <span>Fayl tanlash</span>
                                <input name="pdf" type="file" accept=".pdf" required class="sr-only">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">Faqat PDF (Max: 20MB)</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal('addModal')" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                    Bekor qilish
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm">
                    Yuklash
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="editModalContent">
        <div class="flex justify-between items-center p-5 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-800">Xaritani tahrirlash</h3>
            <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form method="POST" enctype="multipart/form-data" class="p-5">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sarlavha</label>
                <input type="text" name="title" id="edit_title" required class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tavsif (ixtiyoriy)</label>
                <textarea name="description" id="edit_description" rows="2" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"></textarea>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Yangi PDF Fayl (ixtiyoriy)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-blue-400 transition bg-gray-50 relative overflow-hidden">
                    <div class="space-y-1 text-center">
                        <i class="fas fa-file-pdf text-3xl text-gray-400 mb-2"></i>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                <span>Fayl tanlash</span>
                                <input name="pdf" type="file" accept=".pdf" class="sr-only">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">Agar faylni almashtirmoqchi bo'lsangiz tanlang</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal('editModal')" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                    Bekor qilish
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition shadow-sm">
                    Yangilash
                </button>
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
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 300);
}

function editMap(data) {
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_title').value = data.title;
    document.getElementById('edit_description').value = data.description || '';
    openModal('editModal');
}

function confirmDelete(id) {
    Swal.fire({
        title: 'Ishonchingiz komilmi?',
        text: "Xarita fayli bilan birga butunlay o'chiriladi!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: 'Ha, o\'chirilsin',
        cancelButtonText: 'Bekor qilish',
        customClass: { popup: 'text-sm' }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm_' + id).submit();
        }
    });
}

// Display selected file name
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const fileName = this.files[0].name;
            const textEl = this.closest('.space-y-1').querySelector('p.text-xs');
            textEl.textContent = "Tanlandi: " + fileName;
            textEl.classList.remove('text-gray-500');
            textEl.classList.add('text-indigo-600', 'font-medium');
        }
    });
});
</script>

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

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
