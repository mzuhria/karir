<?php
include "koneksi.php";

$nama     = $_POST['nama_guru'];
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role     = $_POST['role'];

if (empty($role)) {
    header("Location: Daftar_login_admin.php?error=role");
    exit;
}

// cek username sudah ada
$cek = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");

if(mysqli_num_rows($cek) > 0){
    header("Location: Daftar_login_admin.php?error=1");
    exit;
}

// simpan data + role
$query = mysqli_query($conn, "
INSERT INTO admin(nama_guru, username, password, role)
VALUES('$nama','$username','$password','$role')
");

if($query){
    header("Location: Daftar_login_admin.php?success=1");
}else{
    header("Location: Daftar_login_admin.php?error=2");
}
exit;
?>