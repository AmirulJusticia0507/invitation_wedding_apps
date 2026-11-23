<?php include 'auth_check.php'; ?>
<?php include 'koneksi.php'; ?>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-wrapper">
    <div class="p-6 max-w-7xl w-full mx-auto">
        <!-- Breadcrumb & Title -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <nav class="flex text-xs text-slate-400 gap-2 mb-1">
                    <a href="dashboard.php" class="hover:text-sky-400 transition">Home</a>
                    <span>/</span>
                    <span class="text-sky-400 font-medium">Log Akses Undangan</span>
                </nav>
                <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </span>
                    Riwayat Kunjungan / Log Akses Undangan
                </h1>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="overflow-x-auto">
                <table class="table table-bordered table-striped table-hover display w-full" id="tabelLogAkses">
                    <thead>
                        <tr>
                            <th>Nama Tamu</th>
                            <th>Link Undangan</th>
                            <th>Waktu Akses</th>
                            <th>IP Address</th>
                            <th>User Agent / Device</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "
                            SELECT log.*, tamu.nama AS nama_tamu, url.url_undangan
                            FROM log_akses_undangan log
                            JOIN tamu ON log.tamu_id = tamu.id
                            JOIN undangan_url url ON log.undangan_url_id = url.id
                            ORDER BY log.accessed_at DESC
                        ";
                        $data = $weddingku->query($query);
                        if ($data) {
                            while ($row = $data->fetch_assoc()) {
                                echo "<tr>
                                    <td class='font-semibold text-white whitespace-nowrap'>" . h($row['nama_tamu'] ?? '-') . "</td>
                                    <td>
                                        <a href='{$row['url_undangan']}' target='_blank' class='text-xs text-sky-400 hover:underline max-w-xs truncate inline-block'>
                                            " . h($row['url_undangan']) . "
                                        </a>
                                    </td>
                                    <td class='whitespace-nowrap text-xs text-slate-300'>
                                        <i class='fa-regular fa-clock text-slate-500 mr-1'></i> " . h($row['accessed_at']) . "
                                    </td>
                                    <td class='text-xs font-mono text-emerald-400'>" . h($row['ip_address']) . "</td>
                                    <td class='text-xs text-slate-400 max-w-sm truncate' title='" . h($row['user_agent']) . "'>" . h($row['user_agent']) . "</td>
                                </tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    $('#tabelLogAkses').DataTable({
        responsive: true,
        scrollX: true,
        order: [[2, 'desc']],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Data tidak ditemukan",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data tersedia",
            infoFiltered: "(difilter dari _MAX_ total data)",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            },
        }
    });
});
</script>
