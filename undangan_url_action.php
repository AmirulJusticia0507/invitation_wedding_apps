<?php
include 'auth_check.php';
include 'koneksi.php';

// Fungsi untuk menghasilkan token undangan unik
function generateToken($pengantin_id, $tamu_id) {
    return md5($pengantin_id . '_' . $tamu_id . '_' . uniqid());
}

// Tambah data undangan URL baru
if (isset($_POST['tambah'])) {
    $pengantin_id = $_POST['pengantin_id'];
    $tamu_id = $_POST['tamu_id'];

    // Generate token untuk undangan
    $token = generateToken($pengantin_id, $tamu_id);

    // Ambil nama tamu
    $tamu = $weddingku->query("SELECT nama FROM tamu WHERE id = $tamu_id")->fetch_assoc();
    $nama_tamu = urlencode(str_replace(' ', '-', $tamu['nama'])); // Biar clean di URL

    // Buat URL undangan versi clean
    $url = "https://invitationweddingku.my.id/rsvp/$token/$nama_tamu";

    // Simpan ke database
    $query = "INSERT INTO undangan_url (pengantin_id, tamu_id, encrypted_token, url_undangan) 
              VALUES ('$pengantin_id', '$tamu_id', '$token', '$url')";
    $weddingku->query($query);

    header("Location: undangan_url.php");
    exit;
}

// Edit data undangan URL
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $pengantin_id = $_POST['pengantin_id'];
    $tamu_id = $_POST['tamu_id'];

    // Generate token baru
    $token = generateToken($pengantin_id, $tamu_id);

    // Ambil nama tamu
    $tamu = $weddingku->query("SELECT nama FROM tamu WHERE id = $tamu_id")->fetch_assoc();
    $nama_tamu = urlencode(str_replace(' ', '-', $tamu['nama']));

    // Buat URL undangan versi clean
    $url = "https://invitationweddingku.my.id/rsvp/$token/$nama_tamu";

    // Update database
    $query = "UPDATE undangan_url 
              SET pengantin_id='$pengantin_id', tamu_id='$tamu_id', encrypted_token='$token', url_undangan='$url'
              WHERE id = $id";
    $weddingku->query($query);

    header("Location: undangan_url.php");
    exit;
}

// Hapus data undangan URL
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    $weddingku->query("DELETE FROM undangan_url WHERE id = $id");

    header("Location: undangan_url.php");
    exit;
}
?>
