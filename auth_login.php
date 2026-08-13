<?php
include 'koneksi.php';
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);
session_regenerate_id(true);

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($email === '' || $password === '') {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire('Error', 'Email dan password wajib diisi!', 'error').then(() => {
            window.history.back();
        });
    </script>";
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
    }
     else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire('Error', 'Email atau password salah!', 'error').then(() => {
                window.history.back();
            });
        </script>";
    }
} else {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire('Error', 'Email atau password salah!', 'error').then(() => {
            window.history.back();
        });
    </script>";
}
?>
