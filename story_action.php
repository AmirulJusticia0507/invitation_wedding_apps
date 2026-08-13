<?php
include 'auth_check.php';
include 'koneksi.php';

// Tambah story
if (isset($_POST['simpan'])) {
    $judul = $_POST['judul'];
    $cerita = $_POST['cerita'];
    $tanggal = $_POST['tanggal'];
    $pengantin_id = $_POST['pengantin_id'];

    $weddingku->query("INSERT INTO story (judul, cerita, tanggal, pengantin_id) VALUES ('$judul', '$cerita', '$tanggal', '$pengantin_id')");
    header('Location: story.php');
}

// Update story
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $cerita = $_POST['cerita'];
    $tanggal = $_POST['tanggal'];
    $pengantin_id = $_POST['pengantin_id'];

    $weddingku->query("UPDATE story SET judul='$judul', cerita='$cerita', tanggal='$tanggal', pengantin_id='$pengantin_id' WHERE id=$id");
    header('Location: story.php');
}

// Hapus story
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $weddingku->query("DELETE FROM story WHERE id=$id");
    header('Location: story.php');
}
