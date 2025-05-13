<?php
session_start();
require_once '../config/koneksi.php';

// Generate Tagihan
if (isset($_GET['generate'])) {
    // Ambil bulan dan tahun saat ini
    $bulan_ini = date('m');
    $tahun_ini = date('Y');
    
    // Cek apakah bulan ini sudah semua pelanggan memiliki tagihan
    $query_cek = "SELECT p.id_pelanggan FROM pelanggan p 
                  LEFT JOIN tagihan t ON p.id_pelanggan = t.id_pelanggan AND t.bulan = '$bulan_ini' AND t.tahun = '$tahun_ini' 
                  WHERE t.id_tagihan IS NULL";
    $result_cek = mysqli_query($koneksi, $query_cek);
    
    if (mysqli_num_rows($result_cek) == 0) {
        $_SESSION['message'] = "Semua pelanggan sudah memiliki tagihan untuk bulan ini";
        $_SESSION['message_type'] = "info";
        header("Location: ../admin/tagihan.php");
        exit();
    }
    
    $berhasil = 0;
    $gagal = 0;
    
    // Generate tagihan untuk pelanggan yang belum memiliki tagihan bulan ini
    while ($row = mysqli_fetch_assoc($result_cek)) {
        $id_pelanggan = $row['id_pelanggan'];
        
        // Cek penggunaan terakhir pelanggan
        $query_penggunaan = "SELECT * FROM penggunaan 
                            WHERE id_pelanggan = '$id_pelanggan' 
                            ORDER BY tahun DESC, bulan DESC LIMIT 1";
        $result_penggunaan = mysqli_query($koneksi, $query_penggunaan);
        
        if (mysqli_num_rows($result_penggunaan) > 0) {
            $data_penggunaan = mysqli_fetch_assoc($result_penggunaan);
            $meter_akhir_lama = $data_penggunaan['meter_akhir'];
            
            // Buat penggunaan baru (asumsi meter akhir = meter akhir lama + 100)
            $meter_awal_baru = $meter_akhir_lama;
            $meter_akhir_baru = $meter_akhir_lama + 100; // Ini hanya contoh, idealnya data dari input petugas
            
            $query_insert_penggunaan = "INSERT INTO penggunaan (id_pelanggan, bulan, tahun, meter_awal, meter_akhir) 
                                       VALUES ('$id_pelanggan', '$bulan_ini', '$tahun_ini', '$meter_awal_baru', '$meter_akhir_baru')";
            $result_insert_penggunaan = mysqli_query($koneksi, $query_insert_penggunaan);
            
            if ($result_insert_penggunaan) {
                $id_penggunaan = mysqli_insert_id($koneksi);
                $jumlah_meter = $meter_akhir_baru - $meter_awal_baru;
                
                // Buat tagihan
                $query_insert_tagihan = "INSERT INTO tagihan (id_penggunaan, id_pelanggan, bulan, tahun, jumlah_meter, status) 
                                        VALUES ('$id_penggunaan', '$id_pelanggan', '$bulan_ini', '$tahun_ini', '$jumlah_meter', 'belum_bayar')";
                $result_insert_tagihan = mysqli_query($koneksi, $query_insert_tagihan);
                
                if ($result_insert_tagihan) {
                    $berhasil++;
                } else {
                    $gagal++;
                }
            } else {
                $gagal++;
            }
        } else {
            // Belum ada penggunaan sebelumnya, buat baru dari 0
            $meter_awal_baru = 0;
            $meter_akhir_baru = 100; // Ini hanya contoh, idealnya data dari input petugas
            
            $query_insert_penggunaan = "INSERT INTO penggunaan (id_pelanggan, bulan, tahun, meter_awal, meter_akhir) 
                                       VALUES ('$id_pelanggan', '$bulan_ini', '$tahun_ini', '$meter_awal_baru', '$meter_akhir_baru')";
            $result_insert_penggunaan = mysqli_query($koneksi, $query_insert_penggunaan);
            
            if ($result_insert_penggunaan) {
                $id_penggunaan = mysqli_insert_id($koneksi);
                $jumlah_meter = $meter_akhir_baru - $meter_awal_baru;
                
                // Buat tagihan
                $query_insert_tagihan = "INSERT INTO tagihan (id_penggunaan, id_pelanggan, bulan, tahun, jumlah_meter, status) 
                                        VALUES ('$id_penggunaan', '$id_pelanggan', '$bulan_ini', '$tahun_ini', '$jumlah_meter', 'belum_bayar')";
                $result_insert_tagihan = mysqli_query($koneksi, $query_insert_tagihan);
                
                if ($result_insert_tagihan) {
                    $berhasil++;
                } else {
                    $gagal++;
                }
            } else {
                $gagal++;
            }
        }
    }
    
    if ($berhasil > 0) {
        $_SESSION['message'] = "Berhasil generate $berhasil tagihan baru" . ($gagal > 0 ? ", gagal generate $gagal tagihan" : "");
        $_SESSION['message_type'] = ($gagal > 0) ? "warning" : "success";
    } else {
        $_SESSION['message'] = "Gagal generate tagihan";
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: ../admin/tagihan.php");
    exit();
}

// Redirect jika tidak ada aksi
header("Location: ../admin/tagihan.php");
exit();
?>