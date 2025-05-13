<?php
session_start();
require_once '../config/koneksi.php';

// Proses Tambah Tarif
if (isset($_POST['add'])) {
    $daya = mysqli_real_escape_string($koneksi, $_POST['daya']);
    $tarifperkwh = mysqli_real_escape_string($koneksi, $_POST['tarifperkwh']);
    
    // Simpan data
    $query = "INSERT INTO tarif (daya, tarifperkwh) VALUES ('$daya', '$tarifperkwh')";
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        $_SESSION['message'] = "Data tarif berhasil ditambahkan";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal menambahkan data tarif: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: ../admin/tarif.php");
    exit();
}

// Proses Edit Tarif
if (isset($_POST['edit'])) {
    $id_tarif = $_POST['id_tarif'];
    $daya = mysqli_real_escape_string($koneksi, $_POST['daya']);
    $tarifperkwh = mysqli_real_escape_string($koneksi, $_POST['tarifperkwh']);
    
    // Update data
    $query = "UPDATE tarif SET daya = '$daya', tarifperkwh = '$tarifperkwh' WHERE id_tarif = '$id_tarif'";
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        $_SESSION['message'] = "Data tarif berhasil diupdate";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal mengupdate data tarif: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: ../admin/tarif.php");
    exit();
}

// Proses Hapus Tarif
if (isset($_GET['delete'])) {
    $id_tarif = $_GET['delete'];
    
    // Cek apakah tarif sedang digunakan
    $query_cek = "SELECT * FROM pelanggan WHERE id_tarif = '$id_tarif'";
    $result_cek = mysqli_query($koneksi, $query_cek);
    
    if (mysqli_num_rows($result_cek) > 0) {
        $_SESSION['message'] = "Tarif tidak dapat dihapus karena sedang digunakan oleh pelanggan";
        $_SESSION['message_type'] = "danger";
    } else {
        // Hapus data tarif
        $query_delete = "DELETE FROM tarif WHERE id_tarif = '$id_tarif'";
        $result = mysqli_query($koneksi, $query_delete);
        
        if ($result) {
            $_SESSION['message'] = "Data tarif berhasil dihapus";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal menghapus data tarif: " . mysqli_error($koneksi);
            $_SESSION['message_type'] = "danger";
        }
    }
    
    header("Location: ../admin/tarif.php");
    exit();
}

// Redirect jika tidak ada aksi
header("Location: ../admin/tarif.php");
exit();
?>