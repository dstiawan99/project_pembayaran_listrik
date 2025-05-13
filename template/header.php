<?php
session_start();
require_once '../config/koneksi.php';
require_once '../config/function.php';

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

// Cek level akses
$current_folder = basename(dirname($_SERVER['PHP_SELF']));
$level = $_SESSION['level']; // Ambil level dari session

if ($current_folder == 'admin' && $level != 1) {
    header("Location: " . BASE_URL . "pelanggan/index.php");
    exit();
} elseif ($current_folder == 'pelanggan' && $level != 2) {
    header("Location: " . BASE_URL . "admin/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Aplikasi Pembayaran Listrik</title>
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="sidebar-brand-text mx-3">PLN Pascabayar</div>
            </a>
            <hr class="sidebar-divider my-0">
            
            <?php if($level == 1): // Menu Admin ?>
            <!-- Menu Admin -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Master Data</div>
            <li class="nav-item">
                <a class="nav-link" href="pelanggan.php">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Data Pelanggan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="tarif.php">
                    <i class="fas fa-fw fa-money-bill"></i>
                    <span>Data Tarif</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="user.php">
                    <i class="fas fa-fw fa-user-cog"></i>
                    <span>Data User</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Transaksi</div>
            <li class="nav-item">
                <a class="nav-link" href="penggunaan.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Penggunaan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="tagihan.php">
                    <i class="fas fa-fw fa-file-invoice"></i>
                    <span>Tagihan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pembayaran.php">
                    <i class="fas fa-fw fa-cash-register"></i>
                    <span>Pembayaran</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Laporan</div>
            <li class="nav-item">
                <a class="nav-link" href="laporan.php">
                    <i class="fas fa-fw fa-print"></i>
                    <span>Laporan</span>
                </a>
            </li>
            
            <?php else: // Menu Pelanggan ?>
            <!-- Menu Pelanggan -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link" href="tagihan.php">
                    <i class="fas fa-fw fa-file-invoice"></i>
                    <span>Tagihan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="penggunaan.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Riwayat Penggunaan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pembayaran.php">
                    <i class="fas fa-fw fa-cash-register"></i>
                    <span>Riwayat Pembayaran</span>
                </a>
            </li>
            <?php endif; ?>
            
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $_SESSION['nama'] ?></span>
                                <img class="img-profile rounded-circle" src="../assets/img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">