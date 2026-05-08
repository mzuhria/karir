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
        <a href="View_siswa.php"><i class="bi bi-people"></i> Data Siswa</a>
        <a href="View_guru.php" class="active"><i class="bi bi-person-badge"></i> Data Guru</a>

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
        <div class="container-fluid">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Data Guru BP</h5>
                        <input type="text" id="searchGuru" class="form-control w-25" placeholder="Cari guru...">
                    </div>
                    <div class="card-body table-responsive">
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

                            <tbody id="tableGuru">
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
            </div>

            <!-- 🔥 MODAL OUTPUT -->
            <?= $modals ?>

        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("searchGuru")
            .addEventListener("keyup", function() {

                let keyword = this.value.toLowerCase();

                let rows = document.querySelectorAll("#tableGuru tr");

                rows.forEach(function(row) {

                    let text = row.innerText.toLowerCase();

                    if (text.includes(keyword)) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }

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