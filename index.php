<?php
include 'koneksi.php';

$token = $_GET['uid'] ?? null;
if (!$token) {
    die("Token undangan tidak ditemukan.");
}

// Ambil data undangan beserta tamu
$sql = "SELECT 
            u.id AS undangan_url_id,
            u.tamu_id,
            tu.nama AS nama_tamu,
            p.nama_pria,
            p.nama_wanita,
            p.ortu_pria,
            p.ortu_wanita,
            p.nama_panggilan_pria,
            p.nama_panggilan_wanita,
            p.tanggal_akad,
            p.jam_akad,
            p.tanggal_resepsi,
            p.jam_resepsi,
            p.alamat_wanita,
            p.foto_pria,
            p.foto_wanita,
            s.deskripsi AS cerita
        FROM undangan_url u
        JOIN tamu tu ON tu.id = u.tamu_id
        JOIN pengantin p ON p.id = u.pengantin_id
        LEFT JOIN story s ON s.pengantin_id = p.id
        WHERE u.encrypted_token = ?";

$stmt = $weddingku->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();

if ($stmt->error) {
    die("Error in query: " . $stmt->error);
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Undangan tidak ditemukan.");
}

$data = $result->fetch_assoc();

// ✅ Log Akses Undangan
$undangan_url_id = $data['undangan_url_id'];
$tamu_id = $data['tamu_id'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$ip_address = $_SERVER['REMOTE_ADDR'];

$log_stmt = $weddingku->prepare("INSERT INTO log_akses_undangan (undangan_url_id, tamu_id, user_agent, ip_address) VALUES (?, ?, ?, ?)");
$log_stmt->bind_param("iiss", $undangan_url_id, $tamu_id, $user_agent, $ip_address);
$log_stmt->execute();

// ✅ Update status_rsvp jadi 'hadir' jika status masih 'belum'
$cek_status_stmt = $weddingku->prepare("SELECT status_rsvp FROM undangan_url WHERE id = ?");
$cek_status_stmt->bind_param("i", $undangan_url_id);
$cek_status_stmt->execute();
$cek_status_result = $cek_status_stmt->get_result();
$status_row = $cek_status_result->fetch_assoc();

if ($status_row && $status_row['status_rsvp'] === 'belum') {
    $update_stmt = $weddingku->prepare("UPDATE undangan_url SET status_rsvp = 'hadir' WHERE id = ?");
    $update_stmt->bind_param("i", $undangan_url_id);
    $update_stmt->execute();
}


// Ambil data ucapan dari story (jika lebih dari satu)
$sql_messages = "SELECT bulan, tahun, deskripsi FROM story WHERE pengantin_id = (
    SELECT pengantin_id FROM undangan_url WHERE encrypted_token = ?
)";
$stmt_msg = $weddingku->prepare($sql_messages);
$stmt_msg->bind_param("s", $token);
$stmt_msg->execute();
$result_msg = $stmt_msg->get_result();
$messages = $result_msg->fetch_all(MYSQLI_ASSOC);

// Ambil data rekening bank dan catatan
$sql_gift = "SELECT bank_id, nomor_rekening, catatan FROM wedding_gift WHERE pengantin_id = (
    SELECT pengantin_id FROM undangan_url WHERE encrypted_token = ?
)";
$stmt_gift = $weddingku->prepare($sql_gift);
$stmt_gift->bind_param("s", $token);
$stmt_gift->execute();
$result_gift = $stmt_gift->get_result();

$rekeningData = [];
while ($gift = $result_gift->fetch_assoc()) {
    $sql_bank = "SELECT nama_bank FROM bank_list WHERE id = ?";
    $stmt_bank = $weddingku->prepare($sql_bank);
    $stmt_bank->bind_param("i", $gift['bank_id']);
    $stmt_bank->execute();
    $result_bank = $stmt_bank->get_result();
    $bank = $result_bank->fetch_assoc();

    $rekeningData[] = [
        'bank' => $bank['nama_bank'],
        'nomor_rekening' => $gift['nomor_rekening'],
        'catatan' => $gift['catatan']
    ];
}

// Fungsi bantu format tanggal
function formatTanggalIndo($date) {
    $hari = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
    $bulan = ['January'=>'Januari','February'=>'Februari','March'=>'Maret','April'=>'April','May'=>'Mei','June'=>'Juni','July'=>'Juli','August'=>'Agustus','September'=>'September','October'=>'Oktober','November'=>'November','December'=>'Desember'];

    $dt = new DateTime($date);
    $hariIni = $hari[$dt->format('l')];
    $bulanIni = $bulan[$dt->format('F')];

    return $hariIni . ', ' . $dt->format('d') . ' ' . $bulanIni . ' ' . $dt->format('Y');
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Undangan Pernikahan</title>
  <!-- Favicon -->
  <link rel="icon" href="images/amirulprojectsolutions.ico" type="image/x-icon">
  <!-- Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CDN -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <!-- Lightbox2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/css/lightbox.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffb6c1" />

  </head>
<body>

<!-- Toggle Button -->
<button id="toggleSidebar" class="sidebar-toggle">
  ☰ Menu Wedding Invitation
</button>

<!-- Sidebar Kanan -->
<div class="slide-sidebar" id="slideSidebar">
  <h4>Main Navigator</h4>
  <ul>
    <li><a href="#slide-1">Slide 1: Pembuka</a></li>
    <li><a href="#slide-2">Slide 2: Save the Date</a></li>
    <li><a href="#slide-3">Slide 3: Akad & Resepsi</a></li>
    <li><a href="#slide-4">Slide 4: Our Story</a></li>
    <li><a href="#slide-5">Slide 5: Gallery Foto</a></li>
    <li><a href="#slide-6">Slide 6: Wedding Gift</a></li>
    <li><a href="#slide-7">Slide 7: Ucapan & Do'a</a></li>
  </ul>
</div>

<!-- Slide 1 -->
<section class="slide slide-1" id="slide-1">
  <h1><?= htmlspecialchars($data['nama_panggilan_pria']) ?> & <?= htmlspecialchars($data['nama_panggilan_wanita']) ?></h1>
  <p>Kepada Bapak/Ibu/Saudara/i</p>
  <h3 class="tamu-nama"><?= htmlspecialchars($data['nama_tamu']) ?></h3>
  <button onclick="openInvitation()" class="btn-buka">
    <i class="fas fa-envelope-open-text me-1"></i> Open Invitation
  </button>
</section>


<audio id="audioBackground" src="audio/Kahitna-Menikahimu.mp3" type="audio/mpeg" hidden loop></audio>

<!-- Slide 2: Save The Date -->
<div class="slide" id="slide-2" style="background-image: url('images/bg-miror.jpg'); display: none;">
  <div class="overlay"></div>
  <div class="container text-center d-flex justify-content-center align-items-center flex-column" style="min-height: 100vh; z-index: 1; position: relative;">
    <h2 class="text-white">Save The Date</h2>
    <p class="tanggal text-white"><?= formatTanggalIndo($data['tanggal_akad']) ?></p>
    
    <div id="countdown" class="d-flex justify-content-center gap-3 flex-wrap text-white mt-4">
      <div class="time-box" id="days-box">
        <span id="days">0</span>
        <p>Hari</p>
      </div>
      <div class="time-box" id="hours-box">
        <span id="hours">0</span>
        <p>Jam</p>
      </div>
      <div class="time-box" id="minutes-box">
        <span id="minutes">0</span>
        <p>Menit</p>
      </div>
      <div class="time-box" id="seconds-box">
        <span id="seconds">0</span>
        <p>Detik</p>
      </div>
    </div>

    <!-- <div class="btn-group mt-5">
      <a onclick="prevSlide()" class="btn btn-light">⬅️ Kembali</a> &emsp;
      <button onclick="nextSlide()" class="btn btn-primary">Lanjut ➡️</button>
    </div> -->
  </div>
</div>

<!-- Slide 3: Akad & Resepsi -->
<div class="slide" id="slide-3" style="background-image: url('images/bg-miror.jpg'); display: none;">
    <div class="overlay"></div>
    <br><br>
    <!-- Foto Pengantin dan Informasi -->
    <div class="d-flex flex-column justify-content-center align-items-center gap-4 mb-4">
        <!-- Foto Pengantin Pria dan Wanita -->
        <div class="d-flex justify-content-center gap-5 flex-wrap">
            <div class="text-center">
                <div class="photo-frame">
                    <img src="uploads/<?= htmlspecialchars($data['foto_pria']) ?>" alt="Pengantin Pria" class="rounded-circle" width="150" height="150">
                </div>
                <p class="mt-2 text-white"><?= htmlspecialchars($data['nama_panggilan_pria']) ?></p>
                <p class="text-white">Orang Tua: <br><?= htmlspecialchars($data['ortu_pria']) ?></p>
            </div>
            <div class="text-center">
                <div class="photo-frame">
                    <img src="uploads/<?= htmlspecialchars($data['foto_wanita']) ?>" alt="Pengantin Wanita" class="rounded-circle" width="150" height="150">
                </div>
                <p class="mt-2 text-white"><?= htmlspecialchars($data['nama_panggilan_wanita']) ?></p>
                <p class="text-white">Orang Tua: <br><?= htmlspecialchars($data['ortu_wanita']) ?></p>
            </div>
        </div>

        <!-- Akad Nikah -->
        <h2 class="text-center text-white">Akad Nikah</h2>
        <p class="text-center text-white">
            <?php 
                if ($data['tanggal_akad'] && $data['jam_akad']) {
                    echo formatTanggalIndo($data['tanggal_akad']) . "<br><br>"; // Tanggal dengan spasi
                    echo "Pukul " . htmlspecialchars($data['jam_akad']) . " WIB"; // Jam di bawah tanggal
                } else {
                    echo "Tanggal atau Jam Akad tidak valid.";
                }
            ?>
        </p>

        <!-- Resepsi -->
        <h2 class="text-center text-white">Resepsi</h2>
        <p class="text-center text-white">
            <?php 
                if ($data['tanggal_resepsi'] && $data['jam_resepsi']) {
                    echo formatTanggalIndo($data['tanggal_resepsi']) . "<br><br>"; // Tanggal dengan spasi
                    echo "Pukul " . htmlspecialchars($data['jam_resepsi']) . " WIB"; // Jam di bawah tanggal
                } else {
                    echo "Tanggal atau Jam Resepsi tidak valid.";
                }
            ?>
        </p>

        <!-- Lokasi -->
        <div class="mt-4 text-center">
            <a href="https://www.google.com/maps/search/<?= urlencode($data['alamat_wanita']) ?>" target="_blank" class="btn btn-light mt-3">📍 Cek Lokasi</a>
            <br><br>
            <!-- QR Code untuk Lokasi -->
            <div class="qr-code">
                <img src="https://api.qrserver.com/v1/create-qr-code/?data=https://www.google.com/maps/search/<?= urlencode($data['alamat_wanita']) ?>&size=150x150" alt="QR Code Lokasi" class="img-thumbnail">
            </div>
        </div>
        <!-- <a href="rsvp_handler.php?uid=<?= $token ?>" onclick="return confirm('Yakin tidak bisa hadir? 😢')" class="btn btn-danger mt-4">Saya Tidak Bisa Hadir</a> -->
        <a href="#" id="rsvpLink" class="btn btn-danger mt-4">Saya Tidak Bisa Hadir</a>
    </div>
</div>

<script>
  document.getElementById('rsvpLink').addEventListener('click', function(e) {
      e.preventDefault(); // Cegah link untuk redirect langsung

      // Menampilkan SweetAlert konfirmasi
      Swal.fire({
          title: 'Yakin tidak bisa hadir? 😢',
          text: "Kamu tidak bisa menghadiri pernikahan ini, apakah kamu yakin?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Tidak Bisa Hadir',
          cancelButtonText: 'Batal'
      }).then((result) => {
          if (result.isConfirmed) {
              // Jika pengguna klik "Tidak Bisa Hadir", redirect ke rsvp_handler.php dengan token
              const token = "<?= $token ?>"; // Ambil token PHP
              window.location.href = `rsvp_handler.php?uid=${token}`; // Redirect
          }
      });
  });
</script>
        <!-- Tombol Navigasi -->
        <!-- <div class="btn-group mt-4">
            <a onclick="prevSlide()" class="btn btn-light">⬅️ Kembali</a> &emsp;
            <button onclick="nextSlide()" class="btn btn-primary">Lanjut ➡️</button>
        </div> -->

<!-- Slide 4: Our Story -->
<div class="slide" id="slide-4" style="background-image: url('images/bg-miror.jpg'); display: none;">
  <div class="overlay"></div>
  <div class="container d-flex flex-column justify-content-center align-items-center text-center text-white" style="min-height: 100vh; position: relative; z-index: 1;">
    
    <h2 class="mb-4">Our Story</h2>

    <!-- Carousel Wrapper -->
    <div id="story-carousel" class="story-carousel w-100" style="max-width: 600px;">
      <?php 
      if (!empty($messages)) {
        foreach ($messages as $message) {
          $bulan = isset($message['bulan']) ? $message['bulan'] : 'Unknown';
          $tahun = isset($message['tahun']) ? $message['tahun'] : 'Unknown';
          $deskripsi = isset($message['deskripsi']) && $message['deskripsi'] !== null 
                      ? htmlspecialchars($message['deskripsi'], ENT_QUOTES, 'UTF-8') 
                      : 'Deskripsi tidak tersedia';

          echo '<div class="story-item" style="display: block;">';
          echo '<h5>📅 ' . formatTanggalIndo($bulan . ' ' . $tahun) . '</h5>';
          echo '<p>' . $deskripsi . '</p>';
          echo '</div>';
        }
      }
      ?>
    </div>

    <!-- Carousel Controls -->
    <div class="mt-4 d-flex gap-3 justify-content-center flex-wrap">
      <button onclick="nextStory()" class="btn btn-outline-light">📖 Cerita Selanjutnya</button>
    </div>

    <!-- Navigasi Slide -->
    <!-- <div class="btn-group mt-5">
      <button onclick="prevSlide()" class="btn btn-light">⬅️ Kembali</button> &emsp;
      <button onclick="nextSlide()" class="btn btn-primary">Lanjut ➡️</button>
    </div> -->
  </div>
</div>

<!-- Slide 5: Gallery Pengantin -->
<div class="slide" id="slide-5" style="background-image: url('images/bg-miror.jpg'); display: none;">
  <div class="overlay"></div>
  <div class="container text-center text-white">
    <h2 class="mb-4">Gallery</h2>

    <div class="row justify-content-center">
    <?php
      include 'koneksi.php';

      // Pastikan $data sudah ada dan memuat id pengantin
      if (!isset($data['id_pengantin'])) {
          echo "<p>Galeri tidak tersedia.</p>";
      } else {
          $pengantin_id = (int) $data['id_pengantin']; // Cast ke int untuk keamanan
          $query = "SELECT * FROM gallery WHERE pengantin_id = $pengantin_id ORDER BY tanggal_upload DESC";
          $result = mysqli_query($conn, $query);
          if ($result && mysqli_num_rows($result) > 0) {
              while ($foto = mysqli_fetch_assoc($result)) {
                  echo '<div class="col-6 col-md-4 mb-4">';
                  echo '  <div class="gallery-item">';
                  // Tambahkan data-lightbox dan data-title untuk lightbox
                  echo '   <a href="uploads/gallery/' . htmlspecialchars($foto['file']) . '" data-lightbox="gallery" data-title="' . htmlspecialchars($foto['judul']) . '">';
                  echo '      <img src="uploads/gallery/' . htmlspecialchars($foto['file']) . '" alt="' . htmlspecialchars($foto['judul']) . '" class="img-fluid rounded shadow">';
                  echo '   </a>';
                  echo '   <p class="mt-2">' . htmlspecialchars($foto['judul']) . '</p>';
                  echo '  </div>';
                  echo '</div>';
              }
          } else {
              echo "<p>Belum ada foto di galeri.</p>";
          }
      }
    ?>
    </div>

    <!-- Navigasi Slide -->
    <!-- <div class="btn-group mt-4">
      <button onclick="prevSlide()" class="btn btn-light">⬅️ Kembali</button> &emsp;
      <button onclick="nextSlide()" class="btn btn-primary">Lanjut ➡️</button>
    </div> -->
  </div>
</div>

<!-- Slide 6: Gift -->
<div class="slide" id="slide-6" style="display: none; background-image: url('images/bg-miror.jpg');">
    <div class="overlay"></div>
    <div class="content-wrapper text-white text-center">
        <section class="gift p-4">
            <h2>🎁 Wedding Gift</h2>
            <p>Doa dan restu Anda merupakan hadiah terbaik bagi kami. Namun jika ingin memberikan hadiah, berikut adalah informasi rekening:</p>
              <div class="bank-info mt-3">
                  <?php foreach ($rekeningData as $index => $rekening): ?>
                      <div class="rekening-box mb-4">
                          <p><strong><?= $rekening['bank'] ?></strong></p>
                          <div class="d-flex justify-content-center align-items-center gap-3"> <!-- Flexbox untuk tengah -->
                              <p id="rekening<?= $index ?>" class="fs-4"><?= $rekening['nomor_rekening'] ?></p>
                              <button onclick="copyToClipboard('rekening<?= $index ?>')" class="btn btn-light">
                                  📋 Salin
                              </button>
                          </div>
                          <br><br>
                          <small>Atas Nama: <br>(<b><?= htmlspecialchars($rekening['catatan']) ?></b>)</small>
                      </div>
                  <?php endforeach; ?>
              </div>
        </section>
    </div>
</div>

<script>
function copyToClipboard(elementId) {
    const text = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(text).then(function() {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Nomor rekening berhasil disalin ke clipboard.',
            timer: 2000,
            showConfirmButton: false
        });
    }, function(err) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Gagal menyalin nomor rekening.',
        });
    });
}
</script>




    <!-- <div class="btn-group d-flex justify-content-center mt-4 gap-3 flex-wrap">
        <a onclick="prevSlide()" class="btn btn-light">⬅️ Kembali</a>
        <button onclick="nextSlide()" class="btn btn-primary">Lanjut ➡️</button>
    </div> -->

    <!-- Slide 7: Ucapan dan Doa -->
    <div class="slide" id="slide-7" style="display: none; background-image: url('images/bg-miror.jpg');">
      <div class="overlay"></div>
      <div class="content-wrapper text-white text-center p-4">
        <h2>💌 Ucapan & Doa</h2>

        <!-- Form Ucapan -->
        <div class="message-form mt-3">
          <form id="ucapanForm" style="width: 100%; max-width: 700px;">
            <fieldset class="border rounded p-4">
              <legend>Berikan Ucapan & Doa</legend>

              <div class="form-group mb-3 text-start">
                <label for="namaUcapan">Nama Anda</label>
                <input type="text" id="namaUcapan" class="form-control" placeholder="Nama Anda" required />
              </div>

              <div class="form-group mb-3 text-start">
                <label for="isiUcapan">Tulis ucapan & doa</label>
                <textarea id="isiUcapan" class="form-control" placeholder="Tulis ucapan & doa..." required></textarea>
              </div>

              <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane ms-1"></i> Kirim</button>
            </fieldset>
          </form>
        </div>

        <!-- History Ucapan -->
        <div class="ucapan-history mt-5 text-start" style="max-width: 700px; margin: auto;">
          <h4>📜 Ucapan Terkini:</h4>
          <ul id="listUcapan" class="list-unstyled mt-3" style="max-height: 200px; overflow-y: auto;">
            <!-- List ucapan akan ditambahkan oleh JavaScript -->
          </ul>
        </div>
      </div>
    </div>

    <!-- Navigasi -->
    <!-- <div class="btn-group d-flex justify-content-center mt-4 gap-3 flex-wrap">
      <button onclick="prevSlide()" class="btn btn-light">⬅️ Kembali</button>
    </div> -->

<footer id="footerInfo" style="text-align:center; padding: 15px; font-size: 14px;">
    2025 - Wedding Invitation - <?= htmlspecialchars($data['nama_pria']) ?> & <?= htmlspecialchars($data['nama_wanita']) ?> - Lokasi: <span id="lokasiDevice">mendeteksi...</span>
</footer>


<?php
// Ambil data dari database
$sql = "SELECT 
            tanggal_akad, 
            jam_akad 
        FROM weddingku_db.pengantin 
        WHERE id = 1"; // Ganti dengan ID pengantin yang sesuai
$result = $weddingku->query($sql);
$data = $result->fetch_assoc();

// Gabungkan tanggal dan jam akad untuk membuat waktu lengkap
$akadDateTime = $data['tanggal_akad'] . 'T' . $data['jam_akad']; // Format 'Y-m-d\TH:i:s'

?>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Lightbox2 JS -->
<script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/js/lightbox.min.js"></script>

<script>
    let startX = 0;
    let isSwiping = false;

    document.addEventListener("touchstart", function (e) {
        const touchStart = e.touches[0];
        startX = touchStart.clientX;
        isSwiping = true;
    });

    document.addEventListener("touchmove", function (e) {
        if (!isSwiping) return;

        const touchMove = e.touches[0];
        const moveX = touchMove.clientX;
        const diff = startX - moveX;

        if (Math.abs(diff) > 50) {  // 50px threshold for swipe
            if (diff > 0) {
                // Swipe ke kanan (Next)
                nextSlide();
            } else {
                // Swipe ke kiri (Prev)
                prevSlide();
            }
            isSwiping = false;
        }
    });

    document.addEventListener("touchend", function () {
        isSwiping = false;
    });

    document.getElementById('ucapanForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const nama = document.getElementById('namaUcapan').value.trim();
        const isi = document.getElementById('isiUcapan').value.trim();

        // Mengambil parameter 'uid' dan 'guest' dari URL saat ini
        const urlParams = new URLSearchParams(window.location.search);
        const uid = urlParams.get('uid');  // Ambil nilai 'uid' dari URL
        const guest = urlParams.get('guest');  // Ambil nilai 'guest' dari URL

        if (nama && isi && uid && guest) {  // Pastikan semua data ada
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "insert_ucapan.php?uid=" + encodeURIComponent(uid) + "&guest=" + encodeURIComponent(guest), true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            const data = `namaUcapan=${encodeURIComponent(nama)}&isiUcapan=${encodeURIComponent(isi)}`;

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = xhr.responseText.trim();
                    if (response === 'success') {
                        // Menampilkan ucapan baru di halaman
                        const li = document.createElement('li');
                        li.innerHTML = `<strong>${nama}</strong>: ${isi}`;
                        document.getElementById('listUcapan').prepend(li);

                        // Mengosongkan form input setelah ucapan berhasil ditambahkan
                        document.getElementById('namaUcapan').value = '';
                        document.getElementById('isiUcapan').value = '';
                    } else {
                        alert("Gagal mengirim ucapan. Coba lagi.");
                    }
                } else {
                    alert("Terjadi kesalahan. Silakan coba lagi.");
                }
            };

            // Mengirim data ke server
            xhr.send(data);
        } else {
            alert("Harap lengkapi semua data!");
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
  // Ambil parameter uid dari URL
  const urlParams = new URLSearchParams(window.location.search);
  const uid = urlParams.get("uid");

  // Pastikan UID tersedia
  if (uid) {
    fetch("get_ucapan.php?uid=" + uid)
      .then((response) => response.json())
      .then((data) => {
        const list = document.getElementById("listUcapan");

        if (data.length === 0) {
          list.innerHTML = "<li class='mb-2'>Belum ada ucapan 😇</li>";
          return;
        }

        // Loop dan tampilkan pesan
        data.forEach((item) => {
          const li = document.createElement("li");
          li.className = "mb-3 p-2 bg-white text-dark rounded shadow-sm";
          li.innerHTML = `<strong>${item.nama}:</strong> <br> ${item.pesan}`;
          list.appendChild(li);
        });
      })
      .catch((error) => {
        console.error("Gagal mengambil ucapan:", error);
      });
  }
});

  let currentSlide = 1;
  let storyIndex = 0;

  function openInvitation() {
    // Menampilkan slide berikutnya (misalnya ke slide 2)
    document.getElementById('slide-1').style.display = 'none';
    document.getElementById('slide-2').style.display = 'block';

    // Play audio
    var audio = document.getElementById('audioBackground');
    audio.play().catch(function(error) {
      console.log("Autoplay mungkin diblokir browser: ", error);
    });
  }

  function nextSlide() {
    const current = document.getElementById(`slide-${currentSlide}`);
    const next = document.getElementById(`slide-${currentSlide + 1}`);

    if (next) {
      current.style.display = 'none';
      next.style.display = (currentSlide + 1 === 2) ? 'flex' : 'block'; // slide-2 uses flex
      currentSlide++;
    }

    // Reset "Our Story" carousel when entering slide-4
    if (currentSlide === 4 && storyIndex > 0) {
      const stories = document.querySelectorAll('.story-item');
      stories.forEach(story => story.style.display = 'none');
      stories[0].style.display = 'block';
      storyIndex = 0;
    }
  }

  function prevSlide() {
    const current = document.getElementById(`slide-${currentSlide}`);
    const prev = document.getElementById(`slide-${currentSlide - 1}`);

    if (prev) {
      current.style.display = 'none';
      prev.style.display = (currentSlide - 1 === 2) ? 'flex' : 'block'; // slide-2 uses flex
      currentSlide--;
    }
  }

  // "Cerita Selanjutnya" pada slide-4
  function nextStory() {
    const stories = document.querySelectorAll('.story-item');
    stories[storyIndex].style.display = 'none';

    storyIndex = (storyIndex + 1) % stories.length;
    stories[storyIndex].style.display = 'block';
  }

// Mengambil waktu akad yang sudah digabungkan dari PHP
const akadDateTime = new Date('<?= $akadDateTime ?>'); // PHP menghasilkan format yang bisa dipakai JavaScript

// Cek apakah waktu akad valid
if (isNaN(akadDateTime.getTime())) {
    console.error("Waktu akad tidak valid");
} else {
    console.log("Waktu akad yang valid: ", akadDateTime);
}

  // Fungsi untuk memperbarui countdown
  function updateCountdown() {
      const now = new Date();
      const timeLeft = akadDateTime - now;

      // Cek apakah waktu akad sudah lewat
      if (timeLeft <= 0) {
          document.getElementById('countdown').innerHTML = "Waktu Akad telah tiba!";
          clearInterval(countdownInterval); // Hentikan interval jika waktu sudah habis
          return;
      }

      // Hitung waktu tersisa dalam hari, jam, menit, dan detik
      const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
      const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

      // Update elemen dengan ID sesuai unit waktu
      document.getElementById('days').innerHTML = days;
      document.getElementById('hours').innerHTML = hours;
      document.getElementById('minutes').innerHTML = minutes;
      document.getElementById('seconds').innerHTML = seconds;
  }

  // Perbarui countdown setiap detik
  const countdownInterval = setInterval(updateCountdown, 1000);

    function centerSlides() {
    const slides = document.querySelectorAll('.slide');
    slides.forEach(slide => {
      slide.style.display = 'flex';
      slide.style.flexDirection = 'column';
      slide.style.justifyContent = 'center';
      slide.style.alignItems = 'center';
    });
  }

  window.addEventListener('load', centerSlides);
  window.addEventListener('resize', centerSlides);

  const toggleBtn = document.getElementById("toggleSidebar");
  const sidebar = document.getElementById("slideSidebar");

  toggleBtn.addEventListener("click", function () {
    if (sidebar.style.display === "none" || sidebar.style.display === "") {
      sidebar.style.display = "block";
    } else {
      sidebar.style.display = "none";
    }
  });

  // ambil device location
  if ("geolocation" in navigator) {
    navigator.geolocation.getCurrentPosition(function(position) {
      const latitude = position.coords.latitude;
      const longitude = position.coords.longitude;

      // Ambil nama lokasi menggunakan reverse geocoding
      fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`)
        .then(response => response.json())
        .then(data => {
          const locationName = data.address.city || data.address.town || data.address.village || data.address.county || "tidak diketahui";
          document.getElementById("lokasiDevice").textContent = locationName;
        })
        .catch(err => {
          document.getElementById("lokasiDevice").textContent = "tidak bisa dideteksi";
        });
    }, function() {
      document.getElementById("lokasiDevice").textContent = "tidak diizinkan";
    });
  } else {
    document.getElementById("lokasiDevice").textContent = "tidak tersedia";
  }

  var lightbox = new SimpleLightbox('[data-lightbox]', { /* options */ });

  if ("serviceWorker" in navigator) {
    navigator.serviceWorker.register("/service-worker.js")
      .then((reg) => console.log("Service worker registered.", reg));
  }
</script>

</body>
</html>
