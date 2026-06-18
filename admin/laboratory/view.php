<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('/admin/laboratory/index.php');
}

$item = $db->get_data_by_table('laboratory_materials', ['id' => $id]);
if (!$item || !file_exists(__DIR__ . '/../../' . $item['file_path'])) {
    die_with_swal("Topilmadi", "Fayl topilmadi yoki o'chirilgan.");
}

// Increment views
$db->query("UPDATE laboratory_materials SET views = views + 1 WHERE id = {$id}");

$pdfUrl = BASE_URL . '/' . $item['file_path'];
$page_title = $item['title'] . " - O'qish";
include __DIR__ . '/../../includes/admin_header.php';
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-xl font-bold text-gray-800 line-clamp-1"><?= h($item['title']) ?></h2>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?= BASE_URL ?>/admin/laboratory/category.php?id=<?= $item['category_id'] ?>" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium transition shadow-sm flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Orqaga
        </a>
    </div>
</div>

<div class="bg-gray-200 rounded-2xl border border-gray-300 overflow-hidden relative shadow-inner" style="height: calc(100vh - 160px);">
    <div id="viewer-container" class="w-full h-full relative overflow-hidden touch-none cursor-grab active:cursor-grabbing" style="touch-action: none; user-select: none; -webkit-user-select: none;">
        <canvas id="pdf-canvas" class="origin-top-left"></canvas>
    </div>

    <!-- Controls Overlay -->
    <div class="absolute bottom-6 left-0 right-0 flex justify-center z-10 pointer-events-none">
        <div class="pointer-events-auto bg-white/90 backdrop-blur shadow-xl border border-gray-100 rounded-full px-4 py-2.5 flex items-center gap-4">
            <button id="prev-btn" class="w-10 h-10 rounded-full bg-gray-50 text-gray-600 hover:bg-gray-200 disabled:opacity-50 flex items-center justify-center transition shadow-sm border border-gray-100">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="text-sm font-bold text-gray-700 font-mono w-20 text-center">
                <span id="page-num">1</span> / <span id="page-count">-</span>
            </div>
            <button id="next-btn" class="w-10 h-10 rounded-full bg-gray-50 text-gray-600 hover:bg-gray-200 disabled:opacity-50 flex items-center justify-center transition shadow-sm border border-gray-100">
                <i class="fas fa-chevron-right"></i>
            </button>
            <div class="w-px h-6 bg-gray-300 mx-1"></div>
            <button id="rotate-btn" class="w-10 h-10 rounded-full bg-gray-50 text-gray-600 hover:bg-gray-200 flex items-center justify-center transition shadow-sm border border-gray-100" title="Aylantirish">
                <i class="fas fa-redo"></i>
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    const url = <?= json_encode($pdfUrl) ?>;
    let pdfDoc = null,
        pageNum = 1,
        pageRendering = false,
        pageNumPending = null,
        scale = 1.0, 
        canvas = document.getElementById('pdf-canvas'),
        ctx = canvas.getContext('2d'),
        container = document.getElementById('viewer-container');

    let currentScale = 1;
    let currentX = 0;
    let currentY = 0;
    let currentRotation = 0;

    function renderPage(num) {
        pageRendering = true;
        pdfDoc.getPage(num).then(function(page) {
            const unscaledViewport = page.getViewport({ scale: 1, rotation: currentRotation });
            scale = container.clientWidth / unscaledViewport.width;
            if (unscaledViewport.height * scale > container.clientHeight) {
                scale = container.clientHeight / unscaledViewport.height;
            }

            const renderScale = scale * 2.5; 
            const viewport = page.getViewport({ scale: renderScale, rotation: currentRotation });
            
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            
            canvas.style.width = (viewport.width / 2.5) + 'px';
            canvas.style.height = (viewport.height / 2.5) + 'px';

            currentScale = 1;
            currentX = (container.clientWidth - (viewport.width / 2.5)) / 2;
            currentY = (container.clientHeight - (viewport.height / 2.5)) / 2;
            updateTransform();

            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            
            const renderTask = page.render(renderContext);

            renderTask.promise.then(function() {
                pageRendering = false;
                if (pageNumPending !== null) {
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }
            });
        });

        document.getElementById('page-num').textContent = num;
        document.getElementById('prev-btn').disabled = (num <= 1);
        document.getElementById('next-btn').disabled = (num >= pdfDoc.numPages);
    }

    function queueRenderPage(num) {
        if (pageRendering) {
            pageNumPending = num;
        } else {
            renderPage(num);
        }
    }

    document.getElementById('prev-btn').addEventListener('click', function() {
        if (pageNum <= 1) return;
        pageNum--;
        queueRenderPage(pageNum);
    });

    document.getElementById('next-btn').addEventListener('click', function() {
        if (pageNum >= pdfDoc.numPages) return;
        pageNum++;
        queueRenderPage(pageNum);
    });

    document.getElementById('rotate-btn').addEventListener('click', function() {
        currentRotation = (currentRotation + 90) % 360;
        queueRenderPage(pageNum);
    });

    pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
        pdfDoc = pdfDoc_;
        document.getElementById('page-count').textContent = pdfDoc.numPages;
        renderPage(pageNum);
    });

    // --- Pinch to Zoom and Pan logic using Pointer Events ---
    let pointers = [];
    let initialPinchDistance = null;
    let initialScale = 1;
    let lastPanPos = null;

    function updateTransform() {
        canvas.style.transform = `translate(${currentX}px, ${currentY}px) scale(${currentScale})`;
    }

    container.addEventListener('pointerdown', (e) => {
        if (e.pointerType === 'mouse' && e.button !== 0) return;
        
        container.setPointerCapture(e.pointerId);
        pointers.push({ id: e.pointerId, x: e.clientX, y: e.clientY });
        if (pointers.length === 1) {
            lastPanPos = { x: e.clientX, y: e.clientY };
            container.classList.add('cursor-grabbing');
        } else if (pointers.length === 2) {
            initialPinchDistance = Math.hypot(pointers[0].x - pointers[1].x, pointers[0].y - pointers[1].y);
            initialScale = currentScale;
        }
    });

    container.addEventListener('pointermove', (e) => {
        const index = pointers.findIndex(p => p.id === e.pointerId);
        if (index === -1) return;
        pointers[index].x = e.clientX;
        pointers[index].y = e.clientY;

        if (pointers.length === 1) {
            const dx = e.clientX - lastPanPos.x;
            const dy = e.clientY - lastPanPos.y;
            currentX += dx;
            currentY += dy;
            lastPanPos = { x: e.clientX, y: e.clientY };
            requestAnimationFrame(updateTransform);
        } else if (pointers.length === 2) {
            const dist = Math.hypot(pointers[0].x - pointers[1].x, pointers[0].y - pointers[1].y);
            const centerX = (pointers[0].x + pointers[1].x) / 2;
            const centerY = (pointers[0].y + pointers[1].y) / 2;

            let newScale = initialScale * (dist / initialPinchDistance);
            newScale = Math.max(1, Math.min(newScale, 5)); 

            const rect = container.getBoundingClientRect();
            const relX = centerX - rect.left;
            const relY = centerY - rect.top;

            const scaleRatio = newScale / currentScale;
            currentX = relX - (relX - currentX) * scaleRatio;
            currentY = relY - (relY - currentY) * scaleRatio;
            
            currentScale = newScale;
            requestAnimationFrame(updateTransform);
        }
    });

    function removePointer(e) {
        if(container.releasePointerCapture) container.releasePointerCapture(e.pointerId);
        pointers = pointers.filter(p => p.id !== e.pointerId);
        if (pointers.length === 1) {
            lastPanPos = { x: pointers[0].x, y: pointers[0].y };
        }
        if (pointers.length === 0) {
            container.classList.remove('cursor-grabbing');
        }
    }

    container.addEventListener('pointerup', removePointer);
    container.addEventListener('pointercancel', removePointer);
    container.addEventListener('pointerout', removePointer);
    container.addEventListener('pointerleave', removePointer);

    container.addEventListener('wheel', (e) => {
        e.preventDefault();
        const zoomFactor = e.deltaY < 0 ? 1.1 : 0.9;
        let newScale = currentScale * zoomFactor;
        newScale = Math.max(1, Math.min(newScale, 5));

        const rect = container.getBoundingClientRect();
        const relX = e.clientX - rect.left;
        const relY = e.clientY - rect.top;

        const scaleRatio = newScale / currentScale;
        currentX = relX - (relX - currentX) * scaleRatio;
        currentY = relY - (relY - currentY) * scaleRatio;
        
        currentScale = newScale;
        requestAnimationFrame(updateTransform);
    }, { passive: false });

    container.addEventListener('touchstart', (e) => {
        e.preventDefault();
    }, { passive: false });

</script>

<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
