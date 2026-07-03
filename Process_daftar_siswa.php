<?php
include "koneksi.php";

// ambil data
$nama      = htmlspecialchars($_POST['nama']);
$kelas     = htmlspecialchars($_POST['kelas']);
$jurusan   = htmlspecialchars($_POST['jurusan']);
$subkelas  = htmlspecialchars($_POST['subkelas']);
$email     = htmlspecialchars($_POST['email']);
$username  = htmlspecialchars($_POST['username']);
$no_hp     = htmlspecialchars($_POST['no_hp']);
$password  = $_POST['password'];
$id_admin = $_POST['id_admin'] ?? '';

//validasi guru bp
if (empty($id_admin)) {
    echo "<script>alert('Pilih guru BP terlebih dahulu!');history.back();</script>";
    exit;
}

// validasi sederhana
if (empty($nama) || empty($kelas) || empty($jurusan) || empty($username) || empty($password)) {
    echo "<script>alert('Data wajib diisi!');history.back();</script>";
    exit;
}

// cek username sudah ada
$cek = mysqli_query($conn, "SELECT * FROM siswa WHERE username='$username'");
if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Username sudah digunakan!');history.back();</script>";
    exit;
}

// hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// default status
$status = 'nonaktif';

// insert data
$query = "INSERT INTO siswa 
(nama, kelas, jurusan, subkelas, email, username, password, no_hp, status, id_admin, created_at)
VALUES 
('$nama','$kelas','$jurusan','$subkelas','$email','$username','$password_hash','$no_hp','$status','$id_admin', NOW())";

if (mysqli_query($conn, $query)) {
    header("Location: Login_siswa.php?success=1");
} else {
    echo "Gagal menyimpan data!";
}