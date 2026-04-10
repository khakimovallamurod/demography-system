<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$lecture = $db->get_data_by_table('lectures', ['id' => $id]);

if ($lecture) {
    if ($lecture['file_path'] && file_exists(__DIR__ . '/../../' . $lecture['file_path'])) {
        unlink(__DIR__ . '/../../' . $lecture['file_path']);
    }
    $db->delete('lectures', "id = $id");
    flash_message('success', "Ma'ruza muvaffaqiyatli o'chirildi!");
} else {
    flash_message('error', "Ma'ruza topilmadi!");
}

redirect('/admin/lectures/index.php');
