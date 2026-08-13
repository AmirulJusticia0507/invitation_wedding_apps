<?php
include 'auth_check.php';
include 'koneksi.php'; // Masukkan file koneksi ke database

header('Content-Type: application/json');

if (isset($_GET['term'])) {
    $term = '%' . $_GET['term'] . '%'; // Ambil nilai dari parameter 'term'
    $query = "SELECT kode_bank, nama_bank FROM bank_list WHERE nama_bank LIKE ? LIMIT 10"; // Query pencarian bank berdasarkan nama
    $stmt = $weddingku->prepare($query);
    $stmt->bind_param("s", $term);
    $stmt->execute();
    $result = $stmt->get_result();

    $banks = [];
    while ($row = $result->fetch_assoc()) {
        $banks[] = [
            'label' => $row['nama_bank'], // Tampilkan nama bank
            'value' => $row['kode_bank']  // Kode bank untuk input form
        ];
    }

    echo json_encode($banks); // Kembalikan data dalam format JSON
}
?>
