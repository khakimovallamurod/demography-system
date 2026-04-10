<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$test = $db->get_data_by_table('tests', ['id' => $id]);

if ($test) {
    $db->delete('tests', "id = $id"); // CASCADE handles questions & options
    flash_message('success', 'Test va barcha savollari o\'chirildi!');
} else {
    flash_message('error', 'Test topilmadi!');
}

redirect('/admin/tests/index.php');
