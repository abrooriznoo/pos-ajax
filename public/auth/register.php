<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <script>
        const base_url = "http://localhost/ppkpi/project_psigit/POS"; // auto-set by PHP
        // console.log(base_url);
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <title>Registrasi</title>
    <style>
        body {
            background: #a3a7aaff;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold">Form Registrasi</h3>
                            <p class="text-muted">Silakan masukkan data lengkap Anda</p>
                        </div>
                        <form method="post" enctype="multipart/form-data" id="form-register">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required
                                    autofocus />
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" required />
                                <div class="form-text">Kami tidak akan membagikan email Anda ke siapa pun.</div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required />
                            </div>


                            <div class="mb-3">
                                <label for="password" class="form-label">Rewrite Password</label>
                                <input type="password" class="form-control" id="password-rewrite"
                                    name="password-rewrite" required />
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                                    required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block">Jenis Kelamin</label>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="jk_laki" name="jenis_kelamin"
                                        value="Laki-laki" required />
                                    <label class="form-check-label" for="jk_laki">Laki-laki</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="jk_perempuan" name="jenis_kelamin"
                                        value="Perempuan" required />
                                    <label class="form-check-label" for="jk_perempuan">Perempuan</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="kebutuhan_khusus" class="form-label">Kebutuhan Khusus</label>
                                <select class="form-select" id="kebutuhan_khusus" name="kebutuhan_khusus" required>
                                    <option value="" disabled selected>Pilih salah satu</option>
                                    <option value="tidak">Tidak</option>
                                    <option value="tuna_rungu">Tuna Rungu</option>
                                    <option value="tuna_grahita">Tuna Grahita</option>
                                    <option value="tuna_wicara">Tuna Wicara</option>
                                </select>
                            </div>


                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-start">
                                    <small class="text-muted d-block d-sm-inline">
                                        <span class="d-block d-sm-inline">Sudah punya akun?</span>
                                        <a href="login.php" class="text-decoration-none d-block d-sm-inline">Masuk di
                                            sini</a>
                                    </small>
                                </div>
                                <div class="justify-content-end gap-2">
                                    <button type="reset" class="btn btn-light bg-gradient">Reset</button>
                                    <button type="submit" class="btn btn-primary bg-gradient">Register</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/setAuth.js"></script>
</body>

</html>