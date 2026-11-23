<?php
include 'auth_check.php';
include 'koneksi.php';

// SIMPAN TAMU
if (isset($_POST['simpan'])) {
    csrf_verify();
    $nama = trim($_POST['nama'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $pengantin_id = (int) ($_POST['pengantin_id'] ?? 0);

    if ($nama === '' || $pengantin_id <= 0) {
        header("Location: tamu.php?error=" . urlencode("Nama tamu dan pasangan pengantin wajib diisi."));
        exit;
    }

    try {
        $stmt = $weddingku->prepare("INSERT INTO tamu (nama, alamat, pengantin_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $nama, $alamat, $pengantin_id);
        $stmt->execute();

        header('Location: tamu.php?msg=saved');
        exit;
    } catch (Throwable $e) {
        header('Location: tamu.php?error=' . urlencode("Gagal menyimpan tamu: " . $e->getMessage()));
        exit;
    }
}

// UPDATE TAMU
if (isset($_POST['update'])) {
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $pengantin_id = (int) ($_POST['pengantin_id'] ?? 0);

    if ($id <= 0 || $nama === '' || $pengantin_id <= 0) {
        header("Location: tamu.php?error=" . urlencode("Data tamu tidak valid atau belum lengkap."));
        exit;
    }

    try {
        $stmt = $weddingku->prepare("UPDATE tamu SET nama=?, alamat=?, pengantin_id=? WHERE id=?");
        $stmt->bind_param("ssii", $nama, $alamat, $pengantin_id, $id);
        $stmt->execute();

        header('Location: tamu.php?msg=updated');
        exit;
    } catch (Throwable $e) {
        header('Location: tamu.php?error=' . urlencode("Gagal memperbarui tamu: " . $e->getMessage()));
        exit;
    }
}

// HAPUS TAMU
if (isset($_GET['hapus'])) {
    csrf_verify();
    $id = (int) ($_GET['hapus'] ?? 0);

    if ($id <= 0) {
        header('Location: tamu.php?error=' . urlencode("ID tamu tidak valid."));
        exit;
    }

    try {
        // Hapus link undangan terkait tamu ini
        $weddingku->query("DELETE FROM undangan_url WHERE tamu_id = $id");

        $stmt = $weddingku->prepare("DELETE FROM tamu WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        header('Location: tamu.php?msg=deleted');
        exit;
    } catch (Throwable $e) {
        header('Location: tamu.php?error=' . urlencode("Gagal menghapus tamu: " . $e->getMessage()));
        exit;
    }
}

header('Location: tamu.php');
exit;
