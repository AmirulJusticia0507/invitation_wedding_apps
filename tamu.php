<?php include 'auth_check.php'; ?>
<?php include 'koneksi.php'; ?>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-wrapper">
    <!-- Breadcrumb -->
    <div class="breadcrumb-section">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data Tamu Undangan</li>
            </ol>
        </nav>
    </div>
    <section class="content p-3">
        </br></br>
        <h4>Data Tamu Undangan</h4>
        <button class="btn btn-outline-primary mb-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahTamu">
            <i class="fas fa-user-plus"></i> Tambah Tamu
        </button>

        </br></br>
        <form action="import_tamu.php" method="POST" enctype="multipart/form-data" class="mb-3">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="input-group" style="max-width: 500px;">
                <input type="file" name="file_excel" class="form-control" accept=".xls,.xlsx" required>
                <button type="submit" name="import" class="btn btn-success">
                    <i class="fa fa-upload"></i> Import Excel
                </button>
            </div>
        </form>

        <table class="table table-bordered table-striped table-hover display" style="width:100%" nowrap id="tabelTamu">
            <thead>
                <tr>
                    <th>Pengantin</th>
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
                    <td>{$row['nama_pengantin']}</td>
                    <td>{$row['nama']}</td>
                    <td>{$row['alamat']}</td>
                    <td>
                        <button class='btn btn-sm btn-outline-warning d-inline-flex align-items-center gap-1' data-bs-toggle='modal' data-bs-target='#modalEditTamu{$row['id']}'>
                            <i class='fas fa-edit'></i> Edit
                        </button>
                    <a href='" . csrf_url("tamu_action.php?hapus={$row['id']}") . "' class='btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1' onclick=\"return confirm('Yakin hapus data tamu?')\">
                            <i class='fas fa-trash-alt'></i> Hapus
                        </a>
                    </td>
                </tr>";

                // Modal Edit
                echo "
                <div class='modal fade' id='modalEditTamu{$row['id']}'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <form method='POST' action='tamu_action.php'>
                                <div class='modal-header'>
                                    <h5>Edit Tamu</h5>
                                </div>
                                <div class='modal-body'>
                                    <?= csrf_field() ?>
                                    <input type='hidden' name='id' value='{$row['id']}'>
                                    <input type='text' name='nama' class='form-control mb-2' value='{$row['nama']}' required>
                                    <textarea name='alamat' class='form-control mb-2'>{$row['alamat']}</textarea>

                                    <select name='pengantin_id' class='form-control mb-2' required>
                                        <option value=''>-- Pilih Pengantin --</option>";
                                        $pengantinList = $weddingku->query("SELECT id, CONCAT(nama_pria, ' & ', nama_wanita) as nama_pengantin FROM pengantin");
                                        while ($p = $pengantinList->fetch_assoc()) {
                                            $selected = $p['id'] == $row['pengantin_id'] ? "selected" : "";
                                            echo "<option value='{$p['id']}' $selected>{$p['nama_pengantin']}</option>";
                                        }
                echo "              </select>
                                </div>
                                <div class='modal-footer'>
                                    <button type='submit' name='update' class='btn btn-success'>Update</button>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>";
            }
            ?>
            </tbody>
        </table>
    </section>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahTamu">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="tamu_action.php">
                <div class="modal-header"><h5>Tambah Tamu</h5></div>
                <div class="modal-body">
                <?= csrf_field() ?>
                <?php
                $pengantinList = $weddingku->query("SELECT id, CONCAT(nama_pria, ' & ', nama_wanita) as nama_pengantin FROM pengantin");
                ?>

                <select name="pengantin_id" class="form-control mb-2" required>
                    <option value="">-- Pilih Pengantin --</option>
                    <?php while ($p = $pengantinList->fetch_assoc()) {
                        echo "<option value='{$p['id']}'>{$p['nama_pengantin']}</option>";
                    } ?>
                </select>

                    <input type="text" name="nama" class="form-control mb-2" placeholder="Nama Tamu" required>
                    <textarea name="alamat" class="form-control mb-2" placeholder="Alamat"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="simpan" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" data-bs-dismiss="modal">
                        <i class="fas fa-times-circle"></i> Batal
                    </button>
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
