<?php include '../template/header.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tagihan Saya</h1>
</div>

<?php
// Pesan dari proses
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-' . $_SESSION['message_type'] . ' alert-dismissible fade show" role="alert">
            ' . $_SESSION['message'] . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Tagihan Listrik</h6>
    </div>
    <div class="card-body">
        <?php
        $id_pelanggan = $_SESSION['user_id'];
        
        // Ambil data tarif
        $query_tarif = mysqli_query($koneksi, "SELECT t.tarifperkwh 
                                              FROM pelanggan p 
                                              JOIN tarif t ON p.id_tarif = t.id_tarif 
                                              WHERE p.id_pelanggan = '$id_pelanggan'");
        $data_tarif = mysqli_fetch_assoc($query_tarif);
        $tarifperkwh = $data_tarif['tarifperkwh'];
        
        $query = mysqli_query($koneksi, "SELECT t.*, p.meter_awal, p.meter_akhir 
                                         FROM tagihan t 
                                         JOIN penggunaan p ON t.id_penggunaan = p.id_penggunaan
                                         WHERE t.id_pelanggan = '$id_pelanggan' 
                                         ORDER BY t.tahun DESC, t.bulan DESC");
        
        if (mysqli_num_rows($query) > 0) {
        ?>
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Bulan/Tahun</th>
                        <th>Penggunaan</th>
                        <th>Total Tagihan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                        $jumlah_meter = $row['meter_akhir'] - $row['meter_awal'];
                        $total_tagihan = $jumlah_meter * $tarifperkwh;
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= getBulan($row['bulan']) ?> <?= $row['tahun'] ?></td>
                        <td><?= $jumlah_meter ?> kWh</td>
                        <td><?= formatRupiah($total_tagihan) ?></td>
                        <td>
                            <?php if ($row['status'] == 'lunas'): ?>
                                <span class="badge badge-success">Lunas</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Belum Bayar</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal<?= $row['id_tagihan'] ?>">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <?php if ($row['status'] == 'lunas'): ?>
                                <a href="../cetak/invoice.php?id=<?= $row['id_tagihan'] ?>" class="btn btn-sm btn-secondary" target="_blank">
                                    <i class="fas fa-print"></i> Cetak
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Detail Modal -->
                    <div class="modal fade" id="detailModal<?= $row['id_tagihan'] ?>" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailModalLabel">Detail Tagihan</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="140">Bulan/Tahun</td>
                                            <td width="10">:</td>
                                            <td><strong><?= getBulan($row['bulan']) ?> <?= $row['tahun'] ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>Meter Awal</td>
                                            <td>:</td>
                                            <td><?= $row['meter_awal'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Meter Akhir</td>
                                            <td>:</td>
                                            <td><?= $row['meter_akhir'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Penggunaan</td>
                                            <td>:</td>
                                            <td><?= $jumlah_meter ?> kWh</td>
                                        </tr>
                                        <tr>
                                            <td>Tarif per kWh</td>
                                            <td>:</td>
                                            <td><?= formatRupiah($tarifperkwh) ?></td>
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
                                                <?php if ($row['status'] == 'lunas'): ?>
                                                    <span class="badge badge-success">Lunas</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">Belum Bayar</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                    <?php if ($row['status'] == 'lunas'): ?>
                                        <a href="../cetak/invoice.php?id=<?= $row['id_tagihan'] ?>" class="btn btn-primary" target="_blank">
                                            <i class="fas fa-print"></i> Cetak Invoice
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-file-invoice fa-4x text-gray-300"></i>
            </div>
            <p>Belum ada tagihan untuk Anda.</p>
        </div>
        <?php } ?>
    </div>
</div>

<?php include '../template/footer.php'; ?>