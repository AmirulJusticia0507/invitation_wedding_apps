<?php include 'auth_check.php'; ?>
<?php include 'koneksi.php'; ?>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#ec4899" />

<div class="content-wrapper">
    <div class="p-6 max-w-7xl w-full mx-auto">
        <!-- Breadcrumb & Title -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <nav class="flex text-xs text-slate-400 gap-2 mb-1">
                    <a href="dashboard.php" class="hover:text-cyan-400 transition">Home</a>
                    <span>/</span>
                    <span class="text-cyan-400 font-medium">Generate URL Undangan</span>
                </nav>
                <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-link"></i>
                    </span>
                    URL Undangan Tamu
                </h1>
            </div>
            <div>
                <button class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 hover:to-blue-400 text-white font-medium px-4 py-2.5 rounded-xl text-sm shadow-lg shadow-cyan-500/20 transition flex items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#modalTambahUndangan">
                    <i class="fa-solid fa-plus-circle"></i> Tambah URL Undangan
                </button>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="overflow-x-auto">
                <table class="table table-bordered table-striped table-hover display w-full" id="tabelUndangan">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pasangan Pengantin</th>
                            <th>Nama Tamu</th>
                            <th>Link Undangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $data = $weddingku->query("
                            SELECT uu.*, 
                                   CONCAT(p.nama_pria, ' & ', p.nama_wanita) AS nama_pengantin,
                                   t.nama AS nama_tamu
                            FROM undangan_url uu
                            JOIN pengantin p ON uu.pengantin_id = p.id
                            LEFT JOIN tamu t ON uu.tamu_id = t.id
                            ORDER BY uu.id DESC
                        ");
                        $no = 1;
                        while ($row = $data->fetch_assoc()) {
                            $namaTamu = !empty($row['nama_tamu']) ? h($row['nama_tamu']) : '<span class="text-slate-500 italic">Tamu tidak ditemukan</span>';

                            echo "<tr>
                                <td class='text-slate-400 text-xs'>" . $no++ . "</td>
                                <td class='font-medium text-pink-400 whitespace-nowrap'>" . h($row['nama_pengantin'] ?? '-') . "</td>
                                <td class='font-semibold text-white'>" . $namaTamu . "</td>
                                <td>
                                    <div class='flex items-center gap-2'>
                                        <a href='{$row['url_undangan']}' target='_blank' class='text-xs text-cyan-400 hover:underline max-w-xs truncate inline-block'>
                                            " . h($row['url_undangan']) . "
                                        </a>
                                        <button onclick=\"navigator.clipboard.writeText('{$row['url_undangan']}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'Link disalin!', showConfirmButton:false, timer:1500, background:'#0f172a', color:'#fff'});\" 
                                                class='text-slate-400 hover:text-white text-xs px-2 py-1 rounded bg-slate-800 border border-slate-700'>
                                            <i class='fa-regular fa-copy'></i>
                                        </button>
                                    </div>
                                </td>
                                <td class='whitespace-nowrap'>
                                    <button class='btn btn-sm btn-outline-warning me-1' data-bs-toggle='modal' data-bs-target='#modalEditUndangan{$row['id']}'>
                                        <i class='fa-solid fa-pen-to-square'></i>
                                    </button>
                                    <a href='" . csrf_url("undangan_url_action.php?hapus={$row['id']}") . "' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Yakin ingin hapus link ini?\")'>
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

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahUndangan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="undangan_url_action.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-link text-cyan-400"></i> Buat URL Undangan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label text-xs text-slate-300">Nama Pasangan Pengantin</label>
                    <select name="pengantin_id" class="form-select" required>
                        <option value="">-- Pilih Pengantin --</option>
                        <?php
                        $pengantin = $weddingku->query("SELECT * FROM pengantin");
                        while ($p = $pengantin->fetch_assoc()) {
                            $nama_pengantin = $p['nama_pria'] . ' & ' . $p['nama_wanita'];
                            echo "<option value='{$p['id']}'>" . htmlspecialchars($nama_pengantin) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label text-xs text-slate-300">Pilih Tamu Undangan</label>
                    <input type="hidden" name="tamu_ids" id="tamu_ids_hidden" required>
                    <div class="input-group">
                        <textarea id="tamu_names_display" class="form-control" placeholder="Klik tombol cari untuk memilih tamu..." rows="2" readonly required></textarea>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCariTamu">
                            <i class="fa-solid fa-magnifying-glass"></i> Cari
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="tambah" class="btn btn-primary">Generate & Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Cari Tamu (Tambah) -->
<div class="modal fade" id="modalCariTamu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-users text-blue-400"></i> Pilih Tamu Undangan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="overflow-x-auto">
                    <table class="table table-bordered table-striped table-hover display w-full" id="tamuTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pilih</th>
                                <th>Nama Tamu</th>
                                <th>Alamat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $tamu = $weddingku->query("SELECT * FROM tamu");
                            $noTamu = 1;
                            while ($t = $tamu->fetch_assoc()) {
                            ?>
                            <tr>
                                <td class="text-slate-400 text-xs"><?= $noTamu++; ?></td>
                                <td><input type="checkbox" class="tamu-check form-check-input" data-id="<?= $t['id']; ?>" data-nama="<?= htmlspecialchars($t['nama'], ENT_QUOTES); ?>"></td>
                                <td class="font-medium text-white"><?= htmlspecialchars($t['nama']); ?></td>
                                <td class="text-slate-300 text-xs"><?= htmlspecialchars($t['alamat']); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="btnPilihTamu" data-bs-dismiss="modal">Gunakan Tamu Terpilih</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Undangan -->
<?php
$dataEdit = $weddingku->query("
    SELECT uu.*, 
           CONCAT(p.nama_pria, ' & ', p.nama_wanita) AS nama_pengantin, 
           t.nama AS nama_tamu
    FROM undangan_url uu
    JOIN pengantin p ON uu.pengantin_id = p.id
    JOIN tamu t ON uu.tamu_id = t.id
");

while ($row = $dataEdit->fetch_assoc()) {
?>
<div class="modal fade" id="modalEditUndangan<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="undangan_url_action.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-amber-400"></i> Edit URL Undangan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                
                <div class="mb-3">
                    <label class="form-label text-xs text-slate-300">Pasangan Pengantin</label>
                    <select name="pengantin_id" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php
                        $pengantin = $weddingku->query("SELECT * FROM pengantin");
                        while ($p = $pengantin->fetch_assoc()) {
                            $nama_pengantin = $p['nama_pria'] . ' & ' . $p['nama_wanita'];
                            $selected = $p['id'] == $row['pengantin_id'] ? 'selected' : '';
                            echo "<option value='{$p['id']}' $selected>" . htmlspecialchars($nama_pengantin) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label text-xs text-slate-300">Nama Tamu</label>
                    <div class="input-group">
                        <textarea name="nama_tamu_display" id="nama_tamu_display_<?= $row['id']; ?>" class="form-control" readonly required><?= htmlspecialchars($row['nama_tamu']); ?></textarea>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalCariTamu<?= $row['id']; ?>">Cari</button>
                    </div>
                    <input type="hidden" name="tamu_id" id="tamu_id_<?= $row['id']; ?>" value="<?= $row['tamu_id']; ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label text-xs text-slate-300">URL Undangan</label>
                    <input type="text" name="url_undangan" class="form-control" value="<?= htmlspecialchars($row['url_undangan']); ?>" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalCariTamu<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-users text-blue-400"></i> Pilih Tamu Undangan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="overflow-x-auto">
                    <table class="table table-bordered table-striped table-hover display w-full">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pilih</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $listTamu = $weddingku->query("SELECT * FROM tamu");
                            $noSub = 1;
                            while ($t = $listTamu->fetch_assoc()) {
                                $idTamu = $t['id'];
                                $namaTamuSafe = htmlspecialchars(addslashes($t['nama']));
                            ?>
                            <tr>
                                <td class="text-slate-400 text-xs"><?= $noSub++; ?></td>
                                <td>
                                    <input type="radio" name="pilih_tamu_<?= $row['id']; ?>" class="form-check-input"
                                        onclick="pilihTamu('<?= $row['id']; ?>', '<?= $idTamu; ?>', '<?= $namaTamuSafe; ?>')"
                                        <?= $idTamu == $row['tamu_id'] ? 'checked' : '' ?>>
                                </td>
                                <td class="font-medium text-white"><?= htmlspecialchars($t['nama']); ?></td>
                                <td class="text-slate-300 text-xs"><?= htmlspecialchars($t['alamat']); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php include 'footer.php'; ?>

<script>
function pilihTamu(rowId, tamuId, namaTamu) {
    document.getElementById('nama_tamu_display_' + rowId).value = namaTamu;
    document.getElementById('tamu_id_' + rowId).value = tamuId;
    const modalEl = document.getElementById('modalCariTamu' + rowId);
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
}

$(document).ready(function() {
    $('#tabelUndangan').DataTable({
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

    $('#tamuTable').DataTable({
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

    document.getElementById('btnPilihTamu')?.addEventListener('click', function () {
        let selectedIds = [];
        let selectedNames = [];

        document.querySelectorAll('#modalCariTamu .tamu-check:checked').forEach(function (checkbox) {
            selectedIds.push(checkbox.getAttribute('data-id'));
            selectedNames.push(checkbox.getAttribute('data-nama'));
        });

        const hiddenEl = document.getElementById('tamu_ids_hidden');
        const displayEl = document.getElementById('tamu_names_display');
        if (hiddenEl) hiddenEl.value = selectedIds.join(',');
        if (displayEl) displayEl.value = selectedNames.join(', ');
    });
});
</script>
