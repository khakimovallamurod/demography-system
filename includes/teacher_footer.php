    </main>
    <footer class="bg-white border-t border-gray-100 px-6 py-3 text-center text-xs text-gray-400 pb-20 md:pb-3">
        &copy; <?= date('Y') ?> <?= SITE_NAME ?>
    </footer>
</div>

<!-- Mobile Bottom Navigation -->
<div class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 flex justify-around items-center h-16 z-50 px-2 pb-[env(safe-area-inset-bottom)] shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
    <a href="<?= BASE_URL ?>/user/dashboard.php" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-green-600 <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? '!text-green-600 font-bold' : '' ?>">
        <i class="fas fa-home text-lg mb-1"></i>
        <span class="text-[10px] uppercase tracking-wide">Bosh sahifa</span>
    </a>
    <a href="<?= BASE_URL ?>/user/lectures/index.php" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-green-600 <?= strpos($_SERVER['REQUEST_URI'], '/user/lectures/') !== false ? '!text-green-600 font-bold' : '' ?>">
        <i class="fas fa-book-open text-lg mb-1"></i>
        <span class="text-[10px] uppercase tracking-wide">Ma'ruzalar</span>
    </a>
    <a href="<?= BASE_URL ?>/user/tests/index.php" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-green-600 <?= strpos($_SERVER['REQUEST_URI'], '/user/tests/') !== false ? '!text-green-600 font-bold' : '' ?>">
        <i class="fas fa-clipboard-list text-lg mb-1"></i>
        <span class="text-[10px] uppercase tracking-wide">Testlar</span>
    </a>
    <button onclick="toggleSidebar()" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-green-600">
        <i class="fas fa-bars text-lg mb-1"></i>
        <span class="text-[10px] uppercase tracking-wide">Menyu</span>
    </button>
</div>

<?php include __DIR__ . '/components/chatbot.php'; ?>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}
</script>
</body>
</html>
