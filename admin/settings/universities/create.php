<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if (empty($name)) {
        flash_message('error', "OTM nomini kiriting!");
    } else {
        $result = $db->insert('universities', ['name' => $name]);
        if ($result) {
            flash_message('success', "OTM muvaffaqiyatli qo'shildi!");
            header("Location: index.php");
            exit;
        } else {
            flash_message('error', "Xatolik yuz berdi!");
        }
    }
}

$page_title = "Yangi OTM qo'shish";
include __DIR__ . '/../../../includes/admin_header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-plus-circle text-blue-500"></i> Yangi OTM
        </h2>

        <form method="POST" action="">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">OTM Nomi</label>
                    <input type="text" id="name" name="name" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Masalan: O'zbekiston Milliy Universiteti">
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
