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

    // Daftar tamu: dari textarea tamu_ids (koma) atau fallback tamu_id tunggal
    $tamu_ids = [];
    if (!empty($_POST['tamu_ids'])) {
        $tamu_ids = array_filter(array_map('intval', explode(',', $_POST['tamu_ids'])));
    } elseif (!empty($_POST['tamu_id'])) {
        $tamu_ids = [(int) $_POST['tamu_id']];
    }

    if ($pengantin_id <= 0 || empty($tamu_ids)) {
        header("Location: undangan_url.php");
        exit;
    }

    $stmt_tamu = $weddingku->prepare("SELECT nama FROM tamu WHERE id = ?");
    $stmt = $weddingku->prepare("INSERT INTO undangan_url (pengantin_id, tamu_id, encrypted_token, url_undangan) VALUES (?, ?, ?, ?)");

    foreach ($tamu_ids as $tamu_id) {
        $token = generateToken($pengantin_id, $tamu_id);

        $stmt_tamu->bind_param("i", $tamu_id);
        $stmt_tamu->execute();
        $tamu = $stmt_tamu->get_result()->fetch_assoc();
        $nama_tamu = $tamu ? urlencode(str_replace(' ', '-', $tamu['nama'])) : '';

        $url = "index.php?uid=$token&guest=$nama_tamu";

        $stmt->bind_param("iiss", $pengantin_id, $tamu_id, $token, $url);
        $stmt->execute();
    }

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

// Proses konfirmasi RSVP
if (isset($_POST['konfirmasi'])) {
    csrf_verify();
    $id = (int) $_POST['id'];
    $status = $_POST['status']; // 'hadir' atau 'tidak_hadir'

    // Validasi status
    if ($status !== 'hadir' && $status !== 'tidak_hadir') {
        die("Status tidak valid!");
    }

    // Update status RSVP
    $stmt = $weddingku->prepare("UPDATE undangan_url SET status_rsvp = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    header("Location: undangan_url.php");
    exit;
}
?>
