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

$query = mysqli_query($conn, "
SELECT
siswa.*,
admin.nama_guru,
karir.nama_karir,
h.skor

FROM siswa

LEFT JOIN admin
ON siswa.id_admin=admin.id_admin

LEFT JOIN hasil h
ON siswa.id_siswa=h.id_siswa

LEFT JOIN karir
ON h.id_karir=karir.id_karir

WHERE siswa.id_siswa='$id'

ORDER BY h.skor DESC
LIMIT 1
");

$row = mysqli_fetch_assoc($query);

$jawaban = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) total
FROM jawaban
WHERE id_siswa='$id'"
    )
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>
        Detail Siswa
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

</head>

<body
    class="bg-body-tertiary
d-flex
align-items-center
justify-content-center"

    style="
min-height:100vh;
padding:25px;
">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-xl-10">

                <div class="card border-0 shadow-lg rounded-4">

                    <div class="card-body p-4">

                        <!-- HEADER -->

                        <div class="d-flex
justify-content-between
align-items-center
flex-wrap
gap-2
mb-4">

                            <div>

                                <h3 class="fw-bold mb-1">

                                    <i class="bi bi-person-vcard me-2"></i>

                                    Detail Siswa

                                </h3>

                                <p class="text-muted mb-0">

                                    Monitoring Data Siswa

                                </p>

                            </div>

                            <a href="View_siswa.php"
                                class="btn btn-outline-secondary rounded-pill">

                                <i class="bi bi-arrow-left"></i>

                                Kembali

                            </a>

                        </div>

                        <div class="row g-4">

                            <!-- PROFIL -->

                            <div class="col-lg-4">

                                <div class="card
border-0
shadow-sm
h-100">

                                    <div class="card-header
bg-primary
text-white
text-center
py-4">

                                        <div class="mb-3">

                                            <i class="bi bi-person-circle"

                                                style="
font-size:90px;
">

                                            </i>

                                        </div>

                                        <h4 class="mb-1">

                                            <?= $row['nama'] ?>

                                        </h4>

                                        <span class="badge
bg-light
text-primary">

                                            <?= $row['kelas'] ?>

                                            <?= $row['jurusan'] ?>

                                            <?= $row['subkelas'] ?>

                                        </span>

                                    </div>

                                    <div class="card-body">

                                        <div class="mb-3">

                                            <div class="text-muted">

                                                Email

                                            </div>

                                            <div class="fw-semibold">

                                                <i class="bi bi-envelope me-1"></i>

                                                <?= $row['email'] ?>

                                            </div>

                                        </div>

                                        <hr>

                                        <div>

                                            <div class="text-muted">

                                                Guru BK

                                            </div>

                                            <div class="fw-semibold">

                                                <i class="bi bi-person-badge me-1"></i>

                                                <?= $row['nama_guru'] ?>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- DETAIL -->

                            <div class="col-lg-8">

                                <div class="row g-3">

                                    <!-- STATUS -->

                                    <div class="col-md-6">

                                        <div class="card
border-0
bg-success-subtle
shadow-sm">

                                            <div class="card-body">

                                                <div class="text-muted">

                                                    Status

                                                </div>

                                                <?php if ($row['status'] == "aktif") { ?>

                                                    <span class="badge bg-success">

                                                        Aktif

                                                    </span>

                                                <?php } else { ?>

                                                    <span class="badge bg-danger">

                                                        Nonaktif

                                                    </span>

                                                <?php } ?>

                                            </div>

                                        </div>

                                    </div>

                                    <!-- JAWABAN -->

                                    <div class="col-md-6">

                                        <div class="card
border-0
bg-primary-subtle
shadow-sm">

                                            <div class="card-body">

                                                <div class="text-muted">

                                                    Jumlah Jawaban

                                                </div>

                                                <h3 class="mb-0">

                                                    <?= $jawaban['total'] ?>

                                                </h3>

                                            </div>

                                        </div>

                                    </div>

                                    <!-- SKOR -->

                                    <div class="col-md-6">

                                        <div class="card
border-0
bg-warning-subtle
shadow-sm">

                                            <div class="card-body">

                                                <div class="text-muted">

                                                    Skor Tertinggi

                                                </div>

                                                <h3 class="text-primary mb-0">

                                                    <?= $row['skor'] ?? '-' ?>

                                                </h3>

                                            </div>

                                        </div>

                                    </div>

                                    <!-- KARIR -->

                                    <div class="col-md-6">

                                        <div class="card
border-0
bg-info-subtle
shadow-sm">

                                            <div class="card-body">

                                                <div class="text-muted">

                                                    Rekomendasi Karir

                                                </div>

                                                <h5 class="text-success mb-0">

                                                    <?= $row['nama_karir']
                                                        ?? 'Belum Ada' ?>

                                                </h5>

                                            </div>

                                        </div>

                                    </div>

                                    <!-- INFORMASI -->

                                    <div class="col-12">

                                        <div class="card
border-0
shadow-sm">

                                            <div class="card-header
bg-white
fw-semibold">

                                                <i class="bi bi-info-circle me-1"></i>

                                                Informasi Tambahan

                                            </div>

                                            <div class="card-body">

                                                <div class="row text-center g-3">

                                                    <div class="col-md-4">

                                                        <div class="p-3
bg-light
rounded">

                                                            <div class="text-muted">

                                                                Jurusan

                                                            </div>

                                                            <h6 class="mb-0">

                                                                <?= $row['jurusan'] ?>

                                                            </h6>

                                                        </div>

                                                    </div>

                                                    <div class="col-md-4">

                                                        <div class="p-3
bg-light
rounded">

                                                            <div class="text-muted">

                                                                Subkelas

                                                            </div>

                                                            <h6 class="mb-0">

                                                                <?= $row['subkelas'] ?>

                                                            </h6>

                                                        </div>

                                                    </div>

                                                    <div class="col-md-4">

                                                        <div class="p-3
bg-light
rounded">

                                                            <div class="text-muted">

                                                                Guru BK

                                                            </div>

                                                            <h6 class="mb-0">

                                                                <?= $row['nama_guru'] ?>

                                                            </h6>

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

                </div>

            </div>

        </div>

</body>

</html>