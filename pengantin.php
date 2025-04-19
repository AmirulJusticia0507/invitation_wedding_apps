<?php include 'koneksi.php'; ?>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-wrapper">
    <!-- Breadcrumb -->
    <div class="breadcrumb-section">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data Pengantin</li>
            </ol>
        </nav>
    </div>
    <section class="content p-3">
        </br></br>
        <h4>Data Pengantin</h4>
        <button class="btn btn-outline-success mb-2 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
          <i class="fas fa-ring me-2"></i> Tambah Pengantin Baru
        </button>

        </br></br>
        <table class="table table-bordered table-striped table-hover display" style="width:100%" nowrap id="tabelPengantin">
            <thead>
                <tr>
                    <th nowrap>Nama Pria</th>
                    <th nowrap>Panggilan Pria</th>
                    <th nowrap>Nama Wanita</th>
                    <th nowrap>Panggilan Wanita</th>
                    <th nowrap>Alamat Wanita</th>
                    <th nowrap>Ortu Pria</th>
                    <th nowrap>Ortu Wanita</th>
                    <th nowrap>Tanggal Akad</th>
                    <th nowrap>Jam Akad</th>
                    <th nowrap>Tanggal Resepsi</th>
                    <th nowrap>Jam Resepsi</th>
                    <th nowrap>Foto Pria</th>
                    <th nowrap>Foto Wanita</th>
                    <th nowrap>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $upload_dir = 'uploads/';
            $data = $weddingku->query("SELECT * FROM pengantin");
            while ($row = $data->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['nama_pria']}</td>
                    <td>{$row['nama_panggilan_pria']}</td>
                    <td>{$row['nama_wanita']}</td>
                    <td>{$row['nama_panggilan_wanita']}</td>
                    <td>{$row['alamat_wanita']}</td>
                    <td>{$row['ortu_pria']}</td>
                    <td>{$row['ortu_wanita']}</td>
                    <td>{$row['tanggal_akad']}</td>
                    <td>{$row['jam_akad']}</td>
                    <td>{$row['tanggal_resepsi']}</td>
                    <td>{$row['jam_resepsi']}</td>
                    <td>
                        <a href='{$upload_dir}{$row['foto_pria']}' data-lightbox='foto-pria-{$row['id']}'>
                            <img src='{$upload_dir}{$row['foto_pria']}' alt='Foto Pria' class='img-thumbnail' width='50'>
                        </a>
                    </td>
                    <td>
                        <a href='{$upload_dir}{$row['foto_wanita']}' data-lightbox='foto-wanita-{$row['id']}'>
                            <img src='{$upload_dir}{$row['foto_wanita']}' alt='Foto Wanita' class='img-thumbnail' width='50'>
                        </a>
                    </td>
                    <td nowrap style='center'>
                        <button class='btn btn-sm btn-outline-warning d-inline-flex align-items-center' data-bs-toggle='modal' data-bs-target='#modalEdit{$row['id']}'>
                            <i class='fas fa-edit me-1'></i> Edit
                        </button>
                        <a href='pengantin_action.php?hapus={$row['id']}' class='btn btn-sm btn-outline-danger d-inline-flex align-items-center' onclick=\"return confirm('Yakin hapus?')\">
                            <i class='fas fa-trash-alt me-1'></i> Hapus
                        </a>
                    </td>
                </tr>";

                // Modal Edit
                echo "
                <div class='modal fade' id='modalEdit{$row['id']}'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                    <form method='POST' action='pengantin_action.php' enctype='multipart/form-data'>
                        <div class='modal-header'><h5>Edit Pengantin</h5></div>
                        <div class='modal-body'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        
                        <!-- Nama Pria -->
                        <div class='input-group mb-2'>
                            <span class='input-group-text'><i class='fas fa-mars'></i></span>
                            <input class='form-control' name='nama_pria' value='{$row['nama_pria']}' required>
                        </div>
                        
                        <!-- Nama Panggilan Pria -->
                        <div class='input-group mb-2'>
                            <span class='input-group-text'><i class='fas fa-user-tag'></i></span>
                            <input class='form-control' name='nama_panggilan_pria' value='{$row['nama_panggilan_pria']}'>
                        </div>

                        <!-- Nama Wanita -->
                        <div class='input-group mb-2'>
                            <span class='input-group-text'><i class='fas fa-venus'></i></span>
                            <input class='form-control' name='nama_wanita' value='{$row['nama_wanita']}' required>
                        </div>
                        
                        <!-- Nama Panggilan Wanita -->
                        <div class='input-group mb-2'>
                            <span class='input-group-text'><i class='fas fa-user-tag'></i></span>
                            <input class='form-control' name='nama_panggilan_wanita' value='{$row['nama_panggilan_wanita']}'>
                        </div>

                        <!-- Alamat Wanita -->
                        <div class='input-group mb-2'>
                            <span class='input-group-text'><i class='fas fa-map-marker-alt'></i></span>
                            <textarea class='form-control' name='alamat_wanita'>{$row['alamat_wanita']}</textarea>
                        </div>

                        <!-- Orang Tua Pria -->
                        <div class='input-group mb-2'>
                            <span class='input-group-text'><i class='fas fa-male'></i></span>
                            <input class='form-control' name='ortu_pria' value='{$row['ortu_pria']}'>
                        </div>

                        <!-- Orang Tua Wanita -->
                        <div class='input-group mb-2'>
                            <span class='input-group-text'><i class='fas fa-female'></i></span>
                            <input class='form-control' name='ortu_wanita' value='{$row['ortu_wanita']}'>
                        </div>

                        <!-- Tanggal Akad -->
                        <div class='input-group mb-2'>
                            <span class='input-group-text'><i class='fas fa-calendar-alt'></i></span>
                            <input type='date' class='form-control' name='tanggal_akad' value='{$row['tanggal_akad']}' required>
                        </div>

                        <!-- Jam Akad -->
                        <div class='input-group mb-2'>
                            <span class='input-group-text'><i class='fas fa-clock'></i></span>
                            <input type='time' class='form-control' name='jam_akad' value='{$row['jam_akad']}' >
                        </div>

                        <div class='row mb-3'>
                          <div class='col-md-6'>
                              <div class='input-group'>
                                  <span class='input-group-text'><i class='fas fa-calendar-alt'></i></span>
                                  <input type='date' class='form-control' name='tanggal_resepsi' value='{$row['tanggal_resepsi']}' >
                              </div>
                          </div>
                          <div class='col-md-6'>
                              <div class='input-group'>
                                  <span class='input-group-text'><i class='fas fa-clock'></i></span>
                                  <input type='time' class='form-control' name='jam_resepsi' value='{$row['jam_resepsi']}' >
                              </div>
                          </div>
                      </div>

                        <!-- Foto Pria -->
                        <div class='input-group mb-2'>
                            <span class='input-group-text'><i class='fas fa-image'></i></span>
                            <input type='file' class='form-control' name='foto_pria' accept='image/*'>
                            <img src='uploads/{$row['foto_pria']}' width='100' class='mt-2' alt='Foto Pria'>
                        </div>

                        <!-- Foto Wanita -->
                        <div class='input-group mb-2'>
                            <span class='input-group-text'><i class='fas fa-image'></i></span>
                            <input type='file' class='form-control' name='foto_wanita' accept='image/*'>
                            <img src='uploads/{$row['foto_wanita']}' width='100' class='mt-2' alt='Foto Wanita'>
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
<div class="modal fade" id="modalTambah">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="pengantin_action.php" enctype="multipart/form-data">
        <div class="modal-header">
          <h5>Tambah Pengantin</h5>
        </div>
        <div class="modal-body">

          <!-- Nama Pria -->
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-mars"></i></span>
            <input class="form-control" name="nama_pria" placeholder="Nama Pria" required>
          </div>

          <!-- Nama Panggilan Pria -->
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
            <input class="form-control" name="nama_panggilan_pria" placeholder="Nama Panggilan Pria">
          </div>

          <!-- Nama Wanita -->
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-venus"></i></span>
            <input class="form-control" name="nama_wanita" placeholder="Nama Wanita" required>
          </div>

          <!-- Nama Panggilan Wanita -->
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
            <input class="form-control" name="nama_panggilan_wanita" placeholder="Nama Panggilan Wanita">
          </div>

          <!-- Alamat Wanita -->
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
            <textarea class="form-control" name="alamat_wanita" placeholder="Alamat Wanita"></textarea>
          </div>

          <!-- Orang Tua Pria -->
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-male"></i></span>
            <input class="form-control" name="ortu_pria" placeholder="Orang Tua Pria">
          </div>

          <!-- Orang Tua Wanita -->
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-female"></i></span>
            <input class="form-control" name="ortu_wanita" placeholder="Orang Tua Wanita">
          </div>

          <!-- Tanggal & Jam Akad -->
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                <input type="date" class="form-control" name="tanggal_akad" placeholder="Tanggal Akad" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                <input type="time" class="form-control" name="jam_akad" placeholder="Jam Akad" >
              </div>
            </div>
          </div>

          <!-- Tanggal & Jam Resepsi -->
          <div class="row mb-3">
              <div class="col-md-6">
                  <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                      <input type="date" class="form-control" name="tanggal_resepsi" placeholder="Tanggal Resepsi" required>
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-clock"></i></span>
                      <input type="time" class="form-control" name="jam_resepsi" placeholder="Jam Resepsi" required>
                  </div>
              </div>
          </div>

          <!-- Foto Pengantin Pria -->
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-image"></i></span>
            <input type="file" class="form-control" name="foto_pria" accept="image/*" >
          </div>

          <!-- Foto Pengantin Wanita -->
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-image"></i></span>
            <input type="file" class="form-control" name="foto_wanita" accept="image/*" >
          </div>

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

    var lightbox = new SimpleLightbox('[data-lightbox]', { /* options */ });
</script>
