<?php
include 'auth_check.php';
include 'koneksi.php';

if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
}

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['import'])) {
    csrf_verify();
    $file = $_FILES['file_excel']['tmp_name'] ?? null;
    $pengantin_id = isset($_POST['pengantin_id']) ? (int) $_POST['pengantin_id'] : 0;

    if ($pengantin_id <= 0) {
        header("Location: tamu.php?error=" . urlencode("Silakan pilih pasangan pengantin terlebih dahulu."));
        exit;
    }

    if ($file && file_exists($file)) {
        try {
            if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
                throw new Exception("PhpSpreadsheet library tidak ditemukan. Jalankan composer install.");
            }

            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            $stmt = $weddingku->prepare("INSERT INTO tamu (nama, alamat, pengantin_id) VALUES (?, ?, ?)");
            $count = 0;

            foreach ($sheet as $index => $row) {
                if ($index === 0) continue; // skip header row

                $nama   = trim((string)($row[0] ?? ''));
                $alamat = trim((string)($row[1] ?? ''));

                if ($nama !== '') {
                    $stmt->bind_param("ssi", $nama, $alamat, $pengantin_id);
                    $stmt->execute();
                    $count++;
                }
            }

            header("Location: tamu.php?msg=" . urlencode("$count data tamu berhasil diimport!"));
            exit;
        } catch (Throwable $e) {
            header("Location: tamu.php?error=" . urlencode("Gagal import: " . $e->getMessage()));
            exit;
        }
    } else {
        header("Location: tamu.php?error=" . urlencode("File Excel tidak ditemukan atau gagal diupload."));
        exit;
    }
}

header("Location: tamu.php");
exit;
