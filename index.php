<?php
session_start();
require_once 'config/koneksi.php';
require_once 'config/function.php';

// Cek apakah sudah login
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['level'] == 1) {
        header("Location: admin/index.php");
    } else {
        header("Location: pelanggan/index.php");
    }
    exit();
}

// Proses login
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);
    
    // Cek login admin
    $query_admin = "SELECT * FROM user WHERE username='$username' AND password='$password'";
    $result_admin = mysqli_query($koneksi, $query_admin);
    
    // Cek login pelanggan
    $query_pelanggan = "SELECT * FROM pelanggan WHERE username='$username' AND password='$password'";
    $result_pelanggan = mysqli_query($koneksi, $query_pelanggan);
    
    if (mysqli_num_rows($result_admin) > 0) {
        $data = mysqli_fetch_assoc($result_admin);
        $_SESSION['user_id'] = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama'] = $data['nama_admin'];
        $_SESSION['level'] = $data['id_level'];
        
        header("Location: admin/index.php");
        exit();
    } elseif (mysqli_num_rows($result_pelanggan) > 0) {
        $data = mysqli_fetch_assoc($result_pelanggan);
        $_SESSION['user_id'] = $data['id_pelanggan'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama'] = $data['nama_pelanggan'];
        $_SESSION['level'] = 2; // Level pelanggan
        
        header("Location: pelanggan/index.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Aplikasi Pembayaran Listrik</title>
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <!-- <div class="col-lg-6 d-none d-lg-block bg-login-image"></div> -->
                             <div class="col-lg-6">
                                <img src="<?= BASE_URL ?>assets/img/listrik.svg" alt="Listrik" class="img-fluid p-5">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Aplikasi Pembayaran Listrik Pascabayar</h1>
                                    </div>
                                    <?php if(isset($error)): ?>
                                    <div class="alert alert-danger">
                                        <?= $error ?>
                                    </div>
                                    <?php endif; ?>
                                    <form class="user" method="post" action="">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" name="username" placeholder="Username" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" name="password" placeholder="Password" required>
                                        </div>
                                        <button type="submit" name="login" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="assets/js/sb-admin-2.min.js"></script>
</body>
</html>