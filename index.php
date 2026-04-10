<?php
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    if (is_admin()) {
        redirect('/admin/dashboard.php');
    } else {
        redirect('/user/dashboard.php');
    }
} else {
    require __DIR__ . '/login.php';
}
