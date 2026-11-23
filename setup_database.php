<?php
$server = "localhost";
$username = "root";
$password = "";

// Koneksi tanpa database terlebih dahulu
$conn = new mysqli($server, $username, $password);

if ($conn->connect_error) {
    die("Koneksi MySQL gagal: " . $conn->connect_error);
}

// Buat database weddingku_db jika belum ada
$conn->query("CREATE DATABASE IF NOT EXISTS `weddingku_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
$conn->select_db("weddingku_db");

// Cek apakah tabel users sudah ada
$tabelCheck = $conn->query("SHOW TABLES LIKE 'users'");
if ($tabelCheck->num_rows == 0) {
    $sqlFile = __DIR__ . '/sql/weddingku_db.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        if ($conn->multi_query($sql)) {
            do {
                if ($res = $conn->store_result()) {
                    $res->free();
                }
            } while ($conn->more_results() && $conn->next_result());
        }
    }
}

// Set password default admin menjadi 'admin123'
$newHash = password_hash('admin123', PASSWORD_BCRYPT);
$conn->query("UPDATE `users` SET `password` = '$newHash' WHERE `email` = 'admin@gmail.com'");

// Pastikan tabel password_resets ada
$conn->query("CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` int NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `token` varchar(64) NOT NULL,
    `expires_at` datetime NOT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `token` (`token`),
    KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if (php_sapi_name() === 'cli') {
    echo "DATABASE_SETUP_SUCCESS" . PHP_EOL;
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Weddingku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-950 text-white min-h-screen flex items-center justify-center font-['Inter'] p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 max-w-md w-full shadow-2xl text-center">
        <div class="w-16 h-16 bg-emerald-500/10 text-emerald-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
            ✓
        </div>
        <h1 class="text-2xl font-bold mb-2">Database Siap Digunakan!</h1>
        <p class="text-slate-400 text-sm mb-6 leading-relaxed">
            Database <code>weddingku_db</code> dan tabel-tabelnya sudah berhasil dibuat dan di-import.
        </p>
        
        <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-4 text-left text-sm text-slate-300 mb-6 space-y-2">
            <div class="flex justify-between items-center border-b border-slate-700/60 pb-2">
                <span class="text-slate-400">Database:</span>
                <span class="font-mono text-emerald-400 font-semibold">weddingku_db</span>
            </div>
            <div class="flex justify-between items-center border-b border-slate-700/60 pb-2">
                <span class="text-slate-400">Email:</span>
                <span class="font-mono text-white font-medium">admin@gmail.com</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-slate-400">Password:</span>
                <span class="font-mono text-pink-400 font-bold">admin123</span>
            </div>
        </div>

        <a href="login.php" class="block w-full py-3 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-400 hover:to-rose-400 text-white rounded-xl font-semibold text-sm shadow-lg shadow-pink-500/20 transition">
            Masuk ke Halaman Login
        </a>
    </div>
</body>
</html>
