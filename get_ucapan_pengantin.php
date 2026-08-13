<?php
include 'auth_check.php';
include 'koneksi.php';

if (isset($_GET['pengantin_id'])) {
    $pengantin_id = (int) $_GET['pengantin_id'];

    // Query to get ucapan for the selected pengantin_id
    $stmt = $weddingku->prepare("SELECT m.id, m.nama, m.pesan, m.created_at FROM message m WHERE m.pengantin_id = ?");
    $stmt->bind_param("i", $pengantin_id);
    $stmt->execute();
    $ucapanQuery = $stmt->get_result();

    // Initialize an empty array to hold the results
    $ucapanList = [];
    while ($row = $ucapanQuery->fetch_assoc()) {
        $ucapanList[] = $row;
    }

    // Return the data as a JSON response
    echo json_encode($ucapanList);
}
?>
