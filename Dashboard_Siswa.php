<?php
session_start();
include "koneksi.php";

// 🔒 Proteksi login siswa
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: Login_Siswa.php");
    exit;
}

// Ambil data siswa dari session
$id_siswa = $_SESSION['id_siswa'];

// 🔍 Ambil data lengkap + nama guru BP
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda</title>
    <link rel="stylesheet" href="bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            color: #333;
        }

        /* HERO (biarkan gradient tapi lebih halus) */
        .content {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            background: linear-gradient(135deg, #4facfe, #6a5acd);
        }

        /* HAPUS ANIMASI BERLEBIHAN */
        .content::before,
        .content::after {
            display: none;
        }

        /* SECTION */
        .section {
            padding: 70px 0;
        }

        /* CARD */
        .card-custom {
            border: none;
            border-radius: 15px;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .card-custom:hover {
            transform: translateY(-5px);
        }

        /* ICON */
        .icon {
            font-size: 40px;
            color: #6a5acd;
        }

        /* NAVBAR */
        .navbar {
            background: #1f2a44 !important;
        }

        /* FOOTER */
        footer {
            background: rgba(0, 0, 0, 0.6) !important;
            color: white;
            text-align: center;
            backdrop-filter: blur(10px);
            padding: 10px 0;
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

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
        <div class="container">

            <!-- LOGO / BRAND (KIRI) -->
            <a class="navbar-brand d-flex align-items-center brand-mobile" href="#">
                <img src="asset/logosekolah.png" class="logo-mobile me-2">

                <div>
                    <div class="fw-bold title-mobile">SMK Dharma Bahari Surabaya</div>
                    <small class="text-light opacity-75 slogan-mobile">
                        "Karakter Kuat, Prestasi Hebat, Masa Depan Siap"
                    </small>
                </div>
            </a>

            <!-- TOGGLE MOBILE -->
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- MENU (KANAN) -->
            <div class="collapse navbar-collapse justify-content-end" id="navMenu">
                <ul class="navbar-nav align-items-lg-center ms-auto gap-lg-3">

                    <li class="nav-item">
                        <a class="nav-link active" href="Dashboard_Siswa.php">Beranda</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="Kuisioner.php">Kuisioner</a>
                    </li>

                    <!-- 👤 DROPDOWN SISWA -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white fw-bold"
                            href="#"
                            id="dropdownUser"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            <i class="bi bi-person-circle"></i>
                            <?php echo $data['nama']; ?>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow">

                            <li class="dropdown-item text-warning">
                                <i class="bi bi-mortarboard-fill"></i>
                                <?php echo $data['nama_guru'] ?? 'Guru BP'; ?>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a href="Data_Diri_Siswa.php" class="dropdown-item">
                                    <i class="bi bi-person"></i> Data Diri
                                </a>
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

    <!-- HERO -->
    <section class="content d-flex align-items-center text-center text-white">
        <div class="container">
            <h1 class="fw-bold display-5 content-title">
                Selamat Datang, <?php echo $data['nama']; ?> 👋
            </h1>

            <p class="lead mt-3 content-title">
                Dapatkan rekomendasi karir berdasarkan minat dan potensi secara cepat dan akurat.
            </p>
            <a href="Kuisioner.php" class="btn btn-light text-primary mt-3 px-4 py-2">
                Kuisioner
            </a>
        </div>
    </section>

    <!-- DESKRIPSI -->
    <section class="section text-center">
        <div class="container">
            <h2 class="mb-4">Deskripsi</h2>
            <p>
                Sistem ini merupakan aplikasi rekomendasi karir yang dirancang untuk membantu siswa
                SMK Dharma Bahari Surabaya dalam menentukan minat dan potensi karir.
                Dengan menggunakan metode <b>Content-Based Filtering</b>, sistem akan menganalisis
                jawaban kuisioner dan memberikan rekomendasi karir yang sesuai.
            </p>
        </div>
    </section>

    <!-- TUJUAN -->
    <section class="section bg-light">
        <div class="container text-center">
            <h2 class="mb-5">Tujuan Sistem</h2>
            <div class="row">

                <div class="col-md-4">
                    <div class="card card-custom p-4">
                        <i class="bi bi-lightbulb icon"></i>
                        <h5 class="mt-3">Mengenali Minat</h5>
                        <p>Membantu siswa memahami minat dan bakat mereka.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-custom p-4">
                        <i class="bi bi-graph-up icon"></i>
                        <h5 class="mt-3">Rekomendasi Karir</h5>
                        <p>Memberikan karir yang sesuai dengan hasil kuisioner.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-custom p-4">
                        <i class="bi bi-check-circle icon"></i>
                        <h5 class="mt-3">Keputusan Tepat</h5>
                        <p>Membantu siswa memilih masa depan dengan lebih yakin.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CARA KERJA -->
    <section class="section text-center">
        <div class="container">
            <h2 class="mb-4">Cara Kerja Sistem</h2>
            <p>
                Pengguna mengisi kuisioner yang berisi beberapa pertanyaan terkait minat dan preferensi.
                Setiap jawaban akan diproses oleh sistem menggunakan metode
                <b>Content-Based Filtering</b>, kemudian sistem akan mencocokkan
                jawaban dengan data karir untuk menghasilkan rekomendasi terbaik.
            </p>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer footer-expand-lg footer-dark bg-dark">
        <p>© 2026 SMK Dharma Bahari Surabaya - "Karakter Kuat, Prestasi Hebat, Masa Depan Siap"</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>