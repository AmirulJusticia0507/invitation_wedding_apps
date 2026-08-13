<?php
include 'auth_check.php';
include 'koneksi.php';

$upload_dir = 'assets/gallery/';
$allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
$pengantin_id = $_POST['pengantin_id'];

// Tambah Foto
if (isset($_POST['simpan'])) {
    $judul = $_POST['judul'];
    $namaFile = $_FILES['file']['name'];
    $tmpFile = $_FILES['file']['tmp_name'];
    $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed_extensions)) {
        // Path frame PNG
        $framePath = 'images/green_frame_transparent.png';

        // Load frame PNG
        $frame = imagecreatefrompng($framePath);
        imagealphablending($frame, true);
        imagesavealpha($frame, true);

        // Load gambar upload sesuai ekstensi
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $uploaded = imagecreatefromjpeg($tmpFile);
                break;
            case 'png':
                $uploaded = imagecreatefrompng($tmpFile);
                break;
            case 'gif':
                $uploaded = imagecreatefromgif($tmpFile);
                break;
            default:
                die("Format gambar tidak didukung.");
        }

        // Resize agar pas dengan ukuran frame
        $uploaded = imagescale($uploaded, imagesx($frame), imagesy($frame));

        // Gabungkan frame ke gambar
        imagecopy($uploaded, $frame, 0, 0, 0, 0, imagesx($frame), imagesy($frame));

        // Simpan hasil akhir
        $namaBaru = time() . '_' . $namaFile;
        $savePath = $upload_dir . $namaBaru;
        imagepng($uploaded, $savePath);

        // Simpan ke database
        $weddingku->query("INSERT INTO gallery (judul, file, pengantin_id) VALUES ('$judul', '$namaBaru', '$pengantin_id')");

        // Bersihkan memori
        imagedestroy($uploaded);
        imagedestroy($frame);
    }

    header('Location: gallery.php');
    exit();
}

// Update Foto
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $file = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    if (!empty($file) && in_array($ext, $allowed_extensions)) {
        $namaBaru = time() . '_' . $file;
        move_uploaded_file($tmp, $upload_dir . $namaBaru);
        $weddingku->query("UPDATE gallery SET judul='$judul', file='$namaBaru' WHERE id=$id");
    } else {
        $weddingku->query("UPDATE gallery SET judul='$judul' WHERE id=$id");
    }

    header('Location: gallery.php');
    exit();
}

// Hapus Foto
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $foto = $weddingku->query("SELECT file FROM gallery WHERE id=$id")->fetch_assoc()['file'];

    if (file_exists($upload_dir . $foto)) {
        unlink($upload_dir . $foto);
    }

    $weddingku->query("DELETE FROM gallery WHERE id=$id");
    header('Location: gallery.php');
    exit();
}
