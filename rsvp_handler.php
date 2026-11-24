<?php
include 'koneksi.php'; // Pastikan ini sudah ada untuk koneksi database

$token = $_POST['uid'] ?? ($_GET['uid'] ?? null);
$status = $_POST['status'] ?? 'tidak_hadir';

if ($token && in_array($status, ['hadir', 'tidak_hadir'], true)) {
    $stmt = $weddingku->prepare("UPDATE undangan_url SET status_rsvp = ? WHERE encrypted_token = ?");
    $stmt->bind_param("ss", $status, $token);
    $stmt->execute();
}

header('Location: index.php?uid=' . rawurlencode((string) $token));
exit;
?>
