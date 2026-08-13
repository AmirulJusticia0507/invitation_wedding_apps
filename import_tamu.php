<?php
include 'auth_check.php';
include 'koneksi.php';
require 'vendor/autoload.php'; // pastikan ini ada setelah composer install

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['import'])) {
    csrf_verify();
    $file = $_FILES['file_excel']['tmp_name'];
    $pengantin_id = isset($_POST['pengantin_id']) ? (int) $_POST['pengantin_id'] : null;

    if ($file) {
        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            $stmt = $weddingku->prepare("INSERT INTO tamu (nama, alamat, pengantin_id) VALUES (?, ?, ?)");

            // Lewati baris pertama (header)
            foreach ($sheet as $index => $row) {
                if ($index == 0) continue; // skip header row

                $nama   = $row[0];
                $alamat = $row[1];

                if (!empty($nama) && !empty($alamat)) {
                    $stmt->bind_param("ssi", $nama, $alamat, $pengantin_id);
                    $stmt->execute();
                }
            }

            echo "<script>alert('Data tamu berhasil diimport!'); window.location.href='tamu.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Gagal import: " . $e->getMessage() . "'); window.location.href='tamu.php';</script>";
        }
    } else {
        echo "<script>alert('File tidak ditemukan.'); window.location.href='tamu.php';</script>";
    }
} else {
    echo "<script>window.location.href='tamu.php';</script>";
}
