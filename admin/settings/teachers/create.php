<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_admin();

$universities = $db->get_data_by_table_all('universities', 'ORDER BY name ASC');

if (empty($universities)) {
    flash_message('error', "Avval OTM qo'shishingiz kerak!");
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $university_id = (int)($_POST['university_id'] ?? 0);
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($university_id) || empty($full_name) || empty($phone) || empty($password)) {
        flash_message('error', "Barcha maydonlarni to'ldiring!");
    } else {
        $result = $db->insert('users', [
            'university_id' => $university_id,
            'full_name' => $full_name,
            'phone' => $phone,
            'password' => md5($password),
            'raw_password' => $password,
            'role' => 'teacher'
        ]);
        if ($result) {
            flash_message('success', "O'qituvchi muvaffaqiyatli qo'shildi!");
            header("Location: index.php");
            exit;
        } else {
            flash_message('error', "Xatolik yuz berdi!");
        }
    }
}

$page_title = "Yangi o'qituvchi qo'shish";
include __DIR__ . '/../../../includes/admin_header.php';
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 42px !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 0.75rem !important;
        background-color: #ffffff !important;
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
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
    }
</style>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-user-plus text-blue-500"></i> Yangi o'qituvchi
        </h2>

        <form method="POST" action="">
            <div class="space-y-3">
                <div>
                    <label for="university_id" class="block text-sm font-medium text-gray-700 mb-1">OTMni tanlang</label>
                    <select id="university_id" name="university_id" required
                            class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white select2">
                        <option value="">OTMni tanlang</option>
                        <?php foreach ($universities as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= h($u['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">To'liq ism</label>
                    <input type="text" id="full_name" name="full_name" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Ism Familiya">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon raqam</label>
                    <input type="text" id="phone" name="phone" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="+998 (__) ___-__-__">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Parol</label>
                    <input type="text" id="password" name="password" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Maxfiy parol">
                </div>
            </div>

            <div class="mt-8 flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition">
                    Saqlash
                </button>
                <a href="index.php" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition">
                    Bekor qilish
                </a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../../includes/admin_footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/imask"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.select2').select2({
        width: '100%',
        placeholder: "OTMni tanlang"
    });
    
    var phoneMask = IMask(
        document.getElementById('phone'), {
            mask: '+{998} (00) 000-00-00'
        }
    );
});
</script>
