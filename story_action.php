<?php
include 'auth_check.php';
include 'koneksi.php';

// Tambah story
if (isset($_POST['simpan'])) {
    csrf_verify();
    $judul = trim($_POST['judul']);
    $cerita = trim($_POST['cerita']);
    $tanggal = $_POST['tanggal'];
    $pengantin_id = (int) $_POST['pengantin_id'];

    $stmt = $weddingku->prepare("INSERT INTO story (judul, cerita, tanggal, pengantin_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $judul, $cerita, $tanggal, $pengantin_id);
    $stmt->execute();
    header('Location: story.php');
}

// Update story
if (isset($_POST['update'])) {
    csrf_verify();
    $id = (int) $_POST['id'];
    $judul = trim($_POST['judul']);
    $cerita = trim($_POST['cerita']);
    $tanggal = $_POST['tanggal'];
    $pengantin_id = (int) $_POST['pengantin_id'];

    $stmt = $weddingku->prepare("UPDATE story SET judul=?, cerita=?, tanggal=?, pengantin_id=? WHERE id=?");
    $stmt->bind_param("sssii", $judul, $cerita, $tanggal, $pengantin_id, $id);
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
