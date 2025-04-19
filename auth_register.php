<?php
include 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password_raw = isset($_POST['password']) ? $_POST['password'] : '';

    // Validasi input
    if (empty($nama) || empty($email) || empty($password_raw)) {
        echo json_encode(["status" => "error", "message" => "Semua field harus diisi."]);
        exit;
    }

    $password = password_hash($password_raw, PASSWORD_BCRYPT);

    try {
        // Cek apakah email sudah terdaftar
        $check = $weddingku->prepare("SELECT * FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "Email sudah digunakan."]);
        } else {
            // Simpan user baru
            $stmt = $weddingku->prepare("INSERT INTO users (email, password, nama_lengkap) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $password, $nama);
            $stmt->execute();

            echo json_encode(["status" => "success", "message" => "Registrasi berhasil."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Terjadi kesalahan: " . $e->getMessage()]);
    }
}
?>
