<?php
include 'koneksi.php';

$uid = isset($_GET['uid']) ? $_GET['uid'] : '';
$data = [];

if ($uid) {
    // Ambil pengantin_id dari token (uid) yang ada di tabel undangan_url
    $result = $weddingku->query("SELECT pengantin_id FROM undangan_url WHERE encrypted_token = '$uid' LIMIT 1");

    if ($result && $row = $result->fetch_assoc()) {
        $pengantin_id = $row['pengantin_id'];

        // Ambil pesan dari tabel message berdasarkan pengantin_id
        $pesanQuery = $weddingku->query("SELECT nama, pesan FROM message WHERE pengantin_id = '$pengantin_id' ORDER BY id DESC");

        while ($pesan = $pesanQuery->fetch_assoc()) {
            $data[] = $pesan;
        }
    }
}

header('Content-Type: application/json');
echo json_encode($data);
?>
