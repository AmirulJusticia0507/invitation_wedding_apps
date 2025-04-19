<?php include 'koneksi.php'; ?>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-wrapper">
    <!-- Breadcrumb -->
    <div class="breadcrumb-section">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data Story Pengantin</li>
            </ol>
        </nav>
    </div>
    <section class="content p-3">
        </br></br>
        <h4>Love Story</h4>
        <button class="btn btn-success mb-3 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahStory">
            <i class="fas fa-heart me-1"></i> Cerita Baru
        </button>

        <table class="table table-bordered table-striped table-hover display" style="width:100%" nowrap id="tabelStory">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Cerita</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $data = $weddingku->query("SELECT s.*, CONCAT(p.nama_pria, ' & ', p.nama_wanita) as nama_pengantin 
            FROM story s 
            JOIN pengantin p ON s.pengantin_id = p.id");
            
            while ($row = $data->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['judul']}</td>
                    <td>{$row['cerita']}</td>
                    <td>{$row['tanggal']}</td>
                    <td>
                        <button class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#modalEditStory{$row['id']}'>Edit</button>
                        <a href='story_action.php?hapus={$row['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin ingin menghapus cerita ini?')\">Hapus</a>
                    </td>
                </tr>";

                // Modal Edit
                echo "
                <div class='modal fade' id='modalEditStory{$row['id']}'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <form method='POST' action='story_action.php'>
                                <div class='modal-header'><h5>Edit Cerita</h5></div>
                                <div class='modal-body'>
                                    <input type='hidden' name='id' value='{$row['id']}'>
                                    <select name='pengantin_id' class='form-control mb-2' required>
                                            <option value=''>-- Pilih Pengantin --</option>";
                                            $pengantinList = $weddingku->query("SELECT id, CONCAT(nama_pria, ' & ', nama_wanita) as nama_pengantin FROM pengantin");
                                            while ($p = $pengantinList->fetch_assoc()) {
                                                $selected = $p['id'] == $row['pengantin_id'] ? "selected" : "";
                                                echo "<option value='{$p['id']}' $selected>{$p['nama_pengantin']}</option>";
                                            }
                                    echo "  </select>
                                    <input type='text' name='judul' class='form-control mb-2' value='{$row['judul']}' required>
                                    <textarea name='cerita' class='form-control mb-2'>{$row['cerita']}</textarea>
                                    <input type='date' name='tanggal' class='form-control mb-2' value='{$row['tanggal']}' required>
                                </div>
                                <div class='modal-footer'>
                                    <button type='submit' name='update' class='btn btn-warning text-dark rounded-pill px-4'>
                                        <i class='fas fa-edit me-1'></i> Perbarui Cerita
                                    </button>
                                    <button type='button' class='btn btn-outline-dark rounded-pill px-4' data-bs-dismiss='modal'>
                                        <i class='fas fa-times me-1'></i> Tutup
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

<!-- Modal Tambah Story -->
<div class="modal fade" id="modalTambahStory">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="story_action.php">
                <div class="modal-header"><h5>Tambah Cerita</h5></div>
                <div class="modal-body">
                    <select name="pengantin_id" class="form-control mb-2" required>
                        <option value="">-- Pilih Pengantin --</option>
                        <?php
                        $pengantin = $weddingku->query("SELECT id, CONCAT(nama_pria, ' & ', nama_wanita) as nama_pengantin FROM pengantin");
                        while ($p = $pengantin->fetch_assoc()) {
                            echo "<option value='{$p['id']}'>{$p['nama_pengantin']}</option>";
                        }
                        ?>
                    </select>
                    <input type="text" name="judul" class="form-control mb-2" placeholder="Judul Cerita" required>
                    <textarea name="cerita" class="form-control mb-2" placeholder="Isi Cerita" required></textarea>
                    <input type="date" name="tanggal" class="form-control mb-2" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="simpan" class="btn btn-pink text-blue rounded-pill px-4">
                        <i class="fas fa-feather-alt me-1"></i> Kirim Cerita
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
        $('#tabelStory').DataTable({
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
