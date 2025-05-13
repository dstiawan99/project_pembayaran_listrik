<?php include '../template/header.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<?php
$id_pelanggan = $_SESSION['user_id'];
$query_pelanggan = mysqli_query($koneksi, "SELECT p.*, t.daya, t.tarifperkwh 
                                          FROM pelanggan p 
                                          JOIN tarif t ON p.id_tarif = t.id_tarif 
                                          WHERE p.id_pelanggan = '$id_pelanggan'");
$data_pelanggan = mysqli_fetch_assoc($query_pelanggan);
?>

<!-- Content Row -->
<div class="row">
    <!-- Profile Card -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Pelanggan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="140">Nama</td>
                        <td width="10">:</td>
                        <td><strong><?= $data_pelanggan['nama_pelanggan'] ?></strong></td>
                    </tr>
                    <tr>
                        <td>Nomor KWH</td>
                        <td>:</td>
                        <td><?= $data_pelanggan['nomor_kwh'] ?></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td><?= $data_pelanggan['alamat'] ?></td>
                    </tr>
                    <tr>
                        <td>Daya</td>
                        <td>:</td>
                        <td><?= $data_pelanggan['daya'] ?> VA</td>
                    </tr>
                    <tr>
                        <td>Tarif per kWh</td>
                        <td>:</td>
                        <td><?= formatRupiah($data_pelanggan['tarifperkwh']) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Tagihan Card -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tagihan Bulan Ini</h6>
            </div>
            <div class="card-body">
                <?php
                $bulan_ini = date('m');
                $tahun_ini = date('Y');
                
                $query_tagihan = mysqli_query($koneksi, "SELECT t.*, p.meter_awal, p.meter_akhir 
                                                       FROM tagihan t 
                                                       JOIN penggunaan p ON t.id_penggunaan = p.id_penggunaan
                                                       WHERE t.id_pelanggan = '$id_pelanggan' 
                                                       AND t.bulan = '$bulan_ini' 
                                                       AND t.tahun = '$tahun_ini'");
                
                if (mysqli_num_rows($query_tagihan) > 0) {
                    $data_tagihan = mysqli_fetch_assoc($query_tagihan);
                    $jumlah_meter = $data_tagihan['meter_akhir'] - $data_tagihan['meter_awal'];
                    $total_tagihan = $jumlah_meter * $data_pelanggan['tarifperkwh'];
                ?>
                
                <table class="table table-borderless">
                    <tr>
                        <td width="140">Bulan/Tahun</td>
                        <td width="10">:</td>
                        <td><strong><?= getBulan($data_tagihan['bulan']) ?> <?= $data_tagihan['tahun'] ?></strong></td>
                    </tr>
                    <tr>
                        <td>Penggunaan</td>
                        <td>:</td>
                        <td><?= $jumlah_meter ?> kWh</td>
                    </tr>
                    <tr>
                        <td>Total Tagihan</td>
                        <td>:</td>
                        <td><strong><?= formatRupiah($total_tagihan) ?></strong></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>
                            <?php if ($data_tagihan['status'] == 'lunas'): ?>
                                <span class="badge badge-success">Lunas</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Belum Bayar</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                
                <?php if ($data_tagihan['status'] != 'lunas'): ?>
                <div class="text-center mt-3">
                    <a href="tagihan.php" class="btn btn-primary">Lihat Detail Tagihan</a>
                </div>
                <?php endif; ?>
                
                <?php } else { ?>
                
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-file-invoice fa-4x text-gray-300"></i>
                    </div>
                    <p>Belum ada tagihan untuk bulan <?= getBulan($bulan_ini) ?> <?= $tahun_ini ?>.</p>
                </div>
                
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Grafik Penggunaan -->
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Grafik Penggunaan Listrik</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data untuk Grafik -->
<?php
// Data untuk area chart (penggunaan bulanan)
$bulan_nama = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
$data_bulanan = array_fill(0, 12, 0);

$tahun_ini = date('Y');
$query_chart = mysqli_query($koneksi, "SELECT p.bulan, p.meter_akhir - p.meter_awal as jumlah_meter 
                FROM penggunaan p
                WHERE p.id_pelanggan = '$id_pelanggan' AND p.tahun = '$tahun_ini' 
                GROUP BY p.bulan");

while ($row = mysqli_fetch_assoc($query_chart)) {
    $index = (int)$row['bulan'] - 1;
    $data_bulanan[$index] = (int)$row['jumlah_meter'];
}
?>

<script>
// Area Chart
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($bulan_nama) ?>,
            datasets: [{
                label: "Penggunaan kWh",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: <?= json_encode($data_bulanan) ?>,
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    time: {
                        unit: 'date'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value, index, values) {
                            return value + ' kWh';
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': ' + tooltipItem.yLabel + ' kWh';
                    }
                }
            }
        }
    });
});
</script>

<?php include '../template/footer.php'; ?>