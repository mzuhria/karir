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
$data = mysqli_query($conn, "
    SELECT 
        admin.*,
        (SELECT COUNT(*) FROM siswa WHERE siswa.id_admin = admin.id_admin) AS jumlah_siswa,
        (SELECT COUNT(*) FROM kuisioner WHERE kuisioner.id_admin = admin.id_admin) AS jumlah_soal
    FROM admin
    WHERE role='guru_bp'
    ORDER BY id_admin DESC
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
    </style>

</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h4 class="text-center py-3">KEPSEK PANEL</h4>
        <hr>

        <a href="Dashboard_kepsek.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="View_siswa.php"><i class="bi bi-people"></i> Data Siswa</a>
        <a href="View_guru.php" class="active"><i class="bi bi-person-badge"></i> Data Guru</a>

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
        <div class="container-fluid">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">📋 Data Guru BP</h5>
                        <input type="text" class="form-control w-25" placeholder="🔍 Cari guru...">
                    </div>

                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Guru</th>
                                <th>Username</th>
                                <th>No HP</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $no = 1;
                            $modals = "";

                            while ($row = mysqli_fetch_assoc($data)) {

                                // 🔹 Ambil siswa bimbingan
                                $siswa = mysqli_query($conn, "
        SELECT nama, kelas, jurusan, subkelas 
        FROM siswa 
        WHERE id_admin = '{$row['id_admin']}'
    ");
                            ?>

                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nama_guru'] ?></td>
                                    <td><?= $row['username'] ?></td>
                                    <td><?= $row['no_hp'] ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detail<?= $row['id_admin'] ?>">
                                            Detail
                                        </button>
                                    </td>
                                </tr>

                                <?php
                                // 🔥 MODAL DETAIL + SISWA
                                $modals .= '
<div class="modal fade" id="detail' . $row['id_admin'] . '" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detail Guru</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p><b>Nama:</b> ' . $row['nama_guru'] . '</p>
                <p><b>Username:</b> ' . $row['username'] . '</p>
                <p><b>No HP:</b> ' . $row['no_hp'] . '</p>
                <p><b>Jumlah Siswa:</b> ' . $row['jumlah_siswa'] . '</p>
                <p><b>Jumlah Soal Dibuat:</b> ' . $row['jumlah_soal'] . '</p>

                <hr>

                <h6>📋 Daftar Siswa Bimbingan</h6>

                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
';

                                // 🔹 LOOP SISWA
                                $no_siswa = 1;
                                $ada = false;

                                while ($s = mysqli_fetch_assoc($siswa)) {
                                    $ada = true;
                                    $modals .= '
        <tr>
            <td>' . $no_siswa++ . '</td>
            <td>' . $s['nama'] . '</td>
            <td>' . $s['kelas'] . ' ' . $s['jurusan'] . ' ' . $s['subkelas'] . '</td>
        </tr>';
                                }

                                // 🔹 Kalau kosong
                                if (!$ada) {
                                    $modals .= '
        <tr>
            <td colspan="3" class="text-center text-muted">
                Belum ada siswa
            </td>
        </tr>';
                                }

                                // 🔹 PENUTUP
                                $modals .= '
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>';
                                ?>

                            <?php } ?>
                        </tbody>

                    </table>

                </div>
            </div>

            <!-- 🔥 MODAL OUTPUT -->
            <?= $modals ?>

        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>