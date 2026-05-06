<?php
session_start();
$no_hp = $_SESSION['no_hp_guru'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Style tambahan -->
    <style>
        body {
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            height: 100vh;
        }

        .login-card {
            border-radius: 15px;
            padding: 30px;
        }

        .login-title {
            font-weight: bold;
        }

        .btn-login {
            background-color: #4e73df;
            border: none;
        }

        .btn-login:hover {
            background-color: #2e59d9;
        }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card login-card shadow col-md-4">

            <div class="text-center mb-4">
                <h3 class="login-title">Login Siswa</h3>
                <p class="text-muted">Silakan masuk ke akun Anda</p>
            </div>

            <?php if (isset($_GET['error'])): ?>

                <?php if ($_GET['error'] == 'username' || $_GET['error'] == 'password'): ?>
                    <div class="alert alert-danger">
                        Username atau Password salah!
                    </div>

                <?php elseif ($_GET['error'] == 'belum_aktif'): ?>
                    <div class="alert alert-warning">
                        Akun Anda belum diaktivasi! <br><br>

                        <?php if ($no_hp): ?>
                            <?php $wa = "62" . substr($no_hp, 1); ?>

                            Hubungi Guru BP Anda: <br>
                            <b><?= $no_hp ?></b>

                    
                        <?php endif; ?>
                    </div>

                <?php endif; ?>

            <?php endif; ?>

            <form action="Process_login_siswa.php" method="POST">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-login text-white">Login</button>
                </div>

            </form>

            <div class="text-center mt-3">
                <p class="mb-1">Belum punya akun?</p>

                <a href="Daftar_login_siswa.php" class="btn btn-outline-success w-100 py-2 fw-semibold">
                    Registrasi
                </a>
                <small class="text-muted">©SMK DHARMA BAHARI SURABAYA</small>
            </div>

        </div>
    </div>

</body>

</html>