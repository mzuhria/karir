<?php
session_start();
include "koneksi.php";

// 🔒 proteksi
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: Login_Siswa.php");
    exit;
}

$id_siswa = $_SESSION['id_siswa'];

$email  = $_POST['email'];
$alamat = $_POST['alamat'];
$no_hp  = $_POST['no_hp'];

// 🔥 DEBUG ERROR (sementara)
if (!$conn) {
    die("Koneksi gagal");
}

// update
$query = mysqli_query($conn, "
    UPDATE siswa SET
        email = '$email',
        alamat = '$alamat',
        no_hp = '$no_hp'
    WHERE id_siswa = '$id_siswa'
");

// 🔥 cek error query
if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

// redirect
header("Location: Data_Diri_Siswa.php?success=1");
exit;
?>