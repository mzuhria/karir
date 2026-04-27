<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Reset dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* Background halaman */
        body {
            height: 100vh;
            background: linear-gradient(135deg, #ce3af3, #224abe);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Container utama */
        .login-container {
            width: 100%;
        }

        /* Box login */
        .login-box {
            width: 350px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            margin: 0 auto;
        }

        /* Judul */
        .login-box h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
        }

        /* Input group */
        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
        }

        /* Input field */
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        /* Tombol login */
        button {
            width: 100%;
            padding: 12px;
        }

        hr {
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="login-container">

        <div class="login-box">
            <h2>Login Admin</h2>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger text-center mx-auto py-1 small" style="width:100%;" role="alert">
                    Username atau password salah!
                </div>
            <?php endif; ?>

            <form method="POST" action="Process_login_admin.php">

                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Masukkan username" required>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>

                <button class="btn btn-primary">Login</button>
                <hr>
                <div class="text-center">
                    <p class="mb-2">Belum punya akun?</p>
                    <a href="Daftar_login_admin.php" class="btn btn-outline-success">Daftar Sekarang</a>
                </div>

            </form>
        </div>

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