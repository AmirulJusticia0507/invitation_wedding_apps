<?php include 'auth_check.php'; ?>
<?php include 'koneksi.php'; ?>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-wrapper">
    <div class="breadcrumb-section">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data Folder</li>
            </ol>
        </nav>
    </div>

    <section class="content p-3">
        <h4>Data Folder</h4>
        <button class="btn btn-outline-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahFolder">
            <i class="fas fa-folder-plus"></i> Tambah Folder
        </button>

        <table class="table table-bordered table-striped" id="tabelFolder">
            <thead>
                <tr>
                    <th>Nama Folder</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = $weddingku->query("SELECT * FROM folders");
                while ($row = $query->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['nama_folder']}</td>
                            <td>{$row['deskripsi']}</td>
                            <td>
                                <button class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#modalEditFolder{$row['id']}'>Edit</button>
                                <a href='" . csrf_url("folder_action.php?hapus={$row['id']}") . "' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin hapus folder?')\">Hapus</a>
                            </td>
                        </tr>";

                    // Modal Edit
                    echo "
                    <div class='modal fade' id='modalEditFolder{$row['id']}'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <form method='POST' action='folder_action.php'>
                                    <div class='modal-header'>
                                        <h5>Edit Folder</h5>
                                    </div>
                                    <div class='modal-body'>
                                        <?= csrf_field() ?>
                                        <input type='hidden' name='id' value='{$row['id']}'>
                                        <input type='text' name='nama_folder' class='form-control mb-2' value='{$row['nama_folder']}' required>
                                        <textarea name='deskripsi' class='form-control mb-2'>{$row['deskripsi']}</textarea>
                                        
                                        <div class='form-group mt-2'>
                                            <label for='pengantin_id'>Pasangan</label>
                                            <select name='pengantin_id' class='form-control'>";
                                            
                                            // Ambil data pasangan pengantin berdasarkan pengantin_id yang terkait dengan folder
                                            $pengantinQuery = $weddingku->query("SELECT id, nama_panggilan_pria, nama_panggilan_wanita FROM pengantin");
                                            while ($pengantin = $pengantinQuery->fetch_assoc()) {
                                                $selected = $pengantin['id'] == $row['pengantin_id'] ? 'selected' : '';
                                                echo "<option value='{$pengantin['id']}' {$selected}>
                                                        {$pengantin['nama_panggilan_pria']} & {$pengantin['nama_panggilan_wanita']}
                                                    </option>";
                                            }

                    echo "
                                            </select>
                                        </div>
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
<div class="modal fade" id="modalTambahFolder">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="folder_action.php">
                <div class="modal-header"><h5>Tambah Folder</h5></div>
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="text" name="nama_folder" class="form-control mb-2" placeholder="Nama Folder" required>
                    <textarea name="deskripsi" class="form-control mb-2" placeholder="Deskripsi (Opsional)"></textarea>
                    <!-- Dropdown Pengantin -->
                    <label for="pengantin_id">Pilih Pasangan</label>
                    <select name="pengantin_id" class="form-control mb-2" required>
                        <option value="">Pilih Pasangan</option>
                        <?php
                        // Ambil data pasangan pengantin
                        include 'koneksi.php';
                        $result = $weddingku->query("SELECT id, nama_panggilan_pria, nama_panggilan_wanita FROM pengantin");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>
                                    {$row['nama_panggilan_pria']} & {$row['nama_panggilan_wanita']}
                                  </option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php include 'footer.php'; ?>

<script>
$(document).ready(function () {
    $('#tabelFolder').DataTable({
        responsive: true,
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
            }
        }
    });
});
</script>
