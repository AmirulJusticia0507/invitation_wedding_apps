<?php
include 'auth_check.php';
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $pengantin_id = $_POST['pengantin_id'];
    $nomor_rekening = $_POST['nomor_rekening'];
    $bank_id = $_POST['bank_id']; // Ambil bank_id dari form
    $catatan = $_POST['catatan'];

    // Periksa apakah bank_id valid
    $stmt = $weddingku->prepare("SELECT id FROM bank_list WHERE id = ?");
    $stmt->bind_param("i", $bank_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bank = $result->fetch_assoc();

    if ($bank) {
        // Simpan data ke wedding_gift
        $query = $weddingku->prepare("INSERT INTO wedding_gift (pengantin_id, nomor_rekening, bank_id, catatan) VALUES (?, ?, ?, ?)");
        $query->bind_param("isis", $pengantin_id, $nomor_rekening, $bank_id, $catatan);
        $query->execute();

        if ($query->affected_rows > 0) {
            header("Location: rekening_pengantin.php");
            exit;
        } else {
            echo "Gagal menyimpan data rekening!";
        }
    } else {
        echo "Bank tidak ditemukan!";
    }

} elseif (isset($_POST['update'])) {
    $id = $_POST['id'];
    $pengantin_id = $_POST['pengantin_id'];
    $nomor_rekening = $_POST['nomor_rekening'];
    $bank_id = $_POST['bank_id']; // Ambil bank_id dari form
    $catatan = $_POST['catatan'];

    // Periksa apakah bank_id valid
    $stmt = $weddingku->prepare("SELECT id FROM bank_list WHERE id = ?");
    $stmt->bind_param("i", $bank_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bank = $result->fetch_assoc();

    if ($bank) {
        // Update data rekening
        $query = $weddingku->prepare("UPDATE wedding_gift SET pengantin_id = ?, nomor_rekening = ?, bank_id = ?, catatan = ? WHERE id = ?");
        $query->bind_param("isisi", $pengantin_id, $nomor_rekening, $bank_id, $catatan, $id);
        $query->execute();

        if ($query->affected_rows > 0) {
            header("Location: rekening_pengantin.php");
            exit;
        } else {
            echo "Gagal mengupdate data rekening!";
        }
    } else {
        echo "Bank tidak ditemukan!";
    }

} elseif (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $weddingku->prepare("DELETE FROM wedding_gift WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: rekening_pengantin.php");
    exit;
}

?>
