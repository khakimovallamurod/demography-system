<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('/user/maps/index.php');
}

$map = $db->get_data_by_table('maps', ['id' => $id]);
if (!$map || !file_exists(__DIR__ . '/../../' . $map['file_path'])) {
    die_with_swal("Topilmadi", "Xarita topilmadi yoki o'chirilgan.");
}

// Increment views
$db->query("UPDATE maps SET views = views + 1 WHERE id = {$id}");

$pdfUrl = BASE_URL . '/' . $map['file_path'];
$page_title = $map['title'] . " - Xarita ko'rish";
include __DIR__ . '/../../includes/user_header.php';
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-xl font-bold text-gray-800 line-clamp-1"><?= h($map['title']) ?></h2>
        <?php if (!empty($map['description'])): ?>
            <p class="text-sm text-gray-500 mt-1"><?= h($map['description']) ?></p>
        <?php endif; ?>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?= BASE_URL ?>/user/maps/index.php" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium transition shadow-sm flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Orqaga
        </a>
    </div>
</div>

<div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden relative shadow-inner" style="height: calc(100vh - 180px); background-image: radial-gradient(circle, #334155 1px, transparent 1px); background-size: 20px 20px;">
    <div id="viewer-container" class="w-full h-full relative overflow-hidden touch-none cursor-grab active:cursor-grabbing" style="touch-action: none; user-select: none; -webkit-user-select: none;">
        <canvas id="pdf-canvas" class="origin-top-left shadow-2xl absolute top-0 left-0"></canvas>
    </div>
    
    <!-- Controls Overlay (Zoom) -->
    <div class="absolute top-6 right-6 flex flex-col gap-2 z-10">
        <button id="zoom-in" class="w-12 h-12 rounded-full bg-white/10 backdrop-blur border border-white/20 text-white hover:bg-white/20 flex items-center justify-center transition shadow-lg">
            <i class="fas fa-plus"></i>
        </button>
        <button id="zoom-out" class="w-12 h-12 rounded-full bg-white/10 backdrop-blur border border-white/20 text-white hover:bg-white/20 flex items-center justify-center transition shadow-lg">
            <i class="fas fa-minus"></i>
        </button>
        <button id="zoom-fit" class="w-12 h-12 rounded-full bg-indigo-600 border border-indigo-500 text-white hover:bg-indigo-700 flex items-center justify-center transition shadow-lg mt-2">
            <i class="fas fa-compress"></i>
        </button>
        <button id="rotate-btn" class="w-12 h-12 rounded-full bg-amber-500 border border-amber-400 text-white hover:bg-amber-600 flex items-center justify-center transition shadow-lg mt-2" title="90 gradusga burish">
            <i class="fas fa-redo"></i>
        </button>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    const url = <?= json_encode($pdfUrl) ?>;
    let pdfDoc = null,
        scale = 1.0, 
        baseScale = 1.0,
        canvas = document.getElementById('pdf-canvas'),
        ctx = canvas.getContext('2d'),
        container = document.getElementById('viewer-container');

    let currentScale = 1;
    let currentX = 0;
    let currentY = 0;
    let currentRotation = 0;

    function renderPage(num) {
        pdfDoc.getPage(num).then(function(page) {
            const unscaledViewport = page.getViewport({ scale: 1, rotation: currentRotation });
            
            // For maps, try to fit either width or height, with a bit of padding
            const padX = 40;
            const padY = 40;
            const scaleX = (container.clientWidth - padX) / unscaledViewport.width;
            const scaleY = (container.clientHeight - padY) / unscaledViewport.height;
            
            scale = Math.min(scaleX, scaleY);
            baseScale = scale; // Save the base fit scale
            
            // High res rendering
            const renderScale = scale * 3.0; 
            const viewport = page.getViewport({ scale: renderScale, rotation: currentRotation });
            
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            
            // Visual size
            canvas.style.width = (viewport.width / 3.0) + 'px';
            canvas.style.height = (viewport.height / 3.0) + 'px';

            resetTransform();

            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            
            page.render(renderContext);
        });
    }

    function resetTransform() {
        currentScale = 1;
        const rect = canvas.getBoundingClientRect();
        const unscaledW = rect.width / currentScale;
        const unscaledH = rect.height / currentScale;
        currentX = (container.clientWidth - unscaledW) / 2;
        currentY = (container.clientHeight - unscaledH) / 2;
        updateTransform();
    }

    pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
        pdfDoc = pdfDoc_;
        renderPage(1); // Maps are usually 1 page
    });

    // --- Pinch to Zoom and Pan logic using Pointer Events ---
    let pointers = [];
    let initialPinchDistance = null;
    let initialScale = 1;
    let lastPanPos = null;

    function updateTransform() {
        canvas.style.transform = `translate(${currentX}px, ${currentY}px) scale(${currentScale})`;
    }

    function applyZoom(zoomFactor, centerX, centerY) {
        let newScale = currentScale * zoomFactor;
        newScale = Math.max(0.5, Math.min(newScale, 10)); // Maps allow more zoom (up to 10x)

        const rect = container.getBoundingClientRect();
        const relX = centerX - rect.left;
        const relY = centerY - rect.top;

        const scaleRatio = newScale / currentScale;
        currentX = relX - (relX - currentX) * scaleRatio;
        currentY = relY - (relY - currentY) * scaleRatio;
        
        currentScale = newScale;
        requestAnimationFrame(updateTransform);
    }

    // Button controls
    document.getElementById('rotate-btn').addEventListener('click', () => {
        currentRotation = (currentRotation + 90) % 360;
        renderPage(1);
    });

    document.getElementById('zoom-in').addEventListener('click', () => {
        const rect = container.getBoundingClientRect();
        applyZoom(1.2, rect.left + rect.width / 2, rect.top + rect.height / 2);
    });

    document.getElementById('zoom-out').addEventListener('click', () => {
        const rect = container.getBoundingClientRect();
        applyZoom(0.8, rect.left + rect.width / 2, rect.top + rect.height / 2);
    });

    document.getElementById('zoom-fit').addEventListener('click', () => {
        resetTransform();
    });

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
            newScale = Math.max(0.5, Math.min(newScale, 10)); 

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
        applyZoom(zoomFactor, e.clientX, e.clientY);
    }, { passive: false });

    container.addEventListener('touchstart', (e) => {
        e.preventDefault();
    }, { passive: false });

</script>

<?php include __DIR__ . '/../../includes/user_footer.php'; ?>
