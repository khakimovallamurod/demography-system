<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$university = $db->get_data_by_table('universities', ['id' => $id]);

if (!$university) {
    flash_message('error', "Bunday OTM topilmadi!");
} else {
    $result = $db->delete('universities', "id = $id");
    if ($result) {
        flash_message('success', "OTM muvaffaqiyatli o'chirildi!");
    } else {
        flash_message('error', "Xatolik yuz berdi! Balki unga biriktirilgan o'qituvchilar mavjuddir.");
    }
}

header("Location: index.php");
exit;
