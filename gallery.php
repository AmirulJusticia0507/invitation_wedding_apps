<?php include 'koneksi.php'; ?>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<?php
$pengantinId = isset($_GET['pengantin_id']) ? $_GET['pengantin_id'] : '';
$filterQuery = $pengantinId ? "WHERE pengantin_id = $pengantinId" : '';
?>

<div class="content-wrapper">
    <!-- Breadcrumb -->
    <div class="breadcrumb-section">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data Gallery Foto Pengantin</li>
            </ol>
        </nav>
    </div>
    <section class="content p-3">
        </br></br>
        <h4>Galeri Foto</h4>
        <?php
        $pengantinId = isset($_GET['pengantin_id']) ? (int) $_GET['pengantin_id'] : '';
        $filterQueryGallery = $pengantinId ? "WHERE pengantin_id = $pengantinId" : '';
        $filterQueryTable = $pengantinId ? "WHERE g.pengantin_id = $pengantinId" : '';
        ?>


        <!-- Form filter -->
        <form method="GET" class="mb-3 d-flex align-items-center gap-2">
            <label for="pengantin_id" class="form-label mb-0 me-2">Pilih Pengantin:</label>
            <select name="pengantin_id" id="pengantin_id" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="">-- Semua Pengantin --</option>
                <?php
                $pengantin = $weddingku->query("SELECT * FROM pengantin");
                while ($p = $pengantin->fetch_assoc()) {
                    $namaLengkap = $p['nama_pria'] . ' & ' . $p['nama_wanita'];
                    $selected = ($p['id'] == $pengantinId) ? 'selected' : '';
                    echo "<option value='{$p['id']}' $selected>$namaLengkap</option>";
                }
                ?>
            </select>
            <?php if ($pengantinId): ?>
                <a href="gallery.php" class="btn btn-secondary btn-sm ms-2">Reset Filter</a>
            <?php endif; ?>
        </form>

        <button class="btn btn-success mb-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahFoto">
            <i class="fas fa-plus-circle"></i> Tambah Galeri
        </button>

        <div class="row">
            <?php
            $data = $weddingku->query("SELECT * FROM gallery $filterQueryGallery ORDER BY tanggal_upload DESC");
            while ($row = $data->fetch_assoc()) {
                echo "
                <div class='col-md-3 mb-4'>
                    <div class='card'>
                        <img src='assets/gallery/{$row['file']}' class='card-img-top' alt='{$row['judul']}' style='height:200px; object-fit:cover;'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$row['judul']}</h5>
                            <p class='card-text'><small>{$row['tanggal_upload']}</small></p>
                            <button class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#modalEditFoto{$row['id']}'>Edit</button>
                            <a href='gallery_action.php?hapus={$row['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin ingin menghapus foto ini?')\">Hapus</a>
                        </div>
                    </div>
                </div>
                ";
                // Modal Edit Foto (tetap sama)
            }
            ?>
        </div>

        <!-- Tabel Galeri -->
        <hr>
        <h5>Daftar Foto</h5>
        <table class="table table-bordered table-striped table-hover display" style="width:100%" nowrap id="tabelGallery">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Foto</th>
                    <th>Pengantin</th>
                    <th>Tanggal Upload</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $data = $weddingku->query("
                    SELECT g.*, p.nama_pria, p.nama_wanita 
                    FROM gallery g
                    LEFT JOIN pengantin p ON g.pengantin_id = p.id
                    $filterQueryTable
                    ORDER BY g.tanggal_upload DESC
                ");            
                while ($row = $data->fetch_assoc()) {
                $nama_pengantin = $row['nama_pria'] . ' & ' . $row['nama_wanita'];
                echo "
                    <tr>
                    <td>{$row['judul']}</td>
                    <td><img src='assets/gallery/{$row['file']}' width='100'></td>
                    <td>$nama_pengantin</td>
                    <td>{$row['tanggal_upload']}</td>
                    <td>
                        <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#modalEditFoto{$row['id']}'>Edit</button>
                        <a href='gallery_action.php?hapus={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Hapus foto ini?\")'>Hapus</a>
                    </td>
                    </tr>
                ";
                }
                ?>
            </tbody>
        </table>
    </section>
</div>

<!-- Modal Tambah Foto -->
<div class="modal fade" id="modalTambahFoto" tabindex="-1" aria-labelledby="modalTambahFotoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="gallery_action.php" enctype="multipart/form-data">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="modalTambahFotoLabel"><i class="fas fa-image"></i> Tambah Foto Baru</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="judul" class="form-label">Judul Foto</label>
            <input type="text" name="judul" class="form-control" placeholder="Misal: Momen Akad Nikah" required>
          </div>
          <div class="mb-3">
            <label for="pengantin_id" class="form-label">Pilih Pasangan Pengantin</label>
            <select name="pengantin_id" class="form-select" required>
              <option value="">-- Pilih --</option>
              <?php
              $pengantin = $weddingku->query("SELECT * FROM pengantin");
              while ($p = $pengantin->fetch_assoc()) {
                $nama = $p['nama_pria'] . " & " . $p['nama_wanita'];
                echo "<option value='{$p['id']}'>$nama</option>";
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="file" class="form-label">Upload Foto</label>
            <input type="file" name="file" class="form-control" accept="image/*" required>
            <small class="text-muted">Hanya gambar: JPG, PNG, atau JPEG</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="simpan" class="btn btn-success">
            <i class="fas fa-save"></i> Simpan Galeri
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>


<script>
    $(document).ready(function() {
        $('#tabelGallery').DataTable({
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