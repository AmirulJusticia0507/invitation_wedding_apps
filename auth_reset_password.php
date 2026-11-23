<?php
include 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = isset($_POST['token']) ? trim($_POST['token']) : '';
    $raw_password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($token) || empty($raw_password)) {
        echo json_encode(["status" => "error", "message" => "Token dan password baru wajib diisi."]);
        exit;
    }

    try {
        $new_password = password_hash($raw_password, PASSWORD_BCRYPT);

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

            $stmt_del = $weddingku->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt_del->bind_param("s", $email);
            $stmt_del->execute();

            echo json_encode(["status" => "success", "message" => "Password berhasil diubah. Silakan login."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Token tidak valid atau sudah kadaluarsa."]);
        }
    } catch (Throwable $e) {
        echo json_encode(["status" => "error", "message" => "Terjadi kesalahan database: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Metode request tidak valid."]);
}
?>
