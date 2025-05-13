<?php
session_start();
require_once '../config/koneksi.php';

// Proses Tambah Penggunaan
if (isset($_POST['add'])) {
    $id_pelanggan = mysqli_real_escape_string($koneksi, $_POST['id_pelanggan']);
    $bulan = mysqli_real_escape_string($koneksi, $_POST['bulan']);
    $tahun = mysqli_real_escape_string($koneksi, $_POST['tahun']);
    $meter_awal = mysqli_real_escape_string($koneksi, $_POST['meter_awal']);
    $meter_akhir = mysqli_real_escape_string($koneksi, $_POST['meter_akhir']);
    
    // Validasi meter
    if ($meter_akhir < $meter_awal) {
        $_SESSION['message'] = "Meter akhir tidak boleh lebih kecil dari meter awal";
        $_SESSION['message_type'] = "danger";
        header("Location: ../admin/penggunaan.php");
        exit();
    }
    
    // Cek apakah data penggunaan sudah ada
    $query_cek = "SELECT * FROM penggunaan WHERE id_pelanggan = '$id_pelanggan' AND bulan = '$bulan' AND tahun = '$tahun'";
    $result_cek = mysqli_query($koneksi, $query_cek);
    
    if (mysqli_num_rows($result_cek) > 0) {
        $_SESSION['message'] = "Data penggunaan untuk pelanggan, bulan dan tahun yang sama sudah ada";
        $_SESSION['message_type'] = "danger";
        header("Location: ../admin/penggunaan.php");
        exit();
    }
    
    // Simpan data
    $query = "INSERT INTO penggunaan (id_pelanggan, bulan, tahun, meter_awal, meter_akhir) 
              VALUES ('$id_pelanggan', '$bulan', '$tahun', '$meter_awal', '$meter_akhir')";
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        // Dapatkan id_penggunaan yang baru saja di-insert
        $id_penggunaan = mysqli_insert_id($koneksi);
        
        // Buat tagihan berdasarkan penggunaan
        $jumlah_meter = $meter_akhir - $meter_awal;
        $query_tagihan = "INSERT INTO tagihan (id_penggunaan, id_pelanggan, bulan, tahun, jumlah_meter, status) 
                         VALUES ('$id_penggunaan', '$id_pelanggan', '$bulan', '$tahun', '$jumlah_meter', 'belum_bayar')";
        $result_tagihan = mysqli_query($koneksi, $query_tagihan);
        
        if ($result_tagihan) {
            $_SESSION['message'] = "Data penggunaan dan tagihan berhasil ditambahkan";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Data penggunaan berhasil ditambahkan, tetapi gagal membuat tagihan: " . mysqli_error($koneksi);
            $_SESSION['message_type'] = "warning";
        }
    } else {
        $_SESSION['message'] = "Gagal menambahkan data penggunaan: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: ../admin/penggunaan.php");
    exit();
}

// Proses Edit Penggunaan
if (isset($_POST['edit'])) {
    $id_penggunaan = $_POST['id_penggunaan'];
    $id_pelanggan = mysqli_real_escape_string($koneksi, $_POST['id_pelanggan']);
    $bulan = mysqli_real_escape_string($koneksi, $_POST['bulan']);
    $tahun = mysqli_real_escape_string($koneksi, $_POST['tahun']);
    $meter_awal = mysqli_real_escape_string($koneksi, $_POST['meter_awal']);
    $meter_akhir = mysqli_real_escape_string($koneksi, $_POST['meter_akhir']);
    
    // Validasi meter
    if ($meter_akhir < $meter_awal) {
        $_SESSION['message'] = "Meter akhir tidak boleh lebih kecil dari meter awal";
        $_SESSION['message_type'] = "danger";
        header("Location: ../admin/penggunaan.php");
        exit();
    }
    
    // Cek apakah data penggunaan sudah ada (selain data yang sedang diedit)
    $query_cek = "SELECT * FROM penggunaan WHERE id_pelanggan = '$id_pelanggan' AND bulan = '$bulan' AND tahun = '$tahun' AND id_penggunaan != '$id_penggunaan'";
    $result_cek = mysqli_query($koneksi, $query_cek);
    
    if (mysqli_num_rows($result_cek) > 0) {
        $_SESSION['message'] = "Data penggunaan untuk pelanggan, bulan dan tahun yang sama sudah ada";
        $_SESSION['message_type'] = "danger";
        header("Location: ../admin/penggunaan.php");
        exit();
    }
    
    // Update data penggunaan
    $query = "UPDATE penggunaan SET 
              id_pelanggan = '$id_pelanggan',
              bulan = '$bulan',
              tahun = '$tahun',
              meter_awal = '$meter_awal',
              meter_akhir = '$meter_akhir'
              WHERE id_penggunaan = '$id_penggunaan'";
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        // Update tagihan berdasarkan penggunaan
        $jumlah_meter = $meter_akhir - $meter_awal;
        $query_tagihan = "UPDATE tagihan SET 
                         id_pelanggan = '$id_pelanggan',
                         bulan = '$bulan',
                         tahun = '$tahun',
                         jumlah_meter = '$jumlah_meter'
                         WHERE id_penggunaan = '$id_penggunaan'";
        $result_tagihan = mysqli_query($koneksi, $query_tagihan);
        
        if ($result_tagihan) {
            $_SESSION['message'] = "Data penggunaan dan tagihan berhasil diupdate";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Data penggunaan berhasil diupdate, tetapi gagal mengupdate tagihan: " . mysqli_error($koneksi);
            $_SESSION['message_type'] = "warning";
        }
    } else {
        $_SESSION['message'] = "Gagal mengupdate data penggunaan: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: ../admin/penggunaan.php");
    exit();
}

// Proses Hapus Penggunaan
if (isset($_GET['delete'])) {
    $id_penggunaan = $_GET['delete'];
    
    // Cek apakah ada tagihan yang sudah dibayar
    $query_cek = "SELECT t.* FROM tagihan t 
                 JOIN pembayaran p ON t.id_tagihan = p.id_tagihan
                 WHERE t.id_penggunaan = '$id_penggunaan'";
    $result_cek = mysqli_query($koneksi, $query_cek);
    
    if (mysqli_num_rows($result_cek) > 0) {
        $_SESSION['message'] = "Penggunaan tidak dapat dihapus karena tagihan sudah dibayar";
        $_SESSION['message_type'] = "danger";
    } else {
        // Hapus tagihan terlebih dahulu
        $query_delete_tagihan = "DELETE FROM tagihan WHERE id_penggunaan = '$id_penggunaan'";
        mysqli_query($koneksi, $query_delete_tagihan);
        
        // Hapus penggunaan
        $query_delete = "DELETE FROM penggunaan WHERE id_penggunaan = '$id_penggunaan'";
        $result = mysqli_query($koneksi, $query_delete);
        
        if ($result) {
            $_SESSION['message'] = "Data penggunaan berhasil dihapus";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal menghapus data penggunaan: " . mysqli_error($koneksi);
            $_SESSION['message_type'] = "danger";
        }
    }
    
    header("Location: ../admin/penggunaan.php");
    exit();
}

// Redirect jika tidak ada aksi
header("Location: ../admin/penggunaan.php");
exit();
?>