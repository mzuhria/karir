<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: Login_Admin.php");
    exit;
}

$nama_admin = $_SESSION['nama_guru'] ?? 'Admin';

// HAPUS ADMIN
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    // CEGAH HAPUS DIRI SENDIRI
    if ($id == $_SESSION['id_admin']) {

        echo "
        <script>
            alert('Anda tidak bisa menghapus akun sendiri!');
            window.location='Data_admin.php';
        </script>
        ";

        exit;
    }

    mysqli_query($conn, "
        DELETE FROM admin
        WHERE id_admin='$id'
    ");

    header("Location: Data_admin.php");
    exit;
}

// ambil data admin
$data = mysqli_query($conn, "
    SELECT * FROM admin
    ORDER BY FIELD(role, 'kepsek', 'admin', 'guru_bp')
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f1f5f9;
            overflow-x: hidden;
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
            border-radius: 10px;
            margin: 6px 12px;
            text-decoration: none;
            transition: 0.2s;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        /* CONTENT */
        .content {
            margin-left: 240px;
            padding: 25px;
            transition: all 0.3s ease;
        }

        /* NAVBAR */
        .topbar {
            margin-left: 240px;
            transition: all 0.3s ease;
        }

        /* TOGGLE BUTTON */
        .toggle-btn {
            display: none;
            border: none;
            background: none;
            font-size: 28px;
        }

        /* MOBILE */
        @media (max-width: 768px) {

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
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
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
        <a href="Riwayat_Admin.php"><i class="bi bi-clock-history"></i> Riwayat Hasil</a>
    </div>

    <!-- NAVBAR -->
    <nav class="navbar navbar-light bg-light px-3 d-flex justify-content-between topbar shadow-sm">

        <!-- tombol toggle -->
        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>

        <!-- profile -->
        <div class="dropdown ms-auto">
            <a class="d-flex align-items-center text-decoration-none dropdown-toggle"
                href="#" data-bs-toggle="dropdown">

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
                    <a class="dropdown-item logout text-danger"
                        href="Logout_Admin.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

    </nav>

    <!-- content -->
    <div class="content">
        <div class="mb-4">
            <strong>Home</strong> / Data Admin
        </div>
        <div class="card shadow-sm">
            <div class="card-body">

                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no = 1;
                        while ($row = mysqli_fetch_assoc($data)) { ?>
                            <tr>

                                <!-- NO -->
                                <td><?= $no++ ?></td>

                                <!-- NAMA -->
                                <td class="text-start fw-semibold"><?= $row['nama_guru'] ?></td>

                                <!-- USERNAME -->
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= $row['username'] ?>
                                    </span>
                                </td>

                                <!-- AKSI -->
                                <td>
                                    <a href="#"
                                        class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal"
                                        data-id="<?= $row['id_admin'] ?>"
                                        data-nama="<?= $row['nama_guru'] ?>"
                                        data-username="<?= $row['username'] ?>"
                                        data-kelas="<?= $row['kelas_akses'] ?>"
                                        data-nohp="<?= $row['no_hp'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="Data_admin.php?hapus=<?= $row['id_admin'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus user ini?')">

                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>

                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>
        </div>

    </div>

    <!-- MODAL EDIT -->
    <div class="modal fade" id="editModal">
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
                            <select name="kelas_akses" id="edit_kelas_akses" class="form-select" required>
                                <option value="">Pilih</option>
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>No HP</label>
                            <input type="text" name="no_hp" id="edit_nohp" class="form-control">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const editModal = document.getElementById('editModal');

        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            document.getElementById('edit_id').value = button.getAttribute('data-id');
            document.getElementById('edit_nama').value = button.getAttribute('data-nama');
            document.getElementById('edit_kelas_akses').value = button.getAttribute('data-kelas');
            document.getElementById('edit_username').value = button.getAttribute('data-username');
            document.getElementById('edit_nohp').value = button.getAttribute('data-nohp');
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

    <script>
        function toggleMenu() {
            var menu = document.getElementById("submenuUser");

            if (menu.style.display === "none") {
                menu.style.display = "block";
            } else {
                menu.style.display = "none";
            }
        }

        function toggleSidebar() {
            document.querySelector(".sidebar").classList.toggle("active");
        }

        // otomatis close ketika klik luar sidebar
        document.addEventListener("click", function(event) {

            const sidebar = document.querySelector(".sidebar");
            const toggleBtn = document.querySelector(".toggle-btn");

            // jika klik bukan sidebar & bukan tombol
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