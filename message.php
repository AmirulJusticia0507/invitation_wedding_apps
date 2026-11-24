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
                    <a href="dashboard.php" class="hover:text-amber-400 transition">Home</a>
                    <span>/</span>
                    <span class="text-amber-400 font-medium">Pesan untuk Pengantin</span>
                </nav>
                <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-comments"></i>
                    </span>
                    Pesan & Ucapan Doa
                </h1>
            </div>
            <div>
                <button class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white font-medium px-4 py-2.5 rounded-xl text-sm shadow-lg shadow-amber-500/20 transition flex items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#modalTambahPesan">
                    <i class="fa-solid fa-plus-circle"></i> Tambah Ucapan
                </button>
            </div>
        </div>

        <?php
        $pengantinList = $weddingku->query("SELECT id, nama_pria, nama_wanita FROM pengantin");
        ?>

        <!-- Table Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="overflow-x-auto">
                <table class="table table-bordered table-striped table-hover display w-full" id="tabelMessage">
                    <thead>
                        <tr>
                            <th>Pasangan Pengantin</th>
                            <th>Nama Pengirim</th>
                            <th>Isi Pesan / Ucapan</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $data = $weddingku->query("
                        SELECT m.*, CONCAT(p.nama_pria, ' & ', p.nama_wanita) AS nama_pengantin 
                        FROM message m 
                        LEFT JOIN pengantin p ON m.pengantin_id = p.id 
                        ORDER BY m.id DESC
                    ");
                    while ($row = $data->fetch_assoc()) {
                        echo "<tr>
                            <td class='font-medium text-pink-400 whitespace-nowrap'>" . h($row['nama_pengantin'] ?? '-') . "</td>
                            <td class='font-semibold text-white whitespace-nowrap'>" . h($row['nama']) . "</td>
                            <td class='text-slate-300 leading-relaxed'>" . nl2br(h($row['pesan'])) . "</td>
                            <td class='text-slate-400 text-xs whitespace-nowrap'>" . h($row['created_at'] ?? '-') . "</td>
                            <td class='whitespace-nowrap'>
                                <button class='btn btn-sm btn-outline-warning me-1' data-bs-toggle='modal' data-bs-target='#modalEditPesan{$row['id']}'>
                                    <i class='fa-solid fa-pen-to-square'></i>
                                </button>
                                <form method='POST' action='message_action.php' class='d-inline' onsubmit=\"return confirm('Yakin hapus pesan ini?')\">
                                    " . csrf_field() . "<input type='hidden' name='id' value='{$row['id']}'><button type='submit' name='hapus' class='btn btn-sm btn-outline-danger'><i class='fa-solid fa-trash-can'></i></button>
                                </form>
                            </td>
                        </tr>";

                        // Modal Edit Pesan
                        echo "
                        <div class='modal fade' id='modalEditPesan{$row['id']}' tabindex='-1' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <form method='POST' action='message_action.php'>
                                        <div class='modal-header'>
                                            <h5 class='modal-title font-bold text-white flex items-center gap-2'>
                                                <i class='fa-solid fa-pen-to-square text-amber-400'></i> Edit Pesan & Doa
                                            </h5>
                                            <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                        </div>
                                        <div class='modal-body'>
                                            " . csrf_field() . "
                                            <input type='hidden' name='id' value='{$row['id']}'>

                                            <div class='mb-3'>
                                                <label class='form-label text-xs text-slate-300'>Untuk Pasangan Pengantin</label>
                                                <select name='pengantin_id' class='form-select' required>
                                                    <option value=''>-- Pilih Pengantin --</option>";
                                                    $pengantinList->data_seek(0);
                                                    while ($p = $pengantinList->fetch_assoc()) {
                                                        $selected = ($p['id'] == $row['pengantin_id']) ? 'selected' : '';
                                                        echo "<option value='{$p['id']}' $selected>" . h($p['nama_pria']) . " & " . h($p['nama_wanita']) . "</option>";
                                                    }
                        echo "                  </select>
                                            </div>

                                            <div class='mb-3'>
                                                <label class='form-label text-xs text-slate-300'>Nama Pengirim</label>
                                                <input type='text' name='nama_pengirim' class='form-control' value='" . h($row['nama']) . "' required>
                                            </div>

                                            <div class='mb-3'>
                                                <label class='form-label text-xs text-slate-300'>Isi Pesan</label>
                                                <textarea name='isi_pesan' class='form-control' rows='4' required>" . h($row['pesan']) . "</textarea>
                                            </div>
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
                                            <button type='submit' name='update' class='btn btn-primary'>Update Pesan</button>
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

<!-- Modal Tambah Pesan -->
<div class="modal fade" id="modalTambahPesan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="message_action.php">
                <div class="modal-header">
                    <h5 class="modal-title font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-comments text-amber-400"></i> Tambah Ucapan Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label text-xs text-slate-300">Untuk Pasangan Pengantin</label>
                        <select name="pengantin_id" class="form-select" required>
                            <option value="">-- Pilih Pengantin --</option>
                            <?php 
                            $pengantinList->data_seek(0);
                            while ($p = $pengantinList->fetch_assoc()): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['nama_pria']) ?> & <?= htmlspecialchars($p['nama_wanita']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-xs text-slate-300">Nama Pengirim</label>
                        <input type="text" name="nama_pengirim" class="form-control" placeholder="Contoh: Aminah dari Bandung" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-xs text-slate-300">Isi Pesan & Doa</label>
                        <textarea name="isi_pesan" class="form-control" rows="4" placeholder="Tuliskan ucapan selamat dan doa terbaik..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan" class="btn btn-primary">Kirim Pesan</button>
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
