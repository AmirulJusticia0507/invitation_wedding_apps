<?php
include 'auth_check.php';
include 'koneksi.php';

// Simpan
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $pengantin_id = $_POST['pengantin_id'];

    $weddingku->query("INSERT INTO tamu (nama, alamat, pengantin_id) VALUES ('$nama', '$alamat', '$pengantin_id')");
    header('Location: tamu.php');
}

// Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $pengantin_id = $_POST['pengantin_id'];

    $weddingku->query("UPDATE tamu SET nama='$nama', alamat='$alamat', pengantin_id='$pengantin_id' WHERE id=$id");
    header('Location: tamu.php');
}

// Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $weddingku->query("DELETE FROM tamu WHERE id=$id");
    header('Location: tamu.php');
}
?>
