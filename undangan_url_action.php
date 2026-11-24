<?php
include 'auth_check.php';
include 'koneksi.php';

// Fungsi untuk menghasilkan token undangan unik
function generateToken($pengantin_id, $tamu_id) {
    return md5($pengantin_id . '_' . $tamu_id . '_' . uniqid(mt_rand(), true));
}

// Tambah data undangan URL baru
if (isset($_POST['tambah'])) {
    csrf_require_post();
    csrf_verify();
    $pengantin_id = (int) ($_POST['pengantin_id'] ?? 0);

    // Daftar tamu: dari hidden input tamu_ids (koma) atau fallback tamu_id tunggal
    $tamu_ids = [];
    if (!empty($_POST['tamu_ids'])) {
        $tamu_ids = array_filter(array_map('intval', explode(',', $_POST['tamu_ids'])));
    } elseif (!empty($_POST['tamu_id'])) {
        $tamu_ids = [(int) $_POST['tamu_id']];
    }

    if ($pengantin_id <= 0 || empty($tamu_ids)) {
        header("Location: undangan_url.php?error=" . urlencode("Silakan pilih pasangan pengantin dan minimal satu tamu undangan."));
        exit;
    }

    try {
        $stmt_tamu = $weddingku->prepare("SELECT nama FROM tamu WHERE id = ? AND pengantin_id = ?");
        $stmt_insert = $weddingku->prepare("INSERT INTO undangan_url (pengantin_id, tamu_id, encrypted_token, url_undangan) VALUES (?, ?, ?, ?)");

        $count = 0;
        foreach ($tamu_ids as $tamu_id) {
            if ($tamu_id <= 0) continue;

            $stmt_tamu->bind_param("ii", $tamu_id, $pengantin_id);
            $stmt_tamu->execute();
            $tamu = $stmt_tamu->get_result()->fetch_assoc();
            if (!$tamu) continue;
            $token = generateToken($pengantin_id, $tamu_id);
            $url = "index.php?uid=" . rawurlencode($token) . "&guest=" . rawurlencode($tamu['nama']);

            $stmt_insert->bind_param("iiss", $pengantin_id, $tamu_id, $token, $url);
            $stmt_insert->execute();
            $count++;
        }

        header("Location: undangan_url.php?msg=" . urlencode("$count link undangan berhasil dibuat!"));
        exit;
    } catch (Throwable $e) {
        header("Location: undangan_url.php?error=" . urlencode("Gagal membuat link undangan: " . $e->getMessage()));
        exit;
    }
}

// Edit data undangan URL
if (isset($_POST['edit'])) {
    csrf_require_post();
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    $pengantin_id = (int) ($_POST['pengantin_id'] ?? 0);
    $tamu_id = (int) ($_POST['tamu_id'] ?? 0);
    $custom_url = trim($_POST['url_undangan'] ?? '');

    if ($id <= 0 || $pengantin_id <= 0 || $tamu_id <= 0) {
        header("Location: undangan_url.php?error=" . urlencode("Data link undangan tidak lengkap."));
        exit;
    }

    try {
        // Ambil nama tamu
        $stmt_tamu = $weddingku->prepare("SELECT nama FROM tamu WHERE id = ? AND pengantin_id = ?");
        $stmt_tamu->bind_param("ii", $tamu_id, $pengantin_id);
        $stmt_tamu->execute();
        $tamu = $stmt_tamu->get_result()->fetch_assoc();
        if (!$tamu) {
            header("Location: undangan_url.php?error=" . urlencode("Tamu tidak terdaftar pada pasangan pengantin yang dipilih."));
            exit;
        }

        // Pertahankan token lama jika ada, atau buat baru jika belum
        $stmt_check = $weddingku->prepare("SELECT encrypted_token FROM undangan_url WHERE id = ?");
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $cur = $stmt_check->get_result()->fetch_assoc();
        if (!$cur) {
            header("Location: undangan_url.php?error=" . urlencode("Link undangan tidak ditemukan."));
            exit;
        }
        $token = !empty($cur['encrypted_token']) ? $cur['encrypted_token'] : generateToken($pengantin_id, $tamu_id);
        $url = "index.php?uid=" . rawurlencode($token) . "&guest=" . rawurlencode($tamu['nama']);

        // Update database
        $stmt = $weddingku->prepare("UPDATE undangan_url SET pengantin_id=?, tamu_id=?, encrypted_token=?, url_undangan=? WHERE id = ?");
        $stmt->bind_param("iissi", $pengantin_id, $tamu_id, $token, $url, $id);
        $stmt->execute();

        header("Location: undangan_url.php?msg=updated");
        exit;
    } catch (Throwable $e) {
        header("Location: undangan_url.php?error=" . urlencode("Gagal memperbarui link undangan: " . $e->getMessage()));
        exit;
    }
}

// Hapus data undangan URL
if (isset($_POST['hapus'])) {
    csrf_require_post();
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        header("Location: undangan_url.php?error=" . urlencode("ID undangan tidak valid."));
        exit;
    }

    try {
        $stmt = $weddingku->prepare("DELETE FROM undangan_url WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        header("Location: undangan_url.php?msg=deleted");
        exit;
    } catch (Throwable $e) {
        header("Location: undangan_url.php?error=" . urlencode("Gagal menghapus link undangan: " . $e->getMessage()));
        exit;
    }
}

// Proses konfirmasi RSVP
if (isset($_POST['konfirmasi'])) {
    csrf_require_post();
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? ''; // 'hadir' atau 'tidak_hadir'

    if ($id <= 0 || ($status !== 'hadir' && $status !== 'tidak_hadir')) {
        header("Location: undangan_url.php?error=" . urlencode("Status RSVP tidak valid."));
        exit;
    }

    try {
        $stmt = $weddingku->prepare("UPDATE undangan_url SET status_rsvp = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();

        header("Location: undangan_url.php?msg=" . urlencode("Status RSVP berhasil diperbarui!"));
        exit;
    } catch (Throwable $e) {
        header("Location: undangan_url.php?error=" . urlencode("Gagal memperbarui RSVP: " . $e->getMessage()));
        exit;
    }
}

header("Location: undangan_url.php");
exit;
