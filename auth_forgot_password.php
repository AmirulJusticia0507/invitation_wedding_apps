<?php
include 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (empty($email)) {
        echo json_encode(["status" => "error", "message" => "Email wajib diisi."]);
        exit;
    }

    try {
        // Pastikan tabel password_resets ada
        $weddingku->query("CREATE TABLE IF NOT EXISTS `password_resets` (
            `id` int NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL,
            `token` varchar(64) NOT NULL,
            `expires_at` datetime NOT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `token` (`token`),
            KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $check = $weddingku->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows == 0) {
            echo json_encode(["status" => "error", "message" => "Email '$email' tidak ditemukan dalam database users."]);
            exit;
        }

        $token = bin2hex(random_bytes(32));
        $expire = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $stmt_del = $weddingku->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt_del->bind_param("s", $email);
        $stmt_del->execute();

        $stmt = $weddingku->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expire);
        $stmt->execute();

        echo json_encode([
            "status" => "success",
            "message" => "Token reset berhasil dibuat.",
            "token" => $token
        ]);
    } catch (Throwable $e) {
        echo json_encode(["status" => "error", "message" => "Terjadi kesalahan database: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Metode request tidak valid."]);
}
?>
