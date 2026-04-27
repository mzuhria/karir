<?php
include "koneksi.php";
$kelas = $_GET['kelas'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Registrasi Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #36b9cc, #4e73df);
            min-height: 100vh;
        }

        .card {
            border-radius: 15px;
            padding: 25px;
        }

        small {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow">

                    <h3 class="text-center mb-3">Registrasi Siswa</h3>

                    <form action="Process_daftar_siswa.php" method="POST">

                        <!-- NAMA -->
                        <div class="mb-2">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control"
                                placeholder="Contoh: Muhammad Zuhri Ardiansyah" required>
                            <small class="text-muted">Gunakan nama sesuai data sekolah</small>
                        </div>

                        <!-- KELAS -->
                        <div class="mb-2">
                            <label>Kelas</label>
                            <select name="kelas" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                <option value="X" <?= $kelas == 'X' ? 'selected' : '' ?>>X</option>
                                <option value="XI" <?= $kelas == 'XI' ? 'selected' : '' ?>>XI</option>
                                <option value="XII" <?= $kelas == 'XII' ? 'selected' : '' ?>>XII</option>
                            </select>
                            <small class="text-muted">Pilih sesuai kelas aktif</small>
                        </div>

                        <!-- JURUSAN -->
                        <div class="mb-2">
                            <label>Jurusan</label>
                            <select name="jurusan" class="form-control" required>
                                <option value="">-- Pilih Jurusan --</option>
                                <option value="TKR">TKR</option>
                                <option value="TKJ">TKJ</option>
                                <option value="DKV">DKV</option>
                                <option value="TPM">TPM</option>
                            </select>
                        </div>

                        <!-- SUBKELAS -->
                        <div class="mb-2">
                            <label>Sub Kelas</label>
                            <input type="text" name="subkelas" class="form-control"
                                placeholder="Contoh: 1 / 2 / 3" required>
                            <small class="text-muted">Isi sesuai pembagian kelas</small>
                        </div>

                        <!-- GURU BP -->
                        <div class="mb-2">
                            <label>Pilih Guru BP</label>
                            <select name="id_admin" class="form-control" required>

                                <option value="">-- Pilih Guru BP --</option>

                                <?php
                                if ($kelas != '') {
                                    $guru = mysqli_query(
                                        $conn,
                                        "SELECT * FROM admin WHERE role='guru_bp' AND kelas_akses='$kelas'"
                                    );

                                    while ($g = mysqli_fetch_assoc($guru)) {
                                        echo "<option value='{$g['id_admin']}'>
                                    {$g['nama_guru']}
                                  </option>";
                                    }
                                }
                                ?>

                            </select>
                            <small class="text-muted">Guru BP sesuai kelas akan muncul otomatis</small>
                        </div>

                        <!-- EMAIL -->
                        <div class="mb-2">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control"
                                placeholder="Contoh: nama@gmail.com" required>
                        </div>

                        <!-- USERNAME -->
                        <div class="mb-2">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control"
                                placeholder="Contoh: zuhri123" required>
                            <small class="text-muted">Gunakan tanpa spasi</small>
                        </div>

                        <!-- PASSWORD -->
                        <div class="mb-2">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="Minimal 6 karakter" required>
                        </div>

                        <!-- NO HP -->
                        <div class="mb-3">
                            <label>No HP</label>
                            <input type="text" name="no_hp" class="form-control"
                                placeholder="Contoh: 081234567890">
                        </div>

                        <button class="btn btn-primary w-100">
                            Daftar Sekarang
                        </button>

                    </form>

                    <div class="text-center mt-2">
                        <a href="Login_Siswa.php">Sudah punya akun? Login</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>