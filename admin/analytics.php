<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'AI Tahlil';

$students = get_student_performance_summary();
$topStudents = array_slice($students, 0, 5);
$lowStudents = $students;
usort($lowStudents, function ($a, $b) {
    if ($a['avg_percent'] == $b['avg_percent']) {
        return $b['attempts'] <=> $a['attempts'];
    }
    return $a['avg_percent'] <=> $b['avg_percent'];
});
$lowStudents = array_slice($lowStudents, 0, 5);

$resultStatsQuery = $db->query("
    SELECT
        COUNT(*) AS total_attempts,
        AVG(CASE WHEN total > 0 THEN (score / total) * 100 END) AS avg_percent
    FROM test_results
    WHERE completed_at IS NOT NULL
");
$resultStats = $resultStatsQuery ? mysqli_fetch_assoc($resultStatsQuery) : ['total_attempts' => 0, 'avg_percent' => 0];

$activeStudents = count(array_filter($students, fn($student) => (int) $student['attempts'] > 0));
$excellentStudents = count(array_filter($students, fn($student) => (float) $student['avg_percent'] >= 70));
$riskStudents = count(array_filter($students, fn($student) => (float) $student['avg_percent'] > 0 && (float) $student['avg_percent'] < 50));

$aiSummary = 'AI tahlil hali faol emas';
if (gemini_is_enabled()) {
    $summaryPayload = [
        'top_students' => array_map(function ($student) {
            return [
                'full_name' => $student['full_name'],
                'username' => $student['username'],
                'attempts' => (int) $student['attempts'],
                'avg_percent' => (float) $student['avg_percent'],
            ];
        }, $topStudents),
        'low_students' => array_map(function ($student) {
            return [
                'full_name' => $student['full_name'],
                'username' => $student['username'],
                'attempts' => (int) $student['attempts'],
                'avg_percent' => (float) $student['avg_percent'],
            ];
        }, $lowStudents),
        'summary' => [
            'total_students' => count($students),
            'active_students' => $activeStudents,
            'excellent_students' => $excellentStudents,
            'risk_students' => $riskStudents,
            'total_attempts' => (int) ($resultStats['total_attempts'] ?? 0),
            'average_percent' => round((float) ($resultStats['avg_percent'] ?? 0), 1),
        ],
    ];

    $aiResult = gemini_generate_text(
        "Quyidagi student analytics ma'lumotlarini tahlil qil va 3-5 jumlada o'zbek tilida xulosa ber. "
        . "Kuchli o'quvchilar, xavfli guruh va admin uchun tavsiyani ayt.\n\n"
        . json_encode($summaryPayload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        [
            'system_instruction' => "Sen ta'lim tizimi uchun analytics yordamchisan. "
                . "Javobni aniq, qisqa va boshqaruv qarori chiqarishga qulay uslubda yoz.",
            'temperature' => 0.4,
            'max_output_tokens' => 600,
        ]
    );

    if ($aiResult['ok']) {
        $aiSummary = $aiResult['text'];
    }
}

function analytics_color_classes($percent) {
    if ($percent >= 70) {
        return ['pill' => 'bg-emerald-50 text-emerald-700', 'bar' => 'bg-emerald-500'];
    }
    if ($percent >= 50) {
        return ['pill' => 'bg-amber-50 text-amber-700', 'bar' => 'bg-amber-400'];
    }
    return ['pill' => 'bg-red-50 text-red-600', 'bar' => 'bg-red-500'];
}

include __DIR__ . '/../includes/admin_header.php';
?>

<div class="space-y-5">
    <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500">Jami talabalar</p>
            <p class="text-3xl font-bold text-gray-800 mt-2"><?= count($students) ?></p>
            <p class="text-xs text-gray-400 mt-2">Ro'yxatdan o'tgan userlar</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500">Faol talabalar</p>
            <p class="text-3xl font-bold text-emerald-600 mt-2"><?= $activeStudents ?></p>
            <p class="text-xs text-gray-400 mt-2">Kamida bitta test ishlaganlar</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500">O'rtacha ko'rsatkich</p>
            <p class="text-3xl font-bold text-amber-500 mt-2"><?= round((float) ($resultStats['avg_percent'] ?? 0), 1) ?>%</p>
            <p class="text-xs text-gray-400 mt-2">Barcha yakunlangan urinishlar bo'yicha</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500">Past natijali talabalar</p>
            <p class="text-3xl font-bold text-red-500 mt-2"><?= $riskStudents ?></p>
            <p class="text-xs text-gray-400 mt-2">50% dan past ko'rsatkich</p>
        </div>
    </div>

    <div class="grid xl:grid-cols-[1.2fr_0.8fr] gap-5">
        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-brain text-cyan-500"></i> AI xulosasi
                </h3>
                <span class="text-xs px-2.5 py-1 rounded-full <?= gemini_is_enabled() ? 'bg-cyan-50 text-cyan-700' : 'bg-gray-100 text-gray-500' ?>">
                    <?= gemini_is_enabled() ? 'Gemini faol' : 'Offline' ?>
                </span>
            </div>
            <div class="p-5">
                <div class="rounded-2xl bg-gradient-to-br from-slate-900 via-cyan-950 to-slate-900 p-[1px]">
                    <div class="rounded-[15px] bg-white px-5 py-5">
                        <p class="text-sm leading-7 text-gray-700 whitespace-pre-wrap"><?= h($aiSummary) ?></p>
                    </div>
                </div>
                <?php if (!gemini_is_enabled()): ?>
                <p class="mt-3 text-xs text-gray-400">AI tahlil hali faol emas</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-indigo-500"></i> Tezkor ko'rsatkichlar
                </h3>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                        <span>Yaxshi o'zlashtirayotganlar</span>
                        <span class="font-semibold text-emerald-600"><?= $excellentStudents ?></span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: <?= count($students) > 0 ? min(100, round($excellentStudents / count($students) * 100)) : 0 ?>%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                        <span>O'rta holatdagi talabalar</span>
                        <span class="font-semibold text-amber-500"><?= max(0, $activeStudents - $excellentStudents - $riskStudents) ?></span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full bg-amber-400 rounded-full" style="width: <?= count($students) > 0 ? min(100, round(max(0, $activeStudents - $excellentStudents - $riskStudents) / count($students) * 100)) : 0 ?>%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                        <span>Past ko'rsatkich</span>
                        <span class="font-semibold text-red-500"><?= $riskStudents ?></span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full bg-red-500 rounded-full" style="width: <?= count($students) > 0 ? min(100, round($riskStudents / count($students) * 100)) : 0 ?>%"></div>
                    </div>
                </div>
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Jami urinishlar</p>
                    <p class="mt-2 text-2xl font-bold text-gray-800"><?= (int) ($resultStats['total_attempts'] ?? 0) ?></p>
                </div>
            </div>
        </section>
    </div>

    <div class="grid xl:grid-cols-2 gap-5">
        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-trophy text-emerald-500"></i> Top students
                </h3>
                <span class="text-xs bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full">Yashil zona</span>
            </div>
            <div class="p-5 space-y-4">
                <?php if (empty($topStudents)): ?>
                <p class="text-sm text-gray-400">Hali ma'lumot yo'q</p>
                <?php else: ?>
                <?php foreach ($topStudents as $student): ?>
                <?php $colors = analytics_color_classes((float) $student['avg_percent']); ?>
                <div class="rounded-2xl border border-gray-100 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate"><?= h($student['full_name']) ?></p>
                            <p class="text-xs text-gray-400">@<?= h($student['username']) ?> · <?= (int) $student['attempts'] ?> urinish</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?= $colors['pill'] ?>"><?= $student['avg_percent'] ?>%</span>
                    </div>
                    <div class="mt-3 h-2 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full rounded-full <?= $colors['bar'] ?>" style="width: <?= min(100, round((float) $student['avg_percent'])) ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-triangle-exclamation text-red-500"></i> Low performance students
                </h3>
                <span class="text-xs bg-red-50 text-red-700 px-2.5 py-1 rounded-full">Qizil zona</span>
            </div>
            <div class="p-5 space-y-4">
                <?php if (empty($lowStudents)): ?>
                <p class="text-sm text-gray-400">Hali ma'lumot yo'q</p>
                <?php else: ?>
                <?php foreach ($lowStudents as $student): ?>
                <?php $colors = analytics_color_classes((float) $student['avg_percent']); ?>
                <div class="rounded-2xl border border-gray-100 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate"><?= h($student['full_name']) ?></p>
                            <p class="text-xs text-gray-400">@<?= h($student['username']) ?> · <?= (int) $student['attempts'] ?> urinish</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?= $colors['pill'] ?>"><?= $student['avg_percent'] ?>%</span>
                    </div>
                    <div class="mt-3 h-2 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full rounded-full <?= $colors['bar'] ?>" style="width: <?= min(100, round((float) $student['avg_percent'])) ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <i class="fas fa-table-list text-slate-500"></i> Student Analytics Table
            </h3>
            <span class="text-xs bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full"><?= count($students) ?> ta</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="text-left px-5 py-3">#</th>
                        <th class="text-left px-5 py-3">Talaba</th>
                        <th class="text-left px-5 py-3">Username</th>
                        <th class="text-left px-5 py-3">Urinishlar</th>
                        <th class="text-left px-5 py-3">O'rtacha ball</th>
                        <th class="text-left px-5 py-3">Holat</th>
                        <th class="text-left px-5 py-3">Oxirgi faollik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($students as $index => $student): ?>
                    <?php $colors = analytics_color_classes((float) $student['avg_percent']); ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3.5 text-gray-400"><?= $index + 1 ?></td>
                        <td class="px-5 py-3.5 font-medium text-gray-800"><?= h($student['full_name']) ?></td>
                        <td class="px-5 py-3.5 text-gray-500">@<?= h($student['username']) ?></td>
                        <td class="px-5 py-3.5 text-gray-700"><?= (int) $student['attempts'] ?></td>
                        <td class="px-5 py-3.5 text-gray-700"><?= $student['avg_percent'] ?>%</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium <?= $colors['pill'] ?>">
                                <?= $student['avg_percent'] >= 70 ? 'Yaxshi' : ($student['avg_percent'] >= 50 ? 'O‘rta' : 'Past') ?>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs"><?= !empty($student['last_activity']) ? date('d.m.Y H:i', strtotime($student['last_activity'])) : '—' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
