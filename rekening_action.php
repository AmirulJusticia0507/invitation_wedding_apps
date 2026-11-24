<?php
include 'auth_check.php';
include 'koneksi.php';

// SIMPAN REKENING
if (isset($_POST['simpan'])) {
    csrf_require_post();
    csrf_verify();
    $pengantin_id = (int) ($_POST['pengantin_id'] ?? 0);
    $nomor_rekening = trim($_POST['nomor_rekening'] ?? '');
    $bank_id = (int) ($_POST['bank_id'] ?? 0);
    $nama_penerima = trim($_POST['nama_penerima'] ?? '');
    $catatan = trim($_POST['catatan'] ?? '');

    if ($pengantin_id <= 0 || empty($nomor_rekening) || $bank_id <= 0) {
        header("Location: rekening_pengantin.php?error=" . urlencode("Pengantin, nomor rekening, dan bank wajib diisi."));
        exit;
    }

    try {
        // Periksa apakah bank_id valid
        $stmt_b = $weddingku->prepare("SELECT id FROM bank_list WHERE id = ?");
        $stmt_b->bind_param("i", $bank_id);
        $stmt_b->execute();
        if ($stmt_b->get_result()->num_rows === 0) {
            header("Location: rekening_pengantin.php?error=" . urlencode("Bank yang dipilih tidak valid."));
            exit;
        }

        $query = $weddingku->prepare("INSERT INTO wedding_gift (pengantin_id, bank_id, nama_penerima, nomor_rekening, catatan) VALUES (?, ?, ?, ?, ?)");
        $query->bind_param("iisss", $pengantin_id, $bank_id, $nama_penerima, $nomor_rekening, $catatan);
        $query->execute();

        header("Location: rekening_pengantin.php?msg=saved");
        exit;
    } catch (Throwable $e) {
        header("Location: rekening_pengantin.php?error=" . urlencode("Gagal menyimpan data rekening: " . $e->getMessage()));
        exit;
    }

} elseif (isset($_POST['update'])) {
    csrf_require_post();
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    $pengantin_id = (int) ($_POST['pengantin_id'] ?? 0);
    $nomor_rekening = trim($_POST['nomor_rekening'] ?? '');
    $bank_id = (int) ($_POST['bank_id'] ?? 0);
    $nama_penerima = trim($_POST['nama_penerima'] ?? '');
    $catatan = trim($_POST['catatan'] ?? '');

    if ($id <= 0 || $pengantin_id <= 0 || empty($nomor_rekening) || $bank_id <= 0) {
        header("Location: rekening_pengantin.php?error=" . urlencode("Data rekening tidak valid atau belum lengkap."));
        exit;
    }

    try {
        // Periksa apakah bank_id valid
        $stmt_b = $weddingku->prepare("SELECT id FROM bank_list WHERE id = ?");
        $stmt_b->bind_param("i", $bank_id);
        $stmt_b->execute();
        if ($stmt_b->get_result()->num_rows === 0) {
            header("Location: rekening_pengantin.php?error=" . urlencode("Bank yang dipilih tidak valid."));
            exit;
        }

        $query = $weddingku->prepare("UPDATE wedding_gift SET pengantin_id = ?, bank_id = ?, nama_penerima = ?, nomor_rekening = ?, catatan = ? WHERE id = ?");
        $query->bind_param("iisssi", $pengantin_id, $bank_id, $nama_penerima, $nomor_rekening, $catatan, $id);
        $query->execute();

        header("Location: rekening_pengantin.php?msg=updated");
        exit;
    } catch (Throwable $e) {
        header("Location: rekening_pengantin.php?error=" . urlencode("Gagal memperbarui data rekening: " . $e->getMessage()));
        exit;
    }

} elseif (isset($_POST['hapus'])) {
    csrf_require_post();
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        header("Location: rekening_pengantin.php?error=" . urlencode("ID rekening tidak valid."));
        exit;
    }

    try {
        $stmt = $weddingku->prepare("DELETE FROM wedding_gift WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        header("Location: rekening_pengantin.php?msg=deleted");
        exit;
    } catch (Throwable $e) {
        header("Location: rekening_pengantin.php?error=" . urlencode("Gagal menghapus data rekening: " . $e->getMessage()));
        exit;
    }
}

header("Location: rekening_pengantin.php");
exit;
