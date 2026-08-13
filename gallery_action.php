<?php
include 'auth_check.php';
include 'koneksi.php';

$upload_dir = 'assets/gallery/';
$allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
$pengantin_id = isset($_POST['pengantin_id']) ? (int) $_POST['pengantin_id'] : 0;

// Tambah Foto
if (isset($_POST['simpan'])) {
    csrf_verify();
    $judul = trim($_POST['judul']);
    $namaFile = $_FILES['file']['name'];
    $tmpFile = $_FILES['file']['tmp_name'];
    $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed_extensions) && is_uploaded_file($tmpFile)) {
        // Path frame PNG
        $framePath = 'images/green_frame_transparent.png';

        // Load frame PNG
        $frame = @imagecreatefrompng($framePath);
        if ($frame) {
            imagealphablending($frame, true);
            imagesavealpha($frame, true);

            // Load gambar upload sesuai ekstensi
            switch ($ext) {
                case 'jpg':
                case 'jpeg':
                    $uploaded = @imagecreatefromjpeg($tmpFile);
                    break;
                case 'png':
                    $uploaded = @imagecreatefrompng($tmpFile);
                    break;
                case 'gif':
                    $uploaded = @imagecreatefromgif($tmpFile);
                    break;
                default:
                    $uploaded = false;
            }

            if ($uploaded) {
                // Resize agar pas dengan ukuran frame
                $uploaded = imagescale($uploaded, imagesx($frame), imagesy($frame));

                // Gabungkan frame ke gambar
                imagecopy($uploaded, $frame, 0, 0, 0, 0, imagesx($frame), imagesy($frame));

                // Simpan hasil akhir
                $namaBaru = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $namaFile);
                $savePath = $upload_dir . $namaBaru;
                imagepng($uploaded, $savePath);

                // Simpan ke database
                $stmt = $weddingku->prepare("INSERT INTO gallery (judul, file, pengantin_id) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $judul, $namaBaru, $pengantin_id);
                $stmt->execute();

                // Bersihkan memori
                imagedestroy($uploaded);
            }
            imagedestroy($frame);
        }
    }

    header('Location: gallery.php');
    exit();
}

// Update Foto
if (isset($_POST['update'])) {
    csrf_verify();
    $id = (int) $_POST['id'];
    $judul = trim($_POST['judul']);
    $file = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    if (!empty($file) && in_array($ext, $allowed_extensions) && is_uploaded_file($tmp)) {
        $namaBaru = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file);
        move_uploaded_file($tmp, $upload_dir . $namaBaru);
        $stmt = $weddingku->prepare("UPDATE gallery SET judul=?, file=? WHERE id=?");
        $stmt->bind_param("ssi", $judul, $namaBaru, $id);
        $stmt->execute();
    } else {
        $stmt = $weddingku->prepare("UPDATE gallery SET judul=? WHERE id=?");
        $stmt->bind_param("si", $judul, $id);
        $stmt->execute();
    }

    header('Location: gallery.php');
    exit();
}

// Hapus Foto
if (isset($_GET['hapus'])) {
    csrf_verify();
    $id = (int) $_GET['hapus'];
    $stmt = $weddingku->prepare("SELECT file FROM gallery WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $foto = $result->fetch_assoc()['file'] ?? null;

    if ($foto && file_exists($upload_dir . $foto)) {
        unlink($upload_dir . $foto);
    }

    $stmt = $weddingku->prepare("DELETE FROM gallery WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header('Location: gallery.php');
    exit();
}
