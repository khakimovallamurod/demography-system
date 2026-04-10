<?php
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    is_admin() ? redirect('/admin/dashboard.php') : redirect('/user/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username va parolni kiriting!';
    } else {
        $user = $db->get_data_by_table('users', ['username' => $db->escape($username)]);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];
            $user['role'] === 'admin' ? redirect('/admin/dashboard.php') : redirect('/user/dashboard.php');
        } else {
            $error = 'Username yoki parol noto\'g\'ri!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirish — <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-md p-8">
            <!-- Logo inside card -->
            <div class="flex items-center gap-4 mb-6 pb-5 border-b border-gray-100">
                <img src="<?= SITE_LOGO ?>" alt="<?= SITE_NAME ?> logo"
                     class="h-20 w-auto object-contain flex-shrink-0"
                     onerror="this.style.display='none'">
                <div>
                    <h1 class="font-bold text-gray-800 leading-tight"><?= SITE_NAME ?></h1>
                    <p class="text-xs text-gray-400">Tizimga kirish</p>
                </div>
            </div>

            <?php if ($error): ?>
            <div class="mb-5 bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
                <i class="fas fa-exclamation-circle flex-shrink-0"></i> <?= h($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Foydalanuvchi nomi</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="username"
                            value="<?= h($_POST['username'] ?? '') ?>"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="username" required autofocus>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Parol</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="password" name="password" id="password"
                            class="w-full pl-10 pr-12 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="••••••••" required>
                        <button type="button" onclick="togglePass()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                            <i class="fas fa-eye" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl transition text-sm flex items-center justify-center gap-2 mt-2">
                    <i class="fas fa-sign-in-alt"></i> Kirish
                </button>
            </form>

            <div class="mt-5 pt-5 border-t border-gray-100 text-center text-sm text-gray-500">
                Hisobingiz yo'qmi?
                <a href="<?= BASE_URL ?>/register.php" class="text-blue-600 hover:underline font-semibold ml-1">
                    Ro'yxatdan o'ting
                </a>
            </div>
        </div>

        <p class="text-center text-gray-400 text-xs mt-5">&copy; <?= date('Y') ?> <?= SITE_NAME ?></p>
    </div>

    <script>
    function togglePass() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eye-icon');
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    }
    </script>
</body>
</html>
