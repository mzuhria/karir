<?php
session_start();
include "koneksi.php";

// 🔒 Ambil input
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = $_POST['password'];

// 🔍 Ambil data user
$query = mysqli_query($conn, "SELECT * FROM siswa WHERE username='$username'");
$data  = mysqli_fetch_assoc($query);

if ($data) {

    // 🔐 cek password
    if (password_verify($password, $data['password'])) {

        // 🔥 CEK STATUS (POSISI BENAR)
        if (strtolower($data['status']) != 'aktif') {

            // 🔥 ambil no HP guru BP berdasarkan id_admin siswa
            $id_admin = $data['id_admin'];

            $guru = mysqli_query($conn, "SELECT no_hp FROM admin WHERE id_admin='$id_admin'");
            $data_guru = mysqli_fetch_assoc($guru);

            // simpan ke session
            $_SESSION['no_hp_guru'] = $data_guru['no_hp'] ?? null;

            header("Location: Login_Siswa.php?error=belum_aktif");
            exit;
        }

        // ✅ session login
        $_SESSION['login']     = true;
        $_SESSION['role']      = 'siswa';
        $_SESSION['id_siswa']  = $data['id_siswa'];
        $_SESSION['nama']      = $data['nama'];
        $_SESSION['kelas']     = $data['kelas'];
        $_SESSION['jurusan']   = $data['jurusan'];
        $_SESSION['subkelas']  = $data['subkelas'];
        $_SESSION['email']     = $data['email'];
        $_SESSION['id_admin']  = $data['id_admin'];

        header("Location: Dashboard_Siswa.php");
        exit;
    } else {
        header("Location: Login_Siswa.php?error=password");
        exit;
    }
} else {
    header("Location: Login_Siswa.php?error=username");
    exit;
}
