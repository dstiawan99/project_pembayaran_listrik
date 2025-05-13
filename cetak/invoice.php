<?php
session_start();
require_once '../config/koneksi.php';
require_once '../config/function.php';

// Cek ID tagihan
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID Tagihan tidak valid");
}

$id_tagihan = $_GET['id'];

// Dapatkan data tagihan
$query = mysqli_query($koneksi, "SELECT t.*, p.nama_pelanggan, p.nomor_kwh, p.alamat, p.id_tarif,
                                tr.daya, tr.tarifperkwh, pg.meter_awal, pg.meter_akhir, 
                                pb.id_pembayaran, pb.tanggal_pembayaran, pb.biaya_admin, pb.total_bayar,
                                u.nama_admin
                                FROM tagihan t
                                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                                JOIN tarif tr ON p.id_tarif = tr.id_tarif
                                JOIN penggunaan pg ON t.id_penggunaan = pg.id_penggunaan
                                JOIN pembayaran pb ON t.id_tagihan = pb.id_tagihan
                                JOIN user u ON pb.id_user = u.id_user
                                WHERE t.id_tagihan = '$id_tagihan'");

if (mysqli_num_rows($query) == 0) {
    die("Data tagihan tidak ditemukan atau belum dibayar");
}

$data = mysqli_fetch_assoc($query);
$jumlah_meter = $data['meter_akhir'] - $data['meter_awal'];
$tagihan_listrik = $jumlah_meter * $data['tarifperkwh'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Pembayaran Listrik</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
        }
        .invoice-header h1 {
            margin: 0;
            color: #333;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .invoice-details-left, .invoice-details-right {
            width: 48%;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-table th, .invoice-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .invoice-table th {
            background-color: #f5f5f5;
        }
        .invoice-total {
            text-align: right;
            margin-top: 20px;
            margin-bottom: 40px;
        }
        .invoice-total table {
            width: 300px;
            margin-left: auto;
        }
        .invoice-total table td {
            padding: 5px;
        }
        .invoice-footer {
            margin-top: 50px;
            text-align: center;
            font-size: 0.9em;
            color: #777;
        }
        .text-right {
            text-align: right;
        }
        .signature {
            margin-top: 70px;
            display: flex;
            justify-content: space-between;
        }
        .signature-item {
            width: 200px;
            text-align: center;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
            .invoice-container {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <h1>STRUK PEMBAYARAN LISTRIK PASCABAYAR</h1>
            <p>PLN Pascabayar - PT. PLN (Persero)</p>
        </div>
        
        <div class="invoice-details">
            <div class="invoice-details-left">
                <p><strong>No. Invoice:</strong> INV-<?= sprintf("%05d", $data['id_pembayaran']) ?></p>
                <p><strong>Tanggal Bayar:</strong> <?= tanggal_indo($data['tanggal_pembayaran']) ?></p>
                <p><strong>Status:</strong> <span style="color: green; font-weight: bold;">LUNAS</span></p>
            </div>
            <div class="invoice-details-right">
                <p><strong>Nama Pelanggan:</strong> <?= $data['nama_pelanggan'] ?></p>
                <p><strong>ID Pelanggan:</strong> <?= $data['id_pelanggan'] ?></p>
                <p><strong>No. Meter:</strong> <?= $data['nomor_kwh'] ?></p>
                <p><strong>Alamat:</strong> <?= $data['alamat'] ?></p>
                <p><strong>Daya:</strong> <?= $data['daya'] ?> VA</p>
            </div>
        </div>
        
        <h3>Detail Tagihan</h3>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Meter Awal</th>
                    <th>Meter Akhir</th>
                    <th>Jumlah kWh</th>
                    <th>Tarif per kWh</th>
                    <th>Total Tagihan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= getBulan($data['bulan']) ?> <?= $data['tahun'] ?></td>
                    <td><?= $data['meter_awal'] ?></td>
                    <td><?= $data['meter_akhir'] ?></td>
                    <td><?= $jumlah_meter ?> kWh</td>
                    <td><?= formatRupiah($data['tarifperkwh']) ?></td>
                    <td><?= formatRupiah($tagihan_listrik) ?></td>
                </tr>
            </tbody>
        </table>
        
        <div class="invoice-total">
            <table>
                <tr>
                    <td>Tagihan Listrik:</td>
                    <td class="text-right"><?= formatRupiah($tagihan_listrik) ?></td>
                </tr>
                <tr>
                    <td>Biaya Admin:</td>
                    <td class="text-right"><?= formatRupiah($data['biaya_admin']) ?></td>
                </tr>
                <tr style="font-weight: bold;">
                    <td>Total Bayar:</td>
                    <td class="text-right"><?= formatRupiah($data['total_bayar']) ?></td>
                </tr>
            </table>
        </div>
        
        <div class="signature">
            <div class="signature-item">
                <p>Pelanggan</p>
                <br><br><br>
                <p><?= $data['nama_pelanggan'] ?></p>
            </div>
            <div class="signature-item">
                <p>Petugas</p>
                <br><br><br>
                <p><?= $data['nama_admin'] ?></p>
            </div>
        </div>
        
        <div class="invoice-footer">
            <p>Terima kasih atas pembayaran Anda. Simpan struk ini sebagai bukti pembayaran yang sah.</p>
            <p>Untuk pertanyaan atau keluhan silahkan hubungi call center PLN di 123.</p>
        </div>
        
        <div class="no-print" style="margin-top: 30px; text-align: center;">
            <button onclick="window.print()" style="padding: 10px 20px; background-color: #4e73df; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Cetak Invoice
            </button>
            <button onclick="window.close()" style="padding: 10px 20px; background-color: #e74a3b; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                Tutup
            </button>
        </div>
    </div>
</body>
</html>