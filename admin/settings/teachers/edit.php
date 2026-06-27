<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$teacher = $db->get_data_by_table('users', ['id' => $id, 'role' => 'teacher']);

if (!$teacher) {
    flash_message('error', "Bunday o'qituvchi topilmadi!");
    header("Location: index.php");
    exit;
}

$universities = $db->get_data_by_table_all('universities', 'ORDER BY name ASC');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $university_id = (int)($_POST['university_id'] ?? 0);
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($university_id) || empty($full_name) || empty($phone)) {
        flash_message('error', "Barcha maydonlarni to'ldiring!");
    } else {
        $update_data = [
            'university_id' => $university_id,
            'full_name' => $full_name,
            'phone' => $phone
        ];
        if (!empty($password)) {
            $update_data['password'] = md5($password);
            $update_data['raw_password'] = $password;
        }
        
        $result = $db->update('users', $update_data, "id = $id");
        
        if ($result) {
            flash_message('success', "O'qituvchi muvaffaqiyatli tahrirlandi!");
            header("Location: index.php");
            exit;
        } else {
            flash_message('error', "Xatolik yuz berdi!");
        }
    }
}

$page_title = "O'qituvchini tahrirlash";
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
            <i class="fas fa-edit text-blue-500"></i> O'qituvchini tahrirlash
        </h2>

        <form method="POST" action="">
            <div class="space-y-3">
                <div>
                    <label for="university_id" class="block text-sm font-medium text-gray-700 mb-1">OTMni tanlang</label>
                    <select id="university_id" name="university_id" required
                            class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white select2">
                        <option value="">OTMni tanlang</option>
                        <?php foreach ($universities as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= $u['id'] == $teacher['university_id'] ? 'selected' : '' ?>><?= h($u['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">To'liq ism</label>
                    <input type="text" id="full_name" name="full_name" required
                           value="<?= h($teacher['full_name']) ?>"
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon raqam</label>
                    <input type="text" id="phone" name="phone" required
                           value="<?= h($teacher['phone']) ?>"
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Parol (o'zgartirish uchun)</label>
                    <input type="text" id="password" name="password"
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Yangi parol (yoki bo'sh qoldiring)">
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
