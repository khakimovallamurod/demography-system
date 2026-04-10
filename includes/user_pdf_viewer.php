<?php
$pdfViewerId = $pdf_viewer_id ?? ('pdf-viewer-' . random_int(1000, 999999));
$pdfViewerUrl = $pdf_url ?? '';
$pdfViewerTitle = $pdf_title ?? 'PDF Viewer';
$pdfViewerAccent = $pdf_accent ?? 'blue';
$pdfViewerOpenLabel = $pdf_open_label ?? 'Yangi oynada';
$pdfViewerDownloadLabel = $pdf_download_label ?? 'Yuklab olish';
$pdfViewerDownloadUrl = $pdf_download_url ?? $pdfViewerUrl;

$accentMap = [
    'blue' => [
        'panel' => 'from-slate-900 via-blue-950 to-slate-900',
        'badge' => 'bg-blue-500/15 text-blue-700 ring-1 ring-blue-100',
        'button' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-300',
        'buttonSoft' => 'bg-white text-slate-700 hover:bg-slate-50 focus:ring-slate-200',
        'loader' => 'border-blue-600',
        'pagePill' => 'bg-blue-50 text-blue-700',
    ],
    'emerald' => [
        'panel' => 'from-slate-900 via-emerald-950 to-slate-900',
        'badge' => 'bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-100',
        'button' => 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-300',
        'buttonSoft' => 'bg-white text-slate-700 hover:bg-slate-50 focus:ring-slate-200',
        'loader' => 'border-emerald-600',
        'pagePill' => 'bg-emerald-50 text-emerald-700',
    ],
    'indigo' => [
        'panel' => 'from-slate-900 via-indigo-950 to-slate-900',
        'badge' => 'bg-indigo-500/15 text-indigo-700 ring-1 ring-indigo-100',
        'button' => 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-300',
        'buttonSoft' => 'bg-white text-slate-700 hover:bg-slate-50 focus:ring-slate-200',
        'loader' => 'border-indigo-600',
        'pagePill' => 'bg-indigo-50 text-indigo-700',
    ],
];

$viewerTheme = $accentMap[$pdfViewerAccent] ?? $accentMap['blue'];
?>

<section
    id="<?= h($pdfViewerId) ?>"
    data-pdf-viewer
    data-pdf-url="<?= h($pdfViewerUrl) ?>"
    class="bg-white rounded-[28px] border border-gray-200 shadow-[0_20px_60px_rgba(15,23,42,0.08)] overflow-hidden"
>
    <div class="bg-gradient-to-r <?= h($viewerTheme['panel']) ?> px-4 py-4 sm:px-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <div class="flex items-center gap-2 text-white/80 text-xs uppercase tracking-[0.28em]">
                    <i class="fas fa-file-pdf text-red-300"></i>
                    Interactive Viewer
                </div>
                <h3 class="text-white font-semibold text-base sm:text-lg truncate mt-1"><?= h($pdfViewerTitle) ?></h3>
                <p class="text-white/60 text-xs sm:text-sm mt-1">Zoom, scroll, touch va sahifa boshqaruvi qo‘llab-quvvatlanadi.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold <?= h($viewerTheme['badge']) ?>">
                    <span class="w-2 h-2 rounded-full bg-current opacity-70"></span>
                    <span data-loading-text>Yuklanmoqda...</span>
                </span>
                <a
                    href="<?= h($pdfViewerUrl) ?>"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-sm font-medium transition focus:outline-none focus:ring-4 <?= h($viewerTheme['buttonSoft']) ?>"
                >
                    <i class="fas fa-up-right-from-square text-xs"></i>
                    <?= h($pdfViewerOpenLabel) ?>
                </a>
                <a
                    href="<?= h($pdfViewerDownloadUrl) ?>"
                    download
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-sm font-medium text-white transition focus:outline-none focus:ring-4 <?= h($viewerTheme['button']) ?>"
                >
                    <i class="fas fa-download text-xs"></i>
                    <?= h($pdfViewerDownloadLabel) ?>
                </a>
            </div>
        </div>
    </div>

    <div class="border-b border-gray-200 bg-gray-50/80 px-3 py-3 sm:px-4">
        <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" data-action="prev" class="inline-flex items-center gap-2 rounded-2xl bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-gray-200 transition hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 disabled:cursor-not-allowed disabled:opacity-50">
                    <i class="fas fa-chevron-left text-xs"></i>
                    Prev
                </button>
                <button type="button" data-action="next" class="inline-flex items-center gap-2 rounded-2xl bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-gray-200 transition hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 disabled:cursor-not-allowed disabled:opacity-50">
                    Next
                    <i class="fas fa-chevron-right text-xs"></i>
                </button>
                <div class="inline-flex items-center gap-2 rounded-2xl px-3 py-2 text-sm font-semibold <?= h($viewerTheme['pagePill']) ?>">
                    <i class="fas fa-book-open text-xs"></i>
                    <span data-page-indicator>0 / 0</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" data-action="zoom-out" class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-gray-200 transition hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200">
                    <i class="fas fa-minus"></i>
                </button>
                <div class="min-w-[88px] rounded-2xl bg-white px-4 py-2.5 text-center text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-200">
                    <span data-zoom-indicator>100%</span>
                </div>
                <button type="button" data-action="zoom-in" class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-gray-200 transition hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200">
                    <i class="fas fa-plus"></i>
                </button>
                <button type="button" data-action="fit-width" class="inline-flex items-center gap-2 rounded-2xl bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-gray-200 transition hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200">
                    <i class="fas fa-maximize text-xs"></i>
                    Fit Width
                </button>
            </div>
        </div>
    </div>

    <div class="relative bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.92),_rgba(241,245,249,0.94))]">
        <div data-loading-overlay class="absolute inset-0 z-20 flex flex-col items-center justify-center gap-4 bg-white/90 backdrop-blur-sm">
            <div class="w-14 h-14 rounded-full border-4 border-gray-200 border-t-transparent animate-spin <?= h($viewerTheme['loader']) ?>"></div>
            <div class="text-center px-6">
                <p class="text-sm font-semibold text-gray-700">PDF yuklanmoqda...</p>
                <p class="text-xs text-gray-500 mt-1">Sahifalar tayyor bo‘lishi uchun biroz kuting.</p>
            </div>
        </div>

        <div data-error-box class="hidden absolute inset-0 z-20 flex items-center justify-center p-6">
            <div class="max-w-md rounded-3xl border border-red-100 bg-white p-6 text-center shadow-xl">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-500">
                    <i class="fas fa-triangle-exclamation text-xl"></i>
                </div>
                <h4 class="text-base font-semibold text-gray-800">PDF ochilmadi</h4>
                <p class="mt-2 text-sm leading-6 text-gray-500">Faylni yuklashda muammo bo‘ldi. Quyidagi tugmalar orqali to‘g‘ridan-to‘g‘ri ochishingiz mumkin.</p>
            </div>
        </div>

        <div data-scroll-container class="relative overflow-auto px-3 py-4 sm:px-6" style="height: min(78vh, 980px); touch-action: pan-x pan-y;">
            <div data-canvas-stack class="mx-auto flex max-w-5xl flex-col gap-5 pb-6"></div>
        </div>
    </div>
</section>

<script type="module">
import * as pdfjsLib from 'https://cdn.jsdelivr.net/npm/pdfjs-dist@5.4.624/build/pdf.min.mjs';

pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@5.4.624/build/pdf.worker.min.mjs';

window.pdfViewers = window.pdfViewers || {};

const container = document.getElementById(<?= json_encode($pdfViewerId) ?>);

if (container) {
    const scrollContainer = container.querySelector('[data-scroll-container]');
    const canvasStack = container.querySelector('[data-canvas-stack]');
    const loadingOverlay = container.querySelector('[data-loading-overlay]');
    const errorBox = container.querySelector('[data-error-box]');
    const pageIndicator = container.querySelector('[data-page-indicator]');
    const zoomIndicator = container.querySelector('[data-zoom-indicator]');
    const loadingText = container.querySelector('[data-loading-text]');
    const buttons = {
        prev: container.querySelector('[data-action="prev"]'),
        next: container.querySelector('[data-action="next"]'),
        zoomIn: container.querySelector('[data-action="zoom-in"]'),
        zoomOut: container.querySelector('[data-action="zoom-out"]'),
        fitWidth: container.querySelector('[data-action="fit-width"]'),
    };

    const state = {
        pdfDoc: null,
        pageCount: 0,
        currentPage: 1,
        scale: 1.15,
        minScale: 0.6,
        maxScale: 3,
        rendering: false,
        rerenderPending: false,
        pageWrappers: [],
        resizeTimer: null,
        pinchDistance: null,
        pinchStartScale: 1.15,
        pinchPreviewScale: 1.15,
        observer: null,
        initialized: false,
    };

    const updateButtons = () => {
        buttons.prev.disabled = state.currentPage <= 1;
        buttons.next.disabled = state.currentPage >= state.pageCount;
        pageIndicator.textContent = `${state.currentPage} / ${state.pageCount || 0}`;
        zoomIndicator.textContent = `${Math.round(state.scale * 100)}%`;
    };

    const setLoading = (loading, text = 'Yuklanmoqda...') => {
        loadingOverlay.classList.toggle('hidden', !loading);
        loadingText.textContent = text;
    };

    const setError = (show) => {
        errorBox.classList.toggle('hidden', !show);
    };

    const waitForVisibleWidth = async () => {
        if (scrollContainer.clientWidth > 0) {
            return;
        }

        await new Promise((resolve) => {
            let tries = 0;
            const timer = window.setInterval(() => {
                tries += 1;
                if (scrollContainer.clientWidth > 0 || tries > 40) {
                    window.clearInterval(timer);
                    resolve();
                }
            }, 120);
        });
    };

    const buildPageShell = (pageNumber) => {
        const wrapper = document.createElement('article');
        wrapper.className = 'pdf-page-wrapper mx-auto w-full rounded-[26px] bg-white shadow-[0_18px_50px_rgba(15,23,42,0.08)] ring-1 ring-slate-200/80 overflow-hidden';
        wrapper.dataset.pageNumber = String(pageNumber);

        const meta = document.createElement('div');
        meta.className = 'flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-4 py-3 text-xs text-slate-500';
        meta.innerHTML = `<span class="font-semibold text-slate-700">Sahifa ${pageNumber}</span><span>High quality canvas render</span>`;

        const canvasHolder = document.createElement('div');
        canvasHolder.className = 'overflow-auto bg-[linear-gradient(180deg,_#f8fafc_0%,_#eef2ff_100%)] p-2 sm:p-4';

        const canvas = document.createElement('canvas');
        canvas.className = 'mx-auto block h-auto max-w-full rounded-2xl bg-white shadow-sm';
        canvasHolder.appendChild(canvas);

        wrapper.appendChild(meta);
        wrapper.appendChild(canvasHolder);
        canvasStack.appendChild(wrapper);

        return { wrapper, canvas };
    };

    const observePages = () => {
        if (state.observer) {
            state.observer.disconnect();
        }

        state.observer = new IntersectionObserver((entries) => {
            const visible = entries
                .filter((entry) => entry.isIntersecting)
                .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

            if (visible) {
                state.currentPage = Number(visible.target.dataset.pageNumber || 1);
                updateButtons();
            }
        }, {
            root: scrollContainer,
            threshold: [0.25, 0.5, 0.75],
        });

        state.pageWrappers.forEach((item) => state.observer.observe(item.wrapper));
    };

    const renderAllPages = async () => {
        if (!state.pdfDoc) {
            return;
        }

        if (state.rendering) {
            state.rerenderPending = true;
            return;
        }

        state.rendering = true;
        setLoading(true, 'Sahifalar yangilanmoqda...');

        try {
            await waitForVisibleWidth();

            canvasStack.innerHTML = '';
            state.pageWrappers = [];

            const pixelRatio = Math.max(window.devicePixelRatio || 1, 1);
            const availableWidth = Math.max(Math.min(scrollContainer.clientWidth - 32, 1200), 280);

            for (let pageNumber = 1; pageNumber <= state.pageCount; pageNumber += 1) {
                const page = await state.pdfDoc.getPage(pageNumber);
                const baseViewport = page.getViewport({ scale: 1 });
                const fitScale = availableWidth / baseViewport.width;
                const viewport = page.getViewport({ scale: fitScale * state.scale });
                const { wrapper, canvas } = buildPageShell(pageNumber);
                const context = canvas.getContext('2d', { alpha: false });

                canvas.width = Math.floor(viewport.width * pixelRatio);
                canvas.height = Math.floor(viewport.height * pixelRatio);
                canvas.style.width = `${Math.floor(viewport.width)}px`;
                canvas.style.height = `${Math.floor(viewport.height)}px`;

                context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
                context.imageSmoothingEnabled = true;
                context.imageSmoothingQuality = 'high';

                await page.render({
                    canvasContext: context,
                    viewport,
                }).promise;

                state.pageWrappers.push({ wrapper, canvas });
            }

            observePages();
            updateButtons();
            setLoading(false);
        } catch (error) {
            console.error('PDF render error:', error);
            setLoading(false);
            setError(true);
        } finally {
            state.rendering = false;

            if (state.rerenderPending) {
                state.rerenderPending = false;
                renderAllPages();
            }
        }
    };

    const goToPage = (pageNumber) => {
        const safePage = Math.min(Math.max(pageNumber, 1), state.pageCount || 1);
        const target = state.pageWrappers.find((item) => Number(item.wrapper.dataset.pageNumber) === safePage);
        if (!target) {
            return;
        }

        state.currentPage = safePage;
        updateButtons();
        target.wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    const setScale = (nextScale) => {
        const clamped = Math.min(Math.max(nextScale, state.minScale), state.maxScale);
        if (Math.abs(clamped - state.scale) < 0.01) {
            updateButtons();
            return;
        }

        state.scale = clamped;
        updateButtons();
        renderAllPages();
    };

    const fitWidth = () => {
        state.scale = 1;
        updateButtons();
        renderAllPages();
    };

    const init = async () => {
        if (state.initialized) {
            return;
        }

        state.initialized = true;
        setError(false);
        setLoading(true, 'PDF fayl yuklanmoqda...');

        try {
            const loadingTask = pdfjsLib.getDocument({
                url: container.dataset.pdfUrl,
                withCredentials: false,
                cMapPacked: true,
            });

            state.pdfDoc = await loadingTask.promise;
            state.pageCount = state.pdfDoc.numPages;
            state.currentPage = 1;
            updateButtons();
            await renderAllPages();
        } catch (error) {
            console.error('PDF load error:', error);
            setLoading(false);
            setError(true);
            loadingText.textContent = 'Yuklashda xatolik';
        }
    };

    buttons.prev.addEventListener('click', () => goToPage(state.currentPage - 1));
    buttons.next.addEventListener('click', () => goToPage(state.currentPage + 1));
    buttons.zoomIn.addEventListener('click', () => setScale(state.scale + 0.15));
    buttons.zoomOut.addEventListener('click', () => setScale(state.scale - 0.15));
    buttons.fitWidth.addEventListener('click', fitWidth);

    scrollContainer.addEventListener('wheel', (event) => {
        if (!(event.ctrlKey || event.metaKey)) {
            return;
        }

        event.preventDefault();
        setScale(state.scale + (event.deltaY < 0 ? 0.1 : -0.1));
    }, { passive: false });

    scrollContainer.addEventListener('touchstart', (event) => {
        if (event.touches.length !== 2) {
            return;
        }

        const [a, b] = event.touches;
        state.pinchDistance = Math.hypot(a.clientX - b.clientX, a.clientY - b.clientY);
        state.pinchStartScale = state.scale;
        state.pinchPreviewScale = state.scale;
    }, { passive: true });

    scrollContainer.addEventListener('touchmove', (event) => {
        if (event.touches.length !== 2 || !state.pinchDistance) {
            return;
        }

        event.preventDefault();
        const [a, b] = event.touches;
        const currentDistance = Math.hypot(a.clientX - b.clientX, a.clientY - b.clientY);
        const ratio = currentDistance / state.pinchDistance;
        state.pinchPreviewScale = Math.min(Math.max(state.pinchStartScale * ratio, state.minScale), state.maxScale);
        zoomIndicator.textContent = `${Math.round(state.pinchPreviewScale * 100)}%`;
    }, { passive: false });

    scrollContainer.addEventListener('touchend', () => {
        if (!state.pinchDistance) {
            return;
        }

        const finalScale = state.pinchPreviewScale;
        state.pinchDistance = null;
        setScale(finalScale);
    });

    window.addEventListener('resize', () => {
        if (!state.pdfDoc) {
            return;
        }

        window.clearTimeout(state.resizeTimer);
        state.resizeTimer = window.setTimeout(() => {
            renderAllPages();
        }, 180);
    });

    window.pdfViewers[<?= json_encode($pdfViewerId) ?>] = {
        init,
        refresh: () => {
            if (!state.pdfDoc) {
                init();
                return;
            }
            renderAllPages();
        },
        goToPage,
    };

    init();
}
</script>
