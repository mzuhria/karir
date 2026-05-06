<?php
session_start();
include "koneksi.php";

// 🔒 CEK LOGIN ADMIN
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: Login_Admin.php");
    exit;
}

// ✅ AMBIL DATA SESSION
$id_admin   = $_SESSION['id_admin'];
$nama_admin = $_SESSION['nama_guru'] ?? 'Admin'; // bisa kamu ganti nanti
$username   = $_SESSION['username'] ?? '-';

// =======================
// 📊 CONTOH STATISTIK
// =======================
$total_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM admin"))['total'];
$total_soal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kuisioner"))['total'];

// 📊 TOTAL SISWA
$total_siswa = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as total FROM siswa
"))['total'];

// 📊 TOTAL HASIL TEST
$total_hasil = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as total FROM hasil
"))['total'];

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
    <title>Dashboard Admin</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f1f5f9;
            font-family: 'Segoe UI', sans-serif;
        }

        /* 🔹 SIDEBAR */
        .sidebar {
            height: 100vh;
            width: 240px;
            position: fixed;
            background: linear-gradient(180deg, #1e293b, #0f172a);
            color: white;
            padding-top: 20px;
        }

        .sidebar h4 {
            font-weight: bold;
            letter-spacing: 1px;
        }

        /* 🔥 HILANGKAN UNDERLINE */
        .sidebar a {
            color: #cbd5e1;
            padding: 12px 20px;
            display: block;
            border-radius: 10px;
            margin: 6px 12px;
            text-decoration: none !important;
            /* ⬅️ ini penting */
            transition: all 0.3s ease;
        }

        /* Hover */
        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            transform: translateX(5px);
        }

        /* 🔥 ACTIVE MENU */
        .sidebar a.active {
            background: #38bdf8;
            color: #0f172a !important;
            font-weight: 600;
        }

        /* 🔹 CONTENT */
        .content {
            margin-left: 240px;
            padding: 30px;
        }

        /* 🔹 NAVBAR */
        .navbar {
            margin-left: 240px;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* 🔹 CARD MODERN */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        /* 🔹 TEXT */
        h3 {
            font-weight: 600;
        }

        /* 🔹 ICON BOX */
        .icon-box {
            font-size: 28px;
            padding: 12px;
            border-radius: 10px;
            color: white;
        }

        .bg-blue {
            background: #3b82f6;
        }

        .bg-green {
            background: #22c55e;
        }

        /* 🔹 LOGOUT */
        .logout {
            color: #ef4444 !important;
        }
    </style>
</head>

<body>

    <!-- 🔹 SIDEBAR -->
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

    <!-- 🔹 NAVBAR -->
    <nav class="navbar navbar-light bg-light px-3 d-flex justify-content-end">

        <!-- Dropdown Profil -->
        <div class="dropdown">
            <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" data-bs-toggle="dropdown">
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
                    <a class="dropdown-item logout" href="Logout_Admin.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

    </nav>

    <!-- 🔹 CONTENT -->
    <div class="content">

        <div class="card p-4 mb-4" style="background: linear-gradient(135deg,#4f46e5,#22c55e); color:white;">
            <h4>Halo, <?= $nama_admin ?> 👋</h4>
            <p>Selamat datang di sistem analisis minat karir siswa</p>
        </div>

        <div class="row mt-4 g-3">

            <!-- USER -->
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6>Total Admin</h6>
                            <h3><?= $total_user ?></h3>
                        </div>
                        <div class="icon-box bg-blue">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SISWA -->
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6>Total Siswa</h6>
                            <h3><?= $total_siswa ?></h3>
                        </div>
                        <div class="icon-box bg-green">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SOAL -->
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6>Total Soal</h6>
                            <h3><?= $total_soal ?></h3>
                        </div>
                        <div class="icon-box bg-warning">
                            <i class="bi bi-file-text"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HASIL -->
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6>Total Test</h6>
                            <h3><?= $total_hasil ?></h3>
                        </div>
                        <div class="icon-box bg-danger">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-4 p-3">
            <h5>Aktivitas Terbaru</h5>

            <ul class="list-group list-group-flush">

                <?php while ($a = mysqli_fetch_assoc($aktivitas)) { ?>

                    <li class="list-group-item d-flex justify-content-between">
                        <div>
                            <b><?= $a['nama'] ?></b><br>
                            <small class="text-muted">
                                Hasil Rekomendasi : <?= $a['nama_karir'] ?>
                            </small>
                        </div>
                        <small><?= date('d M', strtotime($a['tanggal'])) ?></small>
                    </li>

                <?php } ?>

            </ul>
        </div>
    </div>
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

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>