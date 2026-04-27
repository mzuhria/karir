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

/* Fitur Search */
$search = $_GET['search'] ?? '';

if (!empty($search)) {

    $search_safe = mysqli_real_escape_string($conn, $search);

    $query = mysqli_query($conn, "
        SELECT * FROM karir 
        WHERE id_admin='$id_admin' AND (
            nama_karir LIKE '%$search_safe%' OR
            kategori LIKE '%$search_safe%' OR
            jurusan LIKE '%$search_safe%' OR
            deskripsi LIKE '%$search_safe%'
        )
    ");
} else {

    $query = mysqli_query($conn, "SELECT * FROM karir WHERE id_admin='$id_admin' ORDER BY id_karir DESC");
}
?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <title>Kelola Karir</title>

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
        <a href="Kelola_karir.php" class="bg-secondary text-white"><i class="bi bi-briefcase me-2"></i>Kelola Karir</a>
        <a href="Riwayat.php"><i class="bi bi-clock-history me-2"></i>Riwayat</a>
        <hr>
        <a href="Logout_Admin.php" class="text-danger"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <div class="topbar mb-4">
            <strong>Home</strong> / Kelola Karir
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
                                style="width:200px;">

                            <button class="btn btn-primary btn-sm">
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

</body>

</html>