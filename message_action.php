<?php
include 'auth_check.php';
include 'koneksi.php';

// Tambah pesan
if (isset($_POST['simpan'])) {
    csrf_verify();
    $nama = $_POST['nama_pengirim'];
    $pesan = $_POST['isi_pesan'];
    $pengantin_id = (int) $_POST['pengantin_id'];

    $stmt = $weddingku->prepare("INSERT INTO message (nama, pesan, pengantin_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $nama, $pesan, $pengantin_id);
    $stmt->execute();
    $stmt->close();

    header('Location: message.php');
    exit;
}

// Update pesan
if (isset($_POST['update'])) {
    csrf_verify();
    $id = (int) $_POST['id'];
    $nama = $_POST['nama_pengirim'];
    $pesan = $_POST['isi_pesan'];
    $pengantin_id = (int) $_POST['pengantin_id'];

    $stmt = $weddingku->prepare("UPDATE message SET nama=?, pesan=?, pengantin_id=? WHERE id=?");
    $stmt->bind_param("ssii", $nama, $pesan, $pengantin_id, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: message.php');
    exit;
}

// Hapus pesan
if (isset($_GET['hapus'])) {
    csrf_verify();
    $id = (int) $_GET['hapus'];

    $stmt = $weddingku->prepare("DELETE FROM message WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header('Location: message.php');
    exit;
}
?>
