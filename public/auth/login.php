<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

require_once "../bootstrap.php";

$site_root = $_SESSION["site_root"];
// echo $site_root;
// die();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <script>
        // const base_url = "http://localhost/ppkpi/project_psigit/POS"; // auto-set by PHP
        // const base_url = "http://jaringan.com/202503/POS-ABROOR"; // auto-set by PHP
        const base_url = "<?= $site_root; ?>";

        console.log(base_url);
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <title>Login</title>

    <style>
        /* Biar kontainer penuh tinggi viewport dan pakai flex untuk center */
        .full-height-center {
            height: 100vh;
            display: flex;
            justify-content: center;
            /* horizontal center */
            align-items: center;
            /* vertical center */
        }

        body {
            background: #a3a7aaff;
        }
    </style>
</head>

<body>
    <div class="container full-height-center">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold">Login</h3>
                            <p class="text-muted">Masuk ke akun Anda</p>
                        </div>
                        <form method="post" id="form-login">
                            <div class="mb-3">
                                <label for="login_email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="login_email" name="email" required
                                    autofocus />
                            </div>

                            <div class="mb-3">
                                <label for="login_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="login_password" name="password"
                                    required />
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <input type="checkbox" id="remember_me" name="remember_me" />
                                    <label for="remember_me" class="form-label mb-0">Remember me</label>
                                </div>
                                <!-- <a href="#" class="text-decoration-none">Lupa password?</a> -->
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn-light bg-gradient">Reset</button>
                                <button type="submit" class="btn btn-primary bg-gradient">Login</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <small>
                                <p class="mb-0">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                            </small>
                        </div>
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