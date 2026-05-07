<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'guru_bp') {
    header("Location: Login_Admin.php");
    exit;
}

include "koneksi.php";

// AMBIL ID ADMIN (PENTING)
$id_admin = $_SESSION['id_admin'];

// =======================
// TAMBAH SOAL
// =======================
if (isset($_POST['tambah'])) {
    $pertanyaan = $_POST['pertanyaan'];
    $kategori   = $_POST['kategori'];
    $jurusan    = $_POST['jurusan'];

    mysqli_query($conn, "INSERT INTO kuisioner 
        (pertanyaan, jurusan, kategori, id_admin) 
        VALUES 
        ('$pertanyaan','$jurusan','$kategori','$id_admin')");

    header("Location: Kelola_soal.php");
    exit;
}

// =======================
// UPDATE SOAL
// =======================
if (isset($_POST['update'])) {

    $id         = $_POST['id_soal'];
    $pertanyaan = $_POST['pertanyaan'];
    $kategori   = $_POST['kategori'];
    $jurusan    = $_POST['jurusan'];

    mysqli_query($conn, "UPDATE kuisioner SET
        pertanyaan='$pertanyaan',
        kategori='$kategori',
        jurusan='$jurusan'
        WHERE id_soal='$id' AND id_admin='$id_admin'
    ");

    header("Location: Kelola_soal.php");
    exit;
}

// =======================
// HAPUS SOAL
// =======================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    mysqli_query($conn, "DELETE FROM kuisioner 
        WHERE id_soal='$id' AND id_admin='$id_admin'");

    header("Location: Kelola_soal.php");
    exit;
}

// =======================
// FILTER DATA
// =======================
$limit = 5;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) $page = 1;

$start = ($page - 1) * $limit;

$no = $start + 1;

$where = "WHERE id_admin='$id_admin'";

if (!empty($_GET['filter_jurusan'])) {
    $filter = $_GET['filter_jurusan'];
    $where .= " AND jurusan='$filter'";
}

$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM kuisioner $where");
$total_data = mysqli_fetch_assoc($total_query)['total'];

$total_pages = ceil($total_data / $limit);
// =======================
// QUERY DATA
// =======================
$data = mysqli_query(
    $conn,
    "SELECT * FROM kuisioner 
     $where 
     ORDER BY jurusan ASC, id_soal ASC
     LIMIT $start, $limit"
);

$nama_guru = $_SESSION['nama_guru'] ?? 'Guru BP';
$username  = $_SESSION['username'] ?? '-';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Soal</title>
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

        /* TOGGLE BUTTON */
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

            /* FLEX FILTER */
            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: stretch !important;
                gap: 10px;
            }

            form.d-flex.gap-2 {
                flex-direction: column;
                width: 100%;
            }

            form.d-flex.gap-2 .form-select,
            form.d-flex.gap-2 button,
            .btn-success.btn-sm {
                width: 100%;
            }

            /* TABLE */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table {
                min-width: 700px;
            }

            .table th,
            .table td {
                font-size: 13px;
                white-space: nowrap;
            }

            .table td:nth-child(2) {
                white-space: normal;
                min-width: 220px;
            }

            /* PAGINATION */
            .pagination {
                flex-wrap: wrap;
                gap: 5px;
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
    <div class="sidebar" id="sidebar">
        <div class="py-3 border-bottom text-center">
            <i class="bi bi-person-circle fs-3"></i>
            <div style="font-size:14px;">
                <?= htmlspecialchars($nama_guru); ?>
            </div>
        </div>

        <a href="Dashboard_gurubp.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="Kelola_siswa.php"><i class="bi bi-people me-2"></i>Kelola Siswa</a>
        <a href="Kelola_soal.php" class="bg-secondary text-white"><i class="bi bi-ui-checks me-2"></i>Kelola Soal</a>
        <a href="Kelola_karir.php"><i class="bi bi-briefcase me-2"></i>Kelola Karir</a>
        <a href="Riwayat.php"><i class="bi bi-clock-history me-2"></i>Riwayat</a>
        <hr>
        <a href="Logout_Admin.php" class="text-danger">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
    </div>
    <div class="overlay" id="overlay"></div>
    <!-- CONTENT -->
    <div class="content" id="content">

        <div class="topbar mb-4">

            <button class="btn btn-light" id="toggleSidebar">
                <i class="bi bi-list"></i>
            </button>

            <div>
                <strong>Home</strong> / Kelola Soal
            </div>

        </div>

        <div class="card card-custom">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h5 class="mb-0">Daftar Soal Saya</h5>

                    <div class="d-flex gap-2">
                        <?php
                        $filter_jurusan = $_GET['filter_jurusan'] ?? '';
                        ?>
                        <form method="GET" class="d-flex gap-2">
                            <select name="filter_jurusan" class="form-select form-select-sm">
                                <option value="">Semua Jurusan</option>
                                <option value="TKJ" <?= ($filter_jurusan == "TKJ") ? "selected" : "" ?>>TKJ</option>
                                <option value="TKR" <?= ($filter_jurusan == "TKR") ? "selected" : "" ?>>TKR</option>
                                <option value="TPM" <?= ($filter_jurusan == "TPM") ? "selected" : "" ?>>TPM</option>
                                <option value="DKV" <?= ($filter_jurusan == "DKV") ? "selected" : "" ?>>DKV</option>
                            </select>

                            <button type="submit" class="btn btn-secondary btn-sm">
                                Filter
                            </button>
                        </form>

                        <button class="btn btn-success btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalTambah">
                            <i class="bi bi-plus"></i> Tambah Soal
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Pertanyaan</th>
                                <th>Kategori</th>
                                <th>Jurusan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php if (mysqli_num_rows($data) > 0) {
                                while ($row = mysqli_fetch_assoc($data)) { ?>

                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($row['pertanyaan']); ?></td>
                                        <td><span class="badge bg-primary"><?= $row['kategori']; ?></span></td>
                                        <td><span class="badge bg-info text-dark"><?= $row['jurusan']; ?></span></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#edit<?= $row['id_soal']; ?>">
                                                Edit
                                            </button>

                                            <a href="?hapus=<?= $row['id_soal']; ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin hapus?')">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                    <!-- MODAL EDIT -->
                                    <div class="modal fade" id="edit<?= $row['id_soal']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">

                                                <form method="POST">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Soal</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">

                                                        <input type="hidden" name="id_soal" value="<?= $row['id_soal']; ?>">

                                                        <!-- PERTANYAAN -->
                                                        <div class="mb-3">
                                                            <label>Pertanyaan</label>
                                                            <textarea name="pertanyaan" class="form-control" required><?= htmlspecialchars($row['pertanyaan']); ?></textarea>
                                                        </div>

                                                        <!-- JURUSAN -->
                                                        <div class="mb-3">
                                                            <label>Jurusan</label>
                                                            <select name="jurusan" class="form-control" required>
                                                                <option value="TKJ" <?= $row['jurusan'] == "TKJ" ? 'selected' : '' ?>>TKJ</option>
                                                                <option value="TKR" <?= $row['jurusan'] == "TKR" ? 'selected' : '' ?>>TKR</option>
                                                                <option value="TPM" <?= $row['jurusan'] == "TPM" ? 'selected' : '' ?>>TPM</option>
                                                                <option value="DKV" <?= $row['jurusan'] == "DKV" ? 'selected' : '' ?>>DKV</option>
                                                            </select>
                                                        </div>

                                                        <!-- KATEGORI RIASEC -->
                                                        <div class="mb-3">
                                                            <label>Kategori (RIASEC)</label>
                                                            <select name="kategori" class="form-control" required>

                                                                <option value="Realistic" <?= $row['kategori'] == "Realistic" ? 'selected' : '' ?>>Realistic</option>
                                                                <option value="Investigative" <?= $row['kategori'] == "Investigative" ? 'selected' : '' ?>>Investigative</option>
                                                                <option value="Artistic" <?= $row['kategori'] == "Artistic" ? 'selected' : '' ?>>Artistic</option>
                                                                <option value="Social" <?= $row['kategori'] == "Social" ? 'selected' : '' ?>>Social</option>
                                                                <option value="Enterprising" <?= $row['kategori'] == "Enterprising" ? 'selected' : '' ?>>Enterprising</option>
                                                                <option value="Conventional" <?= $row['kategori'] == "Conventional" ? 'selected' : '' ?>>Conventional</option>

                                                            </select>
                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="submit" name="update" class="btn btn-success">
                                                            <i class="bi bi-check-circle"></i> Update
                                                        </button>

                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            Batal
                                                        </button>
                                                    </div>

                                                </form>

                                            </div>
                                        </div>
                                    </div>

                            <?php }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>Belum ada soal</td></tr>";
                            } ?>

                        </tbody>
                    </table>
                    <nav class="mt-3">
                        <ul class="pagination justify-content-center flex-wrap">

                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="?page=<?= $page - 1 ?>&filter_jurusan=<?= $_GET['filter_jurusan'] ?? '' ?>">
                                        Previous
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>

                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="?page=<?= $i ?>&filter_jurusan=<?= $_GET['filter_jurusan'] ?? '' ?>">
                                        <?= $i ?>
                                    </a>
                                </li>

                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="?page=<?= $page + 1 ?>&filter_jurusan=<?= $_GET['filter_jurusan'] ?? '' ?>">
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
                        <h5 class="modal-title">Tambah Soal</h5> <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"> <label>Pertanyaan</label> <textarea name="pertanyaan" class="form-control" required></textarea> </div>
                        <div class="mb-3"> <label>Jurusan</label> <select name="jurusan" class="form-control" required>
                                <option value="">Pilih Jurusan</option>
                                <option value="TKR">TKR</option>
                                <option value="TKJ">TKJ</option>
                                <option value="TPM">TPM</option>
                                <option value="DKV">DKV</option>
                            </select> </div>
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
                    </div>
                    <div class="modal-footer"> <button type="submit" name="tambah" class="btn btn-success"> Simpan </button> <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> Batal </button> </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleBtn = document.getElementById("toggleSidebar");
        const sidebar = document.getElementById("sidebar");
        const overlay = document.getElementById("overlay");

        toggleBtn.addEventListener("click", function() {
            sidebar.classList.toggle("show");
            overlay.classList.toggle("show");
        });

        overlay.addEventListener("click", function() {
            sidebar.classList.remove("show");
            overlay.classList.remove("show");
        });

        // Tutup sidebar saat klik menu mobile
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