<?php
session_start();
include "koneksi.php";

// CEK LOGIN
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'guru_bp') {
    header("Location: Login_Admin.php");
    exit;
}

// AMBIL DATA SESSION
$id_admin = $_SESSION['id_admin'] ?? 0;
$nama_guru = $_SESSION['nama_guru'] ?? 'Guru BP';
$username  = $_SESSION['username'] ?? '-';

// =======================
// STATISTIK (FILTER PER GURU)
// =======================
$total_siswa = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as total FROM siswa WHERE id_admin='$id_admin'"
))['total'];

$total_soal = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as total FROM kuisioner WHERE id_admin='$id_admin'"
))['total'];

$total_karir = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as total FROM karir WHERE id_admin='$id_admin'"
))['total'];

$total_hasil = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as total
     FROM hasil
     JOIN siswa ON hasil.id_siswa = siswa.id_siswa
     WHERE siswa.id_admin = '$id_admin'"
))['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru BP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            font-family: 'Poppins', sans-serif;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #343a40;
            color: white;
            transition: all 0.3s ease;
            z-index: 999;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 998;
            display: none;
        }

        .overlay.show {
            display: block;
        }

        .dropdown-menu {
            position: absolute !important;
            z-index: 9999 !important;
        }

        .sidebar a {
            color: #ddd;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
        }

        .sidebar a:hover {
            background: #495057;
            color: white;
        }

        .content {
            margin-left: 240px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .content.full {
            margin-left: 0;
        }

        .topbar {
            background: #6f42c1;
            padding: 12px 20px;
            color: white;
            border-radius: 8px;
        }

        #toggleSidebar {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 2001;
            border-radius: 10px;
            width: 45px;
            height: 45px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        @media (min-width: 769px) {
            #toggleSidebar {
                display: none;
            }
        }

        .card-custom {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        @media (max-width: 768px) {

            .sidebar {
                width: 100%;
                left: -100%;
                height: 100vh;
            }

            .sidebar.show {
                left: 0;
            }

            .content {
                margin-left: 0 !important;
                padding: 15px;
            }

            .topbar {
                padding-left: 70px;
            }
        }
    </style>
</head>

<body>
    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <!-- PROFIL -->
        <div class="py-3 border-bottom text-center">
            <i class="bi bi-person-circle fs-3"></i>
            <div style="font-size:14px;">
                <?= htmlspecialchars($nama_guru); ?>
            </div>
        </div>

        <a href="Dashboard_gurubp.php" class="bg-secondary text-white"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="Kelola_siswa.php"><i class="bi bi-people me-2"></i>Kelola Siswa</a>
        <a href="Kelola_soal.php"><i class="bi bi-ui-checks me-2"></i>Kelola Soal</a>
        <a href="Kelola_karir.php"><i class="bi bi-briefcase me-2"></i>Kelola Karir</a>
        <a href="Riwayat.php"><i class="bi bi-clock-history me-2"></i>Riwayat</a>
        <hr>
        <a href="Logout_Admin.php" class="text-danger"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
    </div>

    <div class="overlay" id="overlay"></div>

    <!-- CONTENT -->
    <div class="content" id="content">

        <div class="topbar mb-4 d-flex align-items-center gap-2">

            <button class="btn btn-light" id="toggleSidebar">
                <i class="bi bi-list fs-4"></i>
            </button>

            <div>
                <strong>Home</strong> / Dashboard
            </div>

        </div>

        <!-- SAMBUTAN -->
        <div class="card card-custom p-3 mb-4">
            <h5>Selamat datang, <b><?= htmlspecialchars($nama_guru); ?></b> 👋</h5>
            <small>Dashboard Guru BP</small>
        </div>

        <!-- STATISTIK -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card card-custom p-3 text-center">
                    <i class="bi bi-people fs-2 text-primary"></i>
                    <h5 class="mt-2"><?= $total_siswa ?></h5>
                    <small>Total Siswa</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-custom p-3 text-center">
                    <i class="bi bi-ui-checks fs-2 text-success"></i>
                    <h5 class="mt-2"><?= $total_soal ?></h5>
                    <small>Total Soal</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-custom p-3 text-center">
                    <i class="bi bi-briefcase fs-2 text-warning"></i>
                    <h5 class="mt-2"><?= $total_karir ?></h5>
                    <small>Total Karir</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-custom p-3 text-center">
                    <i class="bi bi-bar-chart fs-2 text-danger"></i>
                    <h5 class="mt-2"><?= $total_hasil ?></h5>
                    <small>Total Hasil</small>
                </div>
            </div>
        </div>

        <!-- RIWAYAT -->
        <div class="card card-custom p-3">
            <h5><i class="bi bi-clock-history me-2"></i>Riwayat Terbaru</h5>

            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jurusan</th>
                        <th>Hasil Karir</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $query = mysqli_query($conn, "
    SELECT 
        siswa.nama,
        siswa.jurusan,
        karir.nama_karir,
        hasil.tanggal
    FROM hasil
    JOIN siswa ON siswa.id_siswa = hasil.id_siswa
    JOIN karir ON karir.id_karir = hasil.id_karir
    WHERE siswa.id_admin = '$id_admin'
    AND hasil.skor = (
        SELECT MAX(h2.skor)
        FROM hasil h2
        WHERE h2.id_siswa = hasil.id_siswa
    )
    ORDER BY hasil.tanggal DESC
    LIMIT 5
");

                    while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['jurusan']) ?></td>
                            <td><?= htmlspecialchars($row['nama_karir']) ?></td>
                            <td><?= $row['tanggal'] ?></td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        toggleBtn.addEventListener('click', () => {

            // hanya mobile
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }

        });

        // klik overlay tutup sidebar
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    </script>
</body>

</html>