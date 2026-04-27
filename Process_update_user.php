<?php
session_start();
include "koneksi.php";

// 🔒 CEK LOGIN
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: Login_Admin.php");
    exit;
}

// 🔹 AMBIL DATA
$id       = $_POST['id'];
$nama     = $_POST['nama'];
$username = $_POST['username'];
$password = $_POST['password'];
$kelas_akses = $_POST['kelas_akses'];

// 🔹 VALIDASI SEDERHANA
if (empty($nama) || empty($username)) {
    die("Data tidak boleh kosong!");
}

// 🔥 CEK USERNAME DUPLIKAT (KECUALI DIRI SENDIRI)
$cek = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username' AND id_admin != '$id'");
if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Username sudah digunakan!'); window.history.back();</script>";
    exit;
}

// ===============================
// 🔥 UPDATE DATA
// ===============================
if (!empty($password)) {
    // jika password diisi → update semua
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE admin SET nama_guru=?, username=?, password=?, kelas_akses=? WHERE id_admin=?");
    $stmt->bind_param("ssssi", $nama, $username, $hash, $kelas_akses, $id);;
} else {
    // jika password kosong → jangan ubah password
    $stmt = $conn->prepare("UPDATE admin SET nama_guru=?, username=?, kelas_akses=? WHERE id_admin=?");
    $stmt->bind_param("sssi", $nama, $username, $kelas_akses, $id);
}

// eksekusi
if ($stmt->execute()) {
    echo "<script>alert('Data berhasil diupdate'); window.location='Kelola_user.php';</script>";
} else {
    echo "Gagal update: " . $stmt->error;
}
