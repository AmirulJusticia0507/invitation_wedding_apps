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
                    <a href="dashboard.php" class="hover:text-violet-400 transition">Home</a>
                    <span>/</span>
                    <span class="text-violet-400 font-medium">Love Story Pengantin</span>
                </nav>
                <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-violet-500/10 text-violet-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-book-open"></i>
                    </span>
                    Kisah Cinta (Love Story)
                </h1>
            </div>
            <div>
                <button class="bg-gradient-to-r from-violet-500 to-purple-500 hover:from-violet-400 hover:to-purple-400 text-white font-medium px-4 py-2.5 rounded-xl text-sm shadow-lg shadow-violet-500/20 transition flex items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#modalTambahStory">
                    <i class="fa-solid fa-feather-pointed"></i> Tulis Cerita Baru
                </button>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="overflow-x-auto">
                <table class="table table-bordered table-striped table-hover display w-full" id="tabelStory">
                    <thead>
                        <tr>
                            <th>Pasangan Pengantin</th>
                            <th>Waktu (Bulan / Tahun)</th>
                            <th>Kisah Cerita</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $data = $weddingku->query("
                        SELECT s.*, CONCAT(p.nama_pria, ' & ', p.nama_wanita) as nama_pengantin 
                        FROM story s 
                        JOIN pengantin p ON s.pengantin_id = p.id
                        ORDER BY s.tahun DESC, s.id DESC
                    ");
                    
                    while ($row = $data->fetch_assoc()) {
                        echo "<tr>
                            <td class='font-medium text-pink-400 whitespace-nowrap'>" . htmlspecialchars($row['nama_pengantin']) . "</td>
                            <td class='whitespace-nowrap'>
                                <span class='bg-violet-500/10 text-violet-300 border border-violet-500/20 px-2.5 py-1 rounded-full text-xs font-semibold'>
                                    " . htmlspecialchars($row['bulan']) . " " . htmlspecialchars($row['tahun']) . "
                                </span>
                            </td>
                            <td class='text-slate-300 leading-relaxed'>" . nl2br(htmlspecialchars($row['deskripsi'])) . "</td>
                            <td class='whitespace-nowrap'>
                                <button class='btn btn-sm btn-outline-warning me-1' data-bs-toggle='modal' data-bs-target='#modalEditStory{$row['id']}'>
                                    <i class='fa-solid fa-pen-to-square'></i>
                                </button>
                                <a href='" . csrf_url("story_action.php?hapus={$row['id']}") . "' class='btn btn-sm btn-outline-danger' onclick=\"return confirm('Yakin ingin menghapus cerita ini?')\">
                                    <i class='fa-solid fa-trash-can'></i>
                                </a>
                            </td>
                        </tr>";

                        // Modal Edit Story
                        echo "
                        <div class='modal fade' id='modalEditStory{$row['id']}' tabindex='-1' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <form method='POST' action='story_action.php'>
                                        <div class='modal-header'>
                                            <h5 class='modal-title font-bold text-white flex items-center gap-2'>
                                                <i class='fa-solid fa-pen-to-square text-amber-400'></i> Edit Cerita
                                            </h5>
                                            <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                        </div>
                                        <div class='modal-body'>
                                            <?= csrf_field() ?>
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
                                echo "          </select>
                                            </div>

                                            <div class='row g-2 mb-3'>
                                                <div class='col-7'>
                                                    <label class='form-label text-xs text-slate-300'>Bulan</label>
                                                    <input type='text' name='bulan' class='form-control' value='" . htmlspecialchars($row['bulan'], ENT_QUOTES) . "' required>
                                                </div>
                                                <div class='col-5'>
                                                    <label class='form-label text-xs text-slate-300'>Tahun</label>
                                                    <input type='number' name='tahun' class='form-control' value='" . htmlspecialchars($row['tahun'], ENT_QUOTES) . "' required>
                                                </div>
                                            </div>

                                            <div class='mb-3'>
                                                <label class='form-label text-xs text-slate-300'>Deskripsi Cerita</label>
                                                <textarea name='deskripsi' class='form-control' rows='4'>" . htmlspecialchars($row['deskripsi']) . "</textarea>
                                            </div>
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
                                            <button type='submit' name='update' class='btn btn-primary'>Update Cerita</button>
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

<!-- Modal Tambah Story -->
<div class="modal fade" id="modalTambahStory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="story_action.php">
                <div class="modal-header">
                    <h5 class="modal-title font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-feather-pointed text-violet-400"></i> Tambah Kisah Cinta
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label text-xs text-slate-300">Pasangan Pengantin</label>
                        <select name="pengantin_id" class="form-select" required>
                            <option value="">-- Pilih Pengantin --</option>
                            <?php
                            $pengantin = $weddingku->query("SELECT id, CONCAT(nama_pria, ' & ', nama_wanita) as nama_pengantin FROM pengantin");
                            while ($p = $pengantin->fetch_assoc()) {
                                echo "<option value='{$p['id']}'>" . htmlspecialchars($p['nama_pengantin']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label text-xs text-slate-300">Bulan</label>
                            <input type="text" name="bulan" class="form-control" placeholder="Contoh: Januari" required>
                        </div>
                        <div class="col-5">
                            <label class="form-label text-xs text-slate-300">Tahun</label>
                            <input type="number" name="tahun" class="form-control" placeholder="Contoh: 2024" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-xs text-slate-300">Cerita Singkat</label>
                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Ceritakan momen indah pertemuan, lamaran, dll..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Cerita</button>
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
