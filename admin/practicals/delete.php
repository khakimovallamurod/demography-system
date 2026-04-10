<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$practical = $db->get_data_by_table('practicals', ['id' => $id]);

if ($practical) {
    if ($practical['file_path'] && file_exists(__DIR__ . '/../../' . $practical['file_path'])) {
        unlink(__DIR__ . '/../../' . $practical['file_path']);
    }
    $db->delete('practicals', "id = $id");
    flash_message('success', "Mashg'ulot muvaffaqiyatli o'chirildi!");
} else {
    flash_message('error', 'Mashg\'ulot topilmadi!');
}

redirect('/admin/practicals/index.php');
