<?php
session_start();
require_once '../config/koneksi.php';

// Proses Tambah User
if (isset($_POST['add'])) {
    $nama_admin = mysqli_real_escape_string($koneksi, $_POST['nama_admin']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']); // gunakan password_hash() untuk produksi
    
    // Cek username sudah digunakan atau belum
    $cek_username = mysqli_query($koneksi, "SELECT * FROM user WHERE username = '$username'");
    if (mysqli_num_rows($cek_username) > 0) {
        $_SESSION['message'] = "Username sudah digunakan!";
        $_SESSION['message_type'] = "danger";
        header("Location: ../admin/user.php");
        exit();
    }
    
    // Simpan data dengan level 1 (admin)
    $query = "INSERT INTO user (username, password, nama_admin, id_level) 
              VALUES ('$username', '$password', '$nama_admin', 1)";
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        $_SESSION['message'] = "Data admin berhasil ditambahkan";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal menambahkan data admin: " . mysqli_error($koneksi);
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
    
    // Cek username
    $cek_username = mysqli_query($koneksi, "SELECT * FROM user WHERE username = '$username' AND id_user != '$id_user'");
    if (mysqli_num_rows($cek_username) > 0) {
        $_SESSION['message'] = "Username sudah digunakan!";
        $_SESSION['message_type'] = "danger";
        header("Location: ../admin/user.php");
        exit();
    }
    
    // Update data tanpa mengubah level
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $query = "UPDATE user SET 
                  username = '$username', 
                  password = '$password', 
                  nama_admin = '$nama_admin'
                  WHERE id_user = '$id_user' AND id_level = 1";
    } else {
        $query = "UPDATE user SET 
                  username = '$username', 
                  nama_admin = '$nama_admin'
                  WHERE id_user = '$id_user' AND id_level = 1";
    }
    
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        $_SESSION['message'] = "Data admin berhasil diupdate";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal mengupdate data admin: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: ../admin/user.php");
    exit();
}

// Proses Hapus User
if (isset($_GET['delete'])) {
    $id_user = $_GET['delete'];
    
    // Cek apakah user yang akan dihapus adalah admin (level 1)
    $query_cek = "SELECT * FROM user WHERE id_user = '$id_user' AND id_level = 1";
    $result_cek = mysqli_query($koneksi, $query_cek);
    
    if (mysqli_num_rows($result_cek) > 0) {
        // Pastikan bukan user yang sedang login
        if ($id_user == $_SESSION['user_id']) {
            $_SESSION['message'] = "Tidak dapat menghapus akun yang sedang aktif!";
            $_SESSION['message_type'] = "danger";
        } else {
            // Hapus data admin
            $query_delete = "DELETE FROM user WHERE id_user = '$id_user' AND id_level = 1";
            $result = mysqli_query($koneksi, $query_delete);
            
            if ($result) {
                $_SESSION['message'] = "Data admin berhasil dihapus";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Gagal menghapus data admin: " . mysqli_error($koneksi);
                $_SESSION['message_type'] = "danger";
            }
        }
    } else {
        $_SESSION['message'] = "Data admin tidak ditemukan";
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: ../admin/user.php");
    exit();
}

// Redirect jika tidak ada aksi
header("Location: ../admin/user.php");
exit();
?>