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

        /* SIDEBAR */
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

        /* HILANGKAN UNDERLINE */
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

        /* ACTIVE MENU */
        .sidebar a.active {
            background: linear-gradient(135deg, #4f46e5, #22c55e);
            color: white !important;
            font-weight: 600;
            border-radius: 10px;
        }

        /* CONTENT */
        .content {
            margin-left: 240px;
            padding: 30px;
        }

        /* NAVBAR */
        .navbar {
            margin-left: 240px;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* CARD MODERN */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        /* TEXT */
        h3 {
            font-weight: 600;
        }

        /* ICON BOX */
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
                    <a class="dropdown-item logout text-danger" href="Logout_Admin.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
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
                                        <button class="btn btn-info btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detail<?= $row['id_siswa'] ?>">
                                            Detail
                                        </button>
                                    </td>
                                </tr>

                                <?php
                                // 🔥 ambil jumlah jawaban
                                $id = $row['id_siswa'];
                                $jawaban = mysqli_fetch_assoc(mysqli_query(
                                    $conn,
                                    "SELECT COUNT(*) as total FROM jawaban WHERE id_siswa='$id'"
                                ))['total'];

                                // 🔥 simpan modal (DI LUAR TABLE)
                                $modals .= '
<div class="modal fade" id="detail' . $row['id_siswa'] . '" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Detail Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p><b>Nama:</b> ' . $row['nama'] . '</p>
        <p><b>Kelas:</b> ' . $row['kelas'] . '</p>
        <p><b>Jurusan:</b> ' . $row['jurusan'] . '</p>
        <p><b>Subkelas:</b> ' . $row['subkelas'] . '</p>
        <p><b>Email:</b> ' . $row['email'] . '</p>
        <p><b>Status:</b> ' . $row['status'] . '</p>
        <p><b>Guru BP:</b> ' . ($row['nama_guru'] ?? 'Belum ada') . '</p>
        <p><b>Skor:</b> ' . ($row['skor'] ?? '-') . '</p>
        <p><b>Rekomendasi:</b> ' . ($row['nama_karir'] ?? 'Belum Ada') . '</p>
        <p><b>Jumlah Jawaban:</b> ' . $jawaban . '</p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>

    </div>
  </div>
</div>
';
                                ?>

                            <?php } ?>
                        </tbody>

                    </table>
                </div>
            </div>
            <?= $modals ?>
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
</body>

</html>