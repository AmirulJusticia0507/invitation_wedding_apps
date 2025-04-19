<?php include 'koneksi.php'; ?>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-wrapper">
    <div class="breadcrumb-section">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Log Akses Undangan</li>
            </ol>
        </nav>
    </div>

    <section class="content p-3">
        <h4>Log Akses Undangan</h4>

        <table class="table table-bordered table-striped table-hover display" style="width:100%" nowrap id="tabelLogAkses">
            <thead>
                <tr>
                    <th>Nama Tamu</th>
                    <th>Link Undangan</th>
                    <th>Waktu Akses</th>
                    <th>IP Address</th>
                    <th>User Agent</th>
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
                while ($row = $data->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['nama_tamu']}</td>
                        <td><a href='{$row['url_undangan']}' target='_blank'>{$row['url_undangan']}</a></td>
                        <td>{$row['accessed_at']}</td>
                        <td>{$row['ip_address']}</td>
                        <td>{$row['user_agent']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
</div>

<?php include 'footer.php'; ?>

<script>
    $(document).ready(function() {
        $('#tabelLogAkses').DataTable({
            responsive: true,
            scrollX: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                },
            }
        });
    });
</script>
