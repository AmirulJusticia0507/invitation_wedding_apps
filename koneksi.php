<?php
// Informasi koneksi database
$server = getenv('WEDDING_DB_HOST') ?: '127.0.0.1';
$username = getenv('WEDDING_DB_USER') ?: 'root';
$password = getenv('WEDDING_DB_PASSWORD') ?: 'mysql';
$database = getenv('WEDDING_DB_NAME') ?: 'weddingku_db';

mysqli_report(MYSQLI_REPORT_OFF);

// Membuat koneksi ke database
$weddingku = new mysqli($server, $username, $password, $database);

// Memeriksa apakah koneksi berhasil
if ($weddingku->connect_error) {
    http_response_code(500);
    die("Koneksi database gagal. Periksa WEDDING_DB_PASSWORD di konfigurasi Laragon.");
}
?>
