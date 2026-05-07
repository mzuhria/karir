<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'guru_bp') {
    header("Location: Login_Admin.php");
    exit;
}

include "koneksi.php";
$id_admin = $_SESSION['id_admin'];
$nama_guru = $_SESSION['nama_guru'] ?? 'Guru BP';
/* HAPUS KARIR */
if (isset($_GET['hapus'])) {

    $id = intval($_GET['hapus']);

    mysqli_query($conn, "DELETE FROM karir 
    WHERE id_karir='$id' AND id_admin='$id_admin'");

    header("Location: Kelola_karir.php");
    exit;
}

// modal edit
if (isset($_POST['edit'])) {

    $id = $_POST['id_karir'];
    $nama = $_POST['nama_karir'];
    $kategori = $_POST['kategori'];
    $jurusan = $_POST['jurusan'];
    $deskripsi = $_POST['deskripsi'];

    mysqli_query($conn, "UPDATE karir SET 
        nama_karir='$nama',
        kategori='$kategori',
        jurusan='$jurusan',
        deskripsi='$deskripsi'
        WHERE id_karir='$id' AND id_admin='$id_admin'
    ");

    header("Location: Kelola_karir.php");
    exit;
}

//modal tambah
if (isset($_POST['tambah'])) {

    $nama = $_POST['nama_karir'];
    $kategori = $_POST['kategori'];
    $jurusan = $_POST['jurusan'];
    $deskripsi = $_POST['deskripsi'];

    mysqli_query($conn, "INSERT INTO karir
    (nama_karir, kategori, jurusan, deskripsi, id_admin)
    VALUES ('$nama','$kategori','$jurusan','$deskripsi','$id_admin')");


    header("Location: Kelola_karir.php");
    exit;
}

/* PAGINATION */
$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$start = ($page - 1) * $limit;

$no = $start + 1;

/* SEARCH */
$search = $_GET['search'] ?? '';

/* WHERE */
$where = "WHERE id_admin='$id_admin'";

if (!empty($search)) {

    $search_safe = mysqli_real_escape_string($conn, $search);

    $where .= " AND (
        nama_karir LIKE '%$search_safe%' OR
        kategori LIKE '%$search_safe%' OR
        jurusan LIKE '%$search_safe%' OR
        deskripsi LIKE '%$search_safe%'
    )";
}

/* TOTAL DATA */
$total_query = mysqli_query($conn, "
    SELECT COUNT(*) as total
    FROM karir
    $where
");

$total_data = mysqli_fetch_assoc($total_query)['total'];

$total_pages = ceil($total_data / $limit);

/* QUERY DATA */
$query = mysqli_query($conn, "
    SELECT *
    FROM karir
    $where
    ORDER BY id_karir DESC
    LIMIT $start, $limit
");
?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Karir</title>
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

        /* TABLE */
        .table td,
        .table th {
            vertical-align: middle;
        }

        /* MOBILE */
        @media (max-width: 768px) {

            body {
                overflow-x: hidden;
            }

            /* TOGGLE */
            #toggleSidebar {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 42px;
                height: 42px;
                border-radius: 10px;
                border: none;
            }

            /* SIDEBAR */
            .sidebar {
                left: -240px;
                width: 240px;
            }

            .sidebar.show {
                left: 0;
            }

            /* CONTENT */
            .content {
                margin-left: 0;
                width: 100%;
                padding: 12px;
            }

            /* TOPBAR */
            .topbar {
                font-size: 14px;
                padding: 12px;
            }

            /* HEADER FLEX */
            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: stretch !important;
                gap: 10px;
            }

            .d-flex.align-items-center.gap-2 {
                flex-direction: column;
                width: 100%;
            }

            .d-flex.align-items-center.gap-2 form {
                width: 100%;
            }

            .d-flex.align-items-center.gap-2 input,
            .d-flex.align-items-center.gap-2 button {
                width: 100% !important;
            }

            /* TABLE */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table {
                min-width: 950px;
            }

            .table th,
            .table td {
                font-size: 13px;
                white-space: nowrap;
            }

            /* DESKRIPSI */
            .table td:nth-child(5) {
                min-width: 250px;
                white-space: normal;
            }

            /* AKSI */
            .table td:last-child {
                min-width: 170px;
            }

            /* MODAL */
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
        <a href="Kelola_siswa.php"><i class="bi bi-people me-2"></i>Kelola Siswa</a>
        <a href="Kelola_soal.php"><i class="bi bi-ui-checks me-2"></i>Kelola Soal</a>
        <a href="Kelola_karir.php" class="bg-secondary text-white"><i class="bi bi-briefcase me-2"></i>Kelola Karir</a>
        <a href="Riwayat.php"><i class="bi bi-clock-history me-2"></i>Riwayat</a>
        <hr>
        <a href="Logout_Admin.php" class="text-danger"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
    </div>

    <div class="overlay" id="overlay"></div>

    <!-- CONTENT -->
    <div class="content">

        <div class="topbar mb-4">

            <button class="btn btn-light" id="toggleSidebar">
                <i class="bi bi-list"></i>
            </button>

            <div>
                <strong>Home</strong> / Kelola Karir
            </div>

        </div>

        <div class="card card-custom">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h5 class="mb-0">Daftar Karir</h5>

                    <div class="d-flex align-items-center gap-2">

                        <!-- SEARCH -->
                        <form method="GET" class="d-flex align-items-center">

                            <input type="text"
                                name="search"
                                value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>"
                                class="form-control form-control-sm me-2"
                                placeholder="Cari karir..."
                                class="form-control form-control-sm me-2"">

                            <button class=" btn btn-primary btn-sm">
                            <i class="bi bi-search"></i>
                            </button>

                        </form>

                        <!-- IMPORT -->
                        <button class="btn btn-info btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalImport">

                            <i class="bi bi-file-earmark-excel"></i>
                            Import
                        </button>

                        <!-- TAMBAH -->
                        <button class="btn btn-success btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalTambah">

                            <i class="bi bi-plus"></i>
                            Tambah
                        </button>

                    </div>

                </div>

                <!-- Tabel Karir -->
                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">

                            <tr>

                                <th width="5%">No</th>
                                <th>Nama Karir</th>
                                <th>Kategori</th>
                                <th>Jurusan</th>
                                <th>Deskripsi</th>
                                <th width="15%">Aksi</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php
                            $no = 1;

                            if (mysqli_num_rows($query) > 0) {

                                while ($row = mysqli_fetch_assoc($query)) {
                            ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $row['nama_karir']; ?></td>

                                        <td>
                                            <span class="badge bg-primary">
                                                <?= $row['kategori']; ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge bg-info text-dark">
                                                <?= $row['jurusan']; ?>
                                            </span>
                                        </td>

                                        <td><?= $row['deskripsi']; ?></td>

                                        <td>
                                            <button
                                                class="btn btn-warning btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEdit<?= $row['id_karir']; ?>">
                                                <i class="bi bi-pencil"></i>
                                                Edit
                                            </button>

                                            <a href="?hapus=<?= $row['id_karir']; ?>"
                                                onclick="return confirm('Yakin ingin menghapus data ini?')"
                                                class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="modalEdit<?= $row['id_karir']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">

                                                <form method="POST">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Karir</h5>
                                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">

                                                        <input type="hidden" name="id_karir" value="<?= $row['id_karir']; ?>">

                                                        <div class="mb-3">
                                                            <label>Nama Karir</label>
                                                            <input type="text" name="nama_karir" class="form-control"
                                                                value="<?= $row['nama_karir']; ?>" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label>Kategori</label>
                                                            <input type="text" name="kategori" class="form-control"
                                                                value="<?= $row['kategori']; ?>" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label>Jurusan</label>
                                                            <select name="jurusan" class="form-control">
                                                                <option value="TKJ" <?= ($row['jurusan'] == "TKJ") ? 'selected' : '' ?>>TKJ</option>
                                                                <option value="TKR" <?= ($row['jurusan'] == "TKR") ? 'selected' : '' ?>>TKR</option>
                                                                <option value="TPM" <?= ($row['jurusan'] == "TPM") ? 'selected' : '' ?>>TPM</option>
                                                                <option value="DKV" <?= ($row['jurusan'] == "DKV") ? 'selected' : '' ?>>DKV</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label>Deskripsi</label>
                                                            <textarea name="deskripsi" class="form-control"><?= $row['deskripsi']; ?></textarea>
                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="submit" name="edit" class="btn btn-success">
                                                            Update
                                                        </button>
                                                    </div>

                                                </form>

                                            </div>
                                        </div>
                                    </div>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center text-muted'>Data tidak ditemukan</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <nav class="mt-3">
                        <ul class="pagination justify-content-center flex-wrap">

                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">
                                        Previous
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>

                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>

                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">
                                        Next
                                    </a>
                                </li>
                            <?php endif; ?>

                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH -->
    <div class="modal fade" id="modalTambah">

        <div class="modal-dialog">

            <div class="modal-content">

                <form method="POST">

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Karir</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Nama Karir</label>
                            <input type="text" name="nama_karir" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Kategori</label>
                            <select name="kategori" class="form-control" required>
                                <option value="">Pilih Kategori</option>
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

                            <select name="jurusan" class="form-control">
                                <option value="">Pilih Kelas</option>
                                <option value="TKJ">TKJ</option>
                                <option value="TKR">TKR</option>
                                <option value="TPM">TPM</option>
                                <option value="DKV">DKV</option>
                            </select>

                        </div>

                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="tambah" class="btn btn-success">
                            Simpan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalImport">

        <div class="modal-dialog">

            <div class="modal-content">

                <form method="POST" action="Process_import_karir.php" enctype="multipart/form-data">

                    <div class="modal-header">

                        <h5 class="modal-title">Import Data Karir</h5>

                        <button class="btn-close" data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label class="form-label">
                                Upload File Excel
                            </label>

                            <input type="file"
                                name="file_excel"
                                class="form-control"
                                accept=".csv"
                                required>

                            <small class="text-muted">
                                File harus berformat <b>.csv</b>
                            </small>

                        </div>

                        <div class="text-center">

                            <a href="template_karir.csv"
                                class="btn btn-outline-primary btn-sm">

                                <i class="bi bi-download"></i>
                                Download Template Excel

                            </a>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="submit" class="btn btn-success">
                            Import Data
                        </button>

                    </div>

                </form>

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