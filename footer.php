<footer class="mt-auto py-4 px-6 text-center text-xs text-slate-500 border-t border-slate-800 bg-slate-950/80">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 max-w-7xl mx-auto">
        <span>&copy; <?php echo date("Y"); ?> <a href="dashboard.php" class="text-pink-400 hover:text-pink-300 font-medium">Weddingku Invitation App</a>. All rights reserved.</span>
        <span class="text-slate-600">Built with Tailwind & PHP</span>
    </div>
</footer>

<!-- Script JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Lightbox2 JS -->
<script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/js/lightbox.min.js"></script>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) {
        sidebar.classList.toggle('-translate-x-full');
    }
    if (overlay) {
        overlay.classList.toggle('hidden');
    }
}

// Flash notification handler via URL params
(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');
    const err = urlParams.get('error');

    if (msg) {
        let title = 'Berhasil!';
        if (msg === 'saved') title = 'Data berhasil disimpan!';
        else if (msg === 'updated') title = 'Data berhasil diperbarui!';
        else if (msg === 'deleted') title = 'Data berhasil dihapus!';
        else if (msg === 'imported') title = 'Data berhasil diimport!';
        else title = decodeURIComponent(msg);

        Swal.fire({
            icon: 'success',
            title: title,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#0f172a',
            color: '#f8fafc',
            iconColor: '#10b981'
        });
    } else if (err) {
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: decodeURIComponent(err),
            background: '#0f172a',
            color: '#f8fafc',
            confirmButtonColor: '#ec4899'
        });
    }

    // Clean URL without reloading page
    if (msg || err) {
        urlParams.delete('msg');
        urlParams.delete('error');
        const newSearch = urlParams.toString();
        const newUrl = window.location.pathname + (newSearch ? '?' + newSearch : '') + window.location.hash;
        window.history.replaceState({}, '', newUrl);
    }
})();
</script>

</body>
</html>
