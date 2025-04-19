<?php
include 'koneksi.php'; // Pastikan koneksi database sudah benar

// Mengambil parameter dari URL
$uid = isset($_GET['uid']) ? $_GET['uid'] : '';
$guest = isset($_GET['guest']) ? $_GET['guest'] : '';

// Debugging: Output parameter UID dan Guest
echo "UID: " . $uid . "<br>";
echo "Guest: " . $guest . "<br>";

// Mengambil data ucapan dari POST
$namaUcapan = isset($_POST['namaUcapan']) ? $_POST['namaUcapan'] : '';
$isiUcapan = isset($_POST['isiUcapan']) ? $_POST['isiUcapan'] : '';

// Validasi
if (!empty($namaUcapan) && !empty($isiUcapan) && !empty($uid) && !empty($guest)) {
    // Cari pengantin_id dari token UID
    $undangan_query = $weddingku->query("SELECT pengantin_id FROM undangan_url WHERE encrypted_token = '$uid' LIMIT 1");

    if ($undangan_query->num_rows > 0) {
        $undangan = $undangan_query->fetch_assoc();
        $pengantin_id = $undangan['pengantin_id'];

        // Debug
        echo "Pengantin ID: " . $pengantin_id . "<br>";

        // Simpan ke database
        $namaUcapan = $weddingku->real_escape_string($namaUcapan);
        $isiUcapan = $weddingku->real_escape_string($isiUcapan);

        $query = "INSERT INTO message (pengantin_id, nama, pesan, created_at) 
                  VALUES ('$pengantin_id', '$namaUcapan', '$isiUcapan', NOW())";

        if ($weddingku->query($query)) {
            echo "Ucapan berhasil disimpan!<br>";
        } else {
            echo "Gagal menyimpan ucapan: " . $weddingku->error . "<br>";
        }
    } else {
        echo "Token tidak ditemukan!<br>";
    }
} else {
    echo "Data tidak lengkap!<br>";
}
?>
