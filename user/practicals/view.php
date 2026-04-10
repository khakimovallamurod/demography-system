<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$id = (int)($_GET['id'] ?? 0);
$practical = $db->get_data_by_table('practicals', ['id' => $id]);
if (!$practical) {
    flash_message('error', 'Mashg\'ulot topilmadi!');
    redirect('/user/practicals/index.php');
}

$page_title = h($practical['title']);
$practical_pdf_url = $practical['file_path'] ? BASE_URL . '/' . ltrim($practical['file_path'], '/') : '';
include __DIR__ . '/../../includes/user_header.php';
?>

<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-3 mb-5">
        <a href="<?= BASE_URL ?>/user/practicals/index.php"
           class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-50 transition flex-shrink-0">
            <i class="fas fa-arrow-left text-gray-500 text-sm"></i>
        </a>
        <nav class="text-sm text-gray-500 flex items-center gap-1 min-w-0">
            <a href="<?= BASE_URL ?>/user/practicals/index.php" class="hover:text-emerald-600 flex-shrink-0">Amaliy mashg'ulotlar</a>
            <i class="fas fa-chevron-right text-xs text-gray-400 mx-1 flex-shrink-0"></i>
            <span class="text-gray-700 font-medium truncate"><?= h($practical['title']) ?></span>
        </nav>
    </div>

    <!-- Header card -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-5 text-white mb-4">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-flask text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-lg font-bold leading-tight"><?= h($practical['title']) ?></h1>
                <?php if ($practical['description']): ?>
                <p class="text-emerald-100 text-sm mt-1"><?= h($practical['description']) ?></p>
                <?php endif; ?>
                <p class="text-emerald-200 text-xs mt-2">
                    <i class="fas fa-calendar-alt mr-1"></i><?= date('d F Y', strtotime($practical['created_at'])) ?>
                </p>
            </div>
            <?php if ($practical['file_path']): ?>
            <a href="<?= BASE_URL ?>/<?= h($practical['file_path']) ?>" download
               class="flex-shrink-0 bg-white/20 hover:bg-white/30 text-white text-xs font-medium px-3 py-2 rounded-xl transition flex items-center gap-1.5">
                <i class="fas fa-download"></i> Yuklash
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($practical['file_path']): ?>
    <!-- Tab switcher -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-4">
        <div class="flex border-b border-gray-100">
            <button onclick="showTab('text')" id="tab-text"
                class="flex-1 py-3 text-sm font-medium flex items-center justify-center gap-2 border-b-2 border-emerald-600 text-emerald-600 tab-btn">
                <i class="fas fa-align-left"></i> Matn
            </button>
            <button onclick="showTab('pdf')" id="tab-pdf"
                class="flex-1 py-3 text-sm font-medium flex items-center justify-center gap-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 tab-btn">
                <i class="fas fa-file-pdf"></i> PDF fayl
            </button>
        </div>

        <!-- Text content -->
        <div id="panel-text" class="p-6">
            <?php if ($practical['content']): ?>
            <div class="text-gray-700 leading-relaxed whitespace-pre-wrap text-sm">
                <?= h($practical['content']) ?>
            </div>
            <?php else: ?>
            <p class="text-gray-400 text-center py-8 text-sm">Matn kiritilmagan</p>
            <?php endif; ?>
        </div>

        <!-- PDF viewer -->
        <div id="panel-pdf" class="hidden">
            <div class="bg-gray-50 border-b border-gray-100 px-5 py-2.5 flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-file-pdf text-red-500"></i>
                    <span class="font-medium truncate max-w-xs"><?= h($practical['file_name'] ?? '') ?></span>
                </div>
                <a href="<?= BASE_URL ?>/<?= h($practical['file_path']) ?>" target="_blank"
                   class="text-xs text-emerald-600 hover:underline flex items-center gap-1 flex-shrink-0">
                    <i class="fas fa-external-link-alt"></i> Yangi oynada
                </a>
            </div>
            <div class="p-3 sm:p-4 bg-slate-50">
                <?php
                $pdf_viewer_id = 'practical-pdf-viewer';
                $pdf_url = $practical_pdf_url;
                $pdf_title = $practical['title'];
                $pdf_accent = 'emerald';
                $pdf_download_url = $practical_pdf_url;
                include __DIR__ . '/../../includes/user_pdf_viewer.php';
                ?>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- Only text, no PDF -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <?php if ($practical['content']): ?>
        <div class="text-gray-700 leading-relaxed whitespace-pre-wrap text-sm">
            <?= h($practical['content']) ?>
        </div>
        <?php else: ?>
        <p class="text-gray-400 text-center py-8 text-sm">Kontent qo'shilmagan</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="mt-3">
        <a href="<?= BASE_URL ?>/user/practicals/index.php"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-emerald-600 transition">
            <i class="fas fa-arrow-left text-xs"></i> Barcha mashg'ulotlar
        </a>
    </div>
</div>

<script>
function showTab(name) {
    document.getElementById('panel-text').classList.toggle('hidden', name !== 'text');
    document.getElementById('panel-pdf').classList.toggle('hidden', name !== 'pdf');

    const active   = name === 'text' ? 'tab-text' : 'tab-pdf';
    const inactive = name === 'text' ? 'tab-pdf'  : 'tab-text';
    const color    = name === 'text' ? ['border-emerald-600','text-emerald-600'] : ['border-emerald-600','text-emerald-600'];

    ['tab-text','tab-pdf'].forEach(id => {
        const el = document.getElementById(id);
        const isActive = el.id === active;
        el.classList.toggle('border-emerald-600', isActive);
        el.classList.toggle('text-emerald-600', isActive);
        el.classList.toggle('border-transparent', !isActive);
        el.classList.toggle('text-gray-500', !isActive);
    });

    if (name === 'pdf' && window.pdfViewers?.['practical-pdf-viewer']) {
        window.pdfViewers['practical-pdf-viewer'].refresh();
    }
}
</script>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
