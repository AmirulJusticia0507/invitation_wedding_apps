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
                    <a href="dashboard.php" class="hover:text-pink-400 transition">Home</a>
                    <span>/</span>
                    <span class="text-pink-400 font-medium">Data Pengantin</span>
                </nav>
                <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-pink-500/10 text-pink-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-heart"></i>
                    </span>
                    Data Pengantin
                </h1>
            </div>
            <div>
                <button class="bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-400 hover:to-rose-400 text-white font-medium px-4 py-2.5 rounded-xl text-sm shadow-lg shadow-pink-500/20 transition flex items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fa-solid fa-ring"></i> Tambah Pengantin Baru
                </button>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="overflow-x-auto">
                <table class="table table-bordered table-striped table-hover display w-full" id="tabelPengantin">
                    <thead>
                        <tr>
                            <th>Nama Pria</th>
                            <th>Panggilan Pria</th>
                            <th>Nama Wanita</th>
                            <th>Panggilan Wanita</th>
                            <th>Alamat Wanita</th>
                            <th>Ortu Pria</th>
                            <th>Ortu Wanita</th>
                            <th>Akad</th>
                            <th>Resepsi</th>
                            <th>Foto Pria</th>
                            <th>Foto Wanita</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $upload_dir = 'uploads/';
                    $modals = '';
                    $data = $weddingku->query("SELECT * FROM pengantin");
                    while ($row = $data->fetch_assoc()) {
                        $priaImg = !empty($row['foto_pria']) ? "{$upload_dir}{$row['foto_pria']}" : "images/istockphoto-487583818-1024x1024.jpg";
                        $wanitaImg = !empty($row['foto_wanita']) ? "{$upload_dir}{$row['foto_wanita']}" : "images/istockphoto-487583818-1024x1024.jpg";
                        
                        echo "<tr>
                            <td class='font-medium text-white'>" . h($row['nama_pria']) . "</td>
                            <td>" . h($row['nama_panggilan_pria']) . "</td>
                            <td class='font-medium text-white'>" . h($row['nama_wanita']) . "</td>
                            <td>" . h($row['nama_panggilan_wanita']) . "</td>
                            <td class='text-slate-300'>" . h($row['alamat_wanita']) . "</td>
                            <td>" . h($row['ortu_pria']) . "</td>
                            <td>" . h($row['ortu_wanita']) . "</td>
                            <td>
                                <div class='text-xs'>
                                    <span class='block text-white'>" . h($row['tanggal_akad'] ?? '-') . "</span>
                                    <span class='text-slate-400'>" . h($row['jam_akad'] ?? '-') . "</span>
                                </div>
                            </td>
                            <td>
                                <div class='text-xs'>
                                    <span class='block text-white'>" . h($row['tanggal_resepsi'] ?? '-') . "</span>
                                    <span class='text-slate-400'>" . h($row['jam_resepsi'] ?? '-') . "</span>
                                </div>
                            </td>
                            <td>
                                <a href='{$priaImg}' data-lightbox='foto-pria-{$row['id']}'>
                                    <img src='{$priaImg}' alt='Foto Pria' class='w-10 h-10 object-cover rounded-lg border border-slate-700'>
                                </a>
                            </td>
                            <td>
                                <a href='{$wanitaImg}' data-lightbox='foto-wanita-{$row['id']}'>
                                    <img src='{$wanitaImg}' alt='Foto Wanita' class='w-10 h-10 object-cover rounded-lg border border-slate-700'>
                                </a>
                            </td>
                            <td class='whitespace-nowrap'>
                                <button class='btn btn-sm btn-outline-warning me-1' data-bs-toggle='modal' data-bs-target='#modalEdit{$row['id']}'>
                                    <i class='fa-solid fa-pen-to-square'></i>
                                </button>
                                <form method='POST' action='pengantin_action.php' class='d-inline' onsubmit=\"return confirm('Yakin hapus data pengantin ini?')\">
                                    " . csrf_field() . "<input type='hidden' name='id' value='{$row['id']}'><button type='submit' name='hapus' class='btn btn-sm btn-outline-danger'><i class='fa-solid fa-trash-can'></i></button>
                                </form>
                            </td>
                        </tr>";

                        // Modal Edit (ditaruh di luar <tbody> agar HTML valid)
                        $modals .= "
                        <div class='modal fade' id='modalEdit{$row['id']}' tabindex='-1' aria-hidden='true'>
                        <div class='modal-dialog modal-lg'>
                            <div class='modal-content'>
                            <form method='POST' action='pengantin_action.php' enctype='multipart/form-data'>
                                <div class='modal-header'>
                                    <h5 class='modal-title font-bold text-white flex items-center gap-2'>
                                        <i class='fa-solid fa-pen-to-square text-amber-400'></i> Edit Data Pengantin
                                    </h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                </div>
                                <div class='modal-body'>
                                    " . csrf_field() . "
                                    <input type='hidden' name='id' value='{$row['id']}'>

                                    <div class='row g-3'>
                                        <div class='col-md-6'>
                                            <label class='form-label text-xs text-slate-300'>Nama Pria</label>
                                            <div class='input-group'>
                                                <span class='input-group-text'><i class='fa-solid fa-mars'></i></span>
                                                <input class='form-control' name='nama_pria' value='" . h($row['nama_pria']) . "' required>
                                            </div>
                                        </div>
                                        <div class='col-md-6'>
                                            <label class='form-label text-xs text-slate-300'>Panggilan Pria</label>
                                            <div class='input-group'>
                                                <span class='input-group-text'><i class='fa-solid fa-tag'></i></span>
                                                <input class='form-control' name='nama_panggilan_pria' value='" . h($row['nama_panggilan_pria']) . "'>
                                            </div>
                                        </div>

                                        <div class='col-md-6'>
                                            <label class='form-label text-xs text-slate-300'>Nama Wanita</label>
                                            <div class='input-group'>
                                                <span class='input-group-text'><i class='fa-solid fa-venus'></i></span>
                                                <input class='form-control' name='nama_wanita' value='" . h($row['nama_wanita']) . "' required>
                                            </div>
                                        </div>
                                        <div class='col-md-6'>
                                            <label class='form-label text-xs text-slate-300'>Panggilan Wanita</label>
                                            <div class='input-group'>
                                                <span class='input-group-text'><i class='fa-solid fa-tag'></i></span>
                                                <input class='form-control' name='nama_panggilan_wanita' value='" . h($row['nama_panggilan_wanita']) . "'>
                                            </div>
                                        </div>

                                        <div class='col-12'>
                                            <label class='form-label text-xs text-slate-300'>Alamat Wanita</label>
                                            <div class='input-group'>
                                                <span class='input-group-text'><i class='fa-solid fa-location-dot'></i></span>
                                                <textarea class='form-control' name='alamat_wanita' rows='2'>" . h($row['alamat_wanita']) . "</textarea>
                                            </div>
                                        </div>

                                        <div class='col-md-6'>
                                            <label class='form-label text-xs text-slate-300'>Orang Tua Pria</label>
                                            <div class='input-group'>
                                                <span class='input-group-text'><i class='fa-solid fa-user-group'></i></span>
                                                <input class='form-control' name='ortu_pria' value='" . h($row['ortu_pria']) . "'>
                                            </div>
                                        </div>
                                        <div class='col-md-6'>
                                            <label class='form-label text-xs text-slate-300'>Orang Tua Wanita</label>
                                            <div class='input-group'>
                                                <span class='input-group-text'><i class='fa-solid fa-user-group'></i></span>
                                                <input class='form-control' name='ortu_wanita' value='" . h($row['ortu_wanita']) . "'>
                                            </div>
                                        </div>

                                        <div class='col-md-6'>
                                            <label class='form-label text-xs text-slate-300'>Tanggal & Jam Akad</label>
                                            <div class='row g-2'>
                                                <div class='col-7'>
                                                    <input type='date' class='form-control' name='tanggal_akad' value='{$row['tanggal_akad']}' required>
                                                </div>
                                                <div class='col-5'>
                                                    <input type='time' class='form-control' name='jam_akad' value='{$row['jam_akad']}'>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='col-md-6'>
                                            <label class='form-label text-xs text-slate-300'>Tanggal & Jam Resepsi</label>
                                            <div class='row g-2'>
                                                <div class='col-7'>
                                                    <input type='date' class='form-control' name='tanggal_resepsi' value='{$row['tanggal_resepsi']}'>
                                                </div>
                                                <div class='col-5'>
                                                    <input type='time' class='form-control' name='jam_resepsi' value='{$row['jam_resepsi']}'>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='col-md-6'>
                                            <label class='form-label text-xs text-slate-300'>Foto Pria</label>
                                            <input type='file' class='form-control' name='foto_pria' accept='image/*'>
                                        </div>
                                        <div class='col-md-6'>
                                            <label class='form-label text-xs text-slate-300'>Foto Wanita</label>
                                            <input type='file' class='form-control' name='foto_wanita' accept='image/*'>
                                        </div>
                                    </div>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
                                    <button type='submit' name='update' class='btn btn-primary'>Update Perubahan</button>
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

<!-- Modal Edit per data (dikeluarkan dari <tbody>) -->
<?= $modals ?>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="pengantin_action.php" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-ring text-pink-400"></i> Tambah Pengantin Baru
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <?= csrf_field() ?>

          <div class="row g-3">
              <div class="col-md-6">
                  <label class="form-label text-xs text-slate-300">Nama Pria</label>
                  <div class="input-group">
                      <span class="input-group-text"><i class="fa-solid fa-mars"></i></span>
                      <input class="form-control" name="nama_pria" placeholder="Nama Lengkap Pria" required>
                  </div>
              </div>
              <div class="col-md-6">
                  <label class="form-label text-xs text-slate-300">Panggilan Pria</label>
                  <div class="input-group">
                      <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
                      <input class="form-control" name="nama_panggilan_pria" placeholder="Panggilan">
                  </div>
              </div>

              <div class="col-md-6">
                  <label class="form-label text-xs text-slate-300">Nama Wanita</label>
                  <div class="input-group">
                      <span class="input-group-text"><i class="fa-solid fa-venus"></i></span>
                      <input class="form-control" name="nama_wanita" placeholder="Nama Lengkap Wanita" required>
                  </div>
              </div>
              <div class="col-md-6">
                  <label class="form-label text-xs text-slate-300">Panggilan Wanita</label>
                  <div class="input-group">
                      <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
                      <input class="form-control" name="nama_panggilan_wanita" placeholder="Panggilan">
                  </div>
              </div>

              <div class="col-12">
                  <label class="form-label text-xs text-slate-300">Alamat Wanita</label>
                  <div class="input-group">
                      <span class="input-group-text"><i class="fa-solid fa-location-dot"></i></span>
                      <textarea class="form-control" name="alamat_wanita" rows="2" placeholder="Alamat lengkap mempelai wanita"></textarea>
                  </div>
              </div>

              <div class="col-md-6">
                  <label class="form-label text-xs text-slate-300">Orang Tua Pria</label>
                  <div class="input-group">
                      <span class="input-group-text"><i class="fa-solid fa-user-group"></i></span>
                      <input class="form-control" name="ortu_pria" placeholder="Bpk. & Ibu ...">
                  </div>
              </div>
              <div class="col-md-6">
                  <label class="form-label text-xs text-slate-300">Orang Tua Wanita</label>
                  <div class="input-group">
                      <span class="input-group-text"><i class="fa-solid fa-user-group"></i></span>
                      <input class="form-control" name="ortu_wanita" placeholder="Bpk. & Ibu ...">
                  </div>
              </div>

              <div class="col-md-6">
                  <label class="form-label text-xs text-slate-300">Tanggal & Jam Akad</label>
                  <div class="row g-2">
                      <div class="col-7">
                          <input type="date" class="form-control" name="tanggal_akad" required>
                      </div>
                      <div class="col-5">
                          <input type="time" class="form-control" name="jam_akad">
                      </div>
                  </div>
              </div>

              <div class="col-md-6">
                  <label class="form-label text-xs text-slate-300">Tanggal & Jam Resepsi</label>
                  <div class="row g-2">
                      <div class="col-7">
                          <input type="date" class="form-control" name="tanggal_resepsi" required>
                      </div>
                      <div class="col-5">
                          <input type="time" class="form-control" name="jam_resepsi">
                      </div>
                  </div>
              </div>

              <div class="col-md-6">
                  <label class="form-label text-xs text-slate-300">Foto Pengantin Pria</label>
                  <input type="file" class="form-control" name="foto_pria" accept="image/*">
              </div>
              <div class="col-md-6">
                  <label class="form-label text-xs text-slate-300">Foto Pengantin Wanita</label>
                  <input type="file" class="form-control" name="foto_wanita" accept="image/*">
              </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="simpan" class="btn btn-primary">Simpan Pengantin</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    $('#tabelPengantin').DataTable({
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
