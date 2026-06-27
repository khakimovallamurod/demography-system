<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_admin();

if (!file_exists(__DIR__ . '/../../../includes/SimpleXLSX.php')) {
    flash_message('error', 'Excel o\'qish kutubxonasi topilmadi!');
    redirect('/admin/tests/index.php');
}

require_once __DIR__ . '/../../../includes/SimpleXLSX.php';

$test_id = (int)($_POST['test_id'] ?? 0);
if (!$test_id) {
    redirect('/admin/tests/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        flash_message('error', 'Faylni yuklashda xatolik yuz berdi!');
        redirect('/admin/tests/questions/index.php?test_id=' . $test_id);
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'xlsx') {
        flash_message('error', 'Faqat .xlsx formatidagi Excel fayllar qabul qilinadi!');
        redirect('/admin/tests/questions/index.php?test_id=' . $test_id);
    }

    if ( $xlsx = \Shuchkin\SimpleXLSX::parse($file['tmp_name']) ) {
        $rows = $xlsx->rows();
        
        if (count($rows) <= 1) {
            flash_message('error', 'Faylda savollar topilmadi!');
            redirect('/admin/tests/questions/index.php?test_id=' . $test_id);
        }

        // Check headers (optional but good for validation)
        $headers = array_map('trim', $rows[0]);
        if (count($headers) < 6) {
            flash_message('error', 'Fayl strukturasi noto\'g\'ri! Namunaga qarang.');
            redirect('/admin/tests/questions/index.php?test_id=' . $test_id);
        }

        $inserted = 0;

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header

            $question_text = trim($row[0] ?? '');
            $optA = trim($row[1] ?? '');
            $optB = trim($row[2] ?? '');
            $optC = trim($row[3] ?? '');
            $optD = trim($row[4] ?? '');
            $correct = strtoupper(trim($row[5] ?? ''));

            if (empty($question_text) || empty($optA) || empty($optB)) {
                continue; // Skip empty rows
            }

            // Insert Question
            $q_id = $db->insert('questions', [
                'test_id' => $test_id,
                'question_text' => $question_text
            ]);

            if ($q_id) {
                // Insert Options
                $options_map = [
                    'A' => $optA,
                    'B' => $optB,
                    'C' => $optC,
                    'D' => $optD
                ];

                foreach ($options_map as $letter => $text) {
                    if (!empty($text)) {
                        $is_correct = ($letter === $correct) ? 1 : 0;
                        $db->insert('options', [
                            'question_id' => $q_id,
                            'option_text' => $text,
                            'is_correct' => $is_correct
                        ]);
                    }
                }
                $inserted++;
            }
        }

        flash_message('success', "Muvaffaqiyatli! $inserted ta savol bazaga kiritildi.");
    } else {
        flash_message('error', 'Excel faylini o\'qishda xatolik: ' . \Shuchkin\SimpleXLSX::parseError());
    }

    redirect('/admin/tests/questions/index.php?test_id=' . $test_id);
} else {
    redirect('/admin/tests/questions/index.php?test_id=' . $test_id);
}
