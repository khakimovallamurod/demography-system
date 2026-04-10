<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_admin();

$id      = (int)($_GET['id'] ?? 0);
$test_id = (int)($_GET['test_id'] ?? 0);

$question = $db->get_data_by_table('questions', ['id' => $id]);
if ($question) {
    $db->delete('questions', "id = $id"); // CASCADE deletes options
    flash_message('success', "Savol o'chirildi!");
    redirect('/admin/tests/questions/index.php?test_id=' . $question['test_id']);
} else {
    flash_message('error', 'Savol topilmadi!');
    redirect('/admin/tests/index.php');
}
