<?php
include 'auth_check.php';
include 'koneksi.php';

function uploadFoto($file) {
    $target_dir = 'uploads/';
    $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $mime = isset($file['tmp_name']) ? mime_content_type($file['tmp_name']) : '';

    if (!in_array($ext, $allowed) || !in_array($mime, array('image/jpeg', 'image/png', 'image/gif', 'image/webp'))) {
        return false;
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        return false;
    }

    $nama = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    if (!move_uploaded_file($file['tmp_name'], $target_dir . $nama)) {
        return false;
    }
    return $nama;
}

if (isset($_POST['simpan'])) {
    csrf_verify();

    $foto_pria = isset($_FILES['foto_pria']['name']) && $_FILES['foto_pria']['name'] !== '' ? uploadFoto($_FILES['foto_pria']) : null;
    $foto_wanita = isset($_FILES['foto_wanita']['name']) && $_FILES['foto_wanita']['name'] !== '' ? uploadFoto($_FILES['foto_wanita']) : null;

    if (isset($_FILES['foto_pria']['name']) && $_FILES['foto_pria']['name'] !== '' && !$foto_pria) {
        die("Upload foto pria gagal: format tidak didukung atau ukuran melebihi 2MB.");
    }
    if (isset($_FILES['foto_wanita']['name']) && $_FILES['foto_wanita']['name'] !== '' && !$foto_wanita) {
        die("Upload foto wanita gagal: format tidak didukung atau ukuran melebihi 2MB.");
    }

    $tanggal_resepsi = !empty($_POST['tanggal_resepsi']) ? $_POST['tanggal_resepsi'] : NULL;
    $jam_resepsi = !empty($_POST['jam_resepsi']) ? $_POST['jam_resepsi'] : NULL;

    $stmt = $weddingku->prepare("INSERT INTO pengantin (nama_pria, nama_panggilan_pria, nama_wanita, nama_panggilan_wanita, alamat_wanita, ortu_pria, ortu_wanita, tanggal_akad, jam_akad, foto_pria, foto_wanita, tanggal_resepsi, jam_resepsi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss", $_POST['nama_pria'], $_POST['nama_panggilan_pria'], $_POST['nama_wanita'], $_POST['nama_panggilan_wanita'], $_POST['alamat_wanita'], $_POST['ortu_pria'], $_POST['ortu_wanita'], $_POST['tanggal_akad'], $_POST['jam_akad'], $foto_pria, $foto_wanita, $tanggal_resepsi, $jam_resepsi);
    $stmt->execute();
    header("Location: pengantin.php");
}

if (isset($_POST['update'])) {
    csrf_verify();
    $id = (int) $_POST['id'];

    // Pertahankan foto lama jika tidak upload foto baru
    $stmt_old = $weddingku->prepare("SELECT foto_pria, foto_wanita FROM pengantin WHERE id=?");
    $stmt_old->bind_param("i", $id);
    $stmt_old->execute();
    $old = $stmt_old->get_result()->fetch_assoc();

    if (isset($_FILES['foto_pria']['name']) && $_FILES['foto_pria']['name'] !== '') {
        $foto_pria = uploadFoto($_FILES['foto_pria']);
        if (!$foto_pria) {
            die("Upload foto pria gagal: format tidak didukung atau ukuran melebihi 2MB.");
        }
    } else {
        $foto_pria = $old['foto_pria'];
    }

    if (isset($_FILES['foto_wanita']['name']) && $_FILES['foto_wanita']['name'] !== '') {
        $foto_wanita = uploadFoto($_FILES['foto_wanita']);
        if (!$foto_wanita) {
            die("Upload foto wanita gagal: format tidak didukung atau ukuran melebihi 2MB.");
        }
    } else {
        $foto_wanita = $old['foto_wanita'];
    }

    $tanggal_resepsi = !empty($_POST['tanggal_resepsi']) ? $_POST['tanggal_resepsi'] : NULL;
    $jam_resepsi = !empty($_POST['jam_resepsi']) ? $_POST['jam_resepsi'] : NULL;

    $stmt = $weddingku->prepare("UPDATE pengantin SET nama_pria=?, nama_panggilan_pria=?, nama_wanita=?, nama_panggilan_wanita=?, alamat_wanita=?, ortu_pria=?, ortu_wanita=?, tanggal_akad=?, jam_akad=?, foto_pria=?, foto_wanita=?, tanggal_resepsi=?, jam_resepsi=? WHERE id=?");
    $stmt->bind_param("sssssssssssssi", $_POST['nama_pria'], $_POST['nama_panggilan_pria'], $_POST['nama_wanita'], $_POST['nama_panggilan_wanita'], $_POST['alamat_wanita'], $_POST['ortu_pria'], $_POST['ortu_wanita'], $_POST['tanggal_akad'], $_POST['jam_akad'], $foto_pria, $foto_wanita, $tanggal_resepsi, $jam_resepsi, $id);
    $stmt->execute();
    header("Location: pengantin.php");
}

if (isset($_GET['hapus'])) {
    csrf_verify();
    $id = (int) $_GET['hapus'];

    $stmt = $weddingku->prepare("DELETE FROM pengantin WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: pengantin.php");
}
?>
