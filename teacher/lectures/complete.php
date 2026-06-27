<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_type = (int)($_POST['module_type'] ?? 0);
    $module_id = (int)($_POST['module_id'] ?? 0);
    $user_id = (int)$_SESSION['user_id'];

    if ($module_id > 0) {
        // Check if already completed
        $progress = $db->get_data_by_table('user_module_progress', [
            'user_id' => $user_id,
            'module_type' => $module_type,
            'module_id' => $module_id
        ]);

        if (!$progress) {
            $db->insert('user_module_progress', [
                'user_id' => $user_id,
                'module_type' => $module_type,
                'module_id' => $module_id
            ]);
        }

        // Find the test
        $test = $db->get_data_by_table('tests', [
            'module_type' => $module_type,
            'module_id' => $module_id
        ]);

        if ($test) {
            flash_message('success', "Ma'ruzani o'qib tugatdingiz! Endi uning testini ishlashingiz mumkin.");
            redirect('/teacher/lectures/view.php?id=' . $module_id);
        } else {
            flash_message('success', "Ma'ruzani o'qib tugatdingiz!");
            redirect('/teacher/lectures/view.php?id=' . $module_id);
        }
    } else {
        redirect('/teacher/lectures/index.php');
    }
} else {
    redirect('/teacher/lectures/index.php');
}
