<?php
session_start();
require_once '../config/koneksi.php';

// Proses Pembayaran
if (isset($_POST['bayar'])) {
    $id_tagihan = $_POST['id_tagihan'];
    $biaya_admin = $_POST['biaya_admin'];
    $total_bayar = $_POST['total_bayar'];
    $id_user = $_SESSION['user_id'];
    
    // Cek apakah tagihan sudah lunas
    $query_cek = "SELECT * FROM tagihan WHERE id_tagihan = '$id_tagihan'";
    $result_cek = mysqli_query($koneksi, $query_cek);
    $data_tagihan = mysqli_fetch_assoc($result_cek);
    
    if ($data_tagihan['status'] == 'lunas') {
        $_SESSION['message'] = "Tagihan sudah dibayar";
        $_SESSION['message_type'] = "warning";
        header("Location: ../admin/tagihan.php");
        exit();
    }
    
    // Simpan data pembayaran
    $tanggal_bayar = date('Y-m-d');
    $bulan_bayar = $data_tagihan['bulan'];
    
    $query = "INSERT INTO pembayaran (id_tagihan, tanggal_pembayaran, bulan_bayar, biaya_admin, total_bayar, id_user) 
              VALUES ('$id_tagihan', '$tanggal_bayar', '$bulan_bayar', '$biaya_admin', '$total_bayar', '$id_user')";
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        // Update status tagihan menjadi lunas
        $query_update = "UPDATE tagihan SET status = 'lunas' WHERE id_tagihan = '$id_tagihan'";
        $result_update = mysqli_query($koneksi, $query_update);
        
        if ($result_update) {
            $_SESSION['message'] = "Pembayaran berhasil diproses";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Pembayaran berhasil, tetapi gagal mengupdate status tagihan: " . mysqli_error($koneksi);
            $_SESSION['message_type'] = "warning";
        }
    } else {
        $_SESSION['message'] = "Gagal memproses pembayaran: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: ../admin/tagihan.php");
    exit();
}

// Redirect jika tidak ada aksi
header("Location: ../admin/tagihan.php");
exit();
?>