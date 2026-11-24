<?php
include 'auth_check.php';
include 'koneksi.php';

// TAMBAH STORY
if (isset($_POST['simpan'])) {
    csrf_require_post();
    csrf_verify();
    $bulan = trim($_POST['bulan'] ?? '');
    $tahun = (int) ($_POST['tahun'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $pengantin_id = (int) ($_POST['pengantin_id'] ?? 0);

    if ($bulan === '' || $tahun <= 0 || $pengantin_id <= 0) {
        header('Location: story.php?error=' . urlencode("Bulan, tahun, dan pasangan pengantin wajib diisi."));
        exit;
    }

    try {
        $stmt = $weddingku->prepare("INSERT INTO story (bulan, tahun, deskripsi, pengantin_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sisi", $bulan, $tahun, $deskripsi, $pengantin_id);
        $stmt->execute();
        $stmt->close();

        header('Location: story.php?msg=saved');
        exit;
    } catch (Throwable $e) {
        header('Location: story.php?error=' . urlencode("Gagal menyimpan cerita: " . $e->getMessage()));
        exit;
    }
}

// UPDATE STORY
if (isset($_POST['update'])) {
    csrf_require_post();
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    $bulan = trim($_POST['bulan'] ?? '');
    $tahun = (int) ($_POST['tahun'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $pengantin_id = (int) ($_POST['pengantin_id'] ?? 0);

    if ($id <= 0 || $bulan === '' || $tahun <= 0 || $pengantin_id <= 0) {
        header('Location: story.php?error=' . urlencode("Data cerita tidak valid atau belum lengkap."));
        exit;
    }

    try {
        $stmt = $weddingku->prepare("UPDATE story SET bulan=?, tahun=?, deskripsi=?, pengantin_id=? WHERE id=?");
        $stmt->bind_param("sisii", $bulan, $tahun, $deskripsi, $pengantin_id, $id);
        $stmt->execute();
        $stmt->close();

        header('Location: story.php?msg=updated');
        exit;
    } catch (Throwable $e) {
        header('Location: story.php?error=' . urlencode("Gagal memperbarui cerita: " . $e->getMessage()));
        exit;
    }
}

// HAPUS STORY
if (isset($_POST['hapus'])) {
    csrf_require_post();
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        header('Location: story.php?error=' . urlencode("ID cerita tidak valid."));
        exit;
    }

    try {
        $stmt = $weddingku->prepare("DELETE FROM story WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        header('Location: story.php?msg=deleted');
        exit;
    } catch (Throwable $e) {
        header('Location: story.php?error=' . urlencode("Gagal menghapus cerita: " . $e->getMessage()));
        exit;
    }
}

header('Location: story.php');
exit;
