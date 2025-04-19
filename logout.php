<?php
session_start(); // Mulai session

// Hapus semua session yang aktif
session_unset();

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header("Location: login.php"); // Ganti 'login.php' dengan halaman login sesuai proyek kamu
exit;
?>
