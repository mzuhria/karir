<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: Login_Admin.php");
    exit;
}

$nama_admin = $_SESSION['nama_guru'] ?? 'Admin';

$search = $_GET['search'] ?? '';

$where = "WHERE 1=1";
//search
if (!empty($search)) {
    $s = mysqli_real_escape_string($conn, $search);

    $where .= " AND (
        siswa.nama LIKE '%$s%' OR
        siswa.jurusan LIKE '%$s%'
    )";
}

// DELETE SATU
if (isset($_POST['hapus'])) {
    $id = intval($_POST['id']);
    mysqli_query($conn, "DELETE FROM hasil WHERE id_hasil='$id'");
    header("Location: Riwayat_Admin.php");
    exit;
}

// DELETE BANYAK
if (isset($_POST['hapus_banyak'])) {
    if (!empty($_POST['ids'])) {

        $ids = implode(',', array_map('intval', $_POST['ids']));
        mysqli_query($conn, "DELETE FROM hasil WHERE id_hasil IN ($ids)");

        header("Location: Riwayat_Admin.php");
        exit;
    }
}

$data = mysqli_query($conn, "
    SELECT 
        hasil.id_hasil,
        siswa.nama,
        siswa.jurusan,
        karir.nama_karir,
        karir.kategori,
        hasil.skor,
        hasil.tanggal,
        admin.nama_guru
    FROM hasil
    LEFT JOIN siswa ON hasil.id_siswa = siswa.id_siswa
    LEFT JOIN karir ON hasil.id_karir = karir.id_karir
    LEFT JOIN admin ON siswa.id_admin = admin.id_admin
    $where
    ORDER BY hasil.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f1f5f9;
        }

        /* Sidebar tetap */
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
            text-decoration: none;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .content {
            margin-left: 240px;
            padding: 30px;
        }

        .navbar {
            margin-left: 240px;
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
        <a href="Data_soal.php"><i class="bi bi-file-text"></i> Data Soal</a>
        <a href="Riwayat_Admin.php" class="bg-secondary text-white"><i class="bi bi-clock-history"></i> Riwayat Hasil</a>
    </div>

    <!-- NAVBAR -->
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
                    <a class="dropdown-item text-danger" href="Logout_Admin.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="content">
        <div class="topbar mb-3">
            <strong>Home</strong> / Riwayat
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <form method="GET" class="d-flex" style="width:250px;">
                <input type="text" name="search"
                    class="form-control form-control-sm me-2"
                    placeholder="Cari...">

                <button class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i>
                </button>
            </form>

        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST">
                    <!-- DELETE -->
                    <div class="mb-2">
                        <button type="submit" name="hapus_banyak"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Hapus data terpilih?')">
                            <i class="bi bi-trash"></i> Hapus Terpilih
                        </button>
                    </div>

                    <!-- TABLE -->
                    <table class="table table-bordered table-hover align-middle text-center">

                        <thead class="table-light">
                            <tr>
                                <th><input type="checkbox" id="checkAll"></th>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jurusan</th>
                                <th>Nama Karir</th>
                                <th>Kategori</th>
                                <th>Skor</th>
                                <th>Tanggal</th>
                                <th>Guru BP</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            if (mysqli_num_rows($data) > 0) {
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($data)) { ?>
                                    <tr>

                                        <td>
                                            <input type="checkbox"
                                                class="checkItem"
                                                name="ids[]"
                                                value="<?= $row['id_hasil'] ?>">
                                        </td>

                                        <td><?= $no++ ?></td>
                                        <td class="text-start"><?= $row['nama'] ?></td>
                                        <td><?= $row['jurusan'] ?></td>

                                        <td><span class="badge bg-success"><?= $row['nama_karir'] ?></span></td>
                                        <td><span class="badge bg-secondary"><?= $row['kategori'] ?></span></td>
                                        <td><span class="badge bg-dark"><?= $row['skor'] ?></span></td>
                                        <td><?= $row['tanggal'] ?></td>

                                        <td>
                                            <span class="badge bg-info text-dark">
                                                <?= $row['nama_guru'] ?? '-' ?>
                                            </span>
                                        </td>

                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        Data tidak ada
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>

                    </table>

                </form>

            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form method="POST">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Hapus Data</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center">
                        <input type="hidden" name="id" id="delete_id">
                        <p>Yakin ingin menghapus riwayat ini?</p>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="hapus" class="btn btn-danger">Hapus</button>
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
        const deleteModal = document.getElementById('deleteModal');

        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('delete_id').value = button.getAttribute('data-id');
        });
    </script>
    <script>
        const checkAll = document.getElementById('checkAll');
        const items = document.querySelectorAll('.checkItem');

        checkAll.addEventListener('click', function() {
            items.forEach(item => item.checked = this.checked);
        });
    </script>

</body>

</html>