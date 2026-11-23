<?php
include 'auth_check.php';
include 'koneksi.php';

function uploadFoto($file) {
    $target_dir = 'uploads/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $mime = isset($file['tmp_name']) && file_exists($file['tmp_name']) ? mime_content_type($file['tmp_name']) : '';

    if (!in_array($ext, $allowed) || !in_array($mime, array('image/jpeg', 'image/png', 'image/gif', 'image/webp'))) {
        return false;
    }
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        return false;
    }

    $nama = time() . '_' . uniqid() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $target_dir . $nama)) {
        return false;
    }
    return $nama;
}

function clean_date_time($val) {
    $val = trim((string)$val);
    return $val === '' ? null : $val;
}

// SIMPAN PENGANTIN
if (isset($_POST['simpan'])) {
    csrf_verify();

    $foto_pria = null;
    $foto_wanita = null;

    if (!empty($_FILES['foto_pria']['name'])) {
        $foto_pria = uploadFoto($_FILES['foto_pria']);
        if (!$foto_pria) {
            header("Location: pengantin.php?error=" . urlencode("Upload foto pria gagal. Format harus JPG/PNG/WebP maks 5MB."));
            exit;
        }
    }

    if (!empty($_FILES['foto_wanita']['name'])) {
        $foto_wanita = uploadFoto($_FILES['foto_wanita']);
        if (!$foto_wanita) {
            header("Location: pengantin.php?error=" . urlencode("Upload foto wanita gagal. Format harus JPG/PNG/WebP maks 5MB."));
            exit;
        }
    }

    $nama_pria = trim($_POST['nama_pria'] ?? '');
    $nama_panggilan_pria = trim($_POST['nama_panggilan_pria'] ?? '');
    $nama_wanita = trim($_POST['nama_wanita'] ?? '');
    $nama_panggilan_wanita = trim($_POST['nama_panggilan_wanita'] ?? '');
    $alamat_wanita = trim($_POST['alamat_wanita'] ?? '');
    $ortu_pria = trim($_POST['ortu_pria'] ?? '');
    $ortu_wanita = trim($_POST['ortu_wanita'] ?? '');

    $tanggal_akad = clean_date_time($_POST['tanggal_akad'] ?? '');
    $jam_akad = clean_date_time($_POST['jam_akad'] ?? '');
    $tanggal_resepsi = clean_date_time($_POST['tanggal_resepsi'] ?? '');
    $jam_resepsi = clean_date_time($_POST['jam_resepsi'] ?? '');

    try {
        $stmt = $weddingku->prepare("INSERT INTO pengantin (nama_pria, nama_panggilan_pria, nama_wanita, nama_panggilan_wanita, alamat_wanita, ortu_pria, ortu_wanita, tanggal_akad, jam_akad, foto_pria, foto_wanita, tanggal_resepsi, jam_resepsi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssss", $nama_pria, $nama_panggilan_pria, $nama_wanita, $nama_panggilan_wanita, $alamat_wanita, $ortu_pria, $ortu_wanita, $tanggal_akad, $jam_akad, $foto_pria, $foto_wanita, $tanggal_resepsi, $jam_resepsi);
        $stmt->execute();

        header("Location: pengantin.php?msg=saved");
        exit;
    } catch (Throwable $e) {
        header("Location: pengantin.php?error=" . urlencode("Gagal menyimpan pengantin: " . $e->getMessage()));
        exit;
    }
}

// UPDATE PENGANTIN
if (isset($_POST['update'])) {
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        header("Location: pengantin.php?error=" . urlencode("ID pengantin tidak valid."));
        exit;
    }

    // Ambil data foto lama
    $stmt_old = $weddingku->prepare("SELECT foto_pria, foto_wanita FROM pengantin WHERE id=?");
    $stmt_old->bind_param("i", $id);
    $stmt_old->execute();
    $old = $stmt_old->get_result()->fetch_assoc();

    $foto_pria = $old['foto_pria'] ?? null;
    $foto_wanita = $old['foto_wanita'] ?? null;

    if (!empty($_FILES['foto_pria']['name'])) {
        $uploaded_pria = uploadFoto($_FILES['foto_pria']);
        if (!$uploaded_pria) {
            header("Location: pengantin.php?error=" . urlencode("Upload foto pria gagal. Format harus JPG/PNG/WebP maks 5MB."));
            exit;
        }
        if ($foto_pria && file_exists('uploads/' . $foto_pria)) {
            @unlink('uploads/' . $foto_pria);
        }
        $foto_pria = $uploaded_pria;
    }

    if (!empty($_FILES['foto_wanita']['name'])) {
        $uploaded_wanita = uploadFoto($_FILES['foto_wanita']);
        if (!$uploaded_wanita) {
            header("Location: pengantin.php?error=" . urlencode("Upload foto wanita gagal. Format harus JPG/PNG/WebP maks 5MB."));
            exit;
        }
        if ($foto_wanita && file_exists('uploads/' . $foto_wanita)) {
            @unlink('uploads/' . $foto_wanita);
        }
        $foto_wanita = $uploaded_wanita;
    }

    $nama_pria = trim($_POST['nama_pria'] ?? '');
    $nama_panggilan_pria = trim($_POST['nama_panggilan_pria'] ?? '');
    $nama_wanita = trim($_POST['nama_wanita'] ?? '');
    $nama_panggilan_wanita = trim($_POST['nama_panggilan_wanita'] ?? '');
    $alamat_wanita = trim($_POST['alamat_wanita'] ?? '');
    $ortu_pria = trim($_POST['ortu_pria'] ?? '');
    $ortu_wanita = trim($_POST['ortu_wanita'] ?? '');

    $tanggal_akad = clean_date_time($_POST['tanggal_akad'] ?? '');
    $jam_akad = clean_date_time($_POST['jam_akad'] ?? '');
    $tanggal_resepsi = clean_date_time($_POST['tanggal_resepsi'] ?? '');
    $jam_resepsi = clean_date_time($_POST['jam_resepsi'] ?? '');

    try {
        $stmt = $weddingku->prepare("UPDATE pengantin SET nama_pria=?, nama_panggilan_pria=?, nama_wanita=?, nama_panggilan_wanita=?, alamat_wanita=?, ortu_pria=?, ortu_wanita=?, tanggal_akad=?, jam_akad=?, foto_pria=?, foto_wanita=?, tanggal_resepsi=?, jam_resepsi=? WHERE id=?");
        $stmt->bind_param("sssssssssssssi", $nama_pria, $nama_panggilan_pria, $nama_wanita, $nama_panggilan_wanita, $alamat_wanita, $ortu_pria, $ortu_wanita, $tanggal_akad, $jam_akad, $foto_pria, $foto_wanita, $tanggal_resepsi, $jam_resepsi, $id);
        $stmt->execute();

        header("Location: pengantin.php?msg=updated");
        exit;
    } catch (Throwable $e) {
        header("Location: pengantin.php?error=" . urlencode("Gagal memperbarui pengantin: " . $e->getMessage()));
        exit;
    }
}

// HAPUS PENGANTIN
if (isset($_GET['hapus'])) {
    csrf_verify();
    $id = (int) $_GET['hapus'];

    if ($id <= 0) {
        header("Location: pengantin.php?error=" . urlencode("ID pengantin tidak valid."));
        exit;
    }

    try {
        // Hapus file gallery terkait
        $gal = $weddingku->query("SELECT file FROM gallery WHERE pengantin_id = $id");
        if ($gal) {
            while ($g = $gal->fetch_assoc()) {
                if (!empty($g['file']) && file_exists('assets/gallery/' . $g['file'])) {
                    @unlink('assets/gallery/' . $g['file']);
                }
            }
        }

        // Hapus foto pengantin di uploads/
        $p_old = $weddingku->query("SELECT foto_pria, foto_wanita FROM pengantin WHERE id = $id");
        if ($p_old && $row = $p_old->fetch_assoc()) {
            if (!empty($row['foto_pria']) && file_exists('uploads/' . $row['foto_pria'])) {
                @unlink('uploads/' . $row['foto_pria']);
            }
            if (!empty($row['foto_wanita']) && file_exists('uploads/' . $row['foto_wanita'])) {
                @unlink('uploads/' . $row['foto_wanita']);
            }
        }

        // Bersihkan child rows untuk menghindari foreign key constraint error
        $weddingku->query("DELETE FROM wedding_gift WHERE pengantin_id = $id");
        $weddingku->query("DELETE FROM story WHERE pengantin_id = $id");
        $weddingku->query("DELETE FROM message WHERE pengantin_id = $id");
        $weddingku->query("DELETE FROM gallery WHERE pengantin_id = $id");
        $weddingku->query("DELETE FROM undangan_url WHERE pengantin_id = $id");
        $weddingku->query("DELETE FROM tamu WHERE pengantin_id = $id");

        // Hapus pengantin
        $stmt = $weddingku->prepare("DELETE FROM pengantin WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        header("Location: pengantin.php?msg=deleted");
        exit;
    } catch (Throwable $e) {
        header("Location: pengantin.php?error=" . urlencode("Gagal menghapus pengantin: " . $e->getMessage()));
        exit;
    }
}

header("Location: pengantin.php");
exit;
