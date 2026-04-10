<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$map = $db->get_data_by_table('maps', ['id' => $id]);

if ($map) {
    if (file_exists(__DIR__ . '/../../' . $map['file_path'])) {
        unlink(__DIR__ . '/../../' . $map['file_path']);
    }
    $db->delete('maps', "id = $id");
    flash_message('success', "Xarita muvaffaqiyatli o'chirildi!");
} else {
    flash_message('error', 'Xarita topilmadi!');
}

redirect('/admin/maps/index.php');
