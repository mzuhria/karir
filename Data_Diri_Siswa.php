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

        /* SIDEBAR DESKTOP */
        .sidebar {
            width: 220px;
            height: 100vh;
            position: fixed;
            top: 76px;
            left: 0;
            background: white;
            padding-top: 20px;
            border-right: 1px solid #eee;
            z-index: 100;
        }

        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #555;
            text-decoration: none;
            transition: 0.3s;
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

        /* NAVBAR */
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

            /* SIDEBAR HILANG */
            .sidebar {
                display: none;
            }

            /* CONTENT FULL */
            .content {
                margin-left: 0;
                padding: 15px;
                margin-top: 85px;
            }

            .brand-mobile {
                width: 75%;
            }

            .logo-mobile {
                height: 45px;
            }

            .title-mobile {
                font-size: 14px;
            }

            .slogan-mobile {
                font-size: 9px;
            }

            .card-form {
                padding: 18px;
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
        <a href="Data_Diri_Siswa.php" class="fw-bold text-primary"><i class="bi bi-person"></i> Data Diri</a>
        <a href="Kuisioner.php"><i class="bi bi-clipboard"></i> Kuisioner</a>
        <a href="Riwayat_siswa.php"><i class="bi bi-clock-history"></i> Riwayat</a>
    </div>

    <!-- 🔥 CONTENT -->
    <div class="content">

        <h4>Data Diri</h4>
        <p class="text-muted">Beranda - Data Diri</p>

        <div class="card card-form shadow-sm">

            <h5 class="mb-4">
                Isi Data Yang Kosong
            </h5>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    Data berhasil diperbarui!
                </div>
            <?php endif; ?>

            <form action="Process_update_siswa.php" method="POST">

                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" class="form-control" value="<?php echo $data['nama']; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control"
                        value="<?php echo $data['email']; ?>">
                </div>

                <div class="mb-3">
                    <label>Kelas</label>
                    <input type="text" class="form-control"
                        value="<?php echo $data['kelas'] . ' ' . $data['jurusan'] . ' ' . $data['subkelas']; ?>"
                        readonly>
                </div>

                <div class="mb-3">
                    <label>Alamat</label>
                    <input type="text" name="alamat" class="form-control"
                        value="<?php echo $data['alamat'] ?? ''; ?>"
                        placeholder="Masukkan alamat">
                </div>

                <div class="mb-3">
                    <label>No HP</label>
                    <input type="text" name="no_hp" class="form-control"
                        value="<?php echo $data['no_hp'] ?? ''; ?>"
                        placeholder="Masukkan nomor HP">
                </div>

                <button class="btn btn-warning">Simpan</button>

            </form>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>