<?php
include 'koneksi.php';

$email = 'admin@gmail.com';
$password_plain = 'admin123';
$hash = password_hash($password_plain, PASSWORD_BCRYPT);

// Pastikan user ada atau buat baru
$check = $weddingku->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    $stmt = $weddingku->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hash, $email);
    $stmt->execute();
    $status = "Password akun <strong>$email</strong> berhasil di-reset menjadi: <code>admin123</code>";
} else {
    $nama = 'Amirul Putra Justicia, A.Md., S.Kom.';
    $stmt = $weddingku->prepare("INSERT INTO users (email, password, nama_lengkap) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $hash, $nama);
    $stmt->execute();
    $status = "User <strong>$email</strong> berhasil dibuat dengan password: <code>admin123</code>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-950 text-white min-h-screen flex items-center justify-center font-['Inter'] p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 max-w-md w-full shadow-2xl text-center">
        <div class="w-14 h-14 bg-emerald-500/10 text-emerald-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
            ✓
        </div>
        <h1 class="text-xl font-bold mb-2">Reset Password Berhasil!</h1>
        <p class="text-slate-300 text-sm mb-6 leading-relaxed"><?= $status ?></p>
        
        <div class="bg-slate-800/80 rounded-xl p-4 text-left text-xs text-slate-300 mb-6 space-y-1">
            <p><strong>Email:</strong> admin@gmail.com</p>
            <p><strong>Password:</strong> admin123</p>
        </div>

        <a href="login.php" class="block w-full py-3 bg-pink-500 hover:bg-pink-400 text-white rounded-xl font-semibold text-sm transition">
            Ke Halaman Login
        </a>
    </div>
</body>
</html>
