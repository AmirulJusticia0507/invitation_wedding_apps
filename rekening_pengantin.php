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
                    <span class="text-emerald-400 font-medium">Rekening Pengantin (Wedding Gift)</span>
                </nav>
                <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-credit-card"></i>
                    </span>
                    Nomor Rekening Pengantin
                </h1>
            </div>
            <div>
                <button class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white font-medium px-4 py-2.5 rounded-xl text-sm shadow-lg shadow-emerald-500/20 transition flex items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#modalTambahRekening">
                    <i class="fa-solid fa-plus-circle"></i> Tambah Rekening
                </button>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="overflow-x-auto">
                <table class="table table-bordered table-striped table-hover display w-full" id="tabelRekening">
                    <thead>
                        <tr>
                            <th>Pasangan Pengantin</th>
                            <th>Nomor Rekening</th>
                            <th>Bank</th>
                            <th>Atas Nama</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $data = $weddingku->query("
                        SELECT wedding_gift.*, 
                            CONCAT(pengantin.nama_pria, ' & ', pengantin.nama_wanita) AS nama_pengantin,
                            bank_list.nama_bank AS bank
                        FROM wedding_gift
                        LEFT JOIN pengantin ON wedding_gift.pengantin_id = pengantin.id
                        LEFT JOIN bank_list ON wedding_gift.bank_id = bank_list.id
                        ORDER BY wedding_gift.id DESC
                    ");

                    while ($row = $data->fetch_assoc()) {
                        echo "<tr>
                            <td class='font-medium text-pink-400 whitespace-nowrap'>" . h($row['nama_pengantin'] ?? '-') . "</td>
                            <td>
                                <code class='text-emerald-400 bg-slate-950 px-2 py-1 rounded text-xs font-mono font-bold'>" . h($row['nomor_rekening']) . "</code>
                            </td>
                            <td class='font-semibold text-white'>" . h($row['bank'] ?? '—') . "</td>
                            <td class='text-slate-300'>" . h($row['nama_penerima']) . "</td>
                            <td class='whitespace-nowrap'>
                                <button class='btn btn-sm btn-outline-warning me-1' data-bs-toggle='modal' data-bs-target='#modalEditRekening{$row['id']}'>
                                    <i class='fa-solid fa-pen-to-square'></i>
                                </button>
                                <form method='POST' action='rekening_action.php' class='d-inline' onsubmit=\"return confirm('Yakin hapus data rekening ini?')\">
                                    " . csrf_field() . "<input type='hidden' name='id' value='{$row['id']}'><button type='submit' name='hapus' class='btn btn-sm btn-outline-danger'><i class='fa-solid fa-trash-can'></i></button>
                                </form>
                            </td>
                        </tr>";

                        // Modal Edit Rekening
                        echo "
                        <div class='modal fade' id='modalEditRekening{$row['id']}' tabindex='-1' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <form method='POST' action='rekening_action.php'>
                                        <div class='modal-header'>
                                            <h5 class='modal-title font-bold text-white flex items-center gap-2'>
                                                <i class='fa-solid fa-pen-to-square text-amber-400'></i> Edit Nomor Rekening
                                            </h5>
                                            <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                        </div>
                                        <div class='modal-body'>
                                            " . csrf_field() . "
                                            <input type='hidden' name='id' value='{$row['id']}'>
                                            
                                            <div class='mb-3'>
                                                <label class='form-label text-xs text-slate-300'>Pasangan Pengantin</label>
                                                <select name='pengantin_id' class='form-select' required>
                                                    <option value=''>-- Pilih Pengantin --</option>";
                                                    $pengantinList = $weddingku->query("SELECT id, CONCAT(nama_pria, ' & ', nama_wanita) as nama_pengantin FROM pengantin");
                                                    while ($p = $pengantinList->fetch_assoc()) {
                                                        $selected = $p['id'] == $row['pengantin_id'] ? "selected" : "";
                                                        echo "<option value='{$p['id']}' $selected>" . h($p['nama_pengantin']) . "</option>";
                                                    }
                                echo "          </select>
                                            </div>

                                            <div class='mb-3'>
                                                <label class='form-label text-xs text-slate-300'>Nomor Rekening</label>
                                                <input type='text' name='nomor_rekening' class='form-control' value='" . h($row['nomor_rekening']) . "' required>
                                            </div>

                                            <div class='mb-3'>
                                                <label class='form-label text-xs text-slate-300'>Nama Bank</label>
                                                <select name='bank_id' class='form-select' required>
                                                    <option value=''>-- Pilih Bank --</option>";
                                                    $bankList = $weddingku->query("SELECT id, nama_bank FROM bank_list ORDER BY nama_bank");
                                                    while ($b = $bankList->fetch_assoc()) {
                                                        $selectedBank = $b['id'] == $row['bank_id'] ? "selected" : "";
                                                        echo "<option value='{$b['id']}' $selectedBank>" . h($b['nama_bank']) . "</option>";
                                                    }
                                echo "          </select>
                                            </div>

                                            <div class='mb-3'>
                                                <label class='form-label text-xs text-slate-300'>Atas Nama Rekening</label>
                                                <input type='text' name='nama_penerima' class='form-control' value='" . h($row['nama_penerima']) . "' placeholder='Contoh: Amirul Putra' required>
                                            </div>
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
                                            <button type='submit' name='update' class='btn btn-primary'>Update Rekening</button>
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

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahRekening" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="rekening_action.php">
                <div class="modal-header">
                    <h5 class="modal-title font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-credit-card text-emerald-400"></i> Tambah Rekening Pengantin
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <?php
                    $pengantinList = $weddingku->query("SELECT id, CONCAT(nama_pria, ' & ', nama_wanita) as nama_pengantin FROM pengantin");
                    ?>
                    <div class="mb-3">
                        <label class="form-label text-xs text-slate-300">Pasangan Pengantin</label>
                        <select name="pengantin_id" class="form-select" required>
                            <option value="">-- Pilih Pengantin --</option>
                            <?php while ($p = $pengantinList->fetch_assoc()) {
                                echo "<option value='{$p['id']}'>" . htmlspecialchars($p['nama_pengantin']) . "</option>";
                            } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-xs text-slate-300">Nomor Rekening</label>
                        <input type="text" id="nomor_rekening" name="nomor_rekening" class="form-control" placeholder="Nomor Rekening" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-xs text-slate-300">Pilih Bank</label>
                        <?php
                        $bankListTambah = $weddingku->query("SELECT id, nama_bank FROM bank_list ORDER BY nama_bank");
                        ?>
                        <select name="bank_id" id="bank_id" class="form-select" required>
                            <option value="">-- Pilih Bank --</option>
                            <?php while ($b = $bankListTambah->fetch_assoc()) {
                                echo "<option value='{$b['id']}'>" . htmlspecialchars($b['nama_bank']) . "</option>";
                            } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label text-xs text-slate-300">Atas Nama Rekening</label>
                        <input type="text" class="form-control" name="nama_penerima" id="nama_penerima" placeholder="Contoh: Amirul Putra" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Rekening</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    $('#tabelRekening').DataTable({
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
