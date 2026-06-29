<?php
session_start();
include "koneksi.php";

$metode = $_POST['metode'] ?? '';

// ======================================
// PUBLIKASI MANUAL
// ======================================
if ($metode == "manual") {

    if (!isset($_POST['soal'])) {

        echo "
        <script>
            alert('Pilih minimal satu soal!');
            history.back();
        </script>
        ";
        exit;
    }

    // reset
    mysqli_query($conn, "
    UPDATE kuisioner
    SET status_publikasi = 0
    ");

    // aktifkan yang dipilih
    foreach ($_POST['soal'] as $id_soal) {

        mysqli_query($conn, "
        UPDATE kuisioner
        SET status_publikasi = 1
        WHERE id_soal = '$id_soal'
        ");
    }

    echo "
    <script>
        alert('Soal berhasil dipublikasikan!');
        window.location='Kelola_soal.php';
    </script>
    ";
    exit;
}

if ($metode == "random") {
$jurusan = $_POST['jurusan'];
$jumlah  = (int) $_POST['jumlah_soal'];

// cek total soal tersedia
$cek = mysqli_query($conn, "
    SELECT COUNT(*) as total
    FROM kuisioner
    WHERE jurusan='$jurusan'
");

$data = mysqli_fetch_assoc($cek);
$totalSoal = $data['total'];

// validasi jumlah soal
if ($jumlah > $totalSoal) {

    echo "
    <script>
        alert('Jumlah soal melebihi soal yang tersedia ($totalSoal soal)');
        history.back();
    </script>
    ";
    exit;
}

// daftar kategori RIASEC
$kategori = [
    'Realistic',
    'Investigative',
    'Artistic',
    'Social',
    'Enterprising',
    'Conventional'
];

// pembagian soal
$perKategori = floor($jumlah / 6);
$sisa = $jumlah % 6;

$jmlKategori = [];

foreach ($kategori as $k) {
    $jmlKategori[$k] = $perKategori;
}

// distribusi sisa mulai dari kategori pertama
for ($i = 0; $i < $sisa; $i++) {
    $jmlKategori[$kategori[$i]]++;
}

// cek ketersediaan soal tiap kategori
foreach ($kategori as $k) {

    $cekKategori = mysqli_query($conn, "
        SELECT COUNT(*) as total
        FROM kuisioner
        WHERE jurusan='$jurusan'
        AND kategori='$k'
    ");

    $dataKategori = mysqli_fetch_assoc($cekKategori);

    if ($dataKategori['total'] < $jmlKategori[$k]) {

        echo "
        <script>
            alert('Soal kategori $k tidak mencukupi.');
            history.back();
        </script>
        ";
        exit;
    }
}

// reset publikasi
mysqli_query($conn, "
    UPDATE kuisioner
    SET status_publikasi = 0
    WHERE jurusan='$jurusan'
");

// random soal per kategori
foreach ($kategori as $k) {

    $limit = $jmlKategori[$k];

    if ($limit > 0) {

        $q = mysqli_query($conn, "
            SELECT id_soal
            FROM kuisioner
            WHERE jurusan='$jurusan'
            AND kategori='$k'
            ORDER BY RAND()
            LIMIT $limit
        ");

        while ($row = mysqli_fetch_assoc($q)) {

            mysqli_query($conn, "
                UPDATE kuisioner
                SET status_publikasi = 1
                WHERE id_soal='".$row['id_soal']."'
            ");
        }
    }
}

echo "
<script>
    alert('Publikasi soal berhasil!');
    window.location='Kelola_soal.php?filter_jurusan=$jurusan';
</script>
";
exit;
}

?>