<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
if (is_admin()) redirect('/admin/dashboard.php');

$page_title = 'Bosh sahifa';

$lecture_count   = count($db->get_data_by_table_all('lectures'));
$practical_count = count($db->get_data_by_table_all('practicals'));
$test_count      = count($db->get_data_by_table_all('tests'));
$map_count       = count($db->get_data_by_table_all('maps'));
$external_resources = get_dashboard_external_resources();

$user_results = $db->get_data_by_table_all('test_results',
    "WHERE user_id = {$_SESSION['user_id']} AND completed_at IS NOT NULL ORDER BY completed_at DESC");

$total_tests_taken = count($user_results);
$avg_score = 0;
if ($total_tests_taken > 0) {
    $sum = array_sum(array_map(fn($r) => $r['total'] > 0 ? ($r['score'] / $r['total'] * 100) : 0, $user_results));
    $avg_score = round($sum / $total_tests_taken, 1);
}

/* Progress bar foizlar (rasmda o'zlashtirish ko'rsatkichlari) */
$max_val  = max($lecture_count, $practical_count, $test_count, 1);
$lec_pct  = min(100, round($lecture_count  / $max_val * 100));
$prc_pct  = min(100, round($practical_count / $max_val * 100));
$tst_pct  = min(100, round($test_count     / $max_val * 100));

/*
 * CARD RASMLAR — joylash yo'li: /demography-system/assets/images/cards/
 *   card-lectures.png     — Ma'ruza mavzulari
 *   card-practicals.png   — Amaliy mashg'ulotlar
 *   card-library.png      — Raqamli kutubxona
 *   card-glossary.png     — Glossary
 *   card-maps.png         — Xaritalar
 *   card-lab.png          — Aholishunoslik laboratoriyasi
 *   card-tests.png        — Testlar
 *   card-results.png      — Test natijalari
 *   card-report.png       — Hisobot
 *
 * BANNER: /demography-system/assets/images/dashboard-banner.png
 */

include __DIR__ . '/../includes/user_header.php';

function card_img_u(string $filename, string $alt, string $fallback_class, string $fallback_icon): string {
    $path = BASE_URL . '/assets/images/cards/' . $filename;
    $real = __DIR__ . '/../assets/images/cards/' . $filename;
    if (file_exists($real)) {
        return '<img src="' . $path . '" alt="' . h($alt) . '" class="w-full h-full object-contain p-2">';
    }
    return '<div class="w-full h-full flex flex-col items-center justify-center gap-1.5 ' . $fallback_class . '">
                <i class="' . $fallback_icon . '" style="font-size:3rem; opacity:.55;"></i>
                <span class="text-xs font-mono opacity-50 px-2 text-center">assets/images/cards/' . $filename . '</span>
            </div>';
}
?>

<div class="flex gap-5 items-start">

    <!-- Main content -->
    <div class="flex-1 min-w-0 space-y-5">

        <!-- Welcome banner -->
        <div class="rounded-3xl overflow-hidden border border-gray-800 shadow-lg relative">
            <div class="absolute inset-0 bg-gradient-to-br from-[#0f172a] via-[#1e293b] to-[#334155] z-0">
                <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 20% 150%, #38bdf8 0%, transparent 50%), radial-gradient(circle at 80% -50%, #818cf8 0%, transparent 50%);"></div>
            </div>
            <div class="flex items-center justify-between gap-4 p-8 md:p-10 relative z-10">
                <div>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-3 tracking-tight">Xush kelibsiz!</h2>
                    <p class="text-blue-100/80 text-sm md:text-base max-w-md leading-relaxed font-medium">
                        <?= SITE_NAME ?> platformasiga xush kelibsiz.<br>
                        O'rganish va bilimingizni sinash uchun bo'limlarni tanlang.
                    </p>
                </div>
                <div class="hidden md:flex w-28 h-28 rounded-3xl bg-white/10 backdrop-blur-md border border-white/20 items-center justify-center flex-shrink-0 shadow-[0_0_40px_rgba(56,189,248,0.15)]">
                    <i class="fas fa-graduation-cap text-5xl text-white drop-shadow-lg"></i>
                </div>
            </div>
        </div>

        <!-- 9 Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">

            <!-- 1. Ma'ruza mavzulari -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:-translate-y-1.5 hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300 group flex flex-col">
                <div class="h-40 relative overflow-hidden bg-blue-50/50">
                    <?= card_img_u('card-lectures.png', "Ma'ruzalar", 'bg-blue-50 text-blue-400', 'fas fa-book-open') ?>
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur text-blue-600 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm border border-blue-50">
                        <?= $lecture_count ?> ta
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="font-bold text-gray-800 text-[15px] mb-1.5 group-hover:text-blue-600 transition-colors">Ma'ruza mavzulari</h3>
                    <p class="text-xs text-gray-500 mb-5 leading-relaxed flex-1">O'quv materiallari, konspektlar va testlar to'plami.</p>
                    <a href="<?= BASE_URL ?>/user/lectures/index.php" class="inline-flex w-full items-center justify-center gap-2 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white px-4 py-2.5 rounded-xl transition-colors">
                        Kirish <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- 2. Amaliy mashg'ulotlar -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:-translate-y-1.5 hover:shadow-xl hover:shadow-emerald-500/10 transition-all duration-300 group flex flex-col">
                <div class="h-40 relative overflow-hidden bg-emerald-50/50">
                    <?= card_img_u('card-practicals.png', "Amaliy mashg'ulotlar", 'bg-emerald-50 text-emerald-400', 'fas fa-chalkboard-teacher') ?>
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur text-emerald-600 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm border border-emerald-50">
                        <?= $practical_count ?> ta
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="font-bold text-gray-800 text-[15px] mb-1.5 group-hover:text-emerald-600 transition-colors">Amaliy mashg'ulotlar</h3>
                    <p class="text-xs text-gray-500 mb-5 leading-relaxed flex-1">Mustaqil ishlash uchun topshiriqlar va uslubiy ko'rsatmalar.</p>
                    <a href="<?= BASE_URL ?>/user/practicals/index.php" class="inline-flex w-full items-center justify-center gap-2 text-xs font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-600 hover:text-white px-4 py-2.5 rounded-xl transition-colors">
                        Kirish <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- 3. Raqamli kutubxona -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:-translate-y-1.5 hover:shadow-xl hover:shadow-orange-500/10 transition-all duration-300 group flex flex-col">
                <div class="h-40 relative overflow-hidden bg-orange-50/50">
                    <?= card_img_u('card-library.png', "Raqamli kutubxona", 'bg-orange-50 text-orange-400', 'fas fa-book-reader') ?>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="font-bold text-gray-800 text-[15px] mb-1.5 group-hover:text-orange-600 transition-colors">Raqamli kutubxona</h3>
                    <p class="text-xs text-gray-500 mb-5 leading-relaxed flex-1">Barcha zarur elektron darslik va qo'llanmalar zaxirasi.</p>
                    <a href="<?= BASE_URL ?>/user/library/index.php" class="inline-flex w-full items-center justify-center gap-2 text-xs font-bold text-orange-600 bg-orange-50 hover:bg-orange-500 hover:text-white px-4 py-2.5 rounded-xl transition-colors">
                        Kirish <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- 4. Glossary -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:-translate-y-1.5 hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300 group flex flex-col">
                <div class="h-40 relative overflow-hidden bg-amber-50/50">
                    <?= card_img_u('card-glossary.png', "Glossary", 'bg-amber-50 text-amber-400', 'fas fa-spell-check') ?>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="font-bold text-gray-800 text-[15px] mb-1.5 group-hover:text-amber-600 transition-colors">Glossary (Lug'at)</h3>
                    <p class="text-xs text-gray-500 mb-5 leading-relaxed flex-1">Fan bo'yicha atama va tushunchalarning izohli lug'ati.</p>
                    <a href="<?= BASE_URL ?>/user/glossary/index.php" class="inline-flex w-full items-center justify-center gap-2 text-xs font-bold text-amber-600 bg-amber-50 hover:bg-amber-500 hover:text-white px-4 py-2.5 rounded-xl transition-colors">
                        Kirish <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- 5. Xaritalar -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:-translate-y-1.5 hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300 group flex flex-col">
                <div class="h-40 relative overflow-hidden bg-indigo-50/50">
                    <?= card_img_u('card-maps.png', "Xaritalar", 'bg-indigo-50 text-indigo-400', 'fas fa-map-marked-alt') ?>
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur text-indigo-600 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm border border-indigo-50">
                        <?= $map_count ?> ta
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="font-bold text-gray-800 text-[15px] mb-1.5 group-hover:text-indigo-600 transition-colors">Interaktiv xaritalar</h3>
                    <p class="text-xs text-gray-500 mb-5 leading-relaxed flex-1">Demografik ma'lumotlar va geografik joylashuvlar tahlili.</p>
                    <a href="<?= BASE_URL ?>/user/maps/index.php" class="inline-flex w-full items-center justify-center gap-2 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white px-4 py-2.5 rounded-xl transition-colors">
                        Kirish <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- 6. Laboratoriya -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:-translate-y-1.5 hover:shadow-xl hover:shadow-teal-500/10 transition-all duration-300 group flex flex-col">
                <div class="h-40 relative overflow-hidden bg-teal-50/50">
                    <?= card_img_u('card-lab.png', "Aholishunoslik laboratoriyasi", 'bg-teal-50 text-teal-400', 'fas fa-microscope') ?>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="font-bold text-gray-800 text-[15px] mb-1.5 group-hover:text-teal-600 transition-colors">Aholishunoslik Lab</h3>
                    <p class="text-xs text-gray-500 mb-5 leading-relaxed flex-1">Ilmiy resurslar, maxsus videolar va statistik portal qism.</p>
                    <a href="<?= BASE_URL ?>/user/laboratory/index.php" class="inline-flex w-full items-center justify-center gap-2 text-xs font-bold text-teal-600 bg-teal-50 hover:bg-teal-600 hover:text-white px-4 py-2.5 rounded-xl transition-colors">
                        Kirish <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- 7. Testlar -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:-translate-y-1.5 hover:shadow-xl hover:shadow-violet-500/10 transition-all duration-300 group flex flex-col">
                <div class="h-40 relative overflow-hidden bg-violet-50/50">
                    <?= card_img_u('card-tests.png', "Testlar", 'bg-violet-50 text-violet-400', 'fas fa-clipboard-list') ?>
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur text-violet-600 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm border border-violet-50">
                        <?= $test_count ?> ta
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="font-bold text-gray-800 text-[15px] mb-1.5 group-hover:text-violet-600 transition-colors">Mavzulashtirilgan Testlar</h3>
                    <p class="text-xs text-gray-500 mb-5 leading-relaxed flex-1">Bilimingizni sinash uchun maxsus tuzilgan nazorat testlari.</p>
                    <a href="<?= BASE_URL ?>/user/tests/index.php" class="inline-flex w-full items-center justify-center gap-2 text-xs font-bold text-white bg-violet-500 hover:bg-violet-600 px-4 py-2.5 rounded-xl transition-colors shadow-sm shadow-violet-200">
                        Boshlash <i class="fas fa-play text-xs ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- 8. Test natijalari -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:-translate-y-1.5 hover:shadow-xl hover:shadow-sky-500/10 transition-all duration-300 group flex flex-col">
                <div class="h-40 relative overflow-hidden bg-sky-50/50">
                    <?= card_img_u('card-results.png', "Test natijalari", 'bg-sky-50 text-sky-400', 'fas fa-poll') ?>
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur text-sky-600 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm border border-sky-50">
                        <?= $total_tests_taken ?> ta
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="font-bold text-gray-800 text-[15px] mb-1.5 group-hover:text-sky-600 transition-colors">Test natijalari</h3>
                    <p class="text-xs text-gray-500 mb-5 leading-relaxed flex-1">Oldin topshirilgan testlar hisoboti va xatolar tahlili.</p>
                    <a href="<?= BASE_URL ?>/user/tests/index.php" class="inline-flex w-full items-center justify-center gap-2 text-xs font-bold text-sky-600 bg-sky-50 hover:bg-sky-500 hover:text-white px-4 py-2.5 rounded-xl transition-colors">
                        Natijalarni ko'rish <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- 9. Hisobot -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:-translate-y-1.5 hover:shadow-xl hover:shadow-rose-500/10 transition-all duration-300 group flex flex-col">
                <div class="h-40 relative overflow-hidden bg-rose-50/50">
                    <?= card_img_u('card-report.png', "Hisobot", 'bg-rose-50 text-rose-400', 'fas fa-chart-line') ?>
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur text-rose-600 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm border border-rose-50">
                        <?= $avg_score ?>%
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="font-bold text-gray-800 text-[15px] mb-1.5 group-hover:text-rose-600 transition-colors">Reyting va Hisobot</h3>
                    <p class="text-xs text-gray-500 mb-5 leading-relaxed flex-1">O'zlashtirish ko'rsatkichlaringizning umumiy tahlili.</p>
                    <a href="<?= BASE_URL ?>/user/tests/index.php" class="inline-flex w-full items-center justify-center gap-2 text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-500 hover:text-white px-4 py-2.5 rounded-xl transition-colors">
                        Batafsil <i class="fas fa-arrow-right"></i>
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
    <div class="w-56 xl:w-[280px] flex-shrink-0 hidden lg:block space-y-5">

        <!-- O'zlashtirish ko'rsatkichlari -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/5 rounded-full blur-2xl"></div>
            <h3 class="text-[15px] font-bold text-gray-800 mb-5 flex items-center gap-2">
                <i class="fas fa-chart-pie text-blue-500"></i> Ko'rsatkichlar
            </h3>
            
            <div class="space-y-5 relative z-10">
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-xs font-semibold text-gray-600 flex items-center gap-1.5"><i class="fas fa-book-open text-blue-500 w-3"></i> Ma'ruzalar</span>
                        <span class="text-xs font-bold text-blue-600"><?= $lec_pct ?>%</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden shadow-inner">
                        <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full" style="width:<?= $lec_pct ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-xs font-semibold text-gray-600 flex items-center gap-1.5"><i class="fas fa-chalkboard-teacher text-emerald-500 w-3"></i> Amaliyotlar</span>
                        <span class="text-xs font-bold text-emerald-600"><?= $prc_pct ?>%</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden shadow-inner">
                        <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full" style="width:<?= $prc_pct ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-xs font-semibold text-gray-600 flex items-center gap-1.5"><i class="fas fa-clipboard-list text-violet-500 w-3"></i> Testlar</span>
                        <span class="text-xs font-bold text-violet-600"><?= $tst_pct ?>%</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden shadow-inner">
                        <div class="h-full bg-gradient-to-r from-violet-400 to-violet-600 rounded-full" style="width:<?= $tst_pct ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test statistikasi -->
        <div class="bg-gradient-to-br from-indigo-900 to-slate-800 rounded-3xl shadow-lg p-6 text-white relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
            <h3 class="text-[15px] font-bold mb-4 flex items-center gap-2">
                <i class="fas fa-star text-yellow-400"></i> Statistika
            </h3>
            <div class="space-y-3 relative z-10">
                <div class="flex items-center justify-between py-2 border-b border-white/10">
                    <span class="text-xs text-indigo-200">Jami testlar</span>
                    <span class="text-sm font-bold"><?= $test_count ?></span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-white/10">
                    <span class="text-xs text-indigo-200">Ishlagan testlar</span>
                    <span class="text-sm font-bold"><?= $total_tests_taken ?></span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-xs text-indigo-200">O'rtacha ball</span>
                    <span class="text-lg font-black <?= $avg_score >= 70 ? 'text-emerald-400' : ($avg_score >= 50 ? 'text-yellow-400' : 'text-rose-400') ?>">
                        <?= $avg_score ?>%
                    </span>
                </div>
            </div>
        </div>

        <!-- So'nggi natijalar -->
        <?php if (!empty($user_results)): ?>
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-[15px] font-bold text-gray-800 flex items-center gap-2"><i class="fas fa-history text-gray-400"></i> Tarix</h3>
                <a href="<?= BASE_URL ?>/user/tests/index.php" class="text-[11px] font-bold text-blue-600 hover:underline uppercase tracking-wider">Hammasi</a>
            </div>
            <div class="space-y-3">
                <?php foreach (array_slice($user_results, 0, 4) as $r):
                    $pct = $r['total'] > 0 ? round($r['score'] / $r['total'] * 100) : 0;
                    $res_test = $db->get_data_by_table('tests', ['id' => $r['test_id']]);
                ?>
                <div class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-xl transition cursor-pointer" onclick="window.location.href='<?= BASE_URL ?>/user/tests/result.php?id=<?= $r['id'] ?>'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0 border
                        <?= $pct >= 70 ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : ($pct >= 50 ? 'bg-yellow-50 text-yellow-600 border-yellow-100' : 'bg-rose-50 text-rose-600 border-rose-100') ?>">
                        <?= $pct ?>%
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-gray-700 truncate"><?= h($res_test['title'] ?? 'Test') ?></p>
                        <p class="text-[11px] text-gray-400 mt-0.5"><?= $r['score'] ?>/<?= $r['total'] ?> to'g'ri</p>
                    </div>
                    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php include __DIR__ . '/../includes/user_footer.php'; ?>
