<?php 
include '../template/header.php';

// Set default filter
$tahun_sekarang = date('Y');
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : $tahun_sekarang;

// Buat kondisi WHERE untuk query
$where = "";
if (!empty($bulan) && !empty($tahun)) {
    $where = "WHERE MONTH(pb.tanggal_pembayaran) = '$bulan' AND YEAR(pb.tanggal_pembayaran) = '$tahun'";
} elseif (empty($bulan) && !empty($tahun) && $tahun != 'semua') {
    $where = "WHERE YEAR(pb.tanggal_pembayaran) = '$tahun'";
}
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan</h1>
</div>

<!-- Filter Form -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="get" action="" class="form-inline">
            <div class="form-group mx-sm-3 mb-2">
                <label for="bulan" class="mr-2">Bulan:</label>
                <select name="bulan" id="bulan" class="form-control">
                    <option value="">Semua Bulan</option>
                    <?php
                    $bulan_array = [
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ];
                    foreach ($bulan_array as $key => $value) {
                        $selected = ($bulan == $key) ? 'selected' : '';
                        echo "<option value='$key' $selected>$value</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <label for="tahun" class="mr-2">Tahun:</label>
                <select name="tahun" id="tahun" class="form-control">
                    <option value="semua">Semua Tahun</option>
                    <?php
                    // Ambil tahun-tahun yang ada di database
                    $query_tahun = mysqli_query($koneksi, "SELECT DISTINCT YEAR(tanggal_pembayaran) as tahun 
                                                         FROM pembayaran 
                                                         ORDER BY tahun DESC");
                    while ($row_tahun = mysqli_fetch_assoc($query_tahun)) {
                        $selected = ($tahun == $row_tahun['tahun']) ? 'selected' : '';
                        echo "<option value='" . $row_tahun['tahun'] . "' $selected>" . $row_tahun['tahun'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mb-2">Filter</button>
        </form>
    </div>
</div>

<!-- Laporan Cards -->
<div class="row">
    <!-- Total Pembayaran -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pembayaran</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php
                            $query = mysqli_query($koneksi, "SELECT SUM(total_bayar) as total FROM pembayaran pb $where");
                            $data = mysqli_fetch_assoc($query);
                            echo formatRupiah($data['total'] ?? 0);
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jumlah Transaksi -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Jumlah Transaksi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php
                            $query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pembayaran pb $where");
                            $data = mysqli_fetch_assoc($query);
                            echo $data['total'];
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Transaksi -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Detail Transaksi</h6>
        <a href="../cetak/laporan_pembayaran.php?<?= $_SERVER['QUERY_STRING'] ?>" target="_blank" 
           class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Cetak Laporan
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Pelanggan</th>
                        <th>Periode</th>
                        <th>Total Penggunaan</th>
                        <th>Total Bayar</th>
                        <th>Admin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($koneksi, "SELECT pb.*, t.bulan, t.tahun, pel.nama_pelanggan, 
                                                    u.nama_admin, pen.meter_awal, pen.meter_akhir
                                                    FROM pembayaran pb
                                                    JOIN tagihan t ON pb.id_tagihan = t.id_tagihan
                                                    JOIN pelanggan pel ON t.id_pelanggan = pel.id_pelanggan
                                                    JOIN penggunaan pen ON t.id_penggunaan = pen.id_penggunaan
                                                    JOIN user u ON pb.id_user = u.id_user
                                                    $where
                                                    ORDER BY pb.tanggal_pembayaran DESC");
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                        $penggunaan = $row['meter_akhir'] - $row['meter_awal'];
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= tanggal_indo($row['tanggal_pembayaran']) ?></td>
                        <td><?= $row['nama_pelanggan'] ?></td>
                        <td><?= getBulan($row['bulan']) . ' ' . $row['tahun'] ?></td>
                        <td><?= $penggunaan ?> kWh</td>
                        <td><?= formatRupiah($row['total_bayar']) ?></td>
                        <td><?= $row['nama_admin'] ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>

<?php include '../template/footer.php'; ?>