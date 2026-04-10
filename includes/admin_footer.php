    </main>
    <footer class="bg-white border-t border-gray-200 px-6 py-3 text-center text-xs text-gray-400">
        &copy; <?= date('Y') ?> <?= SITE_NAME ?> — Admin Panel
    </footer>
</div>

<?php include __DIR__ . '/components/chatbot.php'; ?>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}

function swalDelete(e, el, msg) {
    e.preventDefault();
    Swal.fire({
        title: 'Ishonchingiz komilmi?',
        text: msg || 'Bu amalni qaytarib bo\'lmaydi!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ha, o\'chirish',
        cancelButtonText: 'Bekor qilish',
        reverseButtons: true,
        customClass: { popup: 'text-sm' }
    }).then(function(result) {
        if (result.isConfirmed) window.location.href = el.href;
    });
    return false;
}
</script>
</body>
</html>
