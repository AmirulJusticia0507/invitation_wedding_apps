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
    csrf_require_post();
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
    csrf_require_post();
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
    if (!$old) {
        header("Location: pengantin.php?error=" . urlencode("Data pengantin tidak ditemukan."));
        exit;
    }

    $foto_pria = $old['foto_pria'] ?? null;
    $foto_wanita = $old['foto_wanita'] ?? null;

    if (!empty($_FILES['foto_pria']['name'])) {
        $uploaded_pria = uploadFoto($_FILES['foto_pria']);
        if (!$uploaded_pria) {
            header("Location: pengantin.php?error=" . urlencode("Upload foto pria gagal. Format harus JPG/PNG/WebP maks 5MB."));
            exit;
        }
        $foto_pria = $uploaded_pria;
    }

    if (!empty($_FILES['foto_wanita']['name'])) {
        $uploaded_wanita = uploadFoto($_FILES['foto_wanita']);
        if (!$uploaded_wanita) {
            header("Location: pengantin.php?error=" . urlencode("Upload foto wanita gagal. Format harus JPG/PNG/WebP maks 5MB."));
            exit;
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

        foreach ([['foto_pria', $foto_pria], ['foto_wanita', $foto_wanita]] as [$field, $newFile]) {
            $oldFile = $old[$field] ?? null;
            if ($newFile !== $oldFile && $oldFile && file_exists('uploads/' . basename($oldFile))) {
                @unlink('uploads/' . basename($oldFile));
            }
        }

        header("Location: pengantin.php?msg=updated");
        exit;
    } catch (Throwable $e) {
        foreach ([$foto_pria, $foto_wanita] as $newFile) {
            if ($newFile && !in_array($newFile, [$old['foto_pria'], $old['foto_wanita']], true) && is_file('uploads/' . basename($newFile))) {
                @unlink('uploads/' . basename($newFile));
            }
        }
        header("Location: pengantin.php?error=" . urlencode("Gagal memperbarui pengantin: " . $e->getMessage()));
        exit;
    }
}

// HAPUS PENGANTIN
if (isset($_POST['hapus'])) {
    csrf_require_post();
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        header("Location: pengantin.php?error=" . urlencode("ID pengantin tidak valid."));
        exit;
    }

    try {
        $weddingku->begin_transaction();
        $stmtGal = $weddingku->prepare("SELECT file FROM gallery WHERE pengantin_id = ?");
        $stmtGal->bind_param("i", $id);
        $stmtGal->execute();
        $gal = $stmtGal->get_result();
        $galleryFiles = [];
        if ($gal) {
            while ($g = $gal->fetch_assoc()) {
                if (!empty($g['file'])) $galleryFiles[] = basename($g['file']);
            }
        }

        $stmtPhotos = $weddingku->prepare("SELECT foto_pria, foto_wanita FROM pengantin WHERE id = ?");
        $stmtPhotos->bind_param("i", $id);
        $stmtPhotos->execute();
        $couplePhotos = $stmtPhotos->get_result()->fetch_assoc() ?: [];

        // Bersihkan log dan relasi undangan sebelum tamu dan pasangan dihapus.
        $stmtDeleteLogs = $weddingku->prepare("DELETE l FROM log_akses_undangan l JOIN undangan_url u ON l.undangan_url_id = u.id WHERE u.pengantin_id = ?");
        $stmtDeleteLogs->bind_param("i", $id);
        $stmtDeleteLogs->execute();
        foreach (['wedding_gift', 'story', 'message', 'gallery', 'undangan_url', 'tamu', 'folders'] as $table) {
            $delete = $weddingku->prepare("DELETE FROM `$table` WHERE pengantin_id = ?");
            $delete->bind_param("i", $id);
            $delete->execute();
        }

        // Hapus pengantin
        $stmt = $weddingku->prepare("DELETE FROM pengantin WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $weddingku->commit();

        foreach ($galleryFiles as $file) {
            if (is_file('assets/gallery/' . $file)) @unlink('assets/gallery/' . $file);
        }
        foreach (['foto_pria', 'foto_wanita'] as $field) {
            $file = $couplePhotos[$field] ?? '';
            if ($file && is_file('uploads/' . basename($file))) @unlink('uploads/' . basename($file));
        }

        header("Location: pengantin.php?msg=deleted");
        exit;
    } catch (Throwable $e) {
        $weddingku->rollback();
        header("Location: pengantin.php?error=" . urlencode("Gagal menghapus pengantin: " . $e->getMessage()));
        exit;
    }
}

header("Location: pengantin.php");
exit;
