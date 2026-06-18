<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$item = $db->get_data_by_table('laboratory_materials', ['id' => $id]);
if (!$item || empty($item['url'])) {
    die_with_swal("Topilmadi", "URL topilmadi yoki o'chirilgan.");
}

$db->query("UPDATE laboratory_materials SET views = views + 1 WHERE id = $id");

if ((int)$item['category_id'] !== 6) {
    header("Location: " . $item['url']);
    exit;
}

preg_match('/(?:v=|youtu\.be\/|embed\/)([^&?\/]+)/', $item['url'], $vid_match);
$video_id = $vid_match[1] ?? '';

$page_title = $item['title'];
include __DIR__ . '/../../includes/admin_header.php';
?>
<div class="mb-4 flex items-center justify-between">
    <h2 class="text-xl font-bold text-gray-800 line-clamp-1 mr-4"><?= h(stripslashes($item['title'])) ?></h2>
    <a href="<?= BASE_URL ?>/admin/laboratory/category.php?id=<?= $item['category_id'] ?>" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-xl text-sm font-medium transition shadow-sm flex items-center gap-2 whitespace-nowrap">
        <i class="fas fa-arrow-left"></i> Orqaga
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="aspect-video w-full bg-black">
        <iframe class="w-full h-full" src="https://www.youtube.com/embed/<?= $video_id ?>?autoplay=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
    </div>
    <div class="p-6 md:p-8">
        <div class="flex items-center gap-4 text-gray-500 text-sm mb-6 pb-6 border-b border-gray-100">
            <span class="flex items-center gap-2"><i class="far fa-calendar-alt"></i> <?= date('d.m.Y H:i', strtotime($item['created_at'])) ?></span>
            <span class="flex items-center gap-2"><i class="fas fa-eye"></i> <?= $item['views'] ?> ko'rish</span>
        </div>
        <div class="prose max-w-none text-gray-700 leading-relaxed text-sm">
            <?= nl2br(h(stripslashes($item['description']))) ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
