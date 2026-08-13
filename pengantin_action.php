<?php
include 'auth_check.php';
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    // Menangani upload foto
    $foto_pria = $_FILES['foto_pria']['name'];
    $foto_wanita = $_FILES['foto_wanita']['name'];
    $foto_pria_tmp = $_FILES['foto_pria']['tmp_name'];
    $foto_wanita_tmp = $_FILES['foto_wanita']['tmp_name'];
    
    // Menentukan folder tujuan untuk upload foto
    $upload_dir = 'uploads/';
    move_uploaded_file($foto_pria_tmp, $upload_dir.$foto_pria);
    move_uploaded_file($foto_wanita_tmp, $upload_dir.$foto_wanita);

    // Periksa jika tanggal_resepsi kosong, set ke NULL jika kosong
    $tanggal_resepsi = !empty($_POST['tanggal_resepsi']) ? $_POST['tanggal_resepsi'] : NULL;
    $jam_resepsi = !empty($_POST['jam_resepsi']) ? $_POST['jam_resepsi'] : NULL;

    // Query untuk menyimpan data pengantin
    $stmt = $weddingku->prepare("INSERT INTO pengantin (nama_pria, nama_panggilan_pria, nama_wanita, nama_panggilan_wanita, alamat_wanita, ortu_pria, ortu_wanita, tanggal_akad, jam_akad, foto_pria, foto_wanita, tanggal_resepsi, jam_resepsi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss", $_POST['nama_pria'], $_POST['nama_panggilan_pria'], $_POST['nama_wanita'], $_POST['nama_panggilan_wanita'], $_POST['alamat_wanita'], $_POST['ortu_pria'], $_POST['ortu_wanita'], $_POST['tanggal_akad'], $_POST['jam_akad'], $foto_pria, $foto_wanita, $tanggal_resepsi, $jam_resepsi);
    $stmt->execute();
    header("Location: pengantin.php");
}

if (isset($_POST['update'])) {
    // Menangani upload foto
    if ($_FILES['foto_pria']['name'] != "") {
        $foto_pria = $_FILES['foto_pria']['name'];
        move_uploaded_file($_FILES['foto_pria']['tmp_name'], 'uploads/' . $foto_pria);
    } else {
        $foto_pria = $_POST['foto_pria_lama'];
    }

    if ($_FILES['foto_wanita']['name'] != "") {
        $foto_wanita = $_FILES['foto_wanita']['name'];
        move_uploaded_file($_FILES['foto_wanita']['tmp_name'], 'uploads/' . $foto_wanita);
    } else {
        $foto_wanita = $_POST['foto_wanita_lama'];
    }

    // Periksa jika tanggal_resepsi kosong, set ke NULL jika kosong
    $tanggal_resepsi = !empty($_POST['tanggal_resepsi']) ? $_POST['tanggal_resepsi'] : NULL;
    $jam_resepsi = !empty($_POST['jam_resepsi']) ? $_POST['jam_resepsi'] : NULL;

    // Query untuk update data pengantin
    $stmt = $weddingku->prepare("UPDATE pengantin SET nama_pria=?, nama_panggilan_pria=?, nama_wanita=?, nama_panggilan_wanita=?, alamat_wanita=?, ortu_pria=?, ortu_wanita=?, tanggal_akad=?, jam_akad=?, foto_pria=?, foto_wanita=?, tanggal_resepsi=?, jam_resepsi=? WHERE id=?");
    $stmt->bind_param("sssssssssssssi", $_POST['nama_pria'], $_POST['nama_panggilan_pria'], $_POST['nama_wanita'], $_POST['nama_panggilan_wanita'], $_POST['alamat_wanita'], $_POST['ortu_pria'], $_POST['ortu_wanita'], $_POST['tanggal_akad'], $_POST['jam_akad'], $foto_pria, $foto_wanita, $tanggal_resepsi, $jam_resepsi, $_POST['id']);
    $stmt->execute();
    header("Location: pengantin.php");
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $weddingku->query("DELETE FROM pengantin WHERE id=$id");
    header("Location: pengantin.php");
}
?>
