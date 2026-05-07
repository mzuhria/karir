<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: Login_Admin.php");
    exit;
}

$nama_admin = $_SESSION['nama_guru'] ?? 'Admin';

// statistik
$total_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM admin"))['total'];
$total_soal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kuisioner"))['total'];
$total_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa"))['total'];
$total_hasil = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hasil"))['total'];

$aktivitas = mysqli_query($conn, "
    SELECT siswa.nama, hasil.tanggal, karir.nama_karir
    FROM hasil
    LEFT JOIN siswa ON hasil.id_siswa = siswa.id_siswa
    LEFT JOIN karir ON hasil.id_karir = karir.id_karir
    ORDER BY hasil.tanggal DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f1f5f9;
            overflow-x: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            height: 100vh;
            width: 240px;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, #1e293b, #0f172a);
            color: white;
            padding-top: 20px;
            transition: all 0.3s ease;
            z-index: 1050;
        }

        .sidebar h4 {
            font-weight: bold;
            letter-spacing: 1px;
        }

        .sidebar a {
            color: #cbd5e1;
            padding: 12px 20px;
            display: block;
            border-radius: 10px;
            margin: 6px 12px;
            text-decoration: none;
            transition: 0.2s;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        /* CONTENT */
        .content {
            margin-left: 240px;
            padding: 25px;
            transition: all 0.3s ease;
        }

        /* NAVBAR */
        .topbar {
            margin-left: 240px;
            transition: all 0.3s ease;
        }

        /* TOGGLE BUTTON */
        .toggle-btn {
            display: none;
            border: none;
            background: none;
            font-size: 28px;
        }

        /* MOBILE */
        @media (max-width: 768px) {

            .sidebar {
                left: -240px;
            }

            .sidebar.active {
                left: 0;
            }

            .content {
                margin-left: 0;
                padding: 15px;
            }

            .topbar {
                margin-left: 0;
            }

            .toggle-btn {
                display: block;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h4 class="text-center py-3">ADMIN PANEL</h4>
        <hr>
        <a href="Admin.php" class="bg-secondary text-white"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <!-- DATA USER DROPDOWN -->
        <div>
            <a href="#" onclick="toggleMenu()" id="menuUser">
                <i class="bi bi-people"></i> Data User
                <i class="bi bi-chevron-down float-end"></i>
            </a>

            <div id="submenuUser" style="display:none; margin-left:20px;">
                <a href="Data_admin.php">
                    <i class="bi bi-person-badge"></i> Data Admin
                </a>
                <a href="Data_siswa.php">
                    <i class="bi bi-person"></i> Data Siswa
                </a>
            </div>
        </div>
        <a href="Data_soal.php"><i class="bi bi-file-text"></i> Data Soal</a>
        <a href="Riwayat_Admin.php"><i class="bi bi-clock-history"></i> Riwayat Hasil</a>
    </div>

    <!-- NAVBAR -->
    <nav class="navbar navbar-light bg-light px-3 d-flex justify-content-between topbar shadow-sm">

        <!-- tombol toggle -->
        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>

        <!-- profile -->
        <div class="dropdown ms-auto">
            <a class="d-flex align-items-center text-decoration-none dropdown-toggle"
                href="#" data-bs-toggle="dropdown">

                <i class="bi bi-person-circle fs-4 me-2"></i>
                <strong><?php echo $nama_admin; ?></strong>
            </a>

            <ul class="dropdown-menu dropdown-menu-end">
                <li class="dropdown-item">
                    <i class="bi bi-person"></i> <?php echo $nama_admin; ?>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <a class="dropdown-item logout text-danger"
                        href="Logout_Admin.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

    </nav>

    <!-- CONTENT -->
    <div class="content">

        <!-- WELCOME -->
        <div class="card text-white mb-4 shadow-sm"
            style="background: linear-gradient(135deg,#4f46e5,#22c55e);">
            <div class="card-body">
                <h5>Halo, <?= $nama_admin ?> 👋</h5>
                <p class="mb-0">Selamat datang di sistem analisis minat karir siswa</p>
            </div>
        </div>

        <!-- STATISTIK -->
        <div class="row g-3">

            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small>Total Admin</small>
                            <h4><?= $total_user ?></h4>
                        </div>
                        <i class="bi bi-people fs-2 text-primary"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small>Total Siswa</small>
                            <h4><?= $total_siswa ?></h4>
                        </div>
                        <i class="bi bi-person fs-2 text-success"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small>Total Soal</small>
                            <h4><?= $total_soal ?></h4>
                        </div>
                        <i class="bi bi-file-text fs-2 text-warning"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small>Total Test</small>
                            <h4><?= $total_hasil ?></h4>
                        </div>
                        <i class="bi bi-clipboard-check fs-2 text-danger"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- AKTIVITAS -->
        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <h5>Aktivitas Terbaru</h5>

                <ul class="list-group list-group-flush">
                    <?php while ($a = mysqli_fetch_assoc($aktivitas)) { ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <div>
                                <b><?= $a['nama'] ?></b><br>
                                <small class="text-muted">
                                    Rekomendasi: <?= $a['nama_karir'] ?>
                                </small>
                            </div>
                            <small><?= date('d M', strtotime($a['tanggal'])) ?></small>
                        </li>
                    <?php } ?>
                </ul>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleMenu() {
            var menu = document.getElementById("submenuUser");

            if (menu.style.display === "none") {
                menu.style.display = "block";
            } else {
                menu.style.display = "none";
            }
        }
    </script>
    <script>
        function toggleMenu() {
            var menu = document.getElementById("submenuUser");

            if (menu.style.display === "none") {
                menu.style.display = "block";
            } else {
                menu.style.display = "none";
            }
        }

        function toggleSidebar() {
            document.querySelector(".sidebar").classList.toggle("active");
        }

        // otomatis close ketika klik luar sidebar
        document.addEventListener("click", function(event) {

            const sidebar = document.querySelector(".sidebar");
            const toggleBtn = document.querySelector(".toggle-btn");

            // jika klik bukan sidebar & bukan tombol
            if (
                !sidebar.contains(event.target) &&
                !toggleBtn.contains(event.target)
            ) {
                sidebar.classList.remove("active");
            }
        });
    </script>

</body>

</html>