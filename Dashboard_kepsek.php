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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kepsek</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f1f5f9;
            overflow-x: hidden;
            font-family: 'Poppins', sans-serif;
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
            border-radius: 12px;
            margin: 6px 12px;
            text-decoration: none;
            transition: 0.2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: linear-gradient(135deg, #4f46e5, #22c55e);
            color: white;
        }

        /* CONTENT */
        .content {
            margin-left: 240px;
            padding: 25px;
            transition: 0.3s;
        }

        /* TOGGLE */
        .toggle-btn {
            display: none;
            border: none;
            background: none;
            font-size: 28px;
        }

        /* WELCOME */
        .welcome-card {
            background: linear-gradient(135deg, #4f46e5, #22c55e);
            color: white;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .welcome-card h3 {
            font-size: 28px;
            font-weight: bold;
        }

        /* CARD */
        .stat-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
        }

        .icon-box {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
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

        /* MOBILE */
        @media (max-width:768px) {

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

            .welcome-card {
                padding: 20px;
            }

            .welcome-card h3 {
                font-size: 22px;
                line-height: 1.4;
            }

            .welcome-card p {
                font-size: 15px;
            }

            .navbar strong {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h4 class="text-center py-3">KEPSEK PANEL</h4>
        <hr>

        <a href="Dashboard_kepsek.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="View_siswa.php"><i class="bi bi-people"></i> Data Siswa</a>
        <a href="View_guru.php"><i class="bi bi-person-badge"></i> Data Guru</a>

    </div>

    <!-- NAVBAR -->
    <nav class="navbar navbar-light bg-white px-3 d-flex justify-content-between topbar shadow-sm">

        <!-- toggle -->
        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>

        <!-- profile -->
        <div class="dropdown ms-auto">

            <a class="d-flex align-items-center text-decoration-none dropdown-toggle"
                href="#"
                data-bs-toggle="dropdown">

                <i class="bi bi-person-circle fs-4 me-2"></i>

                <strong>
                    <?php echo $nama_kepsek; ?>
                </strong>
            </a>

            <ul class="dropdown-menu dropdown-menu-end">

                <li class="dropdown-item">
                    <i class="bi bi-person"></i>
                    <?php echo $nama_kepsek; ?>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <a class="dropdown-item text-danger"
                        href="Logout_Admin.php">

                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </a>
                </li>

            </ul>

        </div>

    </nav>

    <!-- CONTENT -->
    <div class="content">

        <!-- HEADER -->
        <div class="welcome-card mb-4">

            <h3>
                Halo, <?= $nama_kepsek ?> 👋
            </h3>

            <p class="mb-0">
                Monitoring aktivitas siswa dan guru secara real-time
            </p>

        </div>

        <!-- STAT -->
        <div class="row g-3">

            <div class="col-md-4">
                <div class="card stat-card p-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <small>Total Siswa</small>
                            <h3><?= $total_siswa ?></h3>
                        </div>

                        <div class="icon-box bg-blue">
                            <i class="bi bi-people"></i>
                        </div>

                    </div>

                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card p-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <small>Guru BP</small>
                            <h3><?= $total_guru ?></h3>
                        </div>

                        <div class="icon-box bg-green">
                            <i class="bi bi-person-badge"></i>
                        </div>

                    </div>

                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card p-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <small>Total Tes</small>
                            <h3><?= $total_hasil ?></h3>
                        </div>

                        <div class="icon-box bg-orange">
                            <i class="bi bi-clipboard-check"></i>
                        </div>

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
    <script>
        function toggleSidebar() {
            document.querySelector(".sidebar")
                .classList.toggle("active");
        }

        // close sidebar ketika klik luar
        document.addEventListener("click", function(event) {

            const sidebar = document.querySelector(".sidebar");
            const toggleBtn = document.querySelector(".toggle-btn");

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