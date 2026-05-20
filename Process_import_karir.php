<?php
session_start();
include "koneksi.php";

$id_admin = $_SESSION['id_admin'];

$file = $_FILES['file_excel']['tmp_name'];

$handle = fopen($file,"r");

$row=0;

while(($data=fgetcsv($handle,1000,";")) !== FALSE){

    $row++;

    if($row==1){
        continue;
    }

    $nama_karir=$data[0];
    $kategori=$data[1];
    $jurusan=$data[2];
    $deskripsi=$data[3];

    mysqli_query($conn,"
    INSERT INTO karir(
    nama_karir,
    kategori,
    jurusan,
    deskripsi,
    id_admin
    )
    VALUES(
    '$nama_karir',
    '$kategori',
    '$jurusan',
    '$deskripsi',
    '$id_admin'
    )");

}

fclose($handle);

header("Location: Kelola_karir.php");
exit;
?>