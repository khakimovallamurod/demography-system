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
    "WHERE user_id = {$_SESSION['user_id']} AND completed_at IS NOT NULL ORDER BY completed_at DESC LIMIT 5");

$total_tests_taken  = count($user_results);
$avg_score = 0;
if ($total_tests_taken > 0) {
    $sum = array_sum(array_map(fn($r) => $r['total'] > 0 ? ($r['score'] / $r['total'] * 100) : 0, $user_results));
    $avg_score = round($sum / $total_tests_taken, 1);
}

include __DIR__ . '/../includes/user_header.php';
?>

<div class="flex flex-col xl:flex-row gap-5">

    <!-- ===== LEFT: Main content ===== -->
    <div class="flex-1 min-w-0">

        <!-- Welcome breadcrumb -->
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
            <i class="fas fa-home text-emerald-500"></i>
            <span>Bosh sahifa</span>
            <i class="fas fa-chevron-right text-gray-300"></i>
            <span class="text-gray-600">Platform katalogi</span>
        </div>

        <!-- Map Hero Area -->
        <div class="relative rounded-2xl overflow-hidden mb-5"
             style="background: linear-gradient(135deg, #EEF7FF 0%, #D9EDF7 40%, #E8F5F0 100%); min-height: 380px;">

            <!-- Grid overlay for map effect -->
            <svg class="absolute inset-0 w-full h-full opacity-[0.06]" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#1d4ed8" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)"/>
            </svg>

            <!-- Decorative globe circles -->
            <div class="absolute right-8 top-1/2 -translate-y-1/2 hidden sm:block">
                <div class="w-52 h-52 rounded-full border-2 border-blue-200/40 flex items-center justify-center">
                    <div class="w-36 h-36 rounded-full border-2 border-blue-200/30 flex items-center justify-center">
                        <div class="w-20 h-20 rounded-full bg-blue-100/50 flex items-center justify-center">
                            <i class="fas fa-globe text-blue-400 text-3xl opacity-60"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Continent-like decorative blobs -->
            <div class="absolute top-6 left-1/3 w-24 h-14 bg-green-200/30 rounded-[40%_60%_55%_45%/40%_50%_60%_50%]"></div>
            <div class="absolute bottom-16 left-1/4 w-16 h-10 bg-yellow-200/25 rounded-[55%_45%_60%_40%/45%_55%_45%_55%]"></div>
            <div class="absolute top-1/3 right-1/3 w-12 h-8 bg-orange-200/25 rounded-[60%_40%/50%_50%]"></div>

            <!-- 6 Section Cards Grid -->
            <div class="relative z-10 p-5 grid grid-cols-2 lg:grid-cols-3 gap-3">

                <!-- Ma'ruzalar -->
                <a href="<?= BASE_URL ?>/user/lectures/index.php"
                   class="card-hover bg-white/90 backdrop-blur rounded-2xl p-4 border border-white shadow-sm block group">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-600 transition">
                            <i class="fas fa-book-open text-blue-600 group-hover:text-white transition"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-800 text-sm leading-tight">Ma'ruza mavzulari</h3>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mb-3 line-clamp-2">Barcha ma'ruza mavzularini o'qing va o'rganing</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full">
                            <?= $lecture_count ?> ta mavzu
                        </span>
                        <span class="text-xs text-white bg-blue-500 group-hover:bg-blue-700 px-3 py-1 rounded-full transition font-medium">
                            Ko'rish
                        </span>
                    </div>
                </a>

                <!-- Amaliy mashg'ulotlar -->
                <a href="<?= BASE_URL ?>/user/practicals/index.php"
                   class="card-hover bg-white/90 backdrop-blur rounded-2xl p-4 border border-white shadow-sm block group">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center group-hover:bg-amber-500 transition">
                            <i class="fas fa-flask text-amber-500 group-hover:text-white transition"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-800 text-sm leading-tight">Amaliy mashg'ulotlar</h3>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mb-3 line-clamp-2">Amaliy vazifalar va mashqlarni bajaring</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold bg-amber-50 text-amber-600 px-2.5 py-1 rounded-full">
                            <?= $practical_count ?> ta
                        </span>
                        <span class="text-xs text-white bg-amber-500 group-hover:bg-amber-600 px-3 py-1 rounded-full transition font-medium">
                            Ko'rish
                        </span>
                    </div>
                </a>

                <!-- Testlar -->
                <a href="<?= BASE_URL ?>/user/tests/index.php"
                   class="card-hover bg-white/90 backdrop-blur rounded-2xl p-4 border border-white shadow-sm block group">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center group-hover:bg-red-500 transition">
                            <i class="fas fa-clipboard-check text-red-500 group-hover:text-white transition"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-800 text-sm leading-tight">Testlar</h3>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mb-3 line-clamp-2">Bilimingizni test orqali tekshiring</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold bg-red-50 text-red-500 px-2.5 py-1 rounded-full">
                            <?= $test_count ?> ta test
                        </span>
                        <span class="text-xs text-white bg-red-500 group-hover:bg-red-600 px-3 py-1 rounded-full transition font-medium">
                            Boshlash
                        </span>
                    </div>
                </a>

                <!-- Xaritalar -->
                <a href="<?= BASE_URL ?>/user/maps/index.php"
                   class="card-hover bg-white/90 backdrop-blur rounded-2xl p-4 border border-white shadow-sm block group">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-600 transition">
                            <i class="fas fa-map-marked-alt text-indigo-600 group-hover:text-white transition"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-800 text-sm leading-tight">Xaritalar</h3>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mb-3 line-clamp-2">Demografik xaritalarni PDF ko'rinishida oching</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-full">
                            <?= $map_count ?> ta xarita
                        </span>
                        <span class="text-xs text-white bg-indigo-500 group-hover:bg-indigo-700 px-3 py-1 rounded-full transition font-medium">
                            Ko'rish
                        </span>
                    </div>
                </a>

                <!-- Raqamli kutubxona (UI only) -->
                <div class="bg-white/70 backdrop-blur rounded-2xl p-4 border border-white shadow-sm relative opacity-80">
                    <div class="absolute top-2.5 right-2.5 bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full">Tez orada</div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-pink-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-book text-pink-500"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-700 text-sm leading-tight">Raqamli kutubxona</h3>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mb-3">Kitoblar, maqolalar va elektron resurslar</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs bg-pink-50 text-pink-400 px-2.5 py-1 rounded-full">Resurslar</span>
                        <span class="text-xs text-gray-400 flex items-center gap-1">
                            <i class="fas fa-lock text-xs"></i> Yopiq
                        </span>
                    </div>
                </div>

                <!-- Demokalkulyator (UI only) -->
                <div class="bg-white/70 backdrop-blur rounded-2xl p-4 border border-white shadow-sm relative opacity-80">
                    <div class="absolute top-2.5 right-2.5 bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full">Tez orada</div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-cyan-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calculator text-cyan-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-700 text-sm leading-tight">Demokalkulyator</h3>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mb-3">Demografik ko'rsatkichlarni hisoblash</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs bg-cyan-50 text-cyan-500 px-2.5 py-1 rounded-full">Hisoblash</span>
                        <span class="text-xs text-gray-400 flex items-center gap-1">
                            <i class="fas fa-lock text-xs"></i> Yopiq
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <div class="mt-4">
            <?php include __DIR__ . '/../includes/external_resource_cards.php'; ?>
        </div>
    </div>

    <!-- ===== RIGHT: Stats Panel ===== -->
    <div class="xl:w-72 xl:flex-shrink-0 space-y-4">

        <!-- Test ko'rsatkichlari -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-4 text-white">
                <h3 class="font-bold text-sm flex items-center gap-2">
                    <i class="fas fa-chart-bar"></i> Testlar ko'rsatkichlari
                </h3>
            </div>
            <div class="p-4 space-y-4">
                <!-- Circular progress -->
                <div class="flex items-center justify-center py-2">
                    <div class="relative w-28 h-28">
                        <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                            <circle cx="50" cy="50" r="42" fill="none" stroke="#147c5b" stroke-width="10"
                                stroke-dasharray="<?= round($avg_score / 100 * 263.9) ?> 263.9"
                                stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-bold text-gray-800"><?= $avg_score ?>%</span>
                            <span class="text-xs text-gray-400">o'rtacha</span>
                        </div>
                    </div>
                </div>

                <!-- Stats rows -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-blue-600 text-xs"></i>
                            </div>
                            <span class="text-xs text-gray-600">Jami testlar</span>
                        </div>
                        <span class="font-bold text-gray-800 text-sm"><?= $test_count ?></span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xs"></i>
                            </div>
                            <span class="text-xs text-gray-600">Ishlagan</span>
                        </div>
                        <span class="font-bold text-gray-800 text-sm"><?= $total_tests_taken ?></span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-percentage text-orange-500 text-xs"></i>
                            </div>
                            <span class="text-xs text-gray-600">O'rtacha ball</span>
                        </div>
                        <span class="font-bold text-<?= $avg_score >= 70 ? 'green' : ($avg_score >= 50 ? 'yellow' : 'red') ?>-600 text-sm"><?= $avg_score ?>%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content stats -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <h3 class="font-semibold text-sm text-gray-700 mb-3 flex items-center gap-2">
                <i class="fas fa-layer-group text-emerald-500"></i> Kontent
            </h3>
            <div class="space-y-2.5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-open text-blue-600 text-xs"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-600">Ma'ruzalar</span>
                            <span class="text-xs font-bold text-gray-700"><?= $lecture_count ?></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1">
                            <div class="bg-blue-500 h-1 rounded-full" style="width: <?= min(100, $lecture_count * 10) ?>%"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-flask text-amber-500 text-xs"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-600">Amaliylar</span>
                            <span class="text-xs font-bold text-gray-700"><?= $practical_count ?></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1">
                            <div class="bg-amber-400 h-1 rounded-full" style="width: <?= min(100, $practical_count * 10) ?>%"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-map text-indigo-500 text-xs"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-600">Xaritalar</span>
                            <span class="text-xs font-bold text-gray-700"><?= $map_count ?></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1">
                            <div class="bg-indigo-500 h-1 rounded-full" style="width: <?= min(100, $map_count * 10) ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Results -->
        <?php if (!empty($user_results)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-sm text-gray-700">So'nggi natijalar</h3>
                <a href="<?= BASE_URL ?>/user/tests/index.php" class="text-xs text-emerald-600 hover:underline">Barchasi</a>
            </div>
            <div class="divide-y divide-gray-50">
                <?php foreach (array_slice($user_results, 0, 4) as $r):
                    $pct = $r['total'] > 0 ? round($r['score']/$r['total']*100) : 0;
                    $res_test = $db->get_data_by_table('tests', ['id' => $r['test_id']]);
                ?>
                <div class="flex items-center gap-3 px-4 py-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-bold
                        <?= $pct>=70?'bg-green-100 text-green-600':($pct>=50?'bg-yellow-100 text-yellow-600':'bg-red-100 text-red-500') ?>">
                        <?= $pct ?>%
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-700 truncate"><?= h($res_test['title'] ?? 'Test') ?></p>
                        <p class="text-xs text-gray-400"><?= $r['score'] ?>/<?= $r['total'] ?> to'g'ri</p>
                    </div>
                    <a href="<?= BASE_URL ?>/user/tests/result.php?id=<?= $r['id'] ?>"
                       class="text-gray-300 hover:text-emerald-500 transition">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php include __DIR__ . '/../includes/user_footer.php'; ?>
