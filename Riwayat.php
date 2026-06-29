<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'guru_bp') {
    header("Location: Login_Admin.php");
    exit;
}

include "koneksi.php";
$id_admin = $_SESSION['id_admin'];
$nama_guru = $_SESSION['nama_guru'] ?? 'Guru BP';
$where = "WHERE siswa.id_admin='$id_admin'";

// SEARCH
if (isset($_GET['search']) && $_GET['search'] != '') {

    $search = mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );

    $where .= " AND (
        siswa.nama LIKE '%$search%'
        OR siswa.jurusan LIKE '%$search%'
        OR karir.nama_karir LIKE '%$search%'
        OR karir.kategori LIKE '%$search%'
    )";
}

/* PAGINATION */
$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$start = ($page - 1) * $limit;

$no = $start + 1;

/* TOTAL DATA */
$total_query = mysqli_query($conn, "
SELECT COUNT(*) as total
FROM hasil
JOIN siswa ON siswa.id_siswa = hasil.id_siswa
JOIN karir ON karir.id_karir = hasil.id_karir
$where
");

$total_data = mysqli_fetch_assoc($total_query)['total'];

$total_pages = ceil($total_data / $limit);

$query = "
SELECT 
    siswa.nama,
    siswa.jurusan,
    siswa.kelas,
    siswa.subkelas,
    karir.nama_karir,
    karir.kategori,
    hasil.skor,
    hasil.tanggal
FROM hasil
JOIN siswa ON siswa.id_siswa = hasil.id_siswa
JOIN karir ON karir.id_karir = hasil.id_karir
$where
ORDER BY hasil.tanggal DESC
LIMIT $start, $limit
";

$data = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #343a40;
            color: white;
            transition: all 0.3s ease;
            z-index: 999;
            overflow-y: auto;
        }

        .sidebar a {
            color: #ddd;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            transition: 0.2s;
        }

        .sidebar a:hover {
            background: #495057;
            color: white;
        }

        /* OVERLAY */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            display: none;
        }

        .overlay.show {
            display: block;
        }

        /* CONTENT */
        .content {
            margin-left: 240px;
            padding: 20px;
            transition: 0.3s;
        }

        /* TOPBAR */
        .topbar {
            background: #6f42c1;
            padding: 14px 18px;
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* CARD */
        .card-custom {
            border-radius: 14px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        /* TOGGLE */
        #toggleSidebar {
            display: none;
        }

        /* MOBILE */
        @media (max-width: 768px) {

            #toggleSidebar {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 42px;
                height: 42px;
                border-radius: 10px;
                border: none;
            }

            .sidebar {
                left: -240px;
                width: 240px;
            }

            .sidebar.show {
                left: 0;
            }

            .content {
                margin-left: 0;
                width: 100%;
                padding: 12px;
            }

            .topbar {
                font-size: 14px;
                padding: 12px;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: stretch !important;
                gap: 10px;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .table {
                min-width: 900px;
            }

            .table th,
            .table td {
                font-size: 13px;
                white-space: nowrap;
            }

            .pagination {
                flex-wrap: wrap;
                gap: 5px;
            }

            .modal-dialog {
                margin: 10px;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">

        <!-- PROFIL -->
        <div class="py-3 border-bottom text-center">
            <i class="bi bi-person-circle fs-3"></i>
            <div style="font-size:14px;">
                <?= htmlspecialchars($nama_guru); ?>
            </div>
        </div>

        <a href="Dashboard_gurubp.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="Kelola_soal.php"><i class="bi bi-ui-checks me-2"></i>Kelola Soal</a>
        <a href="Kelola_karir.php"><i class="bi bi-briefcase me-2"></i>Kelola Karir</a>
        <a href="Kelola_siswa.php"><i class="bi bi-people me-2"></i>Kelola Akun Siswa</a>
        <a href="Riwayat.php" class="bg-secondary text-white"><i class="bi bi-clock-history me-2"></i>Riwayat</a>
        <hr>
        <a href="Logout_Admin.php" class="text-danger"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
    </div>

    <div class="overlay" id="overlay"></div>

    <!-- CONTENT -->
    <div class="content">

        <!-- TOPBAR -->
        <div class="topbar mb-4">

            <button class="btn btn-light" id="toggleSidebar">
                <i class="bi bi-list"></i>
            </button>

            <div>
                <strong>Home</strong> / Riwayat
            </div>

        </div>

        <!-- CARD -->
        <div class="card card-custom">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h5 class="mb-0">Riwayat</h5>

                    <div class="d-flex gap-2">
                        <form method="GET" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Cari nama / jurusan..."
                                value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">

                            <button type="submit" class="btn btn-primary btn-sm">
                                Search
                            </button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama</th>
                                <th>Jurusan</th>
                                <th>Nama Karir</th>
                                <th>Kategori</th>
                                <th>Skor</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = mysqli_fetch_assoc($data)) { ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nama'] ?></td>

                                    <td>
                                        <?= $row['kelas'] . " " . $row['jurusan'] . " " . $row['subkelas']; ?>
                                    </td>

                                    <!-- TOP 1 -->
                                    <td>
                                        <span class="badge bg-success"><?= $row['nama_karir'] ?></span>
                                    </td>

                                    <!-- TOP 2 -->
                                    <td>
                                        <span class="badge bg-secondary"><?= $row['kategori'] ?></span>
                                    </td>

                                    <!-- TOP 3 -->
                                    <td>
                                        <span class="badge bg-dark"><?= $row['skor'] ?></span>
                                    </td>

                                    <td><?= $row['tanggal'] ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <nav class="mt-3">
                    <ul class="pagination justify-content-center flex-wrap">

                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?page=<?= $page - 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>">
                                    Previous
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>

                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link"
                                    href="?page=<?= $i ?>&search=<?= urlencode($_GET['search'] ?? '') ?>">
                                    <?= $i ?>
                                </a>
                            </li>

                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?page=<?= $page + 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>">
                                    Next
                                </a>
                            </li>
                        <?php endif; ?>

                    </ul>
                </nav>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('overlay');

        toggleBtn.addEventListener('click', () => {

            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }

        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });

        document.querySelectorAll(".sidebar a").forEach(link => {
            link.addEventListener("click", function() {

                if (window.innerWidth <= 768) {
                    sidebar.classList.remove("show");
                    overlay.classList.remove("show");
                }

            });
        });
    </script>
</body>

</html>