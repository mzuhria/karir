<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Admin</title>

    <link rel="stylesheet" href="bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            height: 100vh;
            background: linear-gradient(135deg, #0d6efd, #6ea8fe);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 380px;
            background: #fff;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-box h2 {
            text-align: center;
            font-weight: 600;
            margin-bottom: 25px;
        }

        label {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            padding: 10px;
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, .25);
        }

        .btn-primary {
            border-radius: 8px;
            padding: 10px;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-primary:hover {
            transform: scale(1.03);
        }

        .btn-outline-success {
            border-radius: 8px;
        }

        hr {
            margin: 20px 0;
        }

        .alert {
            font-size: 13px;
        }
    </style>
</head>

<body>

    <div class="login-box">
        <h2>Daftar Admin</h2>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center">
                Username atau password sudah digunakan
            </div>
        <?php endif; ?>

        <form method="POST" action="Process_daftar_admin.php">
            <div class="mb-3">
                <label>Nama Guru</label>
                <input type="text" name="nama_guru" class="form-control" placeholder="Masukkan nama guru">
            </div>

            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>

            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin">Admin</option>
                    <option value="guru_bp">Guru BK</option>
                    <option value="kepala_sekolah">Kepala Sekolah</option>
                </select>
            </div>

            <button class="btn btn-primary w-100">Daftar</button>

            <hr>

            <div class="text-center">
                <a href="Login_Admin.php"btn btn-outline-success w-100">
                    ← Login Admin
                </a>
            </div>

        </form>
    </div>

    <script>
        setTimeout(function() {
            let alertBox = document.querySelector('.alert');
            if (alertBox) {
                alertBox.style.display = 'none';
            }
        }, 3000);
    </script>

</body>

</html>