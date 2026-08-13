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
                <li class="breadcrumb-item active" aria-current="page">Data Pesan untuk pengantin</li>
            </ol>
        </nav>
    </div>
    <section class="content p-3">
        </br></br>
        <h4>Pesan untuk Pengantin</h4>
        <button class="btn btn-success mb-3 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahPesan">
            <i class="fas fa-plus-circle me-1"></i> Tambah Ucapan
        </button>

        <?php
        $pengantinList = $weddingku->query("SELECT id, nama_pria, nama_wanita FROM pengantin");
        ?>

        <table class="table table-bordered table-striped table-hover display" style="width:100%" nowrap id="tabelMessage">
            <thead>
                <tr>
                    <th>Nama Pengirim</th>
                    <th>Isi Pesan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $data = $weddingku->query("SELECT * FROM message");
            while ($row = $data->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['nama']}</td>
                    <td>{$row['pesan']}</td>
                    <td>
                        <button class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#modalEditPesan{$row['id']}'>Edit</button>
                        <a href='" . csrf_url("message_action.php?hapus={$row['id']}") . "' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin hapus pesan ini?')\">Hapus</a>
                    </td>
                </tr>";

                // Modal Edit Pesan
                echo "
                <div class='modal fade' id='modalEditPesan{$row['id']}'>
                    <div class='modal-dialog'>
                <div class='modal-content border-warning'>
                    <form method='POST' action='message_action.php'>
                        <div class='modal-header bg-warning text-dark'>
                            <h5 class='modal-title'><i class='fas fa-edit me-2'></i>Edit Ucapan</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                        </div>
                        <div class='modal-body'>
                            <?= csrf_field() ?>
                                    <input type='hidden' name='id' value='{$row['id']}'>
                                    <div class='form-group mb-3'>
                                        <label>Nama Pengirim</label>
                                        <input type='text' name='nama_pengirim' class='form-control' value='{$row['nama']}' required>
                                    </div>
                                    <div class='form-group'>
                                        <label>Isi Pesan</label>
                                        <textarea name='isi_pesan' class='form-control' rows='4' required>{$row['pesan']}</textarea>
                                    </div>
                                </div>
                                <div class='modal-footer'>
                                <button type='submit' name='update' class='btn btn-warning text-dark rounded-pill px-4'>
                                    <i class='fas fa-save me-1'></i> Update Pesan
                                </button>
                                <button type='button' class='btn btn-outline-secondary rounded-pill px-4' data-bs-dismiss='modal'>
                                    <i class='fas fa-times me-1'></i> Batal
                                </button>
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

<!-- Modal Tambah Pesan -->
<div class="modal fade" id="modalTambahPesan">
    <div class="modal-dialog">
        <div class="modal-content border-primary">
            <form method="POST" action="message_action.php">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-envelope-open-text me-2"></i>Tambah Ucapan Spesial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class='form-group mb-3'>
                        <label>Untuk Pengantin</label>
                        <select name='pengantin_id' class='form-select' required>
                            <option value=''>-- Pilih Pengantin --</option>
                            <?php while ($p = $pengantinList->fetch_assoc()): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= $p['nama_pria'] ?> & <?= $p['nama_wanita'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="nama_pengirim">Nama Pengirim</label>
                        <input type="text" name="nama_pengirim" class="form-control" placeholder="Contoh: Aminah dari Bandung" required>
                    </div>
                    <div class="form-group">
                        <label for="isi_pesan">Isi Pesan</label>
                        <textarea name="isi_pesan" class="form-control" rows="4" placeholder="Tulis ucapan selamat untuk pengantin..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="simpan" class="btn btn-success rounded-pill px-4">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Pesan
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>

                </div>
            </form>
        </div>
    </div>
</div>


<?php include 'footer.php'; ?>

<script>
    $(document).ready(function() {
        $('#tabelMessage').DataTable({
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
