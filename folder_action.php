<?php 
include 'auth_check.php';
include 'koneksi.php';

// Fungsi untuk membuat slug dari nama folder
function slugify($text) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
}

// Fungsi untuk membuat folder dan menyalin template
function generateFolderForPasangan($pengantin_id) {
    global $weddingku;
    
    // Validasi pengantin_id
    if (!is_numeric($pengantin_id)) {
        echo "<script>alert('ID pengantin tidak valid!'); window.location.href = 'generate_folder.php';</script>";
        return;
    }

    // Ambil data dari DB untuk pengantin
    $stmt = $weddingku->prepare("SELECT * FROM pengantin WHERE id = ?");
    $stmt->bind_param("i", $pengantin_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();
    
    if ($data) {
        // Membuat nama folder berdasarkan nama pengantin
        $folderName = slugify($data['nama_panggilan_pria'] . '-' . $data['nama_panggilan_wanita']);
        $targetDir = __DIR__ . "/undangan/" . $folderName;

        // Cek apakah folder sudah ada
        if (!file_exists($targetDir)) {
            // Membuat folder jika belum ada
            mkdir($targetDir, 0777, true);
            
            // Salin template ke dalam folder baru
            $templateDir = __DIR__ . "/template_undangan";
            if (is_dir($templateDir)) {
                recurse_copy($templateDir, $targetDir); // Menyalin template
            } else {
                echo "<script>alert('Template undangan tidak ditemukan!'); window.location.href = 'generate_folder.php';</script>";
                return;
            }

            // Ekspor data ke file JSON di dalam folder
            file_put_contents($targetDir . "/data_pengantin.json", json_encode($data));
            echo "<script>alert('Folder berhasil dibuat: $folderName'); window.location.href = 'generate_folder.php';</script>";
        } else {
            echo "<script>alert('Folder sudah ada!'); window.location.href = 'generate_folder.php';</script>";
        }
    } else {
        echo "<script>alert('Pengantin dengan ID $pengantin_id tidak ditemukan!'); window.location.href = 'generate_folder.php';</script>";
    }
}

// Fungsi untuk menyalin folder dan isinya secara rekursif
function recurse_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

// Menambah Folder
if (isset($_POST['simpan'])) {
    csrf_require_post();
    csrf_verify();
    $nama_folder = trim($_POST['nama_folder'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $pengantin_id = (int) ($_POST['pengantin_id'] ?? 0);

    // Validasi input
    if ($nama_folder === '' || $pengantin_id <= 0) {
        echo "<script>alert('Nama folder, deskripsi, dan pengantin_id tidak boleh kosong!'); window.location.href = 'generate_folder.php';</script>";
        exit;
    }

    // Gunakan prepared statement untuk mencegah SQL injection
    $stmt = $weddingku->prepare("INSERT INTO folders (nama_folder, deskripsi, pengantin_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $nama_folder, $deskripsi, $pengantin_id); // Bind pengantin_id

    if ($stmt->execute()) {
        echo "<script>alert('Folder berhasil ditambahkan'); window.location.href = 'generate_folder.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan folder'); window.location.href = 'generate_folder.php';</script>";
    }
}

// Mengupdate Folder
if (isset($_POST['update'])) {
    csrf_require_post();
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    $nama_folder = trim($_POST['nama_folder'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $pengantin_id = (int) ($_POST['pengantin_id'] ?? 0);

    // Validasi input
    if ($id <= 0 || $nama_folder === '' || $pengantin_id <= 0) {
        echo "<script>alert('Nama folder dan deskripsi tidak boleh kosong!'); window.location.href = 'generate_folder.php';</script>";
        exit;
    }

    // Update data folder dengan prepared statement
    $stmt = $weddingku->prepare("UPDATE folders SET nama_folder = ?, deskripsi = ?, pengantin_id = ? WHERE id = ?");
    $stmt->bind_param("ssii", $nama_folder, $deskripsi, $pengantin_id, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Folder berhasil diperbarui'); window.location.href = 'generate_folder.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui folder'); window.location.href = 'generate_folder.php';</script>";
    }
}

// Menghapus Folder
if (isset($_POST['hapus'])) {
    csrf_require_post();
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);

    // Hapus data folder
    if ($id <= 0) { header('Location: generate_folder.php'); exit; }
    $stmt = $weddingku->prepare("DELETE FROM folders WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Folder berhasil dihapus'); window.location.href = 'generate_folder.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus folder'); window.location.href = 'generate_folder.php';</script>";
    }
}

// Menambahkan folder untuk pasangan (generate folder untuk pengantin)
if (isset($_POST['generate_folder'])) {
    csrf_require_post();
    csrf_verify();
    $pengantin_id = (int) ($_POST['pengantin_id'] ?? 0);
    generateFolderForPasangan($pengantin_id);
}
?>
