<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: Login_Admin.php");
    exit;
}

// AMBIL DATA SESSION
$id_admin   = $_SESSION['id_admin'];
$kelas_akses = $_SESSION['kelas_akses'];
$nama_admin = $_SESSION['nama_guru'] ?? 'Admin'; // bisa kamu ganti nanti
$username   = $_SESSION['username'] ?? '-';
$data = mysqli_query($conn, "SELECT * FROM admin");
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

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            transform: translateX(5px);
        }

        .sidebar a.active {
            background: #38bdf8;
            color: #0f172a !important;
            font-weight: 600;
        }

        .content {
            margin-left: 240px;
            padding: 30px;
        }

        .navbar {
            margin-left: 240px;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        h3 {
            font-weight: 600;
        }

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

        .logout {
            color: #ef4444 !important;
        }

        .table-clean {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .table-clean thead {
            background: #f8fafc;
        }

        .table-clean th {
            font-weight: 600;
            font-size: 14px;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            padding: 12px;
        }

        .table-clean td {
            padding: 12px;
            font-size: 14px;
            color: #4b5563;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-clean tbody tr:hover {
            background: #f9fafb;
        }

        .btn-clean {
            background: #ffb700;
            border: none;
            color: #000000;
            padding: 5px 10px;
            font-size: 13px;
            border-radius: 6px;
        }

        .btn-clean:hover {
            background: #d1d5db;
        }

        .badge-clean {
            background: #eb8c27;
            color: #ffffff;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
        }

        #submenuUser a {
            font-size: 14px;
            padding-left: 30px;
        }
    </style>
</head>

<body>

    <!-- 🔹 SIDEBAR -->
    <div class="sidebar">
        <h4 class="text-center py-3">ADMIN PANEL</h4>
        <hr>
        <a href="Admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
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
        <a href="Data_soal.php" class="bi bi-file-text"></i> Data Soal</a>
        <a href="Riwayat.php"><i class="bi bi-clock-history"></i> Riwayat Hasil</a>
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
        <div class="topbar mb-4">
            <strong>Home</strong> / Data Admin
        </div>
        <div class="table-clean mt-3">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $no = 1;
                    while ($row = mysqli_fetch_assoc($data)) { ?>
                        <tr>
                            <td><?= $no++ ?></td>

                            <td><?= $row['nama_guru'] ?></td>

                            <td>
                                <span class="badge-clean">
                                    <?= $row['username'] ?>
                                </span>
                            </td>

                            <td>
                                <a href="#"
                                    class="btn btn-clean"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-id="<?= $row['id_admin'] ?>"
                                    data-nama="<?= $row['nama_guru'] ?>"
                                    data-username="<?= $row['username'] ?>"
                                    data-kelas="<?= $row['kelas_akses'] ?>">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="modal fade" id="editModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <form action="Process_update_user.php" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <input type="hidden" name="id" id="edit_id">

                                <div class="mb-3">
                                    <label>Nama</label>
                                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label>Kelas Akses</label>
                                    <select name="kelas_akses" id="edit_kelas_akses" class="form-control" required>
                                        <option value="">Pilih Kelas</option>
                                        <option value="X">X</option>
                                        <option value="XI">XI</option>
                                        <option value="XII">XII</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label>Username</label>
                                    <input type="text" name="username" id="edit_username" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label>Password Baru</label>
                                    <input type="password" name="password" class="form-control">
                                    <small class="text-muted">Kosongkan jika tidak diubah</small>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const editModal = document.getElementById('editModal');

        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            document.getElementById('edit_id').value = button.getAttribute('data-id');
            document.getElementById('edit_nama').value = button.getAttribute('data-nama');
            document.getElementById('edit_kelas_akses').value = button.getAttribute('data-kelas');
            document.getElementById('edit_username').value = button.getAttribute('data-username');
        });
    </script>
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
</body>

</html>