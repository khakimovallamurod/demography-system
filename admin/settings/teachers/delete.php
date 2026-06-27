<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$teacher = $db->get_data_by_table('users', ['id' => $id, 'role' => 'teacher']);

if (!$teacher) {
    flash_message('error', "Bunday o'qituvchi topilmadi!");
} else {
    $result = $db->delete('users', "id = $id AND role = 'teacher'");
    if ($result) {
        flash_message('success', "O'qituvchi muvaffaqiyatli o'chirildi!");
    } else {
        flash_message('error', "Xatolik yuz berdi!");
    }
}

header("Location: index.php");
exit;
