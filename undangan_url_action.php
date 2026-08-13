<?php
include 'auth_check.php';
include 'koneksi.php';

// Fungsi untuk menghasilkan token undangan unik
function generateToken($pengantin_id, $tamu_id) {
    return md5($pengantin_id . '_' . $tamu_id . '_' . uniqid());
}

// Tambah data undangan URL baru
if (isset($_POST['tambah'])) {
    csrf_verify();
    $pengantin_id = (int) $_POST['pengantin_id'];
    $tamu_id = (int) $_POST['tamu_id'];

    // Generate token untuk undangan
    $token = generateToken($pengantin_id, $tamu_id);

    // Ambil nama tamu
    $stmt_tamu = $weddingku->prepare("SELECT nama FROM tamu WHERE id = ?");
    $stmt_tamu->bind_param("i", $tamu_id);
    $stmt_tamu->execute();
    $tamu = $stmt_tamu->get_result()->fetch_assoc();
    $nama_tamu = $tamu ? urlencode(str_replace(' ', '-', $tamu['nama'])) : '';

    // Buat URL undangan versi clean
    $url = "index.php?uid=$token&guest=$nama_tamu";

    // Simpan ke database
    $stmt = $weddingku->prepare("INSERT INTO undangan_url (pengantin_id, tamu_id, encrypted_token, url_undangan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $pengantin_id, $tamu_id, $token, $url);
    $stmt->execute();

    header("Location: undangan_url.php");
    exit;
}

// Edit data undangan URL
if (isset($_POST['edit'])) {
    csrf_verify();
    $id = (int) $_POST['id'];
    $pengantin_id = (int) $_POST['pengantin_id'];
    $tamu_id = (int) $_POST['tamu_id'];

    // Generate token baru
    $token = generateToken($pengantin_id, $tamu_id);

    // Ambil nama tamu
    $stmt_tamu = $weddingku->prepare("SELECT nama FROM tamu WHERE id = ?");
    $stmt_tamu->bind_param("i", $tamu_id);
    $stmt_tamu->execute();
    $tamu = $stmt_tamu->get_result()->fetch_assoc();
    $nama_tamu = $tamu ? urlencode(str_replace(' ', '-', $tamu['nama'])) : '';

    // Buat URL undangan versi clean
    $url = "index.php?uid=$token&guest=$nama_tamu";

    // Update database
    $stmt = $weddingku->prepare("UPDATE undangan_url SET pengantin_id=?, tamu_id=?, encrypted_token=?, url_undangan=? WHERE id = ?");
    $stmt->bind_param("iissi", $pengantin_id, $tamu_id, $token, $url, $id);
    $stmt->execute();

    header("Location: undangan_url.php");
    exit;
}

// Hapus data undangan URL
if (isset($_GET['hapus'])) {
    csrf_verify();
    $id = (int) $_GET['hapus'];

    $stmt = $weddingku->prepare("DELETE FROM undangan_url WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: undangan_url.php");
    exit;
}
?>
