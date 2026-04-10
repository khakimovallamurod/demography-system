<?php
require_once __DIR__ . '/../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new Database();

function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        redirect('/login.php');
    }
}

function require_admin() {
    require_login();
    if (!is_admin()) {
        redirect('/user/dashboard.php');
    }
}

function require_user() {
    require_login();
    if (is_admin()) {
        redirect('/admin/dashboard.php');
    }
}

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function flash_message($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function upload_file($file, $folder = 'lectures') {
    // PHP upload error check
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        $php_errors = [
            UPLOAD_ERR_INI_SIZE   => 'Fayl php.ini limitidan katta!',
            UPLOAD_ERR_FORM_SIZE  => 'Fayl forma limitidan katta!',
            UPLOAD_ERR_PARTIAL    => 'Fayl to\'liq yuklanmadi!',
            UPLOAD_ERR_NO_FILE    => 'Fayl tanlanmagan!',
            UPLOAD_ERR_NO_TMP_DIR => 'Vaqtinchalik papka topilmadi!',
            UPLOAD_ERR_CANT_WRITE => 'Faylni diskka yozib bo\'lmadi!',
        ];
        $msg = $php_errors[$file['error']] ?? 'Fayl yuklashda xatolik (#' . $file['error'] . ')';
        return ['success' => false, 'message' => $msg];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($ext !== 'pdf') {
        return ['success' => false, 'message' => 'Faqat PDF fayl yuklash mumkin!'];
    }

    // Double-check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if ($mime !== 'application/pdf') {
        return ['success' => false, 'message' => 'Fayl haqiqiy PDF emas!'];
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return ['success' => false, 'message' => 'Fayl hajmi 2MB dan oshmasligi kerak!'];
    }

    $upload_dir = UPLOAD_PATH . $folder . '/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file['name']);
    $filepath = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        chmod($filepath, 0644);
        return [
            'success'   => true,
            'file_path' => 'uploads/' . $folder . '/' . $filename,
            'file_name' => $file['name']
        ];
    }

    return ['success' => false, 'message' => 'Fayl yuklashda xatolik! (move_uploaded_file muvaffaqiyatsiz)'];
}

function time_ago($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->d == 0) {
        if ($diff->h == 0) return $diff->i . ' daqiqa oldin';
        return $diff->h . ' soat oldin';
    }
    if ($diff->d < 30) return $diff->d . ' kun oldin';
    if ($diff->m < 12) return $diff->m . ' oy oldin';
    return $diff->y . ' yil oldin';
}
