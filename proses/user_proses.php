<?php
session_start();
require_once '../config/koneksi.php';

// Proses Tambah User
if (isset($_POST['add'])) {
    $nama_admin = mysqli_real_escape_string($koneksi, $_POST['nama_admin']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']); // gunakan password_hash() untuk produksi
    $id_level = $_POST['id_level'];
    
    // Cek username sudah digunakan atau belum
    $cek_username = mysqli_query($koneksi, "SELECT * FROM user WHERE username = '$username'");
    if (mysqli_num_rows($cek_username) > 0) {
        $_SESSION['message'] = "Username sudah digunakan!";
        $_SESSION['message_type'] = "danger";
        header("Location: ../admin/user.php");
        exit();
    }
    
    // Simpan data
    $query = "INSERT INTO user (username, password, nama_admin, id_level) 
              VALUES ('$username', '$password', '$nama_admin', '$id_level')";
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        $_SESSION['message'] = "Data user berhasil ditambahkan";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal menambahkan data user: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: ../admin/user.php");
    exit();
}

// Proses Edit User
if (isset($_POST['edit'])) {
    $id_user = $_POST['id_user'];
    $nama_admin = mysqli_real_escape_string($koneksi, $_POST['nama_admin']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $id_level = $_POST['id_level'];
    
    // Cek username
    $cek_username = mysqli_query($koneksi, "SELECT * FROM user WHERE username = '$username' AND id_user != '$id_user'");
    if (mysqli_num_rows($cek_username) > 0) {
        $_SESSION['message'] = "Username sudah digunakan!";
        $_SESSION['message_type'] = "danger";
        header("Location: ../admin/user.php");
        exit();
    }
    
    // Update password jika diisi
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $query = "UPDATE user SET 
                  username = '$username', 
                  password = '$password', 
                  nama_admin = '$nama_admin', 
                  id_level = '$id_level' 
                  WHERE id_user = '$id_user'";
    } else {
        $query = "UPDATE user SET 
                  username = '$username', 
                  nama_admin = '$nama_admin', 
                  id_level = '$id_level' 
                  WHERE id_user = '$id_user'";
    }
    
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        $_SESSION['message'] = "Data user berhasil diupdate";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal mengupdate data user: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: ../admin/user.php");
    exit();
}

// Proses Hapus User
if (isset($_GET['delete'])) {
    $id_user = $_GET['delete'];
    
    // Cek apakah user punya transaksi
    $query_cek = "SELECT * FROM pembayaran WHERE id_user = '$id_user'";
    $result_cek = mysqli_query($koneksi, $query_cek);
    
    if (mysqli_num_rows($result_cek) > 0) {
        $_SESSION['message'] = "User tidak dapat dihapus karena memiliki data transaksi pembayaran";
        $_SESSION['message_type'] = "danger";
    } else {
        // Hapus data user
        $query_delete = "DELETE FROM user WHERE id_user = '$id_user'";
        $result = mysqli_query($koneksi, $query_delete);
        
        if ($result) {
            $_SESSION['message'] = "Data user berhasil dihapus";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal menghapus data user: " . mysqli_error($koneksi);
            $_SESSION['message_type'] = "danger";
        }
    }
    
    header("Location: ../admin/user.php");
    exit();
}

// Redirect jika tidak ada aksi
header("Location: ../admin/user.php");
exit();
?>