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
                    <a href="dashboard.php" class="hover:text-emerald-400 transition">Home</a>
                    <span>/</span>
                    <span class="text-emerald-400 font-medium">Galeri Foto Pengantin</span>
                </nav>
                <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-images"></i>
                    </span>
                    Galeri Foto Pengantin
                </h1>
            </div>
            <div>
                <button class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white font-medium px-4 py-2.5 rounded-xl text-sm shadow-lg shadow-emerald-500/20 transition flex items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#modalTambahFoto">
                    <i class="fa-solid fa-plus-circle"></i> Tambah Foto Baru
                </button>
            </div>
        </div>

        <?php
        $pengantinId = isset($_GET['pengantin_id']) ? (int) $_GET['pengantin_id'] : '';
        $filterQueryGallery = $pengantinId ? "WHERE pengantin_id = $pengantinId" : '';
        $filterQueryTable = $pengantinId ? "WHERE g.pengantin_id = $pengantinId" : '';
        ?>

        <!-- Filter Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 mb-6 shadow-xl flex flex-wrap items-center justify-between gap-3">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <label for="pengantin_id" class="text-xs font-medium text-slate-300">Filter Pasangan:</label>
                <select name="pengantin_id" id="pengantin_id" class="form-select text-sm py-1.5" style="width: auto;" onchange="this.form.submit()">
                    <option value="">-- Semua Pasangan Pengantin --</option>
                    <?php
                    $pengantin = $weddingku->query("SELECT * FROM pengantin");
                    while ($p = $pengantin->fetch_assoc()) {
                        $namaLengkap = $p['nama_pria'] . ' & ' . $p['nama_wanita'];
                        $selected = ($p['id'] == $pengantinId) ? 'selected' : '';
                        echo "<option value='{$p['id']}' $selected>" . htmlspecialchars($namaLengkap) . "</option>";
                    }
                    ?>
                </select>
                <?php if ($pengantinId): ?>
                    <a href="gallery.php" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-2 rounded-lg transition">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Gallery Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
            <?php
            $data = $weddingku->query("SELECT * FROM gallery $filterQueryGallery ORDER BY tanggal_upload DESC");
            if ($data->num_rows > 0) {
                while ($row = $data->fetch_assoc()) {
                    $imgUrl = "assets/gallery/" . htmlspecialchars($row['file']);
                    echo "
                    <div class='bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-lg group hover:border-emerald-500/40 transition duration-300'>
                        <div class='relative h-48 overflow-hidden bg-slate-950'>
                            <a href='{$imgUrl}' data-lightbox='grid-galeri'>
                                <img src='{$imgUrl}' class='w-full h-full object-cover group-hover:scale-105 transition duration-300' alt='" . htmlspecialchars($row['judul']) . "'>
                            </a>
                        </div>
                        <div class='p-4'>
                            <h5 class='font-semibold text-white text-sm truncate'>" . htmlspecialchars($row['judul']) . "</h5>
                            <p class='text-xs text-slate-500 mt-1 mb-3'><i class='fa-regular fa-calendar text-[10px] mr-1'></i> " . htmlspecialchars($row['tanggal_upload']) . "</p>
                            <div class='flex gap-2'>
                                <button class='flex-1 btn btn-sm btn-outline-warning text-xs' data-bs-toggle='modal' data-bs-target='#modalEditFoto{$row['id']}'>
                                    <i class='fa-solid fa-pen-to-square'></i> Edit
                                </button>
                                <a href='" . csrf_url("gallery_action.php?hapus={$row['id']}") . "' class='btn btn-sm btn-outline-danger text-xs' onclick=\"return confirm('Yakin ingin menghapus foto ini?')\">
                                    <i class='fa-solid fa-trash-can'></i>
                                </a>
                            </div>
                        </div>
                    </div>";

                    // Modal Edit Foto
                    echo "
                    <div class='modal fade' id='modalEditFoto{$row['id']}' tabindex='-1' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <form method='POST' action='gallery_action.php' enctype='multipart/form-data'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title font-bold text-white flex items-center gap-2'>
                                            <i class='fa-solid fa-pen-to-square text-amber-400'></i> Edit Foto Galeri
                                        </h5>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <?= csrf_field() ?>
                                        <input type='hidden' name='id' value='{$row['id']}'>

                                        <div class='mb-3'>
                                            <label class='form-label text-xs text-slate-300'>Judul Foto</label>
                                            <input type='text' name='judul' class='form-control' value='" . htmlspecialchars($row['judul'], ENT_QUOTES) . "' required>
                                        </div>

                                        <div class='mb-3'>
                                            <label class='form-label text-xs text-slate-300'>Ganti File Foto (Opsional)</label>
                                            <input type='file' name='file' class='form-control' accept='image/*'>
                                            <small class='text-slate-500 text-xs mt-1 block'>Kosongkan jika tidak ingin mengganti file gambar.</small>
                                        </div>
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
                                        <button type='submit' name='update' class='btn btn-primary'>Update Foto</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>";
                }
            } else {
                echo "<div class='col-span-full py-12 text-center text-slate-500 bg-slate-900/50 rounded-2xl border border-slate-800'>Belum ada foto di galeri.</div>";
            }
            ?>
        </div>

        <!-- Tabel Detail Galeri Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-table-list text-emerald-400"></i> Tabel Daftar Foto Galeri
            </h2>
            <div class="overflow-x-auto">
                <table class="table table-bordered table-striped table-hover display w-full" id="tabelGallery">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Judul</th>
                            <th>Pasangan Pengantin</th>
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
                            $nama_pengantin = htmlspecialchars(($row['nama_pria'] ?? '') . ' & ' . ($row['nama_wanita'] ?? ''));
                            $imgUrl = "assets/gallery/" . htmlspecialchars($row['file']);
                            echo "
                            <tr>
                                <td>
                                    <a href='{$imgUrl}' data-lightbox='table-galeri'>
                                        <img src='{$imgUrl}' class='w-12 h-12 object-cover rounded-lg border border-slate-700' alt=''>
                                    </a>
                                </td>
                                <td class='font-medium text-white'>" . htmlspecialchars($row['judul']) . "</td>
                                <td class='text-pink-400 font-medium'>$nama_pengantin</td>
                                <td class='text-slate-400 text-xs'>{$row['tanggal_upload']}</td>
                                <td class='whitespace-nowrap'>
                                    <button class='btn btn-sm btn-outline-warning me-1' data-bs-toggle='modal' data-bs-target='#modalEditFoto{$row['id']}'>
                                        <i class='fa-solid fa-pen-to-square'></i>
                                    </button>
                                    <a href='" . csrf_url("gallery_action.php?hapus={$row['id']}") . "' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Hapus foto ini?\")'>
                                        <i class='fa-solid fa-trash-can'></i>
                                    </a>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Foto -->
<div class="modal fade" id="modalTambahFoto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="gallery_action.php" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-images text-emerald-400"></i> Tambah Foto Baru ke Galeri
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label for="judul" class="form-label text-xs text-slate-300">Judul Foto</label>
            <input type="text" name="judul" class="form-control" placeholder="Misal: Momen Akad Nikah" required>
          </div>
          <div class="mb-3">
            <label for="pengantin_id" class="form-label text-xs text-slate-300">Pilih Pasangan Pengantin</label>
            <select name="pengantin_id" class="form-select" required>
              <option value="">-- Pilih Pengantin --</option>
              <?php
              $pengantin = $weddingku->query("SELECT * FROM pengantin");
              while ($p = $pengantin->fetch_assoc()) {
                $nama = $p['nama_pria'] . " & " . $p['nama_wanita'];
                echo "<option value='{$p['id']}'>" . htmlspecialchars($nama) . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="file" class="form-label text-xs text-slate-300">Upload Foto</label>
            <input type="file" name="file" class="form-control" accept="image/*" required>
            <small class="text-slate-500 text-xs mt-1 block">Format: JPG, JPEG, PNG, atau GIF</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="simpan" class="btn btn-primary">Simpan ke Galeri</button>
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
