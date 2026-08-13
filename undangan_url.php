<?php include 'auth_check.php'; ?>
<?php include 'koneksi.php'; ?>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>


<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#ffb6c1" />
<div class="content-wrapper">
    <!-- Breadcrumb -->
    <div class="breadcrumb-section">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data Generate URL Undangan</li>
            </ol>
        </nav>
    </div>
    <section class="content p-3">
        </br></br>
        <h4>Data URL Undangan</h4>
        <button class="btn btn-outline-primary mb-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahUndangan">
            <i class="fas fa-link"></i> Tambah Undangan URL
        </button>

        <table class="table table-bordered table-striped table-hover display" style="width:100%" id="tabelUndangan" nowrap>
            <thead>
                <tr>
                    <th nowrap>No</th>
                    <th nowrap>Nama Pengantin</th>
                    <th nowrap>Nama Tamu</th>
                    <th nowrap>URL Undangan</th>
                    <th nowrap>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $data = $weddingku->query("
                SELECT uu.*, 
                    CONCAT(p.nama_pria, ' & ', p.nama_wanita) AS nama_pengantin
                FROM undangan_url uu
                JOIN pengantin p ON uu.pengantin_id = p.id
                ");
                $no = 1;
                while ($row = $data->fetch_assoc()) {
                    // Ambil semua nama tamu terkait dengan undangan_url
                    $tamuQuery = $weddingku->query("
                        SELECT t.nama
                        FROM tamu t
                        JOIN undangan_url uu ON t.id = uu.tamu_id
                        WHERE uu.id = {$row['id']}
                    ");
                    
                    // Menyusun nama tamu menjadi satu string
                    $tamuList = [];
                    while ($tamu = $tamuQuery->fetch_assoc()) {
                        $tamuList[] = $tamu['nama'];
                    }
                    $namaTamu = implode(", ", array_map('htmlspecialchars', $tamuList)); // Gabungkan nama tamu dengan koma

                    echo "<tr>
                        <td nowrap>" . htmlspecialchars($no) . "</td>
                        <td nowrap>" . htmlspecialchars($row['nama_pengantin']) . "</td>
                        <td nowrap>" . $namaTamu . "</td>
                        <td nowrap><a href='{$row['url_undangan']}' target='_blank'>{$row['url_undangan']}</a></td>
                        <td nowrap>
                            <button class='btn btn-sm btn-outline-warning' data-bs-toggle='modal' data-bs-target='#modalEditUndangan{$row['id']}'>
                                <i class='fas fa-edit'></i> Edit
                            </button>
                            <a href='" . csrf_url("undangan_url_action.php?hapus={$row['id']}") . "' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Yakin ingin hapus?\")'>
                                <i class='fas fa-trash'></i> Hapus
                            </a>
                        </td>
                    </tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>

    </section>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahUndangan" tabindex="-1">
    <div class="modal-dialog">
        <form action="undangan_url_action.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah URL Undangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label>Nama Pengantin</label>
                    <select name="pengantin_id" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php
                        $pengantin = $weddingku->query("SELECT * FROM pengantin");
                        while ($p = $pengantin->fetch_assoc()) {
                            $nama_pengantin = $p['nama_pria'] . ' & ' . $p['nama_wanita'];
                            echo "<option value='{$p['id']}'>{$nama_pengantin}</option>";
                        }
                        ?>
                    </select>
                </div>
                <!-- <div class="mb-3">
                    <label>Nama Tamu</label>
                    <select name="tamu_id" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php
                        $tamu = $weddingku->query("SELECT * FROM tamu");
                        while ($t = $tamu->fetch_assoc()) {
                            echo "<option value='{$t['id']}'>{$t['nama']}</option>";
                        }
                        ?>
                    </select>
                </div> -->
                <div class="mb-3">
                    <label>Nama Tamu</label>
                    <div class="input-group">
                        <textarea name="tamu_ids" id="tamu_ids" class="form-control" placeholder="Klik cari untuk pilih tamu" rows="2" readonly></textarea>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCariTamu">Cari</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Cari Tamu -->
<div class="modal fade" id="modalCariTamu" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Tamu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped table-hover display" style="width:100%" id="tamuTable" nowrap>
                    <thead>
                        <tr>
                            <th nowrap>No</th>
                            <th nowrap>Pilih</th>
                            <th nowrap>Nama</th>
                            <th nowrap>Alamat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $tamu = $weddingku->query("SELECT * FROM tamu");
                        $no = 1;
                        while ($t = $tamu->fetch_assoc()) {
                        ?>
                        <tr>
                            <td nowrap><?= $no++; ?></td>
                            <td nowrap><input type="checkbox" class="tamu-check" data-id="<?= $t['id']; ?>" data-nama="<?= htmlspecialchars($t['nama']); ?>"></td>
                            <td nowrap><?= htmlspecialchars($t['nama']); ?></td>
                            <td nowrap><?= htmlspecialchars($t['alamat']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btnPilihTamu" data-bs-dismiss="modal">Pilih</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>



<!-- Modal Edit -->
<?php
$data = $weddingku->query("
    SELECT uu.*, 
           CONCAT(p.nama_pria, ' & ', p.nama_wanita) AS nama_pengantin, 
           t.nama AS nama_tamu
    FROM undangan_url uu
    JOIN pengantin p ON uu.pengantin_id = p.id
    JOIN tamu t ON uu.tamu_id = t.id
");

while ($row = $data->fetch_assoc()) {
?>
<div class="modal fade" id="modalEditUndangan<?php echo $row['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <form action="undangan_url_action.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit URL Undangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <div class="mb-3">
                    <label>Nama Pengantin</label>
                    <select name="pengantin_id" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php
                        $pengantin = $weddingku->query("SELECT * FROM pengantin");
                        while ($p = $pengantin->fetch_assoc()) {
                            $nama_pengantin = $p['nama_pria'] . ' & ' . $p['nama_wanita'];
                            $selected = $p['id'] == $row['pengantin_id'] ? 'selected' : '';
                            echo "<option value='{$p['id']}'>" . htmlspecialchars($nama_pengantin) . "</option>";
                            if ($pengantin->num_rows == 0) {
                                echo "<option disabled>Tidak ada data pengantin</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <!-- <div class="mb-3">
                    <label>Nama Tamu</label>
                    <select name="tamu_id" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php
                        $tamu = $weddingku->query("SELECT * FROM tamu");
                        while ($t = $tamu->fetch_assoc()) {
                            $selected = $t['id'] == $row['tamu_id'] ? 'selected' : '';
                            echo "<option value='{$t['id']}'>" . htmlspecialchars($t['nama']) . "</option>";
                        }
                        ?>
                    </select>
                </div> -->
                <div class="mb-3">
                    <label>Nama Tamu</label>
                    <div class="input-group">
                        <textarea name="nama_tamu_display" id="nama_tamu_display_<?php echo $row['id']; ?>" class="form-control" readonly required><?php echo htmlspecialchars($row['nama_tamu']); ?></textarea>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalCariTamu<?php echo $row['id']; ?>">Cari</button>
                    </div>
                    <!-- Hidden input buat simpan ID tamu -->
                    <input type="hidden" name="tamu_id" id="tamu_id_<?php echo $row['id']; ?>" value="<?php echo $row['tamu_id']; ?>">
                </div>
                <div class="mb-3">
                    <label>URL Undangan</label>
                    <input type="text" name="url_undangan" class="form-control" value="<?php echo htmlspecialchars($row['url_undangan']); ?>" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>
<?php } ?>

<div class="modal fade" id="modalCariTamu<?php echo $row['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Tamu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped table-hover display" style="width:100%" id="tamuTable" nowrap>
                    <thead>
                        <tr>
                            <th nowrap>No</th>
                            <th nowrap>Pilih</th>
                            <th nowrap>Nama</th>
                            <th nowrap>Alamat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $listTamu = $weddingku->query("SELECT * FROM tamu");
                        $no = 1;
                        while ($t = $listTamu->fetch_assoc()) {
                            $idTamu = $t['id'];
                            $namaTamu = htmlspecialchars(addslashes($t['nama']));
                            $alamatTamu = htmlspecialchars($t['alamat']);
                            ?>
                            <tr>
                                <td nowrap><?= $no++; ?>
                                <td nowrap>
                                    <input type="radio" name="pilih_tamu_<?php echo $row['id']; ?>"
                                        onclick="pilihTamu('<?php echo $row['id']; ?>', '<?php echo $idTamu; ?>', '<?php echo $namaTamu; ?>')">
                                </td>
                                <td nowrap><?php echo htmlspecialchars($t['nama']); ?></td>
                                <td nowrap><?php echo $alamatTamu; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    $(document).ready(function() {
        $('#tabelUndangan').DataTable({
            responsive: true,
            scrollX: true,
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

    $(document).ready(function() {
        $('#tamuTable').DataTable({
            responsive: true,
            scrollX: true,
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

    // Untuk Modal Tambah
    document.getElementById('btnPilihTamu').addEventListener('click', function () {
        let selectedIds = [];
        let selectedNames = [];

        document.querySelectorAll('.tamu-check:checked').forEach(function (checkbox) {
            selectedIds.push(checkbox.getAttribute('data-id'));
            selectedNames.push(checkbox.getAttribute('data-nama'));
        });

        document.getElementById('tamu_ids').value = selectedIds.join(',');
        document.getElementById('tamu_ids').setAttribute('data-nama', selectedNames.join(', '));
    });

    // Untuk Modal Edit
    <?php
    $data->data_seek(0); // Reset pointer query
    while ($row = $data->fetch_assoc()) {
    ?>
    document.querySelectorAll('#modalCariTamu<?php echo $row['id']; ?> .tamu-check').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const selected = this;
            const namaTamu = selected.getAttribute('data-nama');
            const idTamu = selected.getAttribute('data-id');

            document.getElementById('nama_tamu_display_<?php echo $row['id']; ?>').value = namaTamu;
            document.getElementById('tamu_id_<?php echo $row['id']; ?>').value = idTamu;

            // Hanya izinkan satu pilihan
            document.querySelectorAll('#modalCariTamu<?php echo $row['id']; ?> .tamu-check').forEach(c => {
                if (c !== selected) c.checked = false;
            });
        });
    });
    <?php } ?>

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('service-worker.js')
        .then(function(registration) {
            console.log('Service Worker registered with scope:', registration.scope);
        }).catch(function(error) {
            console.log('Service Worker registration failed:', error);
        });
    }
</script>

