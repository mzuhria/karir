<?php
session_start();
include "koneksi.php";

// Proteksi Login
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'guru_bp') {
    header("Location: Login_Admin.php");
    exit;
}

$id_admin = $_SESSION['id_admin'];

// =======================
// TAMBAH SOAL
// =======================
if (isset($_POST['tambah'])) {

    $pertanyaan = $_POST['pertanyaan'];
    $kategori   = $_POST['kategori'];
    $jurusan    = $_POST['jurusan'];

    mysqli_query($conn, "
        INSERT INTO kuisioner
        (pertanyaan, jurusan, kategori, id_admin)
        VALUES
        ('$pertanyaan', '$jurusan', '$kategori', '$id_admin')
    ");

    header("Location: Kelola_soal.php");
    exit;
}

// =======================
// UPDATE SOAL
// =======================
if (isset($_POST['update'])) {

    $id         = $_POST['id_soal'];
    $pertanyaan = $_POST['pertanyaan'];
    $kategori   = $_POST['kategori'];
    $jurusan    = $_POST['jurusan'];

    mysqli_query($conn, "
        UPDATE kuisioner
        SET
            pertanyaan='$pertanyaan',
            kategori='$kategori',
            jurusan='$jurusan'
        WHERE id_soal='$id'
        AND id_admin='$id_admin'
    ");

    header("Location: Kelola_soal.php");
    exit;
}

// =======================
// HAPUS SOAL
// =======================
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    mysqli_query($conn, "
        DELETE FROM kuisioner
        WHERE id_soal='$id'
        AND id_admin='$id_admin'
    ");

    header("Location: Kelola_soal.php");
    exit;
}
?>