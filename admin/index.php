<?php 
include '../template/header.php';

// Hitung total pelanggan
$query_pelanggan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pelanggan");
$total_pelanggan = mysqli_fetch_assoc($query_pelanggan)['total'];

// Hitung tagihan belum bayar
$query_tagihan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tagihan WHERE status = 'belum_bayar'");
$tagihan_belum_bayar = mysqli_fetch_assoc($query_tagihan)['total'];

// Hitung total pembayaran bulan ini
$bulan_ini = date('m');
$tahun_ini = date('Y');
$query_pembayaran = mysqli_query($koneksi, "SELECT SUM(total_bayar) as total FROM pembayaran 
                                           WHERE MONTH(tanggal_pembayaran) = '$bulan_ini' 
                                           AND YEAR(tanggal_pembayaran) = '$tahun_ini'");
$total_pembayaran = mysqli_fetch_assoc($query_pembayaran)['total'] ?? 0;

// Hitung total admin
$query_admin = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM user WHERE id_level = 1");
$total_admin = mysqli_fetch_assoc($query_admin)['total'];

// Data untuk grafik pembayaran bulanan
$data_pembayaran = array_fill(0, 12, 0);
$query_grafik = mysqli_query($koneksi, "SELECT MONTH(tanggal_pembayaran) as bulan, SUM(total_bayar) as total 
                                       FROM pembayaran 
                                       WHERE YEAR(tanggal_pembayaran) = '$tahun_ini' 
                                       GROUP BY MONTH(tanggal_pembayaran)");
while ($row = mysqli_fetch_assoc($query_grafik)) {
    $data_pembayaran[$row['bulan']-1] = (float)$row['total'];
}

// Data untuk pie chart status tagihan
$query_status = mysqli_query($koneksi, "SELECT status, COUNT(*) as total FROM tagihan GROUP BY status");
$status_lunas = 0;
$status_belum = 0;
while ($row = mysqli_fetch_assoc($query_status)) {
    if ($row['status'] == 'lunas') {
        $status_lunas = $row['total'];
    } else {
        $status_belum = $row['total'];
    }
}
?>

<!-- Content Row -->
<div class="row">
    <!-- Total Pelanggan Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pelanggan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_pelanggan ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tagihan Belum Bayar Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tagihan Belum Bayar</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $tagihan_belum_bayar ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Pembayaran Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pembayaran (Bulan Ini)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= formatRupiah($total_pembayaran) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Admin Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Admin</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_admin ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-cog fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Grafik Pembayaran Bulanan <?= $tahun_ini ?></h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Status Tagihan</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="myPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Lunas
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-warning"></i> Belum Bayar
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Area Chart
var ctx = document.getElementById("myAreaChart");
var myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
            label: "Pendapatan",
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
            data: <?= json_encode($data_pembayaran) ?>,
        }],
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    callback: function(value) {
                        return 'Rp' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }
                }
            }]
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, chart) {
                    return 'Rp' + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            }
        }
    }
});

// Pie Chart
var ctx2 = document.getElementById("myPieChart");
var myPieChart = new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: ["Lunas", "Belum Bayar"],
        datasets: [{
            data: [<?= $status_lunas ?>, <?= $status_belum ?>],
            backgroundColor: ['#1cc88a', '#f6c23e'],
            hoverBackgroundColor: ['#17a673', '#f4b619'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
        },
        legend: {
            display: false
        },
        cutoutPercentage: 80,
    },
});
</script>

<?php include '../template/footer.php'; ?>