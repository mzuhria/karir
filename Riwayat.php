<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'guru_bp') {
    header("Location: Login_Admin.php");
    exit;
}

include "koneksi.php";
$id_admin = $_SESSION['id_admin'];
$nama_guru = $_SESSION['nama_guru'] ?? 'Guru BP';
$username  = $_SESSION['username'] ?? '-';
$where = "WHERE siswa.id_admin='$id_admin'";

if (isset($_GET['search']) && $_GET['search'] != '') {
    $search = mysqli_real_escape_string($conn, $_GET['search']);

    $where .= " AND (
        siswa.nama LIKE '%$search%' OR
        siswa.subkelas LIKE '%$search%' OR
        karir.nama_karir LIKE '%$search%' OR
        karir.kategori LIKE '%$search%'
    )";
}

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
";

$data = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            font-family: 'Poppins', sans-serif;
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            background: #343a40;
            color: white;
        }

        .sidebar a {
            color: #ddd;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
        }

        .sidebar a:hover {
            background: #495057;
            color: white;
        }

        /* CONTENT */
        .content {
            margin-left: 240px;
            padding: 20px;
        }

        /* TOPBAR */
        .topbar {
            background: #6f42c1;
            padding: 12px 20px;
            color: white;
            border-radius: 8px;
        }

        .card-custom {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
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
        <a href="Kelola_siswa.php"><i class="bi bi-people me-2"></i>Kelola Siswa</a>
        <a href="Kelola_soal.php"><i class="bi bi-ui-checks me-2"></i>Kelola Soal</a>
        <a href="Kelola_karir.php"><i class="bi bi-briefcase me-2"></i>Kelola Karir</a>
        <a href="Riwayat.php" class="bg-secondary text-white"><i class="bi bi-clock-history me-2"></i>Riwayat</a>
        <hr>
        <a href="Logout_Admin.php" class="text-danger"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <!-- TOPBAR -->
        <div class="topbar mb-4">
            <strong>Home</strong> / Riwayat
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

            </div>
        </div>

    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>