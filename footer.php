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
</script>

</body>
</html>
