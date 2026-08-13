<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $check = $weddingku->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        echo json_encode(["status" => "error", "message" => "Email tidak ditemukan."]);
    } else {
        $token = bin2hex(random_bytes(32));
        $expire = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $stmt_del = $weddingku->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt_del->bind_param("s", $email);
        $stmt_del->execute();

        $stmt = $weddingku->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expire);
        $stmt->execute();

        echo json_encode(["status" => "success", "message" => "Token reset dikirim (simulasi).", "token" => $token]);
    }
}
?>