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
                    <a href="dashboard.php" class="hover:text-blue-400 transition">Home</a>
                    <span>/</span>
                    <span class="text-blue-400 font-medium">Data Tamu Undangan</span>
                </nav>
                <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-users"></i>
                    </span>
                    Data Tamu Undangan
                </h1>
            </div>
            <div>
                <button class="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-400 hover:to-cyan-400 text-white font-medium px-4 py-2.5 rounded-xl text-sm shadow-lg shadow-blue-500/20 transition flex items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#modalTambahTamu">
                    <i class="fa-solid fa-user-plus"></i> Tambah Tamu
                </button>
            </div>
        </div>

        <!-- Import Section Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 mb-6 shadow-xl">
            <h2 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-emerald-400"></i> Import Data Tamu dari Excel
            </h2>
            <form action="import_tamu.php" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <?php
                $pengantinImportList = $weddingku->query("SELECT id, CONCAT(nama_pria, ' & ', nama_wanita) as nama_pengantin FROM pengantin");
                ?>
                <select name="pengantin_id" class="form-select flex-1 sm:max-w-xs" required>
                    <option value="">-- Pilih Pengantin --</option>
                    <?php while ($p = $pengantinImportList->fetch_assoc()) {
                        echo "<option value='{$p['id']}'>" . htmlspecialchars($p['nama_pengantin']) . "</option>";
                    } ?>
                </select>
                <input type="file" name="file_excel" class="form-control flex-1" accept=".xls,.xlsx" required>
                <button type="submit" name="import" class="btn btn-success d-inline-flex align-items-center gap-2 px-4">
                    <i class="fa-solid fa-file-import"></i> Import
                </button>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="overflow-x-auto">
                <table class="table table-bordered table-striped table-hover display w-full" id="tabelTamu">
                    <thead>
                        <tr>
                            <th>Pasangan Pengantin</th>
                            <th>Nama Tamu</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $data = $weddingku->query("
                        SELECT tamu.*, 
                               CONCAT(pengantin.nama_pria, ' & ', pengantin.nama_wanita) AS nama_pengantin
                        FROM tamu
                        LEFT JOIN pengantin ON tamu.pengantin_id = pengantin.id
                    ");
                    
                    while ($row = $data->fetch_assoc()) {
                        echo "<tr>
                            <td class='font-medium text-pink-400'>" . h($row['nama_pengantin'] ?? '-') . "</td>
                            <td class='font-semibold text-white'>" . h($row['nama']) . "</td>
                            <td class='text-slate-300'>" . h($row['alamat'] ?? '-') . "</td>
                            <td class='whitespace-nowrap'>
                                <button class='btn btn-sm btn-outline-warning me-1' data-bs-toggle='modal' data-bs-target='#modalEditTamu{$row['id']}'>
                                    <i class='fa-solid fa-pen-to-square'></i>
                                </button>
                                <form method='POST' action='tamu_action.php' class='d-inline' onsubmit=\"return confirm('Yakin hapus data tamu ini?')\">
                                    " . csrf_field() . "<input type='hidden' name='id' value='{$row['id']}'><button type='submit' name='hapus' class='btn btn-sm btn-outline-danger'><i class='fa-solid fa-trash-can'></i></button>
                                </form>
                            </td>
                        </tr>";

                        // Modal Edit Tamu
                        echo "
                        <div class='modal fade' id='modalEditTamu{$row['id']}' tabindex='-1' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <form method='POST' action='tamu_action.php'>
                                        <div class='modal-header'>
                                            <h5 class='modal-title font-bold text-white flex items-center gap-2'>
                                                <i class='fa-solid fa-pen-to-square text-amber-400'></i> Edit Data Tamu
                                            </h5>
                                            <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                        </div>
                                        <div class='modal-body'>
                                            " . csrf_field() . "
                                            <input type='hidden' name='id' value='{$row['id']}'>
                                            
                                            <div class='mb-3'>
                                                <label class='form-label text-xs text-slate-300'>Pasangan Pengantin</label>
                                                <select name='pengantin_id' class='form-select' required>
                                                    <option value=''>-- Pilih Pengantin --</option>";
                                                    $pengantinList = $weddingku->query("SELECT id, CONCAT(nama_pria, ' & ', nama_wanita) as nama_pengantin FROM pengantin");
                                                    while ($p = $pengantinList->fetch_assoc()) {
                                                        $selected = $p['id'] == $row['pengantin_id'] ? "selected" : "";
                                                        echo "<option value='{$p['id']}' $selected>" . htmlspecialchars($p['nama_pengantin']) . "</option>";
                                                    }
                        echo "                  </select>
                                            </div>

                                            <div class='mb-3'>
                                                <label class='form-label text-xs text-slate-300'>Nama Tamu</label>
                                                <input type='text' name='nama' class='form-control' value='" . h($row['nama']) . "' required>
                                            </div>

                                            <div class='mb-3'>
                                                <label class='form-label text-xs text-slate-300'>Alamat / Kota</label>
                                                <textarea name='alamat' class='form-control' rows='3'>" . h($row['alamat']) . "</textarea>
                                            </div>
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
                                            <button type='submit' name='update' class='btn btn-primary'>Update Tamu</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Tamu -->
<div class="modal fade" id="modalTambahTamu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="tamu_action.php">
                <div class="modal-header">
                    <h5 class="modal-title font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-user-plus text-blue-400"></i> Tambah Tamu Undangan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <?php
                    $pengantinList = $weddingku->query("SELECT id, CONCAT(nama_pria, ' & ', nama_wanita) as nama_pengantin FROM pengantin");
                    ?>
                    <div class="mb-3">
                        <label class="form-label text-xs text-slate-300">Pasangan Pengantin</label>
                        <select name="pengantin_id" class="form-select" required>
                            <option value="">-- Pilih Pengantin --</option>
                            <?php while ($p = $pengantinList->fetch_assoc()) {
                                echo "<option value='{$p['id']}'>" . htmlspecialchars($p['nama_pengantin']) . "</option>";
                            } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-xs text-slate-300">Nama Tamu</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Bpk. Bambang & Keluarga" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-xs text-slate-300">Alamat / Kota</label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Contoh: Jakarta Selatan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Tamu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    $('#tabelTamu').DataTable({
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
</script>
