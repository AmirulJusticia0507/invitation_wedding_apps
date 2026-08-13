<?php
include 'koneksi.php';

$uid = isset($_GET['uid']) ? $_GET['uid'] : '';
$data = [];

if ($uid !== '') {
    // Ambil pengantin_id dari token (uid) yang ada di tabel undangan_url
    $stmt = $weddingku->prepare("SELECT pengantin_id FROM undangan_url WHERE encrypted_token = ? LIMIT 1");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        $pengantin_id = (int) $row['pengantin_id'];

        // Ambil pesan dari tabel message berdasarkan pengantin_id
        $stmt_pesan = $weddingku->prepare("SELECT nama, pesan FROM message WHERE pengantin_id = ? ORDER BY id DESC");
        $stmt_pesan->bind_param("i", $pengantin_id);
        $stmt_pesan->execute();
        $pesanQuery = $stmt_pesan->get_result();

        while ($pesan = $pesanQuery->fetch_assoc()) {
            $data[] = $pesan;
        }
    }
}

header('Content-Type: application/json');
echo json_encode($data);
?>
