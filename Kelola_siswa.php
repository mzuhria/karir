<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'guru_bp') {
    header("Location: Login_Admin.php");
    exit;
}

include "koneksi.php";
$id_admin = $_SESSION['id_admin'];
$nama_guru = $_SESSION['nama_guru'] ?? 'Guru BP';
$search = $_GET['search'] ?? '';

//modal Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    mysqli_query($conn, "DELETE FROM siswa WHERE id_siswa='$id' AND id_admin='$id_admin'");
}

// TAMBAH SISWA (MODAL)
if (isset($_POST['tambah_siswa'])) {

    $nama      = mysqli_real_escape_string($conn, $_POST['nama']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $username  = mysqli_real_escape_string($conn, $_POST['username']);
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $kelas = strtoupper(trim($_POST['kelas']));

    // handle jika kelas_akses kosong
    if (empty($_SESSION['kelas_akses'])) {
        die("Kelas akses belum diatur oleh admin!");
    }

    $akses = array_map('trim', explode(',', $_SESSION['kelas_akses']));

    if (!in_array($kelas, $akses)) {
        die("Akses ditolak! Tidak boleh input kelas ini.");
    }
    $jurusan = $_POST['jurusan'];
    $sub     = $_POST['subkelas'];

    // CEK EMAIL DUPLIKAT
    $cek = mysqli_query($conn, "SELECT * FROM siswa WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Email sudah digunakan!";
    } else {

        $query_insert = mysqli_query($conn, "INSERT INTO siswa (nama, kelas, jurusan, subkelas, email, username, password, id_admin, created_at)
        VALUES ('$nama','$kelas','$jurusan','$sub','$email','$username','$password','$id_admin', NOW())") or die("ERROR INSERT: " . mysqli_error($conn));

        if ($query_insert) {
            header("Location: Kelola_siswa.php?success=1");
            exit;
        } else {
            $error = "Gagal tambah data!";
        }
    }
}

// AKTIVASI
if (isset($_GET['aktifkan'])) {
    $id = $_GET['aktifkan'];
    mysqli_query($conn, "UPDATE siswa SET status='aktif' WHERE id_siswa='$id' AND id_admin='$id_admin'");
}

// NONAKTIFKAN
if (isset($_GET['nonaktifkan'])) {
    $id = $_GET['nonaktifkan'];
    mysqli_query($conn, "UPDATE siswa SET status='nonaktif' WHERE id_siswa='$id' AND id_admin='$id_admin'");
}

if (isset($_POST['update_siswa'])) {

    $id_siswa = $_POST['id_siswa'];
    $nama     = $_POST['nama'];
    $kelas    = $_POST['kelas'];
    $jurusan  = $_POST['jurusan'];
    $sub      = $_POST['subkelas'];
    $email    = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // validasi kelas
    $akses = array_map('trim', explode(',', $_SESSION['kelas_akses']));
    if (!in_array($kelas, $akses)) {
        die("Tidak boleh ubah ke kelas ini!");
    }

    // CEK USERNAME DUPLIKAT
    $cek = mysqli_query($conn, "SELECT * FROM siswa WHERE username='$username' AND id_siswa != '$id_siswa'");
    if (mysqli_num_rows($cek) > 0) {
        die("Username sudah digunakan!");
    }

    // JIKA PASSWORD DIISI
    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        mysqli_query($conn, "
            UPDATE siswa SET
            nama='$nama',
            kelas='$kelas',
            jurusan='$jurusan',
            subkelas='$sub',
            email='$email',
            username='$username',
            password='$hash'
            WHERE id_siswa='$id_siswa' AND id_admin='$id_admin'
        ");
    } else {
        // TANPA UBAH PASSWORD
        mysqli_query($conn, "
            UPDATE siswa SET
            nama='$nama',
            kelas='$kelas',
            jurusan='$jurusan',
            subkelas='$sub',
            email='$email',
            username='$username'
            WHERE id_siswa='$id_siswa' AND id_admin='$id_admin'
        ");
    }

    header("Location: Kelola_siswa.php?success=edit");
    exit;
}

// FILTER JURUSAN
$kelas_list = explode(',', $_SESSION['kelas_akses']);

$where = "WHERE siswa.id_admin='$id_admin' AND (";
foreach ($kelas_list as $k) {
    $where .= "siswa.kelas='$k' OR ";
}
$where = rtrim($where, "OR ") . ")";

if (!empty($_GET['filter_jurusan'])) {
    $filter = mysqli_real_escape_string($conn, $_GET['filter_jurusan']);
    $where .= " AND siswa.jurusan='$filter'";
}

$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($conn, $search);

    $where .= " AND (
        siswa.nama LIKE '%$search_safe%' OR
        siswa.subkelas LIKE '%$search_safe%' OR
        siswa.email LIKE '%$search_safe%' OR
        siswa.created_at LIKE '%$search_safe%'
    )";
}

// ================= PAGINATION =================

// halaman aktif
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$limit = 10; // jumlah data per halaman
$start = ($page - 1) * $limit;


// HITUNG TOTAL DATA
$count_query = mysqli_query($conn, "
SELECT COUNT(*) as total
FROM siswa
WHERE id_admin='$id_admin'
");

$count_data = mysqli_fetch_assoc($count_query);

$total_data = $count_data['total'];

$total_pages = ceil($total_data / $limit);

$query = mysqli_query($conn, "
SELECT 
    id_siswa,
    nama,
    kelas,
    jurusan,
    subkelas,
    created_at,
    email,
    username,
    status
FROM siswa
$where
ORDER BY created_at ASC
LIMIT $start, $limit
");

if (!$query) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Siswa</title>
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

            /* TABLE */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table {
                min-width: 900px;
            }

            .table th,
            .table td {
                font-size: 13px;
                white-space: nowrap;
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
        <!-- PROFIL -->
        <div class="py-3 border-bottom text-center">
            <i class="bi bi-person-circle fs-3"></i>
            <div style="font-size:14px;">
                <?= htmlspecialchars($nama_guru); ?>
            </div>
        </div>

        <a href="Dashboard_gurubp.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="Kelola_siswa.php" class="bg-secondary text-white"><i class="bi bi-people me-2"></i>Kelola Siswa</a>
        <a href="Kelola_soal.php"><i class="bi bi-ui-checks me-2"></i>Kelola Soal</a>
        <a href="Kelola_karir.php"><i class="bi bi-briefcase me-2"></i>Kelola Karir</a>
        <a href="Riwayat.php"><i class="bi bi-clock-history me-2"></i>Riwayat</a>
        <hr>
        <a href="Logout_Admin.php" class="text-danger"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
    </div>

    <div class="overlay" id="overlay"></div>

    <!-- CONTENT -->
    <div class="content" id="content">

        <div class="topbar mb-4">

            <button class="btn btn-light" id="toggleSidebar">
                <i class="bi bi-list"></i>
            </button>

            <div>
                <strong>Home</strong> / Kelola Siswa
            </div>

        </div>

        <div class="card card-custom">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h5 class="mb-0">Data Siswa</h5>
                    <div class="d-flex align-items-center gap-2">

                        <form method="GET" class="d-flex align-items-center">

                            <input type="text"
                                name="search"
                                value="<?= htmlspecialchars($search) ?>"
                                class="form-control form-control-sm me-2"
                                placeholder="Cari nama atau email..."
                                style="width:200px;">

                            <button class="btn btn-primary btn-sm">
                                <i class="bi bi-search"></i>
                            </button>

                        </form>

                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="bi bi-plus-circle"></i> Tambah Siswa
                        </button>

                    </div>

                </div>

                <!-- Tabel Karir -->
                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">

                            <tr>

                                <th width="5%">No</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Tanggal</th>
                                <th>Email</th>
                                <th>Aksi</th>
                                <th>Status</th>

                            </tr>

                        </thead>

                        <tbody>
                            <?php
                            $no = $start + 1;

                            if (mysqli_num_rows($query) > 0) {
                                while ($row = mysqli_fetch_assoc($query)) {
                            ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $row['nama']; ?></td>

                                        <td>
                                            <span class="badge bg-info text-dark">
                                                <?= $row['kelas'] . " " . $row['jurusan'] . " " . $row['subkelas']; ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge bg-success">
                                                <?= $row['created_at']; ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge bg-danger">
                                                <?= $row['email']; ?>
                                            </span>
                                        </td>

                                        <td>

                                            <!-- AKTIVASI -->
                                            <?php if ($row['status'] == 'nonaktif'): ?>
                                                <a href="Kelola_siswa.php?aktifkan=<?= $row['id_siswa']; ?>"
                                                    class="btn btn-success btn-sm"
                                                    onclick="return confirm('Aktifkan akun siswa ini?')">
                                                    ✔
                                                </a>
                                            <?php else: ?>
                                                <a href="Kelola_siswa.php?nonaktifkan=<?= $row['id_siswa']; ?>"
                                                    class="btn btn-warning btn-sm"
                                                    onclick="return confirm('Nonaktifkan akun siswa ini?')">
                                                    ❌
                                                </a>
                                            <?php endif; ?>

                                            <!-- HAPUS -->
                                            <a href="Kelola_siswa.php?hapus=<?= $row['id_siswa']; ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus siswa ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>

                                            <!-- EDIT -->
                                            <button
                                                class="btn btn-sm btn-warning"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEdit"
                                                data-id="<?= $row['id_siswa']; ?>"
                                                data-nama="<?= $row['nama']; ?>"
                                                data-kelas="<?= $row['kelas']; ?>"
                                                data-jurusan="<?= $row['jurusan']; ?>"
                                                data-sub="<?= $row['subkelas']; ?>"
                                                data-email="<?= $row['email']; ?>"
                                                data-username="<?= $row['username']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                        </td>

                                        <td>
                                            <?php if ($row['status'] == 'aktif'): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            <?php endif; ?>
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
    <!-- MODAL TAMBAH SISWA -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Siswa</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        <?php if (isset($error)) { ?>
                            <div class="alert alert-danger"><?= $error; ?></div>
                        <?php } ?>

                        <div class="mb-2">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Kelas</label>
                            <select name="kelas" class="form-control" required>

                                <option value="">Pilih Kelas</option>

                                <?php
                                $kelas_list = explode(',', $_SESSION['kelas_akses']);
                                foreach ($kelas_list as $k) {
                                    echo "<option value='$k'>$k</option>";
                                }
                                ?>

                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Jurusan</label>
                            <select name="jurusan" id="jurusan" class="form-control" required>
                                <option value="">Pilih Jurusan</option>
                                <option value="TKR">TKR</option>
                                <option value="TPM">TPM</option>
                                <option value="DKV">DKV</option>
                                <option value="TKJ">TKJ</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Sub Kelas</label>
                            <select name="subkelas" id="subkelas" class="form-control" required>
                                <option value="">Pilih Sub Kelas</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="tambah_siswa" class="btn btn-success">
                            Simpan
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <!-- MODAL EDIT SISWA -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Siswa</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="id_siswa" id="edit_id">

                        <div class="mb-2">
                            <label>Nama</label>
                            <input type="text" name="nama" id="edit_nama" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Kelas</label>
                            <select name="kelas" id="edit_kelas" class="form-control" required>
                                <?php foreach ($kelas_list as $k) {
                                    echo "<option value='$k'>$k</option>";
                                } ?>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Jurusan</label>
                            <select name="jurusan" id="edit_jurusan" class="form-control" required>
                                <option value="TKR">TKR</option>
                                <option value="TPM">TPM</option>
                                <option value="DKV">DKV</option>
                                <option value="TKJ">TKJ</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Sub Kelas</label>
                            <select name="subkelas" id="edit_sub" class="form-control"></select>
                        </div>

                        <div class="mb-2">
                            <label>Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label>Username</label>
                            <input type="text" name="username" id="edit_username" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Password Baru</label>
                            <input type="password" name="password" class="form-control">
                            <small class="text-muted">Kosongkan jika tidak diubah</small>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="update_siswa" class="btn btn-warning">
                            Update
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <script>
        const modalEdit = document.getElementById('modalEdit');

        modalEdit.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            document.getElementById('edit_id').value = button.getAttribute('data-id');
            document.getElementById('edit_nama').value = button.getAttribute('data-nama');
            document.getElementById('edit_kelas').value = button.getAttribute('data-kelas');
            document.getElementById('edit_jurusan').value = button.getAttribute('data-jurusan');
            document.getElementById('edit_email').value = button.getAttribute('data-email');
            document.getElementById('edit_username').value = button.getAttribute('data-username');

            // generate subkelas 1-5
            let sub = button.getAttribute('data-sub');
            let select = document.getElementById('edit_sub');
            select.innerHTML = '';

            for (let i = 1; i <= 5; i++) {
                let opt = document.createElement('option');
                opt.value = i;
                opt.text = i;
                if (i == sub) opt.selected = true;
                select.appendChild(opt);
            }
        });
    </script>
    <script>
        const subkelasSelect = document.getElementById("subkelas");

        function generateSubkelas() {
            subkelasSelect.innerHTML = '<option value="">Pilih Sub Kelas</option>';

            for (let i = 1; i <= 5; i++) {
                let option = document.createElement("option");
                option.value = i;
                option.textContent = "" + i;
                subkelasSelect.appendChild(option);
            }
        }

        // langsung generate saat halaman load
        generateSubkelas();
    </script>
    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
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
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>