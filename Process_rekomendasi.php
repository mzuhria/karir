<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['id_siswa'])) {
    header("Location: Kuisioner.php");
    exit;
}

$id_siswa = $_SESSION['id_siswa'];


// ==============================
// 1. AMBIL JURUSAN
// ==============================
$data = mysqli_query($conn, "SELECT jurusan FROM siswa WHERE id_siswa='$id_siswa'");
$siswa = mysqli_fetch_assoc($data);
$jurusan = $siswa['jurusan'] ?? '';


// ==============================
// 2. STOPWORD
// ==============================
$stopwords = ['saya', 'dan', 'yang', 'dengan', 'di', 'ke', 'dari', 'untuk', 'pada', 'dalam'];

function clean_text($text, $stopwords)
{
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s]/', '', $text);
    $words = explode(" ", $text);

    $filtered = [];
    foreach ($words as $w) {
        if ($w != "" && !in_array($w, $stopwords)) {
            $filtered[] = $w;
        }
    }

    return implode(" ", $filtered);
}


// ==============================
// 3. HITUNG RIASEC (FILTER)
// ==============================
$riasec = [];

$q = mysqli_query($conn, "
SELECT k.kategori, SUM(j.nilai) as total
FROM jawaban j
JOIN kuisioner k ON j.id_soal = k.id_soal
WHERE j.id_siswa='$id_siswa'
GROUP BY k.kategori
ORDER BY total DESC
");

$riasec = [];

while ($row = mysqli_fetch_assoc($q)) {
    $riasec[$row['kategori']] = $row['total'];
}

// urutkan dari terbesar
arsort($riasec);

// ambil top 3
$keys = array_keys($riasec);

$tipe1 = $keys[0] ?? '';
$tipe2 = $keys[1] ?? '';
$tipe3 = $keys[2] ?? '';


// ==============================
// 3.1 BOBOT RIASEC (PENTING 🔥)
// ==============================
$bobot = [
    $tipe1 => 1.0,   // paling dominan
    $tipe2 => 0.8,   // kedua
    $tipe3 => 0.6    // ketiga
];

// ==============================
// 4. PROFIL TEXT USER
// ==============================
$profil_text = "";

$query = mysqli_query($conn, "
SELECT k.pertanyaan, j.nilai
FROM jawaban j
JOIN kuisioner k ON j.id_soal = k.id_soal
WHERE j.id_siswa='$id_siswa'
");

while ($row = mysqli_fetch_assoc($query)) {

    $text = clean_text($row['pertanyaan'], $stopwords);
    $nilai = $row['nilai'];

    for ($i = 0; $i < $nilai; $i++) {
        $profil_text .= " " . $text;
    }
}

if (trim($profil_text) == "") {
    $profil_text = "umum";
}


// ==============================
// 5. AMBIL KARIR (FILTER RIASEC)
// ==============================
$documents = [];
$karir_data = [];

$documents[] = $profil_text;

$q = mysqli_query($conn, "
SELECT * FROM karir 
WHERE jurusan='$jurusan'
AND kategori IN ('$tipe1','$tipe2','$tipe3')
");

while ($row = mysqli_fetch_assoc($q)) {
    $text = clean_text($row['deskripsi'] . " " . $row['nama_karir'], $stopwords);

    $documents[] = $text;
    $karir_data[] = [
        'data' => $row,
        'text' => $text
    ];
}


// ==============================
// 6. HITUNG IDF
// ==============================
function compute_idf($documents)
{
    $df = [];
    $N = count($documents);

    foreach ($documents as $doc) {
        $words = array_unique(explode(" ", $doc));

        foreach ($words as $w) {
            if ($w == "") continue;
            if (!isset($df[$w])) $df[$w] = 0;
            $df[$w]++;
        }
    }

    $idf = [];
    foreach ($df as $word => $val) {
        $idf[$word] = log($N / $val);
    }

    return $idf;
}


// ==============================
// 7. TF-IDF
// ==============================
function tfidf_vector($text, $idf)
{
    $words = explode(" ", $text);
    $tf = [];

    foreach ($words as $w) {
        if ($w == "") continue;
        if (!isset($tf[$w])) $tf[$w] = 0;
        $tf[$w]++;
    }

    $vector = [];

    foreach ($tf as $word => $freq) {
        $vector[$word] = $freq * ($idf[$word] ?? 0);
    }

    return $vector;
}


// ==============================
// 8. COSINE
// ==============================
function cosine($v1, $v2)
{

    $dot = 0;
    $n1 = 0;
    $n2 = 0;

    $keys = array_unique(array_merge(array_keys($v1), array_keys($v2)));

    foreach ($keys as $k) {
        $a = $v1[$k] ?? 0;
        $b = $v2[$k] ?? 0;

        $dot += $a * $b;
        $n1 += $a * $a;
        $n2 += $b * $b;
    }

    if ($n1 == 0 || $n2 == 0) return 0;

    return $dot / (sqrt($n1) * sqrt($n2));
}


// ==============================
// 9. PROSES TF-IDF + COSINE
// ==============================
$idf = compute_idf($documents);

$profil_vector = tfidf_vector($profil_text, $idf);

$hasil = [];

foreach ($karir_data as $k) {

    $karir_vector = tfidf_vector($k['text'], $idf);

    $similarity = cosine($profil_vector, $karir_vector);

    $kategori = $k['data']['kategori'];
    $weight = $bobot[$kategori] ?? 0.1;

    $final_score = $similarity * $weight;

    $hasil[] = [
        'id_karir' => $k['data']['id_karir'],
        'nama' => $k['data']['nama_karir'],
        'deskripsi' => $k['data']['deskripsi'],
        'score' => $final_score
    ];
}


// ==============================
// 10. SORTING
// ==============================
usort($hasil, function ($a, $b) {
    return $b['score'] <=> $a['score'];
});

// ==============================
// 11. TOP 3 (UNTUK DITAMPILKAN)
// ==============================
$top3 = array_slice($hasil, 0, 3);

// ==============================
// 12. SIMPAN TOP 3 KE DATABASE
// ==============================
mysqli_query($conn, "DELETE FROM hasil WHERE id_siswa='$id_siswa'");

foreach ($top3 as $h) {

    $score = round($h['score'], 4);

    mysqli_query($conn, "
    INSERT INTO hasil (id_siswa, id_karir, skor, tanggal)
    VALUES ('$id_siswa', '" . $h['id_karir'] . "', '$score', NOW())
    ");
}

// ==============================
// 13. SIMPAN KE SESSION (TOP 3)
// ==============================
$_SESSION['rekomendasi'] = $top3;

// ==============================
// 14. REDIRECT
// ==============================
header("Location: Hasil.php");
exit;
