<?php
include 'koneksi.php';
require 'vendor/autoload.php'; // pastikan ini ada setelah composer install

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['import'])) {
    $file = $_FILES['file_excel']['tmp_name'];

    if ($file) {
        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            // Lewati baris pertama (header)
            foreach ($sheet as $index => $row) {
                if ($index == 0) continue; // skip header row

                $nama   = $row[0];
                $alamat = $row[1];

                if (!empty($nama) && !empty($alamat)) {
                    $weddingku->query("INSERT INTO tamu (nama, alamat) VALUES ('$nama', '$alamat')");
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
