<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$id = (int)($_GET['id'] ?? 0);
$practical = $db->get_data_by_table('practicals', ['id' => $id]);
if (!$practical) {
    flash_message('error', 'Amaliyot topilmadi!');
    redirect('/user/practicals/index.php');
}

// Check if progress exists
$user_id = (int)$_SESSION['user_id'];
$progress = $db->get_data_by_table('user_module_progress', [
    'user_id' => $user_id,
    'module_type' => 1,
    'module_id' => $id
]);
$is_completed = !empty($progress);

// Progressive Lock Check
$progress_sql = "SELECT l.id, l.order_num 
                 FROM user_module_progress p
                 JOIN lectures l ON p.module_id = l.id
                 WHERE p.user_id = $user_id AND p.module_type = 0";
$progress_res = $db->query($progress_sql);
$completed_lecture_orders = [];
if ($progress_res) {
    while ($row = mysqli_fetch_assoc($progress_res)) {
        if ($row['order_num'] > 0) {
            $lid = $row['id'];
            $t_res = $db->query("SELECT id FROM tests WHERE module_type = 0 AND module_id = $lid");
            $t = mysqli_fetch_assoc($t_res);
            if ($t) {
                $p_res = $db->query("SELECT id FROM test_results WHERE test_id = {$t['id']} AND user_id = $user_id AND (score / total) >= 0.6");
                if (mysqli_num_rows($p_res) > 0) {
                    $completed_lecture_orders[] = (int)$row['order_num'];
                }
            } else {
                $completed_lecture_orders[] = (int)$row['order_num'];
            }
        }
    }
}
$is_unlocked = ((int)$practical['order_num'] === 0) || in_array((int)$practical['order_num'], $completed_lecture_orders);
if (!$is_unlocked) {
    flash_message('error', "Bu amaliyot qulflangan! Dastlab unga tegishli bo'lgan ma'ruzani o'qib tugatishingiz kerak.");
    redirect('/user/practicals/index.php');
}

$page_title = h($practical['title']);
$practical_pdf_url = $practical['file_path'] ? BASE_URL . '/' . ltrim($practical['file_path'], '/') : '';

// By default we will hide the sidebars and make it full width if possible
$hide_sidebar = true; 
include __DIR__ . '/../../includes/user_header.php';
?>

<style>
/* Hide download and new tab buttons in the PDF viewer */
[data-pdf-viewer] a[download], [data-pdf-viewer] a[target="_blank"] { display: none !important; }
body { padding-bottom: 90px; } /* Space for the bottom progress bar */
</style>

<div class="max-w-[95%] mx-auto pb-10">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-3 mb-5">
        <a href="<?= BASE_URL ?>/user/practicals/index.php"
           class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-50 transition flex-shrink-0">
            <i class="fas fa-arrow-left text-gray-500 text-sm"></i>
        </a>
        <nav class="text-sm text-gray-500 flex items-center gap-1 min-w-0">
            <a href="<?= BASE_URL ?>/user/practicals/index.php" class="hover:text-emerald-600 flex-shrink-0">Amaliyotlar</a>
            <i class="fas fa-chevron-right text-xs text-gray-400 mx-1 flex-shrink-0"></i>
            <span class="text-gray-700 font-medium truncate"><?= h($practical['title']) ?></span>
        </nav>
    </div>

    <!-- Header card -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-6 text-white mb-6 shadow-md">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-chalkboard-teacher text-xl"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold leading-tight"><?= h($practical['title']) ?></h1>
                <?php if ($practical['description']): ?>
                <p class="text-emerald-100 text-sm mt-2 max-w-4xl"><?= h($practical['description']) ?></p>
                <?php endif; ?>
                <div class="flex items-center gap-4 mt-3">
                    <p class="text-emerald-200 text-xs flex items-center gap-1.5">
                        <i class="fas fa-calendar-alt"></i><?= date('d.m.Y', strtotime($practical['created_at'])) ?>
                    </p>
                    <?php if ($is_completed): ?>
                    <span class="bg-green-500/20 text-green-100 border border-green-400/30 px-2 py-0.5 rounded-lg text-xs font-bold flex items-center gap-1">
                        <i class="fas fa-check-circle"></i> O'qib tugatilgan
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($practical['file_path']): ?>
    <!-- Content container -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-4">
        <!-- Tabs -->
        <div class="flex border-b border-gray-100">
            <button onclick="showTab('pdf')" id="tab-pdf"
                class="flex-1 py-3.5 text-sm font-bold flex items-center justify-center gap-2 border-b-2 border-emerald-600 text-emerald-600 tab-btn transition-colors">
                <i class="fas fa-file-pdf"></i> PDF Amaliyot
            </button>
            <?php if ($practical['content']): ?>
            <button onclick="showTab('text')" id="tab-text"
                class="flex-1 py-3.5 text-sm font-bold flex items-center justify-center gap-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 tab-btn transition-colors">
                <i class="fas fa-align-left"></i> Matnli shakli
            </button>
            <?php endif; ?>
        </div>

        <!-- PDF viewer -->
        <div id="panel-pdf" class="block">
            <div class="p-3 sm:p-5 bg-slate-50 min-h-[600px]">
                <?php
                $pdf_viewer_id = 'practical-pdf-viewer';
                $pdf_url = $practical_pdf_url;
                $pdf_title = $practical['title'];
                $pdf_accent = 'emerald';
                $pdf_download_url = '';
                include __DIR__ . '/../../includes/user_pdf_viewer.php';
                ?>
            </div>
        </div>

        <!-- Text content -->
        <?php if ($practical['content']): ?>
        <div id="panel-text" class="hidden p-6 sm:p-8">
            <div class="text-gray-700 leading-relaxed whitespace-pre-wrap text-[15px] max-w-none prose prose-emerald">
                <?= h($practical['content']) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <!-- Only text, no PDF -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 min-h-[400px]">
        <?php if ($practical['content']): ?>
        <div class="text-gray-700 leading-relaxed whitespace-pre-wrap text-[15px] prose prose-emerald max-w-none">
            <?= h($practical['content']) ?>
        </div>
        <?php else: ?>
        <p class="text-gray-400 text-center py-16 text-sm flex flex-col items-center gap-3">
            <i class="fas fa-file-alt text-3xl opacity-50"></i>
            Kontent qo'shilmagan
        </p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Fixed bottom completion bar -->
<div class="fixed bottom-16 md:bottom-0 left-0 w-full bg-white border-t border-gray-200 p-4 shadow-[0_-10px_20px_-10px_rgba(0,0,0,0.1)] z-50" id="completion-bar">
    <div class="max-w-[95%] mx-auto flex flex-col sm:flex-row items-center gap-4">
        <div class="flex-1 w-full relative">
            <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 transition-all duration-300" id="progress-line" style="width: <?= $is_completed ? '100%' : '0%' ?>"></div>
            </div>
            <p class="text-xs font-semibold text-center sm:text-left mt-2 tracking-wide" id="progress-text" style="color: <?= $is_completed ? '#16a34a' : '#64748b' ?>">
                <?= $is_completed ? "Amaliyotni o'qib tugatilgan!" : "Amaliyotni ko'rmoqdasiz..." ?>
            </p>
        </div>
        <form action="complete.php" method="POST" class="w-full sm:w-auto" id="complete-form">
            <input type="hidden" name="module_type" value="1">
            <input type="hidden" name="module_id" value="<?= $id ?>">
            <?php if ($is_completed): ?>
            <button type="submit" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold px-8 py-3 rounded-xl transition shadow-md flex items-center justify-center gap-2">
                <i class="fas fa-tasks"></i> Testni ishlash
            </button>
            <?php else: ?>
            <button type="button" disabled id="finish-btn" class="w-full sm:w-auto bg-gray-200 text-gray-400 font-bold px-8 py-3 rounded-xl transition cursor-not-allowed flex items-center justify-center gap-2 select-none">
                <i class="fas fa-check-circle"></i> Ko'rib chiqdim
            </button>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
let isCompleted = <?= $is_completed ? 'true' : 'false' ?>;
let activeTab = '<?= $practical['file_path'] ? 'pdf' : 'text' ?>';

function showTab(name) {
    activeTab = name;
    
    // panels
    const panelText = document.getElementById('panel-text');
    const panelPdf = document.getElementById('panel-pdf');
    if (panelText) panelText.classList.toggle('hidden', name !== 'text');
    if (panelPdf) panelPdf.classList.toggle('hidden', name !== 'pdf');

    // tab styles
    const tabs = { text: document.getElementById('tab-text'), pdf: document.getElementById('tab-pdf') };
    Object.entries(tabs).forEach(([key, el]) => {
        if (!el) return;
        if (key === name) {
            el.classList.add('border-emerald-600', 'text-emerald-600');
            el.classList.remove('border-transparent', 'text-gray-500');
        } else {
            el.classList.remove('border-emerald-600', 'text-emerald-600');
            el.classList.add('border-transparent', 'text-gray-500');
        }
    });

    if (name === 'pdf' && window.pdfViewers?.['practical-pdf-viewer']) {
        window.pdfViewers['practical-pdf-viewer'].refresh();
    }
    
    // Check progress immediately on tab switch if it fits
    checkProgress();
}

function unlockFinishButton() {
    if (isCompleted) return;
    isCompleted = true;
    
    document.getElementById('progress-line').style.width = '100%';
    const pText = document.getElementById('progress-text');
    pText.innerText = "Ajoyib! Amaliyotni to'liq ko'rib chiqdingiz.";
    pText.style.color = '#16a34a';
    
    const btn = document.getElementById('finish-btn');
    if (btn) {
        btn.disabled = false;
        btn.type = 'submit';
        btn.className = "w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white font-bold px-8 py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-[0_0_15px_rgba(34,197,94,0.4)] animate-pulse hover:animate-none";
    }
}

function checkProgress() {
    if (isCompleted) return;
    
    let scrollHeight, scrollTop, clientHeight;
    let targetEl = window;
    
    // If in PDF tab, track the scroll container inside the viewer
    if (activeTab === 'pdf') {
        const pdfContainer = document.querySelector('[data-scroll-container]');
        if (pdfContainer) {
            targetEl = pdfContainer;
            scrollHeight = targetEl.scrollHeight;
            scrollTop = targetEl.scrollTop;
            clientHeight = targetEl.clientHeight;
        } else {
            return;
        }
    } else {
        scrollHeight = document.documentElement.scrollHeight;
        scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        clientHeight = document.documentElement.clientHeight;
    }
    
    let percent = 0;
    if (scrollHeight > clientHeight + 50) {
        percent = Math.min(100, Math.max(0, (scrollTop / (scrollHeight - clientHeight)) * 100));
    } else {
        percent = 100; // fits in screen
    }
    
    document.getElementById('progress-line').style.width = percent + '%';
    
    // Allow slight margin (e.g., 95%) to consider it "read"
    if (percent >= 95) {
        unlockFinishButton();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Add scroll listeners
    window.addEventListener('scroll', () => {
        if (activeTab === 'text' || !document.getElementById('panel-pdf')) checkProgress();
    }, {passive: true});
    
    // Initial check (in case content is short)
    setTimeout(() => {
        const pdfContainer = document.querySelector('[data-scroll-container]');
        if (pdfContainer) {
            pdfContainer.addEventListener('scroll', checkProgress, {passive: true});
        }
        checkProgress();
    }, 1500);
});
</script>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
