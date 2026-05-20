<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['jurusan'])) {
    header("Location: Kuisioner.php");
    exit();
}

$id_siswa = $_SESSION['id_siswa'] ?? 0;

$data = mysqli_query($conn, "SELECT * FROM siswa WHERE id_siswa='$id_siswa'");
$siswa = mysqli_fetch_assoc($data);

$nama = $siswa['nama'] ?? '-';

$map_jurusan = [
    "TKJ" => "Teknik Komputer dan Jaringan",
    "TKR" => "Teknik Kendaraan Ringan",
    "TPM" => "Teknik Pemesinan",
    "DKV" => "Desain Komunikasi Visual"
];

$kelas = $siswa['kelas'] ?? '-';
$jurusan_db = $siswa['jurusan'] ?? '-';
$subkelas = $siswa['subkelas'] ?? '-';
$kelas_full = $kelas . " " . $jurusan_db . " " . $subkelas;
$jurusan = $map_jurusan[$jurusan_db] ?? $jurusan_db;
$rekomendasi = $_SESSION['rekomendasi'] ?? [];
$tanggal = $_GET['tanggal'] ?? null;
$tanggal_tampil = $tanggal
    ? date('d F Y', strtotime($tanggal))
    : date('d F Y');

if ($tanggal) {

    $query = mysqli_query($conn, "
        SELECT hasil.*, karir.nama_karir, karir.deskripsi
        FROM hasil
        LEFT JOIN karir ON hasil.id_karir = karir.id_karir
        WHERE hasil.id_siswa = '$id_siswa'
        AND hasil.tanggal = '$tanggal'
        ORDER BY hasil.skor DESC
    ");

    $rekomendasi = [];

    while ($row = mysqli_fetch_assoc($query)) {
        $rekomendasi[] = [
            'nama' => $row['nama_karir'],
            'deskripsi' => $row['deskripsi'],
            'score' => $row['skor']
        ];
    }
} else {
    // 🔥 HASIL DARI SESSION (SETELAH KUIS)
    $rekomendasi = $_SESSION['rekomendasi'] ?? [];
}

// ==============================
// TOP KARIR
// ==============================
$top1 = $rekomendasi[0]['nama'] ?? '-';
$top2 = $rekomendasi[1]['nama'] ?? '-';
$top3 = $rekomendasi[2]['nama'] ?? '-';

// AMBIL DATA GURU BK
$q_guru = mysqli_query($conn, "
SELECT nama_guru,no_hp
FROM admin
WHERE id_admin='" . $siswa['id_admin'] . "'
LIMIT 1
");

$guru = mysqli_fetch_assoc($q_guru);

$nama_guru_bk = $guru['nama_guru'] ?? 'Guru BK';

// FORMAT NOMOR WA
$nomor_wa = preg_replace('/[^0-9]/', '', $guru['no_hp']);

if (substr($nomor_wa, 0, 1) == '0') {
    $nomor_wa = '62' . substr($nomor_wa, 1);
}

// PESAN WA
$pesan = rawurlencode(
    "Halo " . $nama_guru_bk . ",

Saya ingin konsultasi hasil analisis karir.

Nama : " . $nama . "
Kelas : " . $kelas_full . "
Jurusan : " . $jurusan . "

Rekomendasi:
1. " . $top1 . "
2. " . $top2 . "
3. " . $top3
);

$link_wa = "https://wa.me/" . $nomor_wa . "?text=" . $pesan;
?>

<!DOCTYPE html>
<html lang="id">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Analisa</title>

    <style>
        body {
            background: linear-gradient(135deg, #ce3af3, #224abe);
            font-family: Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            page-break-after: avoid;
            font-size: 12.5px;
            line-height: 1.4;
        }

        /* CARD */
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
        }

        .shadow {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .card-hasil {
            max-width: 900px;
            margin: auto;
            margin-top: 10px;
        }

        /* HEADER */
        .header-table {
            width: 100%;
        }

        .header-table td {
            vertical-align: top;
        }

        /* TABLE */
        .table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #ccc;
            padding: 8px;
            font-size: 12px;
        }

        /* DATA TABLE */
        .table-data td {
            padding: 6px 10px;
            font-size: 12.5px;
        }

        /* ALERT */
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .alert-secondary {
            background: #eee;
        }

        .alert-info {
            background: #d9edf7;
        }

        .alert-warning {
            background: #fcf8e3;
        }

        h5 {
            font-size: 13.5px;
            margin: 10px 0 5px 0;
        }

        p {
            font-size: 12.5px;
        }

        hr {
            margin: 8px 0;
        }

        /* PROGRESS */
        .progress {
            width: 100%;
            height: 12px;
            background: #ccc;
            margin-bottom: 10px;
        }

        .progress-bar {
            height: 100%;
            background: green;
            color: white;
            text-align: center;
            font-size: 10px;
        }

        /* BUTTON */
        .btn {
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            color: white;
            display: inline-block;
        }

        .btn-warning {
            background: orange;
        }

        .btn-success {
            background: red;
        }

        .btn-konsultasi {
            background: #25D366;
            margin-right: 5px;
        }

        /* FLEX (ganti bootstrap d-flex) */
        .flex-between {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        @page {
            size: A4;
            margin: 10px;
        }
    </style>

</head>

<body>

    <div class="card shadow card-hasil p-4">

        <table class="header-table">
            <tr>
                <td width="15%"></td>

                <td width="70%" align="center">
                    <div style="font-size:18px; font-weight:bold;">
                        HASIL TEST ANALISIS
                    </div>
                    <div style="font-size:14px;">
                        SMK DHARMA BAHARI SURABAYA
                    </div>
                    <div style="font-size:12px;">
                        Email : smkdbs@gmail.com<br>
                        Telp: xxxx-xxxx-xxxx
                    </div>
                </td>

                <td width="15%"></td>
            </tr>
        </table>

        <hr>

        <table class="table-data">

            <tr>
                <td width="150"><b>NAMA SISWA</b></td>
                <td width="10">:</td>
                <td><?php echo $nama; ?></td>
            </tr>

            <tr>
                <td width="150"><b>JURUSAN</b></td>
                <td width="10">:</td>
                <td><?php echo $jurusan; ?></td>
            </tr>

            <tr>
                <td><b>KELAS</b></td>
                <td>:</td>
                <td><?php echo $kelas_full; ?></td>
            </tr>

            <tr>
                <td><b>TANGGAL TEST</b></td>
                <td>:</td>
                <td><?php echo $tanggal_tampil; ?></td>
            </tr>

        </table>

        <hr>

        <h5>A. POTENSI KARIR</h5>

        <div class="alert alert-secondary">
            <p>Hasil analisis minat berdasarkan jawaban kuisioner siswa.</p>

            <?php
            // hitung total skor TOP 3
            $total_score = 0;
            foreach ($rekomendasi as $r) {
                $total_score += $r['score'];
            }

            foreach ($rekomendasi as $r):

                $persen = ($total_score > 0)
                    ? ($r['score'] / $total_score) * 100
                    : 0;

                $persen = round($persen);
            ?>

                <label><b><?= $r['nama'] ?></b></label>
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" style="width:<?= $persen ?>%">
                        <?= $persen ?>%
                    </div>
                </div>

            <?php endforeach; ?>
        </div>

        <hr>

        <h5>B. MINAT DOMINAN</h5>

        <div class="alert alert-info">
            <h5 class="text-success">
                <?= $top1 ?> – <?= $top2 ?>
            </h5>

            <p>
                Berdasarkan hasil analisis sistem,
                siswa memiliki kecenderungan minat pada bidang
                <b><?= $top1 ?></b> dan <b><?= $top2 ?></b>
                yang memiliki tingkat kecocokan tertinggi.
            </p>
        </div>

        <hr>

        <h5>C. REKOMENDASI KARIR</h5>

        <table border="1" width="100%" cellpadding="8">

            <tr>
                <th width="50">No</th>
                <th>Karir</th>
                <th>Deskripsi</th>
            </tr>

            <?php
            $no = 1;

            if (!empty($rekomendasi)) {
                foreach ($rekomendasi as $r) {
            ?>

                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $r['nama']; ?></td>
                        <td><?php echo $r['deskripsi']; ?></td>
                    </tr>

                <?php
                }
            } else {
                ?>

                <tr>
                    <td colspan="4" class="text-center">Belum ada hasil rekomendasi</td>
                </tr>

            <?php } ?>

        </table>

        <div class="alert alert-warning">

            <b>Catatan :</b>

            Hasil analisis ini diperoleh dari jawaban kuisioner yang diisi oleh siswa.
            Rekomendasi karir dapat berubah seiring perkembangan minat,
            pengalaman belajar, dan lingkungan siswa.

        </div>

        <?php if (!isset($_GET['pdf'])): ?>
            <div class="flex-between">

                <!-- Isi Kuisioner -->
                <a href="Dashboard_Siswa.php" class="btn btn-warning">
                    <i class="bi bi-arrow-left-circle"></i> Beranda
                </a>

                <div>
                    <a href="<?= $link_wa ?>"
                        target="_blank"
                        class="btn btn-konsultasi">

                        <i class="bi bi-whatsapp"></i>
                        Konsultasi
                    </a>

                    <a href="Cetak_PDF.php"
                        class="btn btn-success">

                        <i class="bi bi-printer"></i>
                        Cetak PDF
                    </a>
                </div>

            </div>
        <?php endif; ?>

    </div>
</body>

</html>