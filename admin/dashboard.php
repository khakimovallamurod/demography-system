<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Bosh sahifa';

$total_lectures   = count($db->get_data_by_table_all('lectures'));
$total_practicals = count($db->get_data_by_table_all('practicals'));
$total_tests      = count($db->get_data_by_table_all('tests'));
$total_users      = count($db->get_data_by_table_all('users', 'WHERE role = "user"'));
$external_resources = get_dashboard_external_resources();

$all_test_results = [];
$resultsQuery = $db->query("
    SELECT tr.id, tr.score, tr.total, tr.completed_at,
           u.full_name, u.username, t.title AS test_title
    FROM test_results tr
    INNER JOIN users u ON u.id = tr.user_id
    INNER JOIN tests t ON t.id = tr.test_id
    WHERE tr.completed_at IS NOT NULL
    ORDER BY tr.completed_at DESC
");
if ($resultsQuery) {
    while ($row = mysqli_fetch_assoc($resultsQuery)) {
        $all_test_results[] = $row;
    }
}

$max_val = max($total_lectures, $total_practicals, $total_tests, 1);
$lec_pct = min(100, round($total_lectures / $max_val * 100));
$prc_pct = min(100, round($total_practicals / $max_val * 100));
$tst_pct = min(100, round($total_tests / $max_val * 100));

/*
 * CARD RASMLAR — quyidagi papkaga joylang:
 *   /demography-system/assets/images/cards/
 *
 *   card-lectures.png        — Ma'ruza mavzulari
 *   card-practicals.png      — Amaliy mashg'ulotlar
 *   card-library.png         — Raqamli kutubxona
 *   card-glossary.png        — Glossary
 *   card-maps.png            — Xaritalar
 *   card-lab.png             — Aholishunoslik laboratoriyasi
 *   card-tests.png           — Testlar
 *   card-results.png         — Test natijalari
 *   card-report.png          — Hisobot
 *
 * BANNER RASM:
 *   /demography-system/assets/images/dashboard-banner.png — Xush kelibsiz! banneri rasmi
 *
 * Rasm o'lchamlari: 480x200 px yoki undan katta (16:7 nisbat)
 */

include __DIR__ . '/../includes/admin_header.php';

/* Helper: card rasm yoki fallback placeholder */
function card_img(string $filename, string $alt, string $fallback_class, string $fallback_icon): string {
    $path = BASE_URL . '/assets/images/cards/' . $filename;
    $real = __DIR__ . '/../assets/images/cards/' . $filename;
    if (file_exists($real)) {
        return '<img src="' . $path . '" alt="' . h($alt) . '" class="w-full h-full object-contain p-2">';
    }
    return '<div class="w-full h-full flex flex-col items-center justify-center gap-2 ' . $fallback_class . '">
                <i class="' . $fallback_icon . '" style="font-size:3rem; opacity:.55;"></i>
                <span class="text-xs font-mono opacity-60">assets/images/cards/' . $filename . '</span>
            </div>';
}
?>

<div class="flex gap-5 items-start">

    <!-- Main content -->
    <div class="flex-1 min-w-0 space-y-5">

        <!-- Welcome banner -->
        <div class="rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-4 p-6 md:p-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Xush kelibsiz!</h2>
                    <p class="text-gray-500 text-sm md:text-base max-w-md">
                        <?= SITE_NAME ?> platformasining admin paneliga xush kelibsiz.<br>
                        Kontent va foydalanuvchilarni boshqaring.
                    </p>
                </div>
                <!-- Banner illustration -->
                <div class="hidden sm:block w-40 md:w-52 h-28 md:h-36 rounded-xl overflow-hidden flex-shrink-0 border border-gray-100 bg-blue-50">
                    <?php
                    $bannerReal = __DIR__ . '/../assets/images/dashboard-banner.png';
                    if (file_exists($bannerReal)): ?>
                    <img src="<?= BASE_URL ?>/assets/images/dashboard-banner.png"
                         alt="Banner" class="w-full h-full object-contain p-1">
                    <?php else: ?>
                    <div class="w-full h-full flex flex-col items-center justify-center text-blue-300 gap-1">
                        <i class="fas fa-image text-3xl opacity-50"></i>
                        <span class="text-xs font-mono text-center px-2 opacity-60">assets/images/<br>dashboard-banner.png</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 6 Feature Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            <!-- 1. Ma'ruzalar -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="h-36 overflow-hidden relative">
                    <?= card_img('card-lectures.png', "Ma'ruzalar", 'bg-blue-50 text-blue-400', 'fas fa-book-open') ?>
                    <div class="absolute top-3 right-3 bg-blue-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                        <?= $total_lectures ?> ta
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-800 text-sm mb-1">Ma'ruza mavzulari va testlar</h3>
                    <p class="text-xs text-gray-500 mb-3">Ma'ruza mavzulari, testlar va o'quv materiallari</p>
                    <a href="<?= BASE_URL ?>/admin/lectures/index.php"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 border border-blue-200 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition">
                        Ko'rish <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- 2. Amaliy mashg'ulotlar -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="h-36 overflow-hidden relative">
                    <?= card_img('card-practicals.png', "Amaliy mashg'ulotlar", 'bg-emerald-50 text-emerald-400', 'fas fa-chalkboard-teacher') ?>
                    <div class="absolute top-3 right-3 bg-emerald-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                        <?= $total_practicals ?> ta
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-800 text-sm mb-1">Amaliy mashg'ulotlar</h3>
                    <p class="text-xs text-gray-500 mb-3">Topshiriqlar va metodik ko'rsatmalar</p>
                    <a href="<?= BASE_URL ?>/admin/practicals/index.php"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 border border-emerald-200 hover:bg-emerald-50 px-3 py-1.5 rounded-lg transition">
                        Ko'rish <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- 3. Raqamli kutubxona -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="h-36 overflow-hidden relative">
                    <?= card_img('card-library.png', "Raqamli kutubxona", 'bg-orange-50 text-orange-400', 'fas fa-book-reader') ?>
                    <div class="absolute top-3 right-3 bg-orange-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                        Kutubxona
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-800 text-sm mb-1">Raqamli kutubxona</h3>
                    <p class="text-xs text-gray-500 mb-3">Darslik, o'quv va uslubiy qo'llanmalar</p>
                    <a href="<?= BASE_URL ?>/admin/library/index.php"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-orange-600 border border-orange-200 hover:bg-orange-50 px-3 py-1.5 rounded-lg transition">
                        Ko'rish <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- 4. Glossary -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="h-36 overflow-hidden relative">
                    <?= card_img('card-glossary.png', "Glossary", 'bg-amber-50 text-amber-400', 'fas fa-spell-check') ?>
                    <div class="absolute top-3 right-3 bg-amber-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                        Lug'at
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-800 text-sm mb-1">Glossary</h3>
                    <p class="text-xs text-gray-500 mb-3">Atama va tushunchalarning izohli lug'ati</p>
                    <a href="<?= BASE_URL ?>/admin/glossary/index.php"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-600 border border-amber-200 hover:bg-amber-50 px-3 py-1.5 rounded-lg transition">
                        Ko'rish <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- 5. Xaritalar -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="h-36 overflow-hidden relative">
                    <?= card_img('card-maps.png', "Xaritalar", 'bg-indigo-50 text-indigo-400', 'fas fa-map-marked-alt') ?>
                    <div class="absolute top-3 right-3 bg-indigo-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                        Xarita
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-800 text-sm mb-1">Xaritalar</h3>
                    <p class="text-xs text-gray-500 mb-3">Interaktiv xaritalar va demografik ma'lumotlar</p>
                    <a href="<?= BASE_URL ?>/admin/maps/index.php"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 border border-indigo-200 hover:bg-indigo-50 px-3 py-1.5 rounded-lg transition">
                        Ko'rish <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- 6. Aholishunoslik laboratoriyasi -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="h-36 overflow-hidden relative">
                    <?= card_img('card-lab.png', "Aholishunoslik laboratoriyasi", 'bg-teal-50 text-teal-400', 'fas fa-microscope') ?>
                    <div class="absolute top-3 right-3 bg-teal-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                        Lab
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-800 text-sm mb-1">Aholishunoslik laboratoriyasi</h3>
                    <p class="text-xs text-gray-500 mb-3">Ilmiy resurslar, videolar, demografik saytlar</p>
                    <a href="<?= BASE_URL ?>/admin/laboratory/index.php"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-600 border border-teal-200 hover:bg-teal-50 px-3 py-1.5 rounded-lg transition">
                        Ko'rish <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- 7. Testlar -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="h-36 overflow-hidden relative">
                    <?= card_img('card-tests.png', "Testlar", 'bg-violet-50 text-violet-400', 'fas fa-clipboard-list') ?>
                    <div class="absolute top-3 right-3 bg-violet-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                        <?= $total_tests ?> ta
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-800 text-sm mb-1">Testlar</h3>
                    <p class="text-xs text-gray-500 mb-3">Baholash uchun test topshiriqlari va savollar</p>
                    <a href="<?= BASE_URL ?>/admin/tests/index.php"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-violet-600 border border-violet-200 hover:bg-violet-50 px-3 py-1.5 rounded-lg transition">
                        Ko'rish <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- 8. Test natijalari -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="h-36 overflow-hidden relative">
                    <?= card_img('card-results.png', "Test natijalari", 'bg-sky-50 text-sky-400', 'fas fa-poll') ?>
                    <div class="absolute top-3 right-3 bg-sky-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                        <?= count($all_test_results) ?> ta
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-800 text-sm mb-1">Test natijalari</h3>
                    <p class="text-xs text-gray-500 mb-3">Foydalanuvchilar topshirgan testlar va ballar</p>
                    <a href="<?= BASE_URL ?>/admin/test-results/index.php"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-sky-600 border border-sky-200 hover:bg-sky-50 px-3 py-1.5 rounded-lg transition">
                        Ko'rish <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- 9. Hisobot -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="h-36 overflow-hidden relative">
                    <?= card_img('card-report.png', "Hisobot", 'bg-rose-50 text-rose-400', 'fas fa-chart-line') ?>
                    <div class="absolute top-3 right-3 bg-rose-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                        Hisobot
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-800 text-sm mb-1">Hisobot</h3>
                    <p class="text-xs text-gray-500 mb-3">Tizim faoliyati bo'yicha umumiy hisobotlar</p>
                    <a href="<?= BASE_URL ?>/admin/reports/index.php"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 border border-rose-200 hover:bg-rose-50 px-3 py-1.5 rounded-lg transition">
                        Ko'rish <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

        </div>

        <!-- External platforms -->
        <div>
            <h3 class="text-sm font-bold text-gray-700 mb-3">Tashqi platformalar bilan integratsiya</h3>
            <?php include __DIR__ . '/../includes/external_resource_cards.php'; ?>
        </div>

    </div>

    <!-- Right panel -->
    <div class="w-56 xl:w-64 flex-shrink-0 hidden lg:block space-y-4">

        <!-- Ko'rsatkichlar -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Tizim ko'rsatkichlari</h3>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-book-open text-blue-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-medium text-gray-600">Ma'ruzalar</span>
                            <span class="text-xs font-bold text-blue-600"><?= $lec_pct ?>%</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width:<?= $lec_pct ?>%"></div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-chalkboard-teacher text-emerald-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-medium text-gray-600">Amaliy</span>
                            <span class="text-xs font-bold text-emerald-600"><?= $prc_pct ?>%</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width:<?= $prc_pct ?>%"></div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-violet-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clipboard-list text-violet-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-medium text-gray-600">Testlar</span>
                            <span class="text-xs font-bold text-violet-600"><?= $tst_pct ?>%</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-violet-500 rounded-full" style="width:<?= $tst_pct ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Umumiy statistika -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-3">Umumiy statistika</h3>
            <div class="space-y-2.5">
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-xs text-gray-500">Ma'ruzalar</span>
                    <span class="text-sm font-bold text-blue-600"><?= $total_lectures ?></span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-xs text-gray-500">Amaliy mashg'ulotlar</span>
                    <span class="text-sm font-bold text-emerald-600"><?= $total_practicals ?></span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-xs text-gray-500">Testlar</span>
                    <span class="text-sm font-bold text-violet-600"><?= $total_tests ?></span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-xs text-gray-500">Foydalanuvchilar</span>
                    <span class="text-sm font-bold text-pink-600"><?= $total_users ?></span>
                </div>
            </div>
        </div>

        <!-- So'nggi natijalar -->
        <?php if (!empty($all_test_results)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-gray-700">So'nggi natijalar</h3>
                <a href="<?= BASE_URL ?>/admin/test-results/index.php" class="text-xs text-blue-600 hover:underline">Hammasi</a>
            </div>
            <div class="space-y-2.5">
                <?php foreach (array_slice($all_test_results, 0, 4) as $result): ?>
                <?php $pct = $result['total'] > 0 ? round($result['score'] / $result['total'] * 100) : 0; ?>
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0
                        <?= $pct >= 70 ? 'bg-emerald-100 text-emerald-700' : ($pct >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') ?>">
                        <?= $pct ?>%
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-700 truncate"><?= h($result['full_name']) ?></p>
                        <p class="text-xs text-gray-400 truncate"><?= h($result['test_title']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
