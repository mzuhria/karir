<?php
session_start();
include "koneksi.php";

if(!isset($_POST['jawaban'])){
    echo "Jawaban tidak ditemukan";
    exit;
}

$id_siswa = $_SESSION['id_siswa'];
$jawaban = $_POST['jawaban'];

foreach($jawaban as $id_soal => $nilai){

mysqli_query($conn,"INSERT INTO jawaban
(id_siswa,id_soal,nilai)
VALUES
('$id_siswa','$id_soal','$nilai')");

}

header("Location: debug.php");
exit;
?>