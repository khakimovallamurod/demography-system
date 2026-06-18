<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $term = trim($_POST['term'] ?? '');
        $definition = trim($_POST['definition'] ?? '');
        
        if ($term && $definition) {
            $db->insert('glossary', [
                'term' => $term,
                'definition' => $definition
            ]);
            flash_message('success', 'Atama muvaffaqiyatli qo\'shildi!');
            redirect('/admin/glossary/index.php');
        } else {
            flash_message('error', 'Barcha maydonlarni to\'ldirish shart!');
        }
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $term = trim($_POST['term'] ?? '');
        $definition = trim($_POST['definition'] ?? '');
        
        if ($id > 0 && $term && $definition) {
            $db->update('glossary', [
                'term' => $term,
                'definition' => $definition
            ], "id = {$id}");
            flash_message('success', 'Atama muvaffaqiyatli yangilandi!');
            redirect('/admin/glossary/index.php');
        } else {
            flash_message('error', 'Xatolik: Barcha maydonlarni to\'ldiring!');
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $db->delete('glossary', "id = {$id}");
            flash_message('success', 'Atama o\'chirildi!');
            redirect('/admin/glossary/index.php');
        }
    }
}

// Fetch terms
$search = trim($_GET['q'] ?? '');
$where = '';
if ($search !== '') {
    $escaped = $db->escape($search);
    $where = "WHERE term LIKE '%{$escaped}%' OR definition LIKE '%{$escaped}%'";
}
$terms = $db->get_data_by_table_all('glossary', "{$where} ORDER BY term ASC");

$page_title = "Glossary boshqaruvi";
include __DIR__ . '/../../includes/admin_header.php';
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Glossary (Lug'at) boshqaruvi</h2>
        <p class="text-sm text-gray-500 mt-1">Demografik atamalar va ularning izohlarini tahrirlash</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="openModal('addModal')" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition shadow-sm flex items-center gap-2">
            <i class="fas fa-plus"></i> Yangi atama
        </button>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
        <form method="GET" class="relative w-full sm:w-96" id="searchForm">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="q" id="searchInput" value="<?= h($search) ?>" placeholder="Atamani qidiring..." class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-300 transition">
        </form>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-5 py-3 font-medium w-16">#</th>
                    <th class="px-5 py-3 font-medium w-1/4">Atama</th>
                    <th class="px-5 py-3 font-medium">Izoh</th>
                    <th class="px-5 py-3 font-medium w-32">Qo'shilgan</th>
                    <th class="px-5 py-3 font-medium text-right w-24">Amallar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($terms)): ?>
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center text-gray-500">
                        Atamalar topilmadi.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($terms as $i => $term): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3.5 text-gray-400"><?= $i + 1 ?></td>
                    <td class="px-5 py-3.5 font-bold text-gray-800"><?= h($term['term']) ?></td>
                    <td class="px-5 py-3.5 text-gray-600">
                        <div class="line-clamp-2"><?= h($term['definition']) ?></div>
                    </td>
                    <td class="px-5 py-3.5 text-gray-500 text-xs">
                        <span class="flex items-center gap-1.5"><i class="far fa-clock text-gray-400"></i> <?= date('d.m.Y', strtotime($term['created_at'])) ?></span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editTerm(<?= htmlspecialchars(json_encode($term), ENT_QUOTES, 'UTF-8') ?>)" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition" title="Tahrirlash">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <form method="POST" id="deleteForm_<?= $term['id'] ?>" class="inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $term['id'] ?>">
                                <button type="button" onclick="confirmDelete(<?= $term['id'] ?>)" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition" title="O'chirish">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="addModalContent">
        <div class="flex justify-between items-center p-5 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-800">Yangi atama qo'shish</h3>
            <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form method="POST" class="p-5">
            <input type="hidden" name="action" value="add">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Atama (so'z)</label>
                <input type="text" name="term" required class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ma'nosi (izoh)</label>
                <textarea name="definition" rows="4" required class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal('addModal')" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                    Bekor qilish
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 transition shadow-sm">
                    Saqlash
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="editModalContent">
        <div class="flex justify-between items-center p-5 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-800">Atamani tahrirlash</h3>
            <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form method="POST" class="p-5">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Atama (so'z)</label>
                <input type="text" name="term" id="edit_term" required class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ma'nosi (izoh)</label>
                <textarea name="definition" id="edit_definition" rows="4" required class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition"></textarea>
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

function editTerm(termData) {
    document.getElementById('edit_id').value = termData.id;
    document.getElementById('edit_term').value = termData.term;
    document.getElementById('edit_definition').value = termData.definition;
    openModal('editModal');
}

function confirmDelete(id) {
    Swal.fire({
        title: 'Ishonchingiz komilmi?',
        text: "Ushbu atama butunlay o'chib ketadi!",
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
</script>

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
