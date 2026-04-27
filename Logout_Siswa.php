<?php
session_start();

// 🔥 Hapus semua session
session_unset();
session_destroy();

// 🔁 Redirect ke login siswa
header("Location: Login_Siswa.php");
exit;
?>