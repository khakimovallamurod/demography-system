    </main>
    <footer class="bg-white border-t border-gray-100 px-6 py-3 text-center text-xs text-gray-400">
        &copy; <?= date('Y') ?> <?= SITE_NAME ?>
    </footer>
</div>

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
