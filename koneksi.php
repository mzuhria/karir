<?php
$conn = mysqli_connect("localhost", "root", "", "dharma_bahari");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>