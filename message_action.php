<?php
include 'auth_check.php';
include 'koneksi.php';

// TAMBAH PESAN
if (isset($_POST['simpan'])) {
    csrf_require_post();
    csrf_verify();
    $nama = trim($_POST['nama_pengirim'] ?? '');
    $pesan = trim($_POST['isi_pesan'] ?? '');
    $pengantin_id = (int) ($_POST['pengantin_id'] ?? 0);

    if ($nama === '' || $pesan === '' || $pengantin_id <= 0) {
        header('Location: message.php?error=' . urlencode("Nama pengirim, ucapan, dan pasangan pengantin wajib diisi."));
        exit;
    }

    try {
        $stmt = $weddingku->prepare("INSERT INTO message (nama, pesan, pengantin_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $nama, $pesan, $pengantin_id);
        $stmt->execute();
        $stmt->close();

        header('Location: message.php?msg=saved');
        exit;
    } catch (Throwable $e) {
        header('Location: message.php?error=' . urlencode("Gagal menyimpan pesan: " . $e->getMessage()));
        exit;
    }
}

// UPDATE PESAN
if (isset($_POST['update'])) {
    csrf_require_post();
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    $nama = trim($_POST['nama_pengirim'] ?? '');
    $pesan = trim($_POST['isi_pesan'] ?? '');
    $pengantin_id = isset($_POST['pengantin_id']) ? (int) $_POST['pengantin_id'] : 0;

    if ($id <= 0 || $nama === '' || $pesan === '') {
        header('Location: message.php?error=' . urlencode("Data pesan tidak valid atau belum lengkap."));
        exit;
    }

    try {
        if ($pengantin_id > 0) {
            $stmt = $weddingku->prepare("UPDATE message SET nama=?, pesan=?, pengantin_id=? WHERE id=?");
            $stmt->bind_param("ssii", $nama, $pesan, $pengantin_id, $id);
        } else {
            $stmt = $weddingku->prepare("UPDATE message SET nama=?, pesan=? WHERE id=?");
            $stmt->bind_param("ssi", $nama, $pesan, $id);
        }

        $stmt->execute();
        $stmt->close();

        header('Location: message.php?msg=updated');
        exit;
    } catch (Throwable $e) {
        header('Location: message.php?error=' . urlencode("Gagal memperbarui pesan: " . $e->getMessage()));
        exit;
    }
}

// HAPUS PESAN
if (isset($_POST['hapus'])) {
    csrf_require_post();
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        header('Location: message.php?error=' . urlencode("ID pesan tidak valid."));
        exit;
    }

    try {
        $stmt = $weddingku->prepare("DELETE FROM message WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        header('Location: message.php?msg=deleted');
        exit;
    } catch (Throwable $e) {
        header('Location: message.php?error=' . urlencode("Gagal menghapus pesan: " . $e->getMessage()));
        exit;
    }
}

header('Location: message.php');
exit;
