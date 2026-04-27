<?php
session_start();
include "koneksi.php";

// Ambil input dengan aman
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = $_POST['password'];

// Ambil data user
$query = mysqli_query($conn, "
    SELECT * FROM admin 
    WHERE TRIM(username) = TRIM('$username')
");
$data  = mysqli_fetch_assoc($query);

//  Validasi login
if ($data && password_verify($password, $data['password'])) {

    // SESSION LENGKAP
    $_SESSION['login']      = true;
    $_SESSION['id_admin']   = $data['id_admin'];
    $_SESSION['kelas_akses'] = $data['kelas_akses'];
    $_SESSION['nama_guru'] = !empty($data['nama_guru'])
        ? $data['nama_guru']
        : $data['username'];
    $_SESSION['username']   = $data['username'];
    $_SESSION['role']       = $data['role'];

    // REDIRECT SESUAI ROLE
    if ($data['role'] == 'admin') {
        header("Location: Admin.php");
        exit;
    } elseif ($data['role'] == 'guru_bp') {
        header("Location: Dashboard_gurubp.php");
        exit;
    } elseif ($data['role'] == 'kepala_sekolah') {
        header("Location: Dashboard_kepsek.php");
        exit;
    }
} else {
    //  LOGIN GAGAL
    header("Location: Login_Admin.php?error=1");
    exit;
}
