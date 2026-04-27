<?php
session_start();
include "koneksi.php";

// 🔒 CEK LOGIN KEPSEK
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'kepala_sekolah') {
    header("Location: Login_Admin.php");
    exit;
}

// ✅ DATA SESSION
$id_kepsek   = $_SESSION['id_admin'];
$nama_kepsek = $_SESSION['nama_guru'] ?? 'Kepala Sekolah';

// =======================
// 📊 STATISTIK
// =======================
$total_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa"))['total'];

$total_guru  = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as total FROM admin WHERE role='guru_bp'
"))['total'];

$total_hasil = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as total FROM hasil
"))['total'];

// 🔥 AKTIVITAS
$aktivitas = mysqli_query($conn, "
    SELECT siswa.nama, hasil.tanggal, karir.nama_karir
    FROM hasil
    LEFT JOIN siswa ON hasil.id_siswa = siswa.id_siswa
    LEFT JOIN karir ON hasil.id_karir = karir.id_karir
    ORDER BY hasil.tanggal DESC
    LIMIT 5
");

// 🔥 KARIR TERPOPULER
$karir = mysqli_query($conn, "
    SELECT karir.nama_karir, COUNT(*) as total
    FROM hasil
    JOIN karir ON hasil.id_karir = karir.id_karir
    GROUP BY hasil.id_karir
    ORDER BY total DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Kepsek</title>

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
            background: linear-gradient(135deg, #4f46e5, #22c55e);
            color: white !important;
            font-weight: 600;
            border-radius: 10px;
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
            font-size: 26px;
            padding: 12px;
            border-radius: 12px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-blue {
            background: #3b82f6;
        }

        .bg-green {
            background: #22c55e;
        }

        .bg-orange {
            background: #f59e0b;
        }

        /* 🔹 LOGOUT */
        .logout {
            color: #ef4444 !important;
        }
    </style>

</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h4 class="text-center py-3">Kepsek Panel</h4>
        <hr>

        <a href="Dashboard_kepsek.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="#"><i class="bi bi-people"></i> Data Siswa</a>
        <a href="#"><i class="bi bi-person-badge"></i> Data Guru</a>

    </div>

    <!-- NAVBAR -->
    <nav class="navbar navbar-light bg-white px-3 d-flex justify-content-end shadow-sm">
        <div class="dropdown">
            <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle fs-4 me-2"></i>
                <strong><?php echo $nama_kepsek; ?></strong>
            </a>

            <ul class="dropdown-menu dropdown-menu-end">
                <li class="dropdown-item">
                    <i class="bi bi-person"></i> <?php echo $nama_kepsek; ?>
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

    <!-- CONTENT -->
    <div class="content">

        <!-- HEADER -->
        <div class="card p-4 mb-4" style="background: linear-gradient(135deg,#4f46e5,#22c55e); color:white;">
            <h4>Halo, Bapak <?= $nama_kepsek ?> 👋</h4>
            <p>Monitoring aktivitas siswa dan guru secara real-time</p>
        </div>

        <!-- STAT -->
        <div class="row g-3">

            <div class="col-md-4">
                <div class="card p-3 d-flex justify-content-between flex-row">
                    <div>
                        <h6>Total Siswa</h6>
                        <h3><?= $total_siswa ?></h3>
                    </div>
                    <div class="icon-box bg-blue">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 d-flex justify-content-between flex-row">
                    <div>
                        <h6>Guru BP</h6>
                        <h3><?= $total_guru ?></h3>
                    </div>
                    <div class="icon-box bg-green">
                        <i class="bi bi-person-badge"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 d-flex justify-content-between flex-row align-items-center">
                    <div>
                        <h6>Total Tes</h6>
                        <h3><?= $total_hasil ?></h3>
                    </div>
                    <div class="icon-box bg-orange">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- CONTENT -->
        <div class="row mt-4">

            <!-- AKTIVITAS -->
            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Aktivitas Terbaru</h5>
                    <hr>

                    <?php while ($a = mysqli_fetch_assoc($aktivitas)) { ?>
                        <div class="d-flex justify-content-between">
                            <div>
                                <b><?= $a['nama'] ?></b><br>
                                <small><?= $a['nama_karir'] ?></small>
                            </div>
                            <small><?= date('d M', strtotime($a['tanggal'])) ?></small>
                        </div>
                        <hr>
                    <?php } ?>

                </div>
            </div>

            <!-- KARIR -->
            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Karir Terpopuler</h5>
                    <hr>

                    <?php while ($k = mysqli_fetch_assoc($karir)) { ?>
                        <div class="d-flex justify-content-between">
                            <span><?= $k['nama_karir'] ?></span>
                            <span class="badge bg-primary"><?= $k['total'] ?></span>
                        </div>
                        <hr>
                    <?php } ?>

                </div>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>