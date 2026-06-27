<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

// Fetch terms
$search = trim($_GET['q'] ?? '');
$where = '';
if ($search !== '') {
    $escaped = $db->escape($search);
    $where = "WHERE term LIKE '%{$escaped}%' OR definition LIKE '%{$escaped}%'";
}
$terms = $db->get_data_by_table_all('glossary', "{$where} ORDER BY term ASC");

$page_title = "Glossary - Atamalar";
include __DIR__ . '/../../includes/teacher_header.php';
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Glossary (Lug'at)</h2>
        <p class="text-sm text-gray-500 mt-1">Demografik atamalar va ularning izohlari bilan tanishing</p>
    </div>
    <form method="GET" class="relative w-full sm:w-80" id="searchForm">
        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" name="q" id="searchInput" value="<?= h($search) ?>" placeholder="Atamani qidiring..." class="w-full rounded-xl border border-gray-200 bg-white pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 transition shadow-sm">
    </form>
</div>

<?php if (empty($terms)): ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 text-center py-16">
    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-book text-gray-300 text-2xl"></i>
    </div>
    <p class="text-gray-500 font-medium">Atamalar topilmadi</p>
    <?php if ($search !== ''): ?>
    <a href="?" class="text-blue-500 hover:underline text-sm mt-2 inline-block">Barcha atamalarni ko'rish</a>
    <?php endif; ?>
</div>
<?php else: ?>
<!-- 2 Column Dictionary Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <?php foreach ($terms as $term): ?>
    <div 
        class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:border-blue-300 hover:shadow-md transition cursor-pointer flex flex-col justify-center"
        onclick="showTerm(this)"
        data-term="<?= h($term['term']) ?>"
        data-definition="<?= h($term['definition']) ?>"
    >
        <div class="flex items-start justify-between gap-2">
            <h3 class="font-bold text-gray-800 text-base flex-1"><?= h($term['term']) ?></h3>
            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-100 transition">
                <i class="fas fa-arrow-right text-xs -rotate-45"></i>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2 flex items-center gap-1.5">
            <i class="far fa-clock"></i> Qo'shilgan: <?= date('d.m.Y', strtotime($term['created_at'])) ?>
        </p>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
// Search auto-submit
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

// Modal for showing term definition
function showTerm(element) {
    const term = element.getAttribute('data-term');
    const definition = element.getAttribute('data-definition');
    
    // Convert newlines to <br> for HTML display
    const formattedDef = definition.replace(/\n/g, '<br>');

    Swal.fire({
        title: `<span class="text-blue-600">${term}</span>`,
        html: `<div class="text-left text-gray-700 leading-relaxed text-[15px] mt-4">${formattedDef}</div>`,
        showCloseButton: true,
        showConfirmButton: false,
        customClass: {
            popup: 'rounded-2xl',
            title: 'text-xl border-b border-gray-100 pb-3 mb-2',
            closeButton: 'focus:outline-none'
        },
        width: '600px'
    });
}
</script>

<?php include __DIR__ . '/../../includes/teacher_footer.php'; ?>
