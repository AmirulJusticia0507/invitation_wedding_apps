<?php
include 'auth_check.php';
include 'koneksi.php';

// Simpan
if (isset($_POST['simpan'])) {
    csrf_verify();
    $nama = trim($_POST['nama']);
    $alamat = trim($_POST['alamat']);
    $pengantin_id = (int) $_POST['pengantin_id'];

    $stmt = $weddingku->prepare("INSERT INTO tamu (nama, alamat, pengantin_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $nama, $alamat, $pengantin_id);
    $stmt->execute();
    header('Location: tamu.php');
}

// Update
if (isset($_POST['update'])) {
    csrf_verify();
    $id = (int) $_POST['id'];
    $nama = trim($_POST['nama']);
    $alamat = trim($_POST['alamat']);
    $pengantin_id = (int) $_POST['pengantin_id'];

    $stmt = $weddingku->prepare("UPDATE tamu SET nama=?, alamat=?, pengantin_id=? WHERE id=?");
    $stmt->bind_param("ssii", $nama, $alamat, $pengantin_id, $id);
    $stmt->execute();
    header('Location: tamu.php');
}

// Hapus
if (isset($_GET['hapus'])) {
    csrf_verify();
    $id = (int) $_GET['hapus'];

    $stmt = $weddingku->prepare("DELETE FROM tamu WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header('Location: tamu.php');
}
?>
