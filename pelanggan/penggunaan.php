<?php include '../template/header.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Riwayat Penggunaan</h1>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Riwayat Penggunaan Listrik</h6>
    </div>
    <div class="card-body">
        <?php
        $id_pelanggan = $_SESSION['user_id'];
        
        $query = mysqli_query($koneksi, "SELECT * FROM penggunaan 
                                         WHERE id_pelanggan = '$id_pelanggan' 
                                         ORDER BY tahun DESC, bulan DESC");
        
        if (mysqli_num_rows($query) > 0) {
        ?>
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Bulan/Tahun</th>
                        <th>Meter Awal</th>
                        <th>Meter Akhir</th>
                        <th>Total Penggunaan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                        $jumlah_meter = $row['meter_akhir'] - $row['meter_awal'];
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= getBulan($row['bulan']) ?> <?= $row['tahun'] ?></td>
                        <td><?= $row['meter_awal'] ?></td>
                        <td><?= $row['meter_akhir'] ?></td>
                        <td><?= $jumlah_meter ?> kWh</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-tachometer-alt fa-4x text-gray-300"></i>
            </div>
            <p>Belum ada data penggunaan listrik.</p>
        </div>
        <?php } ?>
    </div>
</div>

<?php include '../template/footer.php'; ?>