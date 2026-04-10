<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$id = (int)($_GET['id'] ?? 0);
$lecture = $db->get_data_by_table('lectures', ['id' => $id]);
if (!$lecture) {
    flash_message('error', 'Ma\'ruza topilmadi!');
    redirect('/user/lectures/index.php');
}

$page_title = h($lecture['title']);
$lecture_pdf_url = $lecture['file_path'] ? BASE_URL . '/' . ltrim($lecture['file_path'], '/') : '';
include __DIR__ . '/../../includes/user_header.php';
?>

<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-3 mb-5">
        <a href="<?= BASE_URL ?>/user/lectures/index.php"
           class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-50 transition flex-shrink-0">
            <i class="fas fa-arrow-left text-gray-500 text-sm"></i>
        </a>
        <nav class="text-sm text-gray-500 flex items-center gap-1 min-w-0">
            <a href="<?= BASE_URL ?>/user/lectures/index.php" class="hover:text-blue-600 flex-shrink-0">Ma'ruzalar</a>
            <i class="fas fa-chevron-right text-xs text-gray-400 mx-1 flex-shrink-0"></i>
            <span class="text-gray-700 font-medium truncate"><?= h($lecture['title']) ?></span>
        </nav>
    </div>

    <!-- Header card -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-5 text-white mb-4">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-book-open text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-lg font-bold leading-tight"><?= h($lecture['title']) ?></h1>
                <?php if ($lecture['description']): ?>
                <p class="text-blue-100 text-sm mt-1"><?= h($lecture['description']) ?></p>
                <?php endif; ?>
                <p class="text-blue-200 text-xs mt-2">
                    <i class="fas fa-calendar-alt mr-1"></i><?= date('d F Y', strtotime($lecture['created_at'])) ?>
                </p>
            </div>
            <?php if ($lecture['file_path']): ?>
            <a href="<?= BASE_URL ?>/<?= h($lecture['file_path']) ?>" download
               class="flex-shrink-0 bg-white/20 hover:bg-white/30 text-white text-xs font-medium px-3 py-2 rounded-xl transition flex items-center gap-1.5">
                <i class="fas fa-download"></i> Yuklash
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($lecture['file_path']): ?>
    <!-- Tab switcher -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-4">
        <div class="flex border-b border-gray-100">
            <button onclick="showTab('text')" id="tab-text"
                class="flex-1 py-3 text-sm font-medium flex items-center justify-center gap-2 border-b-2 border-blue-600 text-blue-600 tab-btn">
                <i class="fas fa-align-left"></i> Matn
            </button>
            <button onclick="showTab('pdf')" id="tab-pdf"
                class="flex-1 py-3 text-sm font-medium flex items-center justify-center gap-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 tab-btn">
                <i class="fas fa-file-pdf"></i> PDF fayl
            </button>
        </div>

        <!-- Text content -->
        <div id="panel-text" class="p-6">
            <?php if ($lecture['content']): ?>
            <div class="text-gray-700 leading-relaxed whitespace-pre-wrap text-sm">
                <?= h($lecture['content']) ?>
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
                    <span class="font-medium truncate max-w-xs"><?= h($lecture['file_name'] ?? '') ?></span>
                </div>
                <a href="<?= BASE_URL ?>/<?= h($lecture['file_path']) ?>" target="_blank"
                   class="text-xs text-blue-600 hover:underline flex items-center gap-1 flex-shrink-0">
                    <i class="fas fa-external-link-alt"></i> Yangi oynada
                </a>
            </div>
            <div class="p-3 sm:p-4 bg-slate-50">
                <?php
                $pdf_viewer_id = 'lecture-pdf-viewer';
                $pdf_url = $lecture_pdf_url;
                $pdf_title = $lecture['title'];
                $pdf_accent = 'blue';
                $pdf_download_url = $lecture_pdf_url;
                include __DIR__ . '/../../includes/user_pdf_viewer.php';
                ?>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- Only text, no PDF -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <?php if ($lecture['content']): ?>
        <div class="text-gray-700 leading-relaxed whitespace-pre-wrap text-sm">
            <?= h($lecture['content']) ?>
        </div>
        <?php else: ?>
        <p class="text-gray-400 text-center py-8 text-sm">Kontent qo'shilmagan</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="mt-3">
        <a href="<?= BASE_URL ?>/user/lectures/index.php"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 transition">
            <i class="fas fa-arrow-left text-xs"></i> Barcha ma'ruzalar
        </a>
    </div>
</div>

<script>
function showTab(name) {
    // panels
    document.getElementById('panel-text').classList.toggle('hidden', name !== 'text');
    document.getElementById('panel-pdf').classList.toggle('hidden', name !== 'pdf');

    // tab styles
    const tabs = { text: document.getElementById('tab-text'), pdf: document.getElementById('tab-pdf') };
    Object.entries(tabs).forEach(([key, el]) => {
        if (key === name) {
            el.classList.add('border-blue-600', 'text-blue-600');
            el.classList.remove('border-transparent', 'text-gray-500');
        } else {
            el.classList.remove('border-blue-600', 'text-blue-600');
            el.classList.add('border-transparent', 'text-gray-500');
        }
    });

    if (name === 'pdf' && window.pdfViewers?.['lecture-pdf-viewer']) {
        window.pdfViewers['lecture-pdf-viewer'].refresh();
    }
}
</script>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
