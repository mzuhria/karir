<?php
session_start();
include "koneksi.php";

if (
    !isset($_SESSION['login']) ||
    $_SESSION['role'] != 'kepala_sekolah'
) {
    header("Location: Login_Admin.php");
    exit;
}

$id = $_GET['id'];

// DATA GURU
$query = mysqli_query($conn, "
SELECT
admin.*,

(
SELECT COUNT(*)
FROM siswa
WHERE siswa.id_admin=admin.id_admin
) jumlah_siswa,

(
SELECT COUNT(*)
FROM kuisioner
WHERE kuisioner.id_admin=admin.id_admin
) jumlah_soal

FROM admin

WHERE id_admin='$id'
");

$row = mysqli_fetch_assoc($query);

// PAGINATION SISWA
$batas = 3;

$halaman = isset($_GET['halaman'])
    ? (int)$_GET['halaman']
    : 1;

if ($halaman < 1) {
    $halaman = 1;
}

$mulai = ($halaman - 1) * $batas;

// TOTAL SISWA
$total_siswa = mysqli_num_rows(
    mysqli_query(
        $conn,
        "
SELECT *
FROM siswa
WHERE id_admin='$id'
"
    )
);

$total_halaman =
    ceil(
        $total_siswa
            /
            $batas
    );

// DATA SISWA
$siswa = mysqli_query($conn, "
SELECT *
FROM siswa

WHERE id_admin='$id'

ORDER BY nama ASC

LIMIT $mulai,$batas
");
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>
        Detail Guru BK
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

</head>

<body class="bg-body-tertiary d-flex align-items-center justify-content-center" style=" min-height:100vh; padding:25px;">
    <div class="container">

        <div class="row justify-content-center">

            <div class="col-xl-10">

                <div class="card border-0 shadow-lg rounded-4">

                    <div class="card-body p-4">

                        <!-- HEADER -->

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">

                            <div>

                                <h3 class="fw-bold mb-1">

                                    <i class="bi bi-person-badge me-2"></i>

                                    Detail Guru BK

                                </h3>

                                <p class="text-muted mb-0">

                                    Monitoring Guru BK

                                </p>

                            </div>

                            <a href="View_guru.php"
                                class="btn btn-outline-secondary rounded-pill">

                                <i class="bi bi-arrow-left"></i>

                                Kembali

                            </a>

                        </div>

                        <div class="row g-4">

                            <!-- PROFIL -->

                            <div class="col-lg-4">

                                <div class="card border-0 shadow-sm h-100">

                                    <div class="card-header bg-primary text-white text-center py-4">

                                        <div class="mb-3">

                                            <i class="bi bi-person-circle"
                                                style="font-size:90px;">

                                            </i>

                                        </div>

                                        <h4 class="mb-1">

                                            <?= $row['nama_guru'] ?>

                                        </h4>

                                        <span class="badge bg-light text-primary">

                                            Guru BK

                                        </span>

                                    </div>

                                    <div class="card-body">

                                        <div class="mb-3">

                                            <div class="text-muted">

                                                Username

                                            </div>

                                            <div class="fw-semibold">

                                                <i class="bi bi-person me-1"></i>

                                                <?= $row['username'] ?>

                                            </div>

                                        </div>
                                        <hr>
                                    </div>

                                </div>

                            </div>

                            <!-- DETAIL -->

                            <div class="col-lg-8">

                                <div class="row g-3">

                                    <div class="col-md-6">

                                        <div class="card border-0 bg-primary-subtle shadow-sm">

                                            <div class="card-body">

                                                <div class="text-muted">

                                                    Jumlah Siswa

                                                </div>

                                                <h3 class="mb-0">

                                                    <?= $row['jumlah_siswa'] ?>

                                                </h3>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="card border-0 bg-success-subtle shadow-sm">

                                            <div class="card-body">

                                                <div class="text-muted">

                                                    Jumlah Soal

                                                </div>

                                                <h3 class="mb-0">

                                                    <?= $row['jumlah_soal'] ?>

                                                </h3>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="card border-0 bg-info-subtle shadow-sm">

                                            <div class="card-body">

                                                <div class="text-muted">

                                                    Role

                                                </div>

                                                <h5 class="mb-0">

                                                    Guru BK

                                                </h5>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="card border-0 bg-warning-subtle shadow-sm">

                                            <div class="card-body">

                                                <div class="text-muted">

                                                    Nomor HP

                                                </div>

                                                <h5 class="mb-0">

                                                    <?= !empty($row['no_hp']) ? $row['no_hp'] : '-' ?>

                                                </h5>

                                            </div>

                                        </div>

                                    </div>

                                    <!-- SISWA -->

                                    <div class="col-12">

                                        <div class="card border-0 shadow-sm">

                                            <div class="card-header bg-white fw-semibold">

                                                <i class="bi bi-people me-1"></i>

                                                Daftar Siswa Bimbingan

                                            </div>

                                            <div class="card-body table-responsive">

                                                <table class="table table-hover align-middle">

                                                    <thead class="table-light">

                                                        <tr>
                                                            <th>No</th>
                                                            <th>Nama</th>
                                                            <th>Kelas</th>
                                                            <th>Email</th>
                                                        </tr>

                                                    </thead>

                                                    <tbody>

                                                        <?php
                                                        $no = $mulai + 1;

                                                        while ($s = mysqli_fetch_assoc($siswa)) {
                                                        ?>

                                                            <tr>

                                                                <td><?= $no++ ?></td>

                                                                <td><?= $s['nama'] ?></td>

                                                                <td>
                                                                    <?= $s['kelas'] ?>
                                                                    <?= $s['jurusan'] ?>
                                                                    <?= $s['subkelas'] ?>
                                                                </td>

                                                                <td><?= $s['email'] ?></td>

                                                            </tr>

                                                        <?php } ?>

                                                    </tbody>

                                                </table>

                                                <!-- PAGINATION -->

                                                <nav class="mt-3">

                                                    <ul class="pagination pagination-sm justify-content-center">

                                                        <!-- PREVIOUS -->

                                                        <?php if ($halaman > 1) { ?>

                                                            <li class="page-item">

                                                                <a class="page-link"
                                                                    href="?id=<?= $id ?>&halaman=<?= $halaman - 1 ?>">

                                                                    <

                                                                </a>

                                                            </li>

                                                        <?php } ?>

                                                        <!-- ANGKA -->

                                                        <?php for ($i = 1; $i <= $total_halaman; $i++) { ?>

                                                            <li class="page-item <?= ($i == $halaman) ? 'active' : '' ?>">

                                                                <a class="page-link"
                                                                    href="?id=<?= $id ?>&halaman=<?= $i ?>">

                                                                    <?= $i ?>

                                                                </a>

                                                            </li>

                                                        <?php } ?>

                                                        <!-- NEXT -->

                                                        <?php if ($halaman < $total_halaman) { ?>

                                                            <li class="page-item">

                                                                <a class="page-link"
                                                                    href="?id=<?= $id ?>&halaman=<?= $halaman + 1 ?>">

                                                                    >

                                                                </a>

                                                            </li>

                                                        <?php } ?>

                                                    </ul>

                                                </nav>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

</body>

</html>