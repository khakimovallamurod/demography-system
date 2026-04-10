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

function normalize_space($text) {
    $text = html_entity_decode(strip_tags((string) $text), ENT_QUOTES, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', $text);
    return trim((string) $text);
}

function truncate_text($text, $limit = 120) {
    $text = trim((string) $text);
    if ($text === '') {
        return $text;
    }

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text, 'UTF-8') <= $limit) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, $limit - 1, 'UTF-8')) . '…';
    }

    if (strlen($text) <= $limit) {
        return $text;
    }

    return rtrim(substr($text, 0, $limit - 3)) . '...';
}

function fetch_remote_content($url, $timeout = 12) {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => 'DemographySystem/1.0',
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response !== false && $httpCode >= 200 && $httpCode < 400) {
            return $response;
        }

        if ($error !== '') {
            return null;
        }
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: DemographySystem/1.0\r\n",
            'timeout' => $timeout,
            'ignore_errors' => true,
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);
    return $response !== false ? $response : null;
}

function get_external_cache_path($key) {
    $dir = sys_get_temp_dir() . '/demography-system-cache';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }

    return $dir . '/' . preg_replace('/[^a-z0-9_-]/i', '_', $key) . '.json';
}

function get_cached_external_data($cacheKey, callable $resolver, $ttl = 1800) {
    $cacheFile = get_external_cache_path($cacheKey);

    if (is_file($cacheFile) && (time() - filemtime($cacheFile) < $ttl)) {
        $cached = json_decode((string) @file_get_contents($cacheFile), true);
        if (is_array($cached)) {
            return $cached;
        }
    }

    $data = $resolver();
    if (is_array($data)) {
        @file_put_contents($cacheFile, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return $data;
    }

    if (is_file($cacheFile)) {
        $cached = json_decode((string) @file_get_contents($cacheFile), true);
        if (is_array($cached)) {
            return $cached;
        }
    }

    return [];
}

function parse_stat_uz_resource($html) {
    $title = 'O`zbekiston raqamlarda';
    $period = 'Asosiy statistika ko‘rsatkichlari';
    $indicators = [];

    if (preg_match("/<h1[^>]*>O[`']zbekiston raqamlarda<\\/h1><p[^>]*>(.*?)<\\/p>/su", $html, $matches)) {
        $period = normalize_space($matches[1]);
    }

    if (preg_match_all('/<div class="flex indicator-item.*?<div class="flex-1 indicator-labels">\s*<h1>(.*?)<\/h1><h2>(.*?)<\/h2><p>(.*?)<\/p>/su', $html, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $label = normalize_space($match[1]);
            $value = normalize_space($match[2]);
            $note = normalize_space($match[3]);
            if ($label !== '' && $value !== '') {
                $indicators[$label] = [
                    'label' => $label,
                    'value' => $value,
                    'note' => $note,
                ];
            }
        }
    }

    $selected = [];
    foreach (['YALPI ICHKI MAHSULOT', 'SANOAT', 'DOIMIY AHOLI SONI'] as $wanted) {
        if (isset($indicators[$wanted])) {
            $selected[] = $indicators[$wanted];
        }
    }

    if (empty($selected)) {
        $selected[] = [
            'label' => 'Manba',
            'value' => 'Statistik ma`lumotlar',
            'note' => 'Saytdagi blokni o‘qib bo‘lmadi',
        ];
    }

    return [
        'site_key' => 'stat_uz',
        'site_name' => 'stat.uz',
        'url' => 'https://stat.uz/uz/',
        'title' => $title,
        'summary' => $period,
        'items' => array_slice($selected, 0, 3),
        'updated_at' => date('d.m.Y H:i'),
        'status' => 'Jonli ma`lumot',
        'accent' => [
            'shell' => 'from-orange-500 via-amber-500 to-red-500',
            'soft' => 'from-orange-50 to-red-50',
            'ring' => 'border-orange-100',
            'icon' => 'bg-orange-500/15 text-orange-700',
            'badge' => 'bg-orange-500/15 text-orange-700',
            'button' => 'bg-orange-600 hover:bg-orange-700',
        ],
    ];
}

function parse_demografiya_resource($html) {
    $mainBlock = 'Doimiy aholi soni';
    $passportBlock = 'Demografik passport';
    $articleTitle = 'Demografik tahliliy materiallar';
    $articleCategory = 'Demografiya portali';

    if (preg_match('/<div class="title">(.*?)<\/div>/su', $html, $matches)) {
        $mainBlock = normalize_space($matches[1]);
    }

    if (preg_match('/<h2 class="section__title[^"]*">(.*?)<\/h2>/su', $html, $matches)) {
        $passportBlock = normalize_space($matches[1]);
    }

    if (preg_match('/<div class="elementor-post__badge">(.*?)<\/div>.*?<h3 class="elementor-post__title">\s*<a[^>]*>(.*?)<\/a>/su', $html, $matches)) {
        $articleCategory = normalize_space($matches[1]);
        $articleTitle = normalize_space($matches[2]);
    }

    return [
        'site_key' => 'demografiya',
        'site_name' => 'demografiya.uz',
        'url' => 'https://demografiya.uz/uz/',
        'title' => 'Demografiya portali',
        'summary' => 'Portalning asosiy bo‘limlari va dolzarb tahliliy materiallari.',
        'items' => [
            [
                'label' => 'Asosiy blok',
                'value' => $mainBlock,
                'note' => 'Portaldagi markaziy indikator bo‘limi',
            ],
            [
                'label' => 'Yo‘nalish',
                'value' => $passportBlock,
                'note' => 'Hududlar bo‘yicha demografik ma`lumotlar',
            ],
            [
                'label' => truncate_text($articleCategory, 28),
                'value' => truncate_text($articleTitle, 90),
                'note' => 'Portaldagi ko‘rinib turgan so‘nggi material',
            ],
        ],
        'updated_at' => date('d.m.Y H:i'),
        'status' => 'Jonli ma`lumot',
        'accent' => [
            'shell' => 'from-sky-600 via-blue-700 to-cyan-700',
            'soft' => 'from-sky-50 to-cyan-50',
            'ring' => 'border-sky-100',
            'icon' => 'bg-sky-500/15 text-sky-700',
            'badge' => 'bg-sky-500/15 text-sky-700',
            'button' => 'bg-sky-600 hover:bg-sky-700',
        ],
    ];
}

function get_dashboard_external_resources() {
    $resources = [];

    $resources[] = get_cached_external_data('dashboard_demografiya', function () {
        $html = fetch_remote_content('https://demografiya.uz/uz/');
        if (!$html) {
            return [
                'site_key' => 'demografiya',
                'site_name' => 'demografiya.uz',
                'url' => 'https://demografiya.uz/uz/',
                'title' => 'Demografiya portali',
                'summary' => 'Portal bilan aloqa o‘rnatilmadi, ammo batafsil sahifani ochish mumkin.',
                'items' => [
                    ['label' => 'Holat', 'value' => 'Ma`lumot vaqtincha olinmadi', 'note' => 'Tarmoq yoki masofaviy server javobi kerak'],
                ],
                'updated_at' => date('d.m.Y H:i'),
                'status' => 'Vaqtincha offline',
                'accent' => [
                    'shell' => 'from-sky-600 via-blue-700 to-cyan-700',
                    'soft' => 'from-sky-50 to-cyan-50',
                    'ring' => 'border-sky-100',
                    'icon' => 'bg-sky-500/15 text-sky-700',
                    'badge' => 'bg-sky-500/15 text-sky-700',
                    'button' => 'bg-sky-600 hover:bg-sky-700',
                ],
            ];
        }

        return parse_demografiya_resource($html);
    });

    $resources[] = get_cached_external_data('dashboard_stat_uz', function () {
        $html = fetch_remote_content('https://stat.uz/uz/');
        if (!$html) {
            return [
                'site_key' => 'stat_uz',
                'site_name' => 'stat.uz',
                'url' => 'https://stat.uz/uz/',
                'title' => 'O`zbekiston raqamlarda',
                'summary' => 'Statistik blok bilan aloqa o‘rnatilmadi, ammo manba sahifasi ochiladi.',
                'items' => [
                    ['label' => 'Holat', 'value' => 'Ma`lumot vaqtincha olinmadi', 'note' => 'Tarmoq yoki masofaviy server javobi kerak'],
                ],
                'updated_at' => date('d.m.Y H:i'),
                'status' => 'Vaqtincha offline',
                'accent' => [
                    'shell' => 'from-orange-500 via-amber-500 to-red-500',
                    'soft' => 'from-orange-50 to-red-50',
                    'ring' => 'border-orange-100',
                    'icon' => 'bg-orange-500/15 text-orange-700',
                    'badge' => 'bg-orange-500/15 text-orange-700',
                    'button' => 'bg-orange-600 hover:bg-orange-700',
                ],
            ];
        }

        return parse_stat_uz_resource($html);
    });

    return array_values(array_filter($resources, 'is_array'));
}
