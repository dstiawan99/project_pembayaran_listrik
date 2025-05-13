<?php
session_start();
require_once '../config/koneksi.php';

// Proses Tambah Pelanggan
if (isset($_POST['add'])) {
    $nama_pelanggan = mysqli_real_escape_string($koneksi, $_POST['nama_pelanggan']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']); // gunakan password_hash() untuk produksi
    $nomor_kwh = mysqli_real_escape_string($koneksi, $_POST['nomor_kwh']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $id_tarif = $_POST['id_tarif'];
    
    // Cek username sudah digunakan atau belum
    $cek_username = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE username = '$username'");
    if (mysqli_num_rows($cek_username) > 0) {
        $_SESSION['message'] = "Username sudah digunakan!";
        $_SESSION['message_type'] = "danger";
        header("Location: ../admin/pelanggan.php");
        exit();
    }
    
    // Simpan data
    $query = "INSERT INTO pelanggan (username, password, nomor_kwh, nama_pelanggan, alamat, id_tarif) 
              VALUES ('$username', '$password', '$nomor_kwh', '$nama_pelanggan', '$alamat', '$id_tarif')";
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        $_SESSION['message'] = "Data pelanggan berhasil ditambahkan";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal menambahkan data pelanggan: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: ../admin/pelanggan.php");
    exit();
}

// Proses Edit Pelanggan
if (isset($_POST['edit'])) {
    $id_pelanggan = $_POST['id_pelanggan'];
    $nama_pelanggan = mysqli_real_escape_string($koneksi, $_POST['nama_pelanggan']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nomor_kwh = mysqli_real_escape_string($koneksi, $_POST['nomor_kwh']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $id_tarif = $_POST['id_tarif'];
    
    // Cek username
    $cek_username = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE username = '$username' AND id_pelanggan != '$id_pelanggan'");
    if (mysqli_num_rows($cek_username) > 0) {
        $_SESSION['message'] = "Username sudah digunakan!";
        $_SESSION['message_type'] = "danger";
        header("Location: ../admin/pelanggan.php");
        exit();
    }
    
    // Update password jika diisi
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $query = "UPDATE pelanggan SET 
                  username = '$username', 
                  password = '$password', 
                  nomor_kwh = '$nomor_kwh', 
                  nama_pelanggan = '$nama_pelanggan', 
                  alamat = '$alamat', 
                  id_tarif = '$id_tarif' 
                  WHERE id_pelanggan = '$id_pelanggan'";
    } else {
        $query = "UPDATE pelanggan SET 
                  username = '$username', 
                  nomor_kwh = '$nomor_kwh', 
                  nama_pelanggan = '$nama_pelanggan', 
                  alamat = '$alamat', 
                  id_tarif = '$id_tarif' 
                  WHERE id_pelanggan = '$id_pelanggan'";
    }
    
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        $_SESSION['message'] = "Data pelanggan berhasil diupdate";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal mengupdate data pelanggan: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: ../admin/pelanggan.php");
    exit();
}

// Proses Hapus Pelanggan
if (isset($_GET['delete'])) {
    $id_pelanggan = $_GET['delete'];
    
    // Hapus data yang berelasi terlebih dahulu
    $query_delete_pembayaran = "DELETE pb FROM pembayaran pb 
                                JOIN tagihan t ON pb.id_tagihan = t.id_tagihan 
                                WHERE t.id_pelanggan = '$id_pelanggan'";
    mysqli_query($koneksi, $query_delete_pembayaran);
    
    $query_delete_tagihan = "DELETE FROM tagihan WHERE id_pelanggan = '$id_pelanggan'";
    mysqli_query($koneksi, $query_delete_tagihan);
    
    $query_delete_penggunaan = "DELETE FROM penggunaan WHERE id_pelanggan = '$id_pelanggan'";
    mysqli_query($koneksi, $query_delete_penggunaan);
    
    // Hapus data pelanggan
    $query_delete_pelanggan = "DELETE FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'";
    $result = mysqli_query($koneksi, $query_delete_pelanggan);
    
    if ($result) {
        $_SESSION['message'] = "Data pelanggan berhasil dihapus";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal menghapus data pelanggan: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: ../admin/pelanggan.php");
    exit();
}

// Redirect jika tidak ada aksi
header("Location: ../admin/pelanggan.php");
exit();
?>