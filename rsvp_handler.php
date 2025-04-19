<?php
include 'koneksi.php'; // Pastikan ini sudah ada untuk koneksi database

$token = $_GET['uid'] ?? null; // Mendapatkan token dari parameter URL

if ($token) {
    // Update status RSVP menjadi 'tidak_hadir' berdasarkan token yang terenkripsi
    $stmt = $weddingku->prepare("UPDATE undangan_url SET status_rsvp = 'tidak_hadir' WHERE encrypted_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute(); // Eksekusi query
}

header("Location: index.php?uid=$token"); // Redirect ke halaman utama dengan parameter uid
exit; // Keluar dari script setelah redirect
?>
