<?php
// File ini adalah endpoint POST, bukan halaman yang dibuka langsung.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

include 'koneksi.php';
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);
session_regenerate_id(true);

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($email === '' || $password === '') {
    header('Location: login.php?error=' . urlencode('Email dan password wajib diisi.'));
    exit;
}

$stmt = $weddingku->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit;
    } else {
        header('Location: login.php?error=' . urlencode('Email atau password salah.'));
        exit;
    }
} else {
    header('Location: login.php?error=' . urlencode('Email atau password salah.'));
    exit;
}
?>
