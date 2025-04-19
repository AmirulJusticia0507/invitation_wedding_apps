<?php
// Informasi koneksi database
$server = "localhost";   // Nama server database (misalnya "localhost" jika dijalankan secara lokal)
$username = "root";  // Nama pengguna MySQL
$password = "";  // Kata sandi MySQL
$database = "weddingku_db"; // Nama database yang sudah Anda buat

// Membuat koneksi ke database
$weddingku = new mysqli($server, $username, $password, $database);

// Memeriksa apakah koneksi berhasil
if ($weddingku->connect_error) {
    die("Koneksi gagal: " . $weddingku->connect_error);
}

?>