<?php
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    is_admin() ? redirect('/admin/dashboard.php') : redirect('/user/dashboard.php');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name        = trim($_POST['full_name'] ?? '');
    $username         = trim($_POST['username'] ?? '');
    $password         = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (strlen($full_name) < 3)   $errors[] = "Ism kamida 3 ta harfdan iborat bo'lishi kerak!";
    if (strlen($username) < 3)    $errors[] = "Username kamida 3 ta belgidan iborat bo'lishi kerak!";
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) $errors[] = "Username faqat harf, raqam va _ dan iborat bo'lsin!";
    if (strlen($password) < 6)    $errors[] = "Parol kamida 6 ta belgidan iborat bo'lishi kerak!";
    if ($password !== $password_confirm) $errors[] = "Parollar mos kelmadi!";

    if (empty($errors)) {
        $existing = $db->get_data_by_table('users', ['username' => $db->escape($username)]);
        if ($existing) {
            $errors[] = "Bu username band! Boshqa username tanlang.";
        } else {
            $id = $db->insert('users', [
                'full_name' => $full_name,
                'username'  => $username,
                'password'  => password_hash($password, PASSWORD_DEFAULT),
                'role'      => 'user'
            ]);
            if ($id) {
                $success = true;
            } else {
                $errors[] = "Ro'yxatdan o'tishda xatolik yuz berdi!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ro'yxatdan o'tish — <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-md p-8">
            <!-- Logo inside card -->
            <div class="flex items-center gap-3 mb-6 pb-5 border-b border-gray-100">
                <div class="w-11 h-11 bg-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-globe text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="font-bold text-gray-800 leading-tight"><?= SITE_NAME ?></h1>
                    <p class="text-xs text-gray-400">Ro'yxatdan o'tish</p>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-3.5 space-y-1">
                <?php foreach ($errors as $e): ?>
                <p class="text-red-600 text-sm flex items-center gap-2">
                    <i class="fas fa-exclamation-circle flex-shrink-0 text-xs"></i> <?= h($e) ?>
                </p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">To'liq ism</label>
                    <div class="relative">
                        <i class="fas fa-id-card absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="full_name"
                            value="<?= h($_POST['full_name'] ?? '') ?>"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                            placeholder="Ism Familya" required autofocus>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Foydalanuvchi nomi</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="username"
                            value="<?= h($_POST['username'] ?? '') ?>"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                            placeholder="username" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Parol</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="password" name="password" id="pass1"
                            class="w-full pl-10 pr-12 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                            placeholder="Kamida 6 belgi" required minlength="6">
                        <button type="button" onclick="togglePass('pass1','eye1')" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                            <i class="fas fa-eye" id="eye1"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Parolni tasdiqlang</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="password" name="password_confirm" id="pass2"
                            class="w-full pl-10 pr-12 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                            placeholder="Parolni qaytaring" required>
                        <button type="button" onclick="togglePass('pass2','eye2')" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                            <i class="fas fa-eye" id="eye2"></i>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-xl transition text-sm flex items-center justify-center gap-2 mt-2">
                    <i class="fas fa-user-plus"></i> Ro'yxatdan o'tish
                </button>
            </form>

            <div class="mt-5 pt-5 border-t border-gray-100 text-center text-sm text-gray-500">
                Hisobingiz bormi?
                <a href="<?= BASE_URL ?>/login.php" class="text-blue-600 hover:underline font-semibold ml-1">Kirish</a>
            </div>
        </div>

        <p class="text-center text-gray-400 text-xs mt-4">&copy; <?= date('Y') ?> <?= SITE_NAME ?></p>
    </div>

    <?php if ($success): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Muvaffaqiyatli!',
            text: "Ro'yxatdan o'tdingiz. Endi tizimga kiring.",
            confirmButtonColor: '#059669',
            confirmButtonText: 'Kirish'
        }).then(() => { window.location.href = '<?= BASE_URL ?>/login.php'; });
    });
    </script>
    <?php endif; ?>

    <script>
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    }
    </script>
</body>
</html>
