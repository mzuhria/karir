<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: Login_Admin.php");
    exit;
}
$nama_admin = $_SESSION['nama_guru'] ?? 'Admin';

// modal edit
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $pertanyaan = $_POST['pertanyaan'];
    $kategori = $_POST['kategori'];
    $jurusan = $_POST['jurusan'];

    mysqli_query($conn, "
        UPDATE kuisioner SET
        pertanyaan='$pertanyaan',
        kategori='$kategori',
        jurusan='$jurusan'
        WHERE id_soal='$id'
    ");

    header("Location: Data_soal.php");
    exit;
}

// modal delete
if (isset($_POST['hapus'])) {
    $id = $_POST['id'];

    mysqli_query($conn, "DELETE FROM kuisioner WHERE id_soal='$id'");

    header("Location: Data_soal.php");
    exit;
}

// Search
// Search
$search = $_GET['search'] ?? '';

$where = "WHERE 1=1";

if (!empty($search)) {

    $search_safe = mysqli_real_escape_string($conn, $search);

    $where .= " AND (
        admin.nama_guru LIKE '%$search_safe%' OR
        kuisioner.kategori LIKE '%$search_safe%' OR
        kuisioner.jurusan LIKE '%$search_safe%'
    )";
}

// Pagination
$limit = 5;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$start = ($page - 1) * $limit;

// Query Data
$data = mysqli_query($conn, "
    SELECT 
        kuisioner.id_soal,
        kuisioner.pertanyaan,
        kuisioner.kategori,
        kuisioner.jurusan,
        admin.nama_guru
    FROM kuisioner
    LEFT JOIN admin 
        ON kuisioner.id_admin = admin.id_admin

    $where

    ORDER BY kuisioner.id_soal DESC

    LIMIT $start, $limit
");

// Total Data
$total_data_query = mysqli_query($conn, "
    SELECT COUNT(*) as total
    FROM kuisioner
    LEFT JOIN admin 
        ON kuisioner.id_admin = admin.id_admin

    $where
");

$total_data = mysqli_fetch_assoc($total_data_query)['total'];

$total_page = ceil($total_data / $limit);
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
        .table-responsive {
            overflow-x: auto;
        }

        table {
            min-width: 900px;
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
        <a href="Data_soal.php" class="bg-secondary text-white"><i class="bi bi-file-text"></i> Data Soal</a>
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
            <strong>Home</strong> / Data Soal
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex">
                    <form method="GET" class="d-flex align-items-center mb-3 ms-auto" style="max-width:300px; width:100%;">

                        <input type="text"
                            name="search"
                            value="<?= htmlspecialchars($search) ?>"
                            class="form-control me-2"
                            placeholder="Cari Guru BP, Kategori, Jurusan...">

                        <button class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>

                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center">

                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Soal</th>
                                <th>Kategori</th>
                                <th>Jurusan</th>
                                <th>Guru BP</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            if (mysqli_num_rows($data) > 0) {
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($data)) { ?>
                                    <tr>

                                        <td><?= $no++ ?></td>

                                        <td class="text-start fw-semibold">
                                            <?= $row['pertanyaan'] ?>
                                        </td>

                                        <td>
                                            <span class="badge bg-info">
                                                <?= $row['kategori'] ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                <?= $row['jurusan'] ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge bg-success">
                                                <?= $row['nama_guru'] ?? 'Belum Ada' ?>
                                            </span>
                                        </td>

                                        <td>
                                            <button
                                                class="btn btn-warning btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editModal"
                                                data-id="<?= $row['id_soal'] ?>"
                                                data-pertanyaan="<?= htmlspecialchars($row['pertanyaan']) ?>"
                                                data-kategori="<?= $row['kategori'] ?>"
                                                data-jurusan="<?= $row['jurusan'] ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <button
                                                class="btn btn-danger btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-id="<?= $row['id_soal'] ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>

                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center text-muted'>Data tidak ada</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <nav class="mt-3">
                    <ul class="pagination justify-content-center">

                        <!-- tombol prev -->
                        <?php if ($page > 1) { ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">
                                    Previous
                                </a>
                            </li>
                        <?php } ?>

                        <!-- nomor halaman -->
                        <?php for ($i = 1; $i <= $total_page; $i++) { ?>

                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">

                                <a class="page-link"
                                    href="?search=<?= urlencode($search) ?>&page=<?= $i ?>">

                                    <?= $i ?>

                                </a>
                            </li>

                        <?php } ?>

                        <!-- tombol next -->
                        <?php if ($page < $total_page) { ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">
                                    Next
                                </a>
                            </li>
                        <?php } ?>

                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="update_soal.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Soal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="id" id="edit_id">

                        <div class="mb-3">
                            <label>Pertanyaan</label>
                            <textarea name="pertanyaan" id="edit_pertanyaan" class="form-control" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Kategori</label>
                            <select name="kategori" id="edit_kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Realistic">Realistic</option>
                                <option value="Investigative">Investigative</option>
                                <option value="Artistic">Artistic</option>
                                <option value="Social">Social</option>
                                <option value="Enterprising">Enterprising</option>
                                <option value="Conventional">Conventional</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Jurusan</label>
                            <select name="jurusan" id="edit_jurusan" class="form-select" required>
                                <option value="">-- Pilih Jurusan --</option>
                                <option value="TPM">TPM</option>
                                <option value="DKV">DKV</option>
                                <option value="TKR">TKR</option>
                                <option value="TKJ">TKJ</option>
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="update" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form method="POST">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center">
                        <input type="hidden" name="id" id="delete_id">

                        <i class="bi bi-exclamation-triangle fs-1 text-danger"></i>
                        <p class="mt-3">
                            Yakin ingin menghapus data ini?
                        </p>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" name="hapus" class="btn btn-danger">
                            Hapus
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
        const editModal = document.getElementById('editModal');

        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            document.getElementById('edit_id').value = button.getAttribute('data-id');
            document.getElementById('edit_pertanyaan').value = button.getAttribute('data-pertanyaan');
            document.getElementById('edit_kategori').value = button.getAttribute('data-kategori');
            document.getElementById('edit_jurusan').value = button.getAttribute('data-jurusan');
        });
    </script>
    <script>
        const deleteModal = document.getElementById('deleteModal');

        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            document.getElementById('delete_id').value = button.getAttribute('data-id');
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