<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php'; // Pastikan koneksi aktif

// Total pengantin
$jml_pengantin = $weddingku->query("SELECT COUNT(*) as total FROM pengantin")->fetch_assoc()['total'];

// Query untuk data pengantin, tamu, ucapan, dan galeri
$tamu_per_pengantin = $weddingku->query("
    SELECT p.id, CONCAT(p.nama_pria, ' & ', p.nama_wanita) as nama_pengantin, COUNT(t.id) as total_tamu
    FROM pengantin p
    LEFT JOIN tamu t ON p.id = t.pengantin_id
    GROUP BY p.id
");

$ucapan_per_pengantin = $weddingku->query("
    SELECT p.id, CONCAT(p.nama_pria, ' & ', p.nama_wanita) as nama_pengantin, COUNT(m.id) as total_ucapan
    FROM pengantin p
    LEFT JOIN message m ON p.id = m.pengantin_id
    GROUP BY p.id
");

$galeri_per_pengantin = $weddingku->query("
    SELECT p.id, CONCAT(p.nama_pria, ' & ', p.nama_wanita) as nama_pengantin, COUNT(g.id) as total_galeri
    FROM pengantin p
    LEFT JOIN gallery g ON p.id = g.pengantin_id
    GROUP BY p.id
");

// Tambahan untuk total ucapan dan galeri
$total_ucapan = $weddingku->query("SELECT COUNT(*) as total FROM message")->fetch_assoc()['total'];
$total_galeri = $weddingku->query("SELECT COUNT(*) as total FROM gallery")->fetch_assoc()['total'];
$total_tamu = $weddingku->query("SELECT COUNT(*) as total FROM tamu")->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Wedding App</title>
    <!-- Favicon -->
    <link rel="icon" href="images/amirulprojectsolutions.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: url('images/bg-miror.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .breadcrumb-section {
            margin-top: 10px;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include 'sidebar.php'; ?>
    <div class="content-wrapper">
        <!-- Breadcrumb -->
        <div class="breadcrumb-section">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="alert alert-info">Selamat datang di aplikasi undangan pernikahan digital!</div>

                <div class="row">
                    <!-- Total Pengantin -->
                    <div class="col-lg-4 col-12 mb-3">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?= $jml_pengantin ?></h3>
                                <p>Total Pengantin</p>
                                <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePengantinTable" aria-expanded="false" aria-controls="collapsePengantinTable">
                                    <i class="fas fa-eye"></i>  Lihat Detail
                                </button>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>

                        <div class="collapse" id="collapsePengantinTable">
                            <div class="card card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Pria</th>
                                                <th>Nama Wanita</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $pengantinList = $weddingku->query("SELECT nama_pria, nama_wanita FROM pengantin");
                                            $no = 1;
                                            while ($row = $pengantinList->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= $row['nama_pria'] ?></td>
                                                    <td><?= $row['nama_wanita'] ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tamu per Pengantin -->
                    <div class="col-lg-4 col-12 mb-3">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= $total_tamu ?></h3>
                                <h5 class="mb-2">Tamu / Pengantin</h5>
                                <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTamuTable" aria-expanded="false" aria-controls="collapseTamuTable">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </button>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-friends"></i>
                            </div>
                        </div>
                        <div class="collapse" id="collapseTamuTable">
                            <div class="card card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Pengantin</th>
                                                <th>Jumlah Tamu</th>
                                                <th>Detail Tamu</th> <!-- Add a new column for detail link -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $no = 1;
                                            $tamu_per_pengantin->data_seek(0); // Reset pointer
                                            while ($row = $tamu_per_pengantin->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= $row['nama_pengantin'] ?></td>
                                                    <td><?= $row['total_tamu'] ?></td>
                                                    <td>
                                                        <!-- Add a clickable link that will trigger modal -->
                                                        <a href="javascript:void(0)" class="btn btn-info btn-sm" onclick="showGuests(<?= $row['id'] ?>)">Lihat Tamu</a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Guest List -->
                    <div class="modal fade" id="guestModal" tabindex="-1" aria-labelledby="guestModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="guestModalLabel">Guest List</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="guestTableContainer">
                                        <!-- Guest list will be dynamically populated here -->
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama</th>
                                                    <th>Alamat</th>
                                                </tr>
                                            </thead>
                                            <tbody id="guestListBody">
                                                <!-- Data will be appended here using JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ucapan per Pengantin -->
                    <div class="col-lg-4 col-12 mb-3">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= $total_ucapan ?></h3>
                                <p>Ucapan / Pengantin</p>
                                <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUcapan" aria-expanded="false" aria-controls="collapseUcapan">
                                    <i class="fas fa-eye"></i>  Lihat Detail
                                </button>
                            </div>
                            <div class="icon">
                                <i class="fas fa-comments"></i>
                            </div>
                        </div>

                        <div class="collapse" id="collapseUcapan">
                            <div class="card card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Pengantin</th>
                                                <th>Total Ucapan</th>
                                                <th>Detail Ucapan</th> <!-- Added a column for the detail link -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $no = 1;
                                            $ucapan_per_pengantin->data_seek(0); // Reset pointer
                                            while ($row = $ucapan_per_pengantin->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= $row['nama_pengantin'] ?></td>
                                                    <td><?= $row['total_ucapan'] ?></td>
                                                    <td>
                                                        <!-- Add a clickable link to fetch ucapan data -->
                                                        <a href="javascript:void(0)" class="btn btn-info btn-sm" onclick="showUcapan(<?= $row['id'] ?>)">Lihat Ucapan</a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Ucapan (Messages) -->
                    <div class="modal fade" id="ucapanModal" tabindex="-1" aria-labelledby="ucapanModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ucapanModalLabel">Ucapan / Pengantin</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="ucapanTableContainer">
                                        <!-- Ucapan data will be dynamically populated here -->
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama</th>
                                                    <th>Pesan</th>
                                                    <th>Tanggal</th>
                                                </tr>
                                            </thead>
                                            <tbody id="ucapanListBody">
                                                <!-- Data will be appended here using JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Galeri per Pengantin -->
                    <div class="col-lg-4 col-12 mb-3">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= $total_galeri ?></h3>
                                <p>Galeri / Pengantin</p>
                                <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGaleri" aria-expanded="false" aria-controls="collapseGaleri">
                                <i class="fas fa-eye"></i>  Lihat Detail
                                </button>
                            </div>
                            <div class="icon">
                                <i class="fas fa-image"></i>
                            </div>
                        </div>

                        <div class="collapse" id="collapseGaleri">
                            <div class="card card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Pengantin</th>
                                                <th>Total Galeri</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            while ($row = $galeri_per_pengantin->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= $row['nama_pengantin'] ?></td>
                                                    <td><?= $row['total_galeri'] ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
// Function to fetch and show guests in the modal
function showGuests(pengantin_id) {
    // Use AJAX to fetch the guest data
    fetch('get_tamu.php?pengantin_id=' + pengantin_id)
        .then(response => response.json())
        .then(data => {
            // Get the guest list table body
            const guestListBody = document.getElementById('guestListBody');
            
            // Clear any previous data
            guestListBody.innerHTML = '';
            
            // Append each guest to the table
            data.forEach((guest, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${guest.nama}</td>
                    <td>${guest.alamat}</td>
                `;
                guestListBody.appendChild(row);
            });

            // Show the modal
            var myModal = new bootstrap.Modal(document.getElementById('guestModal'));
            myModal.show();
        })
        .catch(error => {
            console.error('Error fetching guest data:', error);
        });
}

// Function to fetch and show ucapan (messages) in the modal
function showUcapan(pengantin_id) {
    // Use AJAX to fetch the ucapan data
    fetch('get_ucapan_pengantin.php?pengantin_id=' + pengantin_id)
        .then(response => response.json())
        .then(data => {
            // Get the ucapan list table body
            const ucapanListBody = document.getElementById('ucapanListBody');
            
            // Clear any previous data
            ucapanListBody.innerHTML = '';
            
            // Append each ucapan to the table
            data.forEach((ucapan, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${ucapan.nama}</td>
                    <td>${ucapan.pesan}</td>
                    <td>${ucapan.created_at}</td>
                `;
                ucapanListBody.appendChild(row);
            });

            // Show the modal
            var myModal = new bootstrap.Modal(document.getElementById('ucapanModal'));
            myModal.show();
        })
        .catch(error => {
            console.error('Error fetching ucapan data:', error);
        });
}
</script>



</body>
</html>
