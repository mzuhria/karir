<?php
include "koneksi.php";

/* Proses Import CSV */
$file = $_FILES['file_excel']['tmp_name'];

$handle = fopen($file, "r");

$row = 0;

while(($data = fgetcsv($handle, 1000, ";")) !== FALSE){

$row++;

if($row == 1){
continue;
}

$nama_karir = $data[0];
$kategori   = $data[1];
$jurusan    = $data[2];
$deskripsi  = $data[3];

mysqli_query($conn,"INSERT INTO karir
(nama_karir,kategori,jurusan,deskripsi)
VALUES
('$nama_karir','$kategori','$jurusan','$deskripsi')");

}

fclose($handle);

header("Location: Kelola_karir.php");

/* Proses Import manual */
if (isset($_POST['simpan'])) {

    $nama_karir = $_POST['nama_karir'];
    $kategori   = $_POST['kategori'];
    $jurusan    = $_POST['jurusan'];
    $deskripsi  = $_POST['deskripsi'];

    $query = mysqli_query($koneksi, "INSERT INTO karir 
        (nama_karir,kategori,jurusan,deskripsi)
        VALUES
        ('$nama_karir','$kategori','$jurusan','$deskripsi')
    ");

    if ($query) {
        header("Location: kelola_karir.php?pesan=berhasil");
    } else {
        echo "Data gagal disimpan";
    }
}

?>