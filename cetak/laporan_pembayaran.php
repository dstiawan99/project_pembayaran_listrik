<?php
session_start();
require_once '../config/koneksi.php';
require_once '../config/function.php';

// Cek level akses
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 1) {
    die("Akses ditolak!");
}

// Filter periode
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'semua';

// Query data pembayaran
$query_where = "";
if ($filter_status != 'semua') {
    $query_where = " AND t.status = '$filter_status'";
}

$query = mysqli_query($koneksi, "SELECT pb.*, t.bulan, t.tahun, t.status,
                                p.nama_pelanggan, p.nomor_kwh, u.nama_admin
                                FROM pembayaran pb 
                                RIGHT JOIN tagihan t ON pb.id_tagihan = t.id_tagihan
                                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                                LEFT JOIN user u ON pb.id_user = u.id_user
                                WHERE t.bulan = '$filter_bulan' AND t.tahun = '$filter_tahun' $query_where
                                ORDER BY t.status ASC, pb.tanggal_pembayaran DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembayaran Listrik</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f5f5f5;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .status {
            font-weight: bold;
        }
        .status-lunas {
            color: green;
        }
        .status-belum {
            color: red;
        }
        .filter-form {
            margin-bottom: 20px;
        }
        .filter-form select, .filter-form button {
            padding: 8px;
            margin-right: 10px;
        }
        .summary {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LAPORAN PEMBAYARAN LISTRIK PASCABAYAR</h1>
            <p>Periode: <?= getBulan($filter_bulan) ?> <?= $filter_tahun ?></p>
            <p>Tanggal Cetak: <?= tanggal_indo(date('Y-m-d')) ?></p>
        </div>
        
        <div class="no-print filter-form">
            <form action="" method="get">
                <select name="bulan">
                    <option value="">-- Pilih Bulan --</option>
                    <?php
                    $bulan_array = array(
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', 
                        '04' => 'April', '05' => 'Mei', '06' => 'Juni', 
                        '07' => 'Juli', '08' => 'Agustus', '09' => 'September', 
                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    );
                    foreach ($bulan_array as $key => $value) {
                        $selected = ($key == $filter_bulan) ? 'selected' : '';
                        echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                    }
                    ?>
                </select>
                
                <select name="tahun">
                    <option value="">-- Pilih Tahun --</option>
                    <?php
                    $tahun_sekarang = date('Y');
                    for ($i = $tahun_sekarang; $i >= $tahun_sekarang - 2; $i--) {
                        $selected = ($i == $filter_tahun) ? 'selected' : '';
                        echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                    }
                    ?>
                </select>
                
                <select name="status">
                    <option value="semua" <?= ($filter_status == 'semua') ? 'selected' : '' ?>>Semua Status</option>
                    <option value="lunas" <?= ($filter_status == 'lunas') ? 'selected' : '' ?>>Lunas</option>
                    <option value="belum_bayar" <?= ($filter_status == 'belum_bayar') ? 'selected' : '' ?>>Belum Bayar</option>
                </select>
                
                <button type="submit">Filter</button>
                <button type="button" onclick="window.print()">Cetak</button>
            </form>
        </div>
        
        <?php if (mysqli_num_rows($query) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Nama Pelanggan</th>
                    <th width="12%">No. Meter</th>
                    <th width="10%">Bulan/Tahun</th>
                    <th width="15%">Tanggal Bayar</th>
                    <th width="13%">Total Bayar</th>
                    <th width="10%">Status</th>
                    <th width="15%">Petugas</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $total_pendapatan = 0;
                $total_tagihan = 0;
                $belum_dibayar = 0;
                while ($row = mysqli_fetch_assoc($query)):
                    if ($row['status'] == 'lunas') {
                        $total_pendapatan += $row['total_bayar'];
                        $total_tagihan += 1;
                    } else {
                        $belum_dibayar += 1;
                    }
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= $row['nama_pelanggan'] ?></td>
                    <td><?= $row['nomor_kwh'] ?></td>
                    <td><?= getBulan($row['bulan']) ?> <?= $row['tahun'] ?></td>
                    <td><?= ($row['status'] == 'lunas') ? tanggal_indo($row['tanggal_pembayaran']) : '-' ?></td>
                    <td class="text-right"><?= ($row['status'] == 'lunas') ? formatRupiah($row['total_bayar']) : '-' ?></td>
                    <td class="text-center">
                        <?php if ($row['status'] == 'lunas'): ?>
                            <span class="status status-lunas">LUNAS</span>
                        <?php else: ?>
                            <span class="status status-belum">BELUM BAYAR</span>
                        <?php endif; ?>
                    </td>
                    <td><?= ($row['status'] == 'lunas') ? $row['nama_admin'] : '-' ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div class="summary">
            <p>Total Tagihan: <?= $total_tagihan + $belum_dibayar ?> tagihan</p>
            <p>Sudah Bayar: <?= $total_tagihan ?> tagihan</p>
            <p>Belum Bayar: <?= $belum_dibayar ?> tagihan</p>
            <p>Total Pendapatan: <?= formatRupiah($total_pendapatan) ?></p>
        </div>
        <?php else: ?>
        <div class="text-center" style="padding: 50px 0;">
            <h3>Tidak ada data untuk periode yang dipilih</h3>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>