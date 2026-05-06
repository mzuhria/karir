<?php
session_start();
include "koneksi.php";

// 🔒 Proteksi login
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: Login_Siswa.php");
    exit;
}

$id_siswa = $_SESSION['id_siswa'];

// Ambil data siswa + guru
$query = mysqli_query($conn, "
    SELECT siswa.*, admin.nama_guru 
    FROM siswa
    LEFT JOIN admin ON siswa.id_admin = admin.id_admin
    WHERE siswa.id_siswa = '$id_siswa'
");

$data = mysqli_fetch_assoc($query);

$query_hasil = mysqli_query($conn, "
    SELECT 
        tanggal,
        MAX(id_hasil) as id_hasil
    FROM hasil
    WHERE id_siswa = '$id_siswa'
    GROUP BY tanggal
    ORDER BY tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Diri Siswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f5f6fa;
        }

        /* SIDEBAR */
        .sidebar {
            width: 220px;
            height: 100vh;
            position: fixed;
            top: 70px;
            left: 0;
            background: white;
            padding-top: 20px;
            border-right: 1px solid #eee;
        }

        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #555;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #f1f1f1;
        }

        /* CONTENT */
        .content {
            margin-left: 220px;
            margin-top: 90px;
            padding: 20px;
        }

        /* CARD */
        .card-form {
            border-radius: 15px;
            padding: 25px;
        }

        input {
            background: #f1f3f6 !important;
        }

        .dropdown-menu {
            border-radius: 10px;
        }

        .dropdown-item i {
            margin-right: 8px;
        }

        .logo-mobile {
            height: 60px;
        }

        .title-mobile {
            font-size: 1.2rem;
            line-height: 1.2;
        }

        .slogan-mobile {
            font-size: 0.8rem;
        }

        /* MOBILE */
        @media (max-width: 768px) {

            .navbar .container {
                align-items: flex-start;
            }

            .brand-mobile {
                width: 80%;
            }

            .logo-mobile {
                height: 45px;
            }

            .title-mobile {
                font-size: 15px;
            }

            .slogan-mobile {
                font-size: 10px;
                display: block;
            }

            .navbar-toggler {
                margin-top: 5px;
            }

            .navbar-collapse {
                margin-top: 15px;
                background: #212529;
                padding: 10px;
                border-radius: 10px;
            }

            .navbar-nav {
                gap: 0 !important;
            }

            .nav-link {
                padding: 10px 0;
            }
        }
    </style>

</head>

<body>

    <!-- 🔥 NAVBAR (PUNYA KAMU) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow py-2">
        <div class="container">

            <a class="navbar-brand d-flex align-items-center brand-mobile" href="#">
                <img src="asset/logosekolah.png" class="logo-mobile me-2">
                <div>
                    <div class="fw-bold title-mobile">SMK Dharma Bahari Surabaya</div>
                    <small class="text-light opacity-75 slogan-mobile">"Karakter Kuat, Prestasi Hebat, Masa Depan Siap"</small>
                </div>
            </a>

            <button class="navbar-toggler d-lg-none"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#mobileSidebar">

                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="d-none d-lg-flex ms-auto">
                <ul class="navbar-nav align-items-lg-center ms-auto gap-lg-3">
                    <!-- DROPDOWN -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white"
                            data-bs-toggle="dropdown">

                            <i class="bi bi-person-circle"></i>
                            <?php echo $data['nama']; ?>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-item text-warning">
                                🎓 <?php echo $data['nama_guru']; ?>
                            </li>
                            <li>
                                <hr>
                            </li>
                            <li>
                                <a href="Logout_Siswa.php" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <div class="offcanvas offcanvas-start bg-dark text-white"
        tabindex="-1"
        id="mobileSidebar">

        <div class="offcanvas-header">

            <h5 class="offcanvas-title">
                Menu
            </h5>

            <button type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="offcanvas">
            </button>

        </div>

        <div class="offcanvas-body">

            <!-- MENU -->
            <a href="Dashboard_Siswa.php"
                class="d-block text-white mb-3 text-decoration-none">

                <i class="bi bi-house"></i>
                Beranda
            </a>

            <a href="Kuisioner.php"
                class="d-block text-white mb-3 text-decoration-none">

                <i class="bi bi-clipboard"></i>
                Kuisioner
            </a>

            <a href="Data_Diri_Siswa.php"
                class="d-block text-white mb-3 text-decoration-none">

                <i class="bi bi-person"></i>
                Data Diri
            </a>

            <a href="Riwayat_siswa.php"
                class="d-block text-white mb-3 text-decoration-none">

                <i class="bi bi-clock-history"></i>
                Riwayat
            </a>

            <hr class="border-light">

            <!-- USER -->
            <div class="dropdown">

                <a class="btn btn-outline-light dropdown-toggle w-100"
                    data-bs-toggle="dropdown">

                    <i class="bi bi-person-circle"></i>
                    <?php echo $data['nama']; ?>

                </a>

                <ul class="dropdown-menu dropdown-menu-dark w-100">

                    <li class="dropdown-item text-warning">

                        <i class="bi bi-mortarboard-fill"></i>
                        <?php echo $data['nama_guru'] ?? 'Guru BP'; ?>

                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a href="Logout_Siswa.php"
                            class="dropdown-item text-danger">

                            <i class="bi bi-box-arrow-right"></i>
                            Logout
                        </a>
                    </li>

                </ul>

            </div>

        </div>

    </div>

    <!-- 🔥 SIDEBAR -->
    <div class="sidebar d-none d-lg-block">
        <a href="Dashboard_Siswa.php"><i class="bi bi-house"></i> Beranda</a>
        <a href="Data_Diri_Siswa.php"><i class="bi bi-person"></i> Data Diri</a>
        <a href="Kuisioner.php"><i class="bi bi-clipboard"></i> Kuisioner</a>
        <a href="Riwayat_siswa.php" class="fw-bold text-primary"><i class="bi bi-clock-history"></i> Riwayat</a>
    </div>

    <!-- 🔥 CONTENT -->
    <div class="content">

        <h4>Data Diri</h4>
        <p class="text-muted">Beranda - Riwayat</p>

        <div class="card card-form shadow-sm">

            <h5 class="mb-3">Riwayat Hasil Test</h5>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Karir</th>
                            <th>Skor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no = 1;
                        if (mysqli_num_rows($query_hasil) > 0) {
                            while ($row = mysqli_fetch_assoc($query_hasil)) {
                                $detail = mysqli_query($conn, "SELECT hasil.*, karir.nama_karir FROM hasil LEFT JOIN karir ON hasil.id_karir = karir.id_karir
                                                        WHERE hasil.id_siswa='$id_siswa' AND hasil.tanggal='{$row['tanggal']}' ORDER BY skor DESC LIMIT 1 ");
                                $d = mysqli_fetch_assoc($detail);
                        ?>

                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                                    <td><?php echo $d['nama_karir']; ?></td>
                                    <td class="text-center"><?php echo $d['skor']; ?></td>
                                    <td class="text-center">
                                        <a href="Hasil.php?tanggal=<?php echo $row['tanggal']; ?>"
                                            class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>

                            <?php
                            }
                        } else {
                            ?>

                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Belum ada riwayat hasil
                                </td>
                            </tr>

                        <?php } ?>

                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>