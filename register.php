<?php
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    is_admin() ? redirect('/admin/dashboard.php') : redirect('/user/dashboard.php');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name        = trim($_POST['full_name'] ?? '');
    $phone            = trim($_POST['phone'] ?? '');
    $university_id    = (int)($_POST['university_id'] ?? 0);
    $password         = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (strlen($full_name) < 3)   $errors[] = "Ism kamida 3 ta harfdan iborat bo'lishi kerak!";
    if (empty($phone))    $errors[] = "Telefon raqamni kiriting!";
    if (empty($university_id))    $errors[] = "OTM ni tanlang!";
    
    if (strlen($password) < 6)    $errors[] = "Parol kamida 6 ta belgidan iborat bo'lishi kerak!";
    if ($password !== $password_confirm) $errors[] = "Parollar mos kelmadi!";

    if (empty($errors)) {
        $existing = $db->get_data_by_table('users', ['phone' => $db->escape($phone)]);
        if ($existing) {
            $errors[] = "Bu telefon raqam band! Boshqa raqam kiriting.";
        } else {
            $id = $db->insert('users', [
                'full_name' => $full_name,
                'phone'  => $phone,
                'university_id' => $university_id,
                'password'  => md5($password),
                'role'      => 'user'
            ]);
            if ($id) {
                $_SESSION['user_id'] = $id;
                $_SESSION['phone']   = $phone;
                $_SESSION['role']    = 'user';
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/imask"></script>
    <style>
        .select2-container .select2-selection--single {
            height: 42px !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            background-color: #f9fafb !important;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151 !important;
            padding-left: 1rem !important;
            font-size: 0.875rem !important;
            line-height: normal !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 0.5rem !important;
        }
        .select2-container--open .select2-selection--single,
        .select2-container--focus .select2-selection--single {
            background-color: #ffffff !important;
            border-color: #10b981 !important;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2) !important;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-md p-8">
            <!-- Logo inside card -->
            <div class="flex items-center gap-4 mb-6 pb-5 border-b border-gray-100">
                <img src="<?= SITE_LOGO ?>" alt="<?= SITE_NAME ?> logo"
                     class="h-28 w-auto object-contain flex-shrink-0"
                     onerror="this.style.display='none'">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">OTMni tanlang</label>
                    <div class="relative">
                        <select name="university_id" id="university_id" class="w-full text-sm bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 select2" required>
                            <option value="">-- OTMni tanlang --</option>
                            <?php 
                            $unis = $db->get_data_by_table_all('universities', 'ORDER BY name ASC');
                            foreach ($unis as $u): ?>
                                <option value="<?= $u['id'] ?>" <?= (isset($_POST['university_id']) && $_POST['university_id'] == $u['id']) ? 'selected' : '' ?>><?= h($u['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Telefon raqam</label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm z-10"></i>
                        <input type="text" name="phone" id="phone"
                            value="<?= h($_POST['phone'] ?? '') ?>"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                            placeholder="+998 (__) ___-__-__" required>
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
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        Toast.fire({
            icon: 'success',
            title: 'Muvaffaqiyatli ro\'yxatdan o\'tdingiz!'
        }).then(() => {
            window.location.href = '<?= BASE_URL ?>/user/dashboard.php';
        });
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

    <script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            placeholder: "-- OTMni tanlang --"
        });
        
        var phoneMask = IMask(
            document.getElementById('phone'), {
                mask: '+{998} (00) 000-00-00'
            }
        );
    });
    </script>
</body>
</html>
