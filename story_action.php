<?php
include 'auth_check.php';
include 'koneksi.php';

// Tambah story
if (isset($_POST['simpan'])) {
    csrf_verify();
    $bulan = trim($_POST['bulan']);
    $tahun = (int) $_POST['tahun'];
    $deskripsi = trim($_POST['deskripsi']);
    $pengantin_id = (int) $_POST['pengantin_id'];

    $stmt = $weddingku->prepare("INSERT INTO story (bulan, tahun, deskripsi, pengantin_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sisi", $bulan, $tahun, $deskripsi, $pengantin_id);
    $stmt->execute();
    header('Location: story.php');
}

// Update story
if (isset($_POST['update'])) {
    csrf_verify();
    $id = (int) $_POST['id'];
    $bulan = trim($_POST['bulan']);
    $tahun = (int) $_POST['tahun'];
    $deskripsi = trim($_POST['deskripsi']);
    $pengantin_id = (int) $_POST['pengantin_id'];

    $stmt = $weddingku->prepare("UPDATE story SET bulan=?, tahun=?, deskripsi=?, pengantin_id=? WHERE id=?");
    $stmt->bind_param("sisii", $bulan, $tahun, $deskripsi, $pengantin_id, $id);
    $stmt->execute();
    header('Location: story.php');
}

// Hapus story
if (isset($_GET['hapus'])) {
    csrf_verify();
    $id = (int) $_GET['hapus'];

    $stmt = $weddingku->prepare("DELETE FROM story WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header('Location: story.php');
}
