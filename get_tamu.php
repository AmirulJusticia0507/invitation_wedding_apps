<?php
include 'auth_check.php';
include 'koneksi.php';

if (isset($_GET['pengantin_id'])) {
    $pengantin_id = (int) $_GET['pengantin_id'];

    // Query to get guests for the selected pengantin_id
    $stmt = $weddingku->prepare("SELECT t.id, t.nama, t.alamat FROM tamu t WHERE t.pengantin_id = ?");
    $stmt->bind_param("i", $pengantin_id);
    $stmt->execute();
    $tamuQuery = $stmt->get_result();

    // Initialize an empty array to hold the results
    $tamuList = [];
    while ($row = $tamuQuery->fetch_assoc()) {
        $tamuList[] = $row;
    }

    // Return the data as a JSON response
    echo json_encode($tamuList);
}
?>
