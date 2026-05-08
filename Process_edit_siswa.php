<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: Login_Admin.php");
    exit;
}

$id      = $_POST['id_siswa'];
$nama    = $_POST['nama'];
$alamat  = $_POST['alamat'];
$no_hp   = $_POST['no_hp'];

mysqli_query($conn, "
    UPDATE siswa SET
    nama='$nama',
    alamat='$alamat',
    no_hp='$no_hp'
    WHERE id_siswa='$id'
");

header("Location: Data_siswa.php");
exit;
?>