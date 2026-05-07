<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: Login_Siswa.php");
    exit;
}

$id_siswa = $_SESSION['id_siswa'];

$querySiswa = mysqli_query($conn, "
    SELECT * FROM siswa WHERE id_siswa = '$id_siswa'
");

$dataSiswa = mysqli_fetch_assoc($querySiswa);

$jurusan = $dataSiswa['jurusan'];

if (!isset($_SESSION['jurusan'])) {
    header("Location: Kuisioner.php");
    exit();
}

$jurusan = $_SESSION['jurusan'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuisioner</title>
    <link rel="stylesheet" href="bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<style>
    body {
        background: linear-gradient(135deg, #ce3af3, #224abe);
        font-family: 'Poppins', sans-serif;
        min-height: 100vh;
    }

    /* ===== CARD ===== */
    .card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    /* ===== HEADER ===== */
    .page-title {
        color: #1e293b;
        font-weight: 600;
    }

    /* ===== PETUNJUK BOX ===== */
    .petunjuk-box {
        background: #eff6ff;
        border-left: 5px solid #2563eb;
        padding: 20px;
        border-radius: 10px;
    }

    /* ===== RADIO STYLE ===== */
    .form-check-input:checked {
        background-color: #2563eb;
        border-color: #2563eb;
    }

    .judul-kuisioner {
        background: rgba(9, 140, 211, 0.5);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 15px;
        color: white;
        font-weight: 600;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
        font-weight: 700;
    }
</style>

<body>

    <div class="container py-5">

        <div class="card p-4 col-lg-8 mx-auto shadow">
            <!-- PETUNJUK -->
            <div class="modal fade" id="petunjuk" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">Petunjuk Pengisian Kuisioner</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <div class="alert alert-primary">
                                <strong>Perhatikan sebelum mengisi:</strong>
                            </div>

                            <ol>
                                <li>Baca setiap pertanyaan dengan teliti.</li>
                                <li>Pilih jawaban sesuai minat dan kemampuan kamu.</li>
                                <li>Jawab semua pertanyaan.</li>
                                <li>Jangan refresh halaman saat mengisi.</li>
                                <li>Klik tombol Submit setelah selesai.</li>
                            </ol>

                            <hr>

                            <p>
                                Skala penilaian:
                            </p>

                            <ul>
                                <li>STS = Sangat Tidak Setuju</li>
                                <li>TS = Tidak Setuju</li>
                                <li>N = Netral</li>
                                <li>S = Setuju</li>
                                <li>SS = Sangat Setuju</li>
                            </ul>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-danger" data-bs-dismiss="modal">
                                Mengerti
                            </button>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Kuisioner -->
            <h4 class="text-center mb-4 judul-kuisioner">
                <?php echo $dataSiswa['nama']; ?> - Kuisioner Jurusan <?php echo $jurusan; ?>
            </h4>

            <form method="POST" action="Process_jawaban.php">

                <?php
                $no = 1;

                $query = mysqli_query($conn, "SELECT * FROM kuisioner WHERE jurusan='$jurusan'");

                while ($row = mysqli_fetch_assoc($query)) {
                ?>

                    <p><strong><?= $no++ ?>. <?= $row['pertanyaan'] ?></strong></p>

                    <label>
                        <input type="radio" name="jawaban[<?= $row['id_soal'] ?>]" value="1" required> STS
                    </label>

                    <label>
                        <input type="radio" name="jawaban[<?= $row['id_soal'] ?>]" value="2"> TS
                    </label>

                    <label>
                        <input type="radio" name="jawaban[<?= $row['id_soal'] ?>]" value="3"> N
                    </label>

                    <label>
                        <input type="radio" name="jawaban[<?= $row['id_soal'] ?>]" value="4"> S
                    </label>

                    <label>
                        <input type="radio" name="jawaban[<?= $row['id_soal'] ?>]" value="5"> SS
                    </label>

                    <hr>

                <?php } ?>

                <div class="d-flex justify-content-between mt-4">

                    <!-- Tombol kiri -->
                    <button type="button"
                        class="btn btn-warning px-4"
                        data-bs-toggle="modal"
                        data-bs-target="#petunjuk">
                        Lihat Petunjuk
                    </button>

                    <!-- Tombol kanan -->
                    <button type="submit" class="btn btn-success px-4">
                        Submit
                    </button>

                </div>

            </form>

        </div>
    </div>
    <script src="bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>