<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_admin();

// Ensure SimpleXLSXGen is available
if (!file_exists(__DIR__ . '/../../../includes/SimpleXLSXGen.php')) {
    flash_message('error', 'Excel generatsiya qilish kutubxonasi topilmadi!');
    redirect('/admin/tests/index.php');
}

require_once __DIR__ . '/../../../includes/SimpleXLSXGen.php';

$test_id = (int)($_GET['test_id'] ?? 0);
if (!$test_id) {
    redirect('/admin/tests/index.php');
}

$sample_question = "1. Geografiya fani o'z o'rganish predmeti, maqsadi va mohiyatiga qarab bir nechta qismlarga bo'linadi: \nI. tabiiy geografiya\nII. sotsial geografiya  \nIII. tabiiy-sotsial geografiya \nIV. geografiyaga bevosita daxldor fanlar \nQuyida berilgan tarmoqlar qaysi yo'nalish tarkibiga kirishini aniqlang: a)aholi va mehnat resurslari geografiyasi b) siyosiy geografiya c) landshaftshunoslik d)tibbiy geografiya e) iqlimshunoslik f) geoekologiya g) kartografiya h) toponomika";

$data = [
    ['Savol matni', 'A varianti', 'B varianti', 'C varianti', 'D varianti', 'To\'g\'ri javob (A,B,C,D)'],
    [
        $sample_question,
        "I-c,e; II-a,b; III-d,f; IV-g,h",
        "I-a,e; II-a,c; III-d,h; IV-f,h",
        "I-c,f; II-a,b; III-d,e; IV-g,h",
        "I-b,d; II-a,b; III-c,e; IV-g,h",
        "A"
    ]
];

$xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data);

// Optional: you can bold the first row
$xlsx->setColWidth(1, 70); // Savol matni
$xlsx->setColWidth(2, 35); // A
$xlsx->setColWidth(3, 35); // B
$xlsx->setColWidth(4, 35); // C
$xlsx->setColWidth(5, 35); // D
$xlsx->setColWidth(6, 25); // To'g'ri javob

$xlsx->downloadAs('Savollar_namunasi.xlsx');
exit;
