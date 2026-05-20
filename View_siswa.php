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

// STATISTIK
$total_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa"))['total'];
$tkr = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa WHERE jurusan='TKR'"))['total'];
$tpm = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa WHERE jurusan='TPM'"))['total'];
$dkv = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa WHERE jurusan='DKV'"))['total'];
$tkj = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa WHERE jurusan='TKJ'"))['total'];

// Kuisioner
$sudah_isi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT id_siswa) as total FROM jawaban"))['total'];
$belum_isi = $total_siswa - $sudah_isi;

// Rekomendasi
$sudah_rekom = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hasil"))['total'];

//FILTER
$where = "WHERE 1=1";

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $where .= " AND siswa.nama LIKE '%$search%'";
}

if (isset($_GET['jurusan']) && $_GET['jurusan'] != '') {
    $jurusan = $_GET['jurusan'];
    $where .= " AND siswa.jurusan='$jurusan'";
}

// PAGINATION
$batas = 5;

$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;

if ($halaman < 1) {
    $halaman = 1;
}

$mulai = ($halaman - 1) * $batas;

// TOTAL DATA
$total_data = mysqli_num_rows(mysqli_query($conn, "
SELECT siswa.id_siswa
FROM siswa
LEFT JOIN admin ON siswa.id_admin = admin.id_admin
$where
"));

$total_halaman = ceil($total_data / $batas);

// DATA SISWA (JOIN)
$data = mysqli_query($conn, "
SELECT 
    siswa.*, 
    h.skor,
    karir.nama_karir,
    admin.nama_guru

FROM siswa

LEFT JOIN (
    SELECT *
    FROM hasil h1
    WHERE h1.skor = (
        SELECT MAX(h2.skor)
        FROM hasil h2
        WHERE h2.id_siswa = h1.id_siswa
    )
) h ON siswa.id_siswa = h.id_siswa

LEFT JOIN karir ON h.id_karir = karir.id_karir
LEFT JOIN admin ON siswa.id_admin = admin.id_admin

$where

ORDER BY siswa.id_siswa DESC
LIMIT $mulai, $batas
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

        <a href="Dashboard_kepsek.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="View_siswa.php" class="active"><i class="bi bi-people"></i> Data Siswa</a>
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
        <div class="container-fluid mt-4">
            <!-- STATISTIK -->
            <div class="row mb-4">

                <div class="col-md-4">
                    <div class="card shadow-sm border-0 text-center p-3">
                        <h6>Total Siswa</h6>
                        <h2><?= $total_siswa ?></h2>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm border-0 text-center p-3">
                        <h6>Sudah Isi</h6>
                        <h2><?= $sudah_isi ?></h2>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm border-0 text-center p-3">
                        <h6>Belum Isi</h6>
                        <h2><?= $belum_isi ?></h2>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card p-3 mb-4 shadow-sm">
                        <h5 class="mb-3">Distribusi Jurusan</h5>
                        <div style="height:200px;">
                            <canvas id="chartJurusan"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- FILTER -->
            <form method="GET" class="row mb-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="🔍 Cari nama siswa...">
                </div>

                <div class="col-md-3">
                    <select name="jurusan" class="form-control">
                        <option value="">Semua Jurusan</option>
                        <option value="TKR">TKR</option>
                        <option value="TPM">TPM</option>
                        <option value="DKV">DKV</option>
                        <option value="TKJ">TKJ</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary">Filter</button>
                </div>
            </form>
            <!-- TABEL -->
            <div class="card">
                <div class="card-header">
                    Data Siswa
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">

                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>Status</th>
                                <th>Skor</th>
                                <th>Rekomendasi Karir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <?php $no = 1;
                        $modals = ""; ?>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($data)) { ?>

                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nama'] ?></td>
                                    <td><?= $row['kelas'] ?></td>
                                    <td><?= $row['jurusan'] ?></td>

                                    <td>
                                        <?php if ($row['status'] == 'aktif') { ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php } else { ?>
                                            <span class="badge bg-danger">Nonaktif</span>
                                        <?php } ?>
                                    </td>

                                    <td><?= $row['skor'] ?? '-' ?></td>
                                    <td><?= $row['nama_karir'] ?? 'Belum Ada' ?></td>

                                    <td>
                                        <a href="Detail_siswa_kepsek.php?id=<?= $row['id_siswa'] ?>"
                                            class="btn btn-info btn-sm">

                                            <i class="bi bi-eye"></i>
                                            Detail

                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>

                    </table>
                </div>
                <!-- PAGINATION -->
                <nav class="mt-3">
                    <ul class="pagination justify-content-center">

                        <!-- PREV -->
                        <?php if ($halaman > 1) { ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?halaman=<?= $halaman - 1 ?>&search=<?= $_GET['search'] ?? '' ?>&jurusan=<?= $_GET['jurusan'] ?? '' ?>">
                                    Previous
                                </a>
                            </li>
                        <?php } ?>

                        <!-- ANGKA -->
                        <?php for ($i = 1; $i <= $total_halaman; $i++) { ?>

                            <li class="page-item <?= ($i == $halaman) ? 'active' : '' ?>">

                                <a class="page-link"
                                    href="?halaman=<?= $i ?>&search=<?= $_GET['search'] ?? '' ?>&jurusan=<?= $_GET['jurusan'] ?? '' ?>">

                                    <?= $i ?>

                                </a>

                            </li>

                        <?php } ?>

                        <!-- NEXT -->
                        <?php if ($halaman < $total_halaman) { ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?halaman=<?= $halaman + 1 ?>&search=<?= $_GET['search'] ?? '' ?>&jurusan=<?= $_GET['jurusan'] ?? '' ?>">
                                    Next
                                </a>
                            </li>
                        <?php } ?>

                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('chartJurusan');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['TKR', 'TPM', 'DKV', 'TKJ'],
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: [
                        <?= $tkr ?>,
                        <?= $tpm ?>,
                        <?= $dkv ?>,
                        <?= $tkj ?>
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
    <script>
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                document.body.classList.remove('modal-open');
                document.body.style = '';
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            });
        });
    </script>
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