<?php
include 'auth_check.php';
include 'koneksi.php';

$upload_dir = 'assets/gallery/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');

function processAndSaveImage($tmpFile, $origName, $upload_dir) {
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $namaBaru = time() . '_' . uniqid() . '.' . $ext;
    $savePath = $upload_dir . $namaBaru;

    // Cek apakah GD tersedia dan frame ada
    $framePath = 'images/green_frame_transparent.png';
    $useFrame = function_exists('imagecreatefrompng') && file_exists($framePath);

    if ($useFrame) {
        try {
            $frame = @imagecreatefrompng($framePath);
            if ($frame) {
                imagealphablending($frame, true);
                imagesavealpha($frame, true);

                $uploaded = false;
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
                    case 'webp':
                        if (function_exists('imagecreatefromwebp')) {
                            $uploaded = @imagecreatefromwebp($tmpFile);
                        }
                        break;
                }

                if ($uploaded) {
                    $w = imagesx($frame);
                    $h = imagesy($frame);
                    $resized = imagescale($uploaded, $w, $h);
                    if ($resized) {
                        imagecopy($resized, $frame, 0, 0, 0, 0, $w, $h);
                        imagepng($resized, $savePath);
                        imagedestroy($resized);
                        imagedestroy($uploaded);
                        imagedestroy($frame);
                        return $namaBaru;
                    }
                    imagedestroy($uploaded);
                }
                imagedestroy($frame);
            }
        } catch (Throwable $t) {
            // Jika pemrosesan GD gagal (misal Out of Memory), fallback ke upload biasa
        }
    }

    // Fallback: simpan langsung file aslinya
    if (move_uploaded_file($tmpFile, $savePath)) {
        return $namaBaru;
    }
    return false;
}

// TAMBAH FOTO
if (isset($_POST['simpan'])) {
    csrf_require_post();
    csrf_verify();
    $judul = trim($_POST['judul'] ?? '');
    $pengantin_id = (int) ($_POST['pengantin_id'] ?? 0);
    $namaFile = $_FILES['file']['name'] ?? '';
    $tmpFile = $_FILES['file']['tmp_name'] ?? '';
    $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    if ($judul === '' || $pengantin_id <= 0 || empty($namaFile)) {
        header('Location: gallery.php?error=' . urlencode("Judul, pasangan pengantin, dan file foto wajib diisi."));
        exit;
    }

    if (!in_array($ext, $allowed_extensions) || !is_uploaded_file($tmpFile)) {
        header('Location: gallery.php?error=' . urlencode("Format file tidak didukung. Format harus JPG, JPEG, PNG, GIF, atau WebP."));
        exit;
    }

    try {
        $savedFile = processAndSaveImage($tmpFile, $namaFile, $upload_dir);
        if (!$savedFile) {
            header('Location: gallery.php?error=' . urlencode("Gagal menyimpan foto ke server."));
            exit;
        }

        $stmt = $weddingku->prepare("INSERT INTO gallery (judul, file, pengantin_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $judul, $savedFile, $pengantin_id);
        $stmt->execute();
        $stmt->close();

        header('Location: gallery.php?msg=saved');
        exit;
    } catch (Throwable $e) {
        header('Location: gallery.php?error=' . urlencode("Gagal menyimpan galeri: " . $e->getMessage()));
        exit;
    }
}

// UPDATE FOTO
if (isset($_POST['update'])) {
    csrf_require_post();
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    $judul = trim($_POST['judul'] ?? '');
    $pengantin_id = isset($_POST['pengantin_id']) ? (int) $_POST['pengantin_id'] : 0;
    $namaFile = $_FILES['file']['name'] ?? '';
    $tmpFile = $_FILES['file']['tmp_name'] ?? '';

    if ($id <= 0 || $judul === '') {
        header('Location: gallery.php?error=' . urlencode("Data foto tidak valid."));
        exit;
    }

    try {
        $savedFile = null;
        // Ambil data foto lama
        $stmt_old = $weddingku->prepare("SELECT file, pengantin_id FROM gallery WHERE id=?");
        $stmt_old->bind_param("i", $id);
        $stmt_old->execute();
        $old = $stmt_old->get_result()->fetch_assoc();
        if (!$old) {
            header('Location: gallery.php?error=' . urlencode("Foto tidak ditemukan."));
            exit;
        }
        $oldFile = $old['file'] ?? '';
        $final_pengantin_id = $pengantin_id > 0 ? $pengantin_id : ($old['pengantin_id'] ?? null);

        if (!empty($namaFile) && is_uploaded_file($tmpFile)) {
            $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_extensions)) {
                header('Location: gallery.php?error=' . urlencode("Format file foto tidak didukung."));
                exit;
            }

            $savedFile = processAndSaveImage($tmpFile, $namaFile, $upload_dir);
            if (!$savedFile) {
                header('Location: gallery.php?error=' . urlencode("Gagal mengupload file foto baru."));
                exit;
            }

            $stmt = $weddingku->prepare("UPDATE gallery SET judul=?, file=?, pengantin_id=? WHERE id=?");
            $stmt->bind_param("ssii", $judul, $savedFile, $final_pengantin_id, $id);
        } else {
            $stmt = $weddingku->prepare("UPDATE gallery SET judul=?, pengantin_id=? WHERE id=?");
            $stmt->bind_param("sii", $judul, $final_pengantin_id, $id);
        }

        $stmt->execute();
        $stmt->close();

        if (isset($savedFile) && $oldFile && $oldFile !== $savedFile && file_exists($upload_dir . $oldFile)) {
            @unlink($upload_dir . $oldFile);
        }

        header('Location: gallery.php?msg=updated');
        exit;
    } catch (Throwable $e) {
        if (!empty($savedFile) && is_file($upload_dir . basename($savedFile))) {
            @unlink($upload_dir . basename($savedFile));
        }
        header('Location: gallery.php?error=' . urlencode("Gagal memperbarui foto: " . $e->getMessage()));
        exit;
    }
}

// HAPUS FOTO
if (isset($_POST['hapus'])) {
    csrf_require_post();
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        header('Location: gallery.php?error=' . urlencode("ID foto tidak valid."));
        exit;
    }

    try {
        $stmt = $weddingku->prepare("SELECT file FROM gallery WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $foto = $result->fetch_assoc()['file'] ?? null;

        $stmt = $weddingku->prepare("DELETE FROM gallery WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        if ($foto && file_exists($upload_dir . basename($foto))) {
            @unlink($upload_dir . basename($foto));
        }

        header('Location: gallery.php?msg=deleted');
        exit;
    } catch (Throwable $e) {
        header('Location: gallery.php?error=' . urlencode("Gagal menghapus foto: " . $e->getMessage()));
        exit;
    }
}

header('Location: gallery.php');
exit;
