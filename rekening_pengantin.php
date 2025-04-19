<?php include 'koneksi.php'; ?>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content-wrapper">
    <!-- Breadcrumb -->
    <div class="breadcrumb-section">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data Wedding Gift</li>
            </ol>
        </nav>
    </div>
    <section class="content p-3">
        </br></br>
        <h4>Nomor Rekening Pengantin</h4>
        <button class="btn btn-outline-primary mb-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahRekening">
            <i class="fas fa-credit-card"></i> Tambah Nomor Rekening
        </button>

        <table class="table table-bordered table-striped table-hover display" style="width:100%" nowrap id="tabelRekening">
            <thead>
                <tr>
                    <th>Pengantin</th>
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
            ");

            while ($row = $data->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['nama_pengantin']}</td>
                    <td>{$row['nomor_rekening']}</td>
                    <td>" . ($row['bank'] ?? '—') . "</td>
                    <td>{$row['catatan']}</td>
                    <td>
                        <button class='btn btn-sm btn-outline-warning d-inline-flex align-items-center gap-1' data-bs-toggle='modal' data-bs-target='#modalEditRekening{$row['id']}'>
                            <i class='fas fa-edit'></i> Edit
                        </button>
                        <a href='rekening_action.php?hapus={$row['id']}' class='btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1' onclick=\"return confirm('Yakin hapus data rekening?')\">
                            <i class='fas fa-trash-alt'></i> Hapus
                        </a>
                    </td>
                </tr>";

                // Modal Edit
                echo "
                <div class='modal fade' id='modalEditRekening{$row['id']}'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <form method='POST' action='rekening_action.php'>
                                <div class='modal-header'>
                                    <h5>Edit Nomor Rekening</h5>
                                </div>
                                <div class='modal-body'>
                                    <input type='hidden' name='id' value='{$row['id']}'>
                                    <select name='pengantin_id' class='form-control mb-2' required>
                                        <option value=''>-- Pilih Pengantin --</option>";
                                        $pengantinList = $weddingku->query("SELECT id, CONCAT(nama_pria, ' & ', nama_wanita) as nama_pengantin FROM pengantin");
                                        while ($p = $pengantinList->fetch_assoc()) {
                                            $selected = $p['id'] == $row['pengantin_id'] ? "selected" : "";
                                            echo "<option value='{$p['id']}' $selected>{$p['nama_pengantin']}</option>";
                                        }
                                    echo "</select>
                                    <input type='text' name='nomor_rekening' class='form-control mb-2' value='{$row['nomor_rekening']}' required placeholder='Nomor Rekening'>

                                    <select name='bank_id' class='form-control mb-2' required>
                                        <option value=''>-- Pilih Bank --</option>";
                                        $bankList = $weddingku->query("SELECT id, nama_bank FROM bank_list");
                                        while ($b = $bankList->fetch_assoc()) {
                                            $selectedBank = $b['id'] == $row['bank_id'] ? "selected" : "";
                                            echo "<option value='{$b['id']}' $selectedBank>{$b['nama_bank']}</option>";
                                        }
                                    echo "</select>

                                    <input type='text' name='catatan' class='form-control mb-2' value='{$row['catatan']}' placeholder='Atas Nama Rekening'>
                                </div>
                                <div class='modal-footer'>
                                    <button type='submit' name='update' class='btn btn-primary'>Simpan</button>
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
<div class="modal fade" id="modalTambahRekening">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="rekening_action.php">
                <div class="modal-header"><h5>Tambah Nomor Rekening Pengantin</h5></div>
                <div class="modal-body">
                    <?php
                    $pengantinList = $weddingku->query("SELECT id, CONCAT(nama_pria, ' & ', nama_wanita) as nama_pengantin FROM pengantin");
                    ?>

                    <select name="pengantin_id" class="form-control mb-2" required>
                        <option value="">-- Pilih Pengantin --</option>
                        <?php while ($p = $pengantinList->fetch_assoc()) {
                            echo "<option value='{$p['id']}'>{$p['nama_pengantin']}</option>";
                        } ?>
                    </select>

                    <input type="text" id="nomor_rekening" name="nomor_rekening" class="form-control mb-2" placeholder="Nomor Rekening" required>
                    <input type="text" id="bank" name="bank" class="form-control mb-2" placeholder="Bank penerima" required autocomplete="off">
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Atas Nama</label>
                        <input type="text" class="form-control" name="catatan" id="catatan" placeholder="Atas nama rekening" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" name="simpan" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" data-bs-dismiss="modal">
                        <i class="fas fa-times-circle"></i> Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.6-beta.26/inputmask.min.js"></script>
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

    $("#bank").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "get_bank_codes.php",
                type: "GET",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            // Set kode bank yang dipilih ke input
            $("#bank").val(ui.item.value); // Pastikan yang terpilih adalah kode_bank yang benar
        }
    });

    $(document).ready(function() {
        // Apply autocomplete for bank selection in the Edit modal
        $("#editBank").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "get_bank_codes.php",
                    type: "GET",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                // Set the selected bank id to the hidden input field (in case you need it)
                $("#editBank").val(ui.item.value); // Update the bank name
            }
        });
    });

    $(document).ready(function() {
        // Apply Inputmask to nomor_rekening input
        $('#nomor_rekening').inputmask('9999-9999-9999-9999'); // You can change this pattern as per your requirement
    });
</script>
