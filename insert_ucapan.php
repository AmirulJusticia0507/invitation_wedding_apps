<?php
include 'koneksi.php'; // Pastikan koneksi database sudah benar

// Mengambil parameter dari URL
$uid = isset($_GET['uid']) ? $_GET['uid'] : '';
$guest = isset($_GET['guest']) ? $_GET['guest'] : '';

// Mengambil data ucapan dari POST
$namaUcapan = isset($_POST['namaUcapan']) ? trim($_POST['namaUcapan']) : '';
$isiUcapan = isset($_POST['isiUcapan']) ? trim($_POST['isiUcapan']) : '';

// Validasi
if (!empty($namaUcapan) && !empty($isiUcapan) && !empty($uid) && !empty($guest)) {
    // Cari pengantin_id dari token UID
    $stmt_undangan = $weddingku->prepare("SELECT pengantin_id FROM undangan_url WHERE encrypted_token = ? LIMIT 1");
    $stmt_undangan->bind_param("s", $uid);
    $stmt_undangan->execute();
    $undangan_query = $stmt_undangan->get_result();

    if ($undangan_query->num_rows > 0) {
        $undangan = $undangan_query->fetch_assoc();
        $pengantin_id = (int) $undangan['pengantin_id'];

        // Simpan ke database
        $stmt = $weddingku->prepare("INSERT INTO message (pengantin_id, nama, pesan, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $pengantin_id, $namaUcapan, $isiUcapan);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Gagal menyimpan ucapan.";
        }
    } else {
        echo "Token tidak ditemukan!";
    }
} else {
    echo "Data tidak lengkap!";
}
?>
