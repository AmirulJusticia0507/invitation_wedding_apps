<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $weddingku->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $data = $result->fetch_assoc();
        $email = $data['email'];

        $stmt = $weddingku->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $email);
        $stmt->execute();

        $weddingku->query("DELETE FROM password_resets WHERE email = '$email'");

        echo json_encode(["status" => "success", "message" => "Password berhasil direset."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Token tidak valid atau sudah kadaluarsa."]);
    }
}
?>