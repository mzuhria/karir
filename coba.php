<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuisioner</title>
    <link rel="stylesheet" href="bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        html,
        body {
            height: 100%;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: linear-gradient(135deg, #ce3af3, #224abe);
            color: #333;
            padding-top: 90px;
        }

        /* CONTENT */
        .content {
            flex: 1;
        }

        .form-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .navbar {
            background: rgba(0, 0, 0, 0.6) !important;
            backdrop-filter: blur(10px);
        }


        /* FOOTER */
        footer {
            background: rgba(0, 0, 0, 0.6) !important;
            color: white;
            text-align: center;
            backdrop-filter: blur(10px);
            padding: 10px 0;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
        <div class="container">

            <!-- LOGO / BRAND (KIRI) -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="img/logosekolah.png" height="60" class="me-2">

                <div>
                    <div class="fw-bold">SMK Dharma Bahari Surabaya</div>
                    <small class="text-light opacity-75">
                        "Karakter Kuat, Prestasi Hebat, Masa Depan Siap"
                    </small>
                </div>
            </a>

            <!-- TOGGLE MOBILE -->
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- MENU (KANAN) -->
            <div class="collapse navbar-collapse justify-content-end" id="navMenu">
                <ul class="navbar-nav gap-3">
                    <li class="nav-item"><a class="nav-link" href="Beranda.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link active" href="Kuisioner.php">Kuisioner</a></li>
                </ul>
            </div>

        </div>
    </nav>

    <!-- FORM -->
    <div class="content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="form-box">
                        <div class="header text-center mb-4">
                            <H4>Selamat Mengisi kuisioner</H4>
                            <small>Dapatkan rekomendasi karir berdasarkan minat dan potensi secara cepat dan akurat.</small>
                        </div>
                        <form action="SoalKuisioner.php" method="POST">

                            <!-- NAMA -->
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Nama</label>
                                <div class="col-sm-9">
                                    <input type="text" name="nama" class="form-control" placeholder="Nama" required>
                                </div>
                            </div>

                            <!-- KELAS -->
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Kelas</label>
                                <div class="col-sm-9">
                                    <select name="kelas" class="form-select" id="kelas" required>
                                        <option value="">Pilih Kelas</option>
                                        <option value="X">X</option>
                                        <option value="XI">XI</option>
                                        <option value="XII">XII</option>
                                    </select>
                                </div>
                            </div>

                            <!-- JURUSAN -->
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Jurusan</label>
                                <div class="col-sm-9">
                                    <select name="jurusan" class="form-select" id="jurusan" required>
                                        <option value="">Pilih Jurusan</option>
                                        <option value="TKR">TKR</option>
                                        <option value="TKJ">TKJ</option>
                                        <option value="DKV">DKV</option>
                                        <option value="TPM">TPM</option>
                                    </select>
                                </div>
                            </div>

                            <!-- SUB KELAS -->
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Sub Kelas</label>
                                <div class="col-sm-9">
                                    <select name="subkelas" class="form-select" id="subkelas" required>
                                        <option value="">Pilih Sub Kelas</option>
                                    </select>
                                </div>
                            </div>

                            <!-- EMAIL -->
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                                </div>
                            </div>

                            <!-- BUTTON -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary mt-3 px-4 py-2">
                                    Next
                                </button>
                            </div>

                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer footer-expand-lg footer-dark bg-dark">
        <p>© 2026 SMK Dharma Bahari Surabaya - "Karakter Kuat, Prestasi Hebat, Masa Depan Siap"</p>
    </footer>

    <script>
        const kelas = document.getElementById("kelas");
        const jurusan = document.getElementById("jurusan");
        const subKelas = document.getElementById("subkelas");

        function updateSubkelas() {

            // pastikan kelas dan jurusan sudah dipilih
            if (!kelas.value || !jurusan.value) return;

            let kelasDipilih = kelas.value;
            let jurusanDipilih = jurusan.value; // ambil jurusan juga

            // kosongkan dropdown
            subKelas.innerHTML = '<option selected disabled>Pilih Sub Kelas</option>';

            let daftarSub = ["1", "2", "3", "4", "5"];

            daftarSub.forEach(function(huruf) {
                let option = document.createElement("option");

                // GABUNG kelas + jurusan + huruf
                option.value = kelasDipilih + " " + jurusanDipilih + " " + huruf;
                option.text = kelasDipilih + " " + jurusanDipilih + " " + huruf;

                subkelas.appendChild(option);
            });
        }

        // jalankan saat kelas atau jurusan berubah
        kelas.addEventListener("change", updateSubkelas);
        jurusan.addEventListener("change", updateSubkelas);
    </script>

</body>

</html>