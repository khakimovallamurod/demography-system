<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_type = (int)($_POST['module_type'] ?? 1); // 1 = Practical
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
            flash_message('success', "Amaliyotni o'qib tugatdingiz! Endi uning testini ishlashingiz mumkin.");
            // Redirect directly to the test page or take page
            redirect('/user/tests/take.php?id=' . $test['id']);
        } else {
            flash_message('success', "Amaliyotni o'qib tugatdingiz!");
            redirect('/user/practicals/view.php?id=' . $module_id);
        }
    } else {
        redirect('/user/practicals/index.php');
    }
} else {
    redirect('/user/practicals/index.php');
}
