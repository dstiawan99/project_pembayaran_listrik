<?php include '../template/header.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Tagihan</h1>
    <a href="../proses/tagihan_proses.php?generate=true" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-sync fa-sm text-white-50"></i> Generate Tagihan
    </a>
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
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Bulan/Tahun</th>
                        <th>Jumlah Meter</th>
                        <th>Total Tagihan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($koneksi, "SELECT t.*, p.nama_pelanggan, p.id_tarif, 
                                                    tr.tarifperkwh, pen.meter_awal, pen.meter_akhir
                                                    FROM tagihan t 
                                                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                                                    JOIN tarif tr ON p.id_tarif = tr.id_tarif
                                                    JOIN penggunaan pen ON t.id_penggunaan = pen.id_penggunaan
                                                    ORDER BY t.id_tagihan DESC");
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                        $jumlah_meter = $row['meter_akhir'] - $row['meter_awal'];
                        $total_tagihan = $jumlah_meter * $row['tarifperkwh'];
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['nama_pelanggan'] ?></td>
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
                            <?php if ($row['status'] != 'lunas'): ?>
                                <a href="#" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#bayarModal<?= $row['id_tagihan'] ?>">
                                    <i class="fas fa-money-bill-wave"></i> Bayar
                                </a>
                            <?php else: ?>
                                <a href="../cetak/invoice.php?id=<?= $row['id_tagihan'] ?>" class="btn btn-sm btn-info" target="_blank">
                                    <i class="fas fa-print"></i> Cetak
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Modal Bayar -->
                    <?php if ($row['status'] != 'lunas'): ?>
                    <div class="modal fade" id="bayarModal<?= $row['id_tagihan'] ?>" tabindex="-1" role="dialog" aria-labelledby="bayarModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="bayarModalLabel">Pembayaran Tagihan</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="../proses/pembayaran_proses.php" method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="id_tagihan" value="<?= $row['id_tagihan'] ?>">
                                        <input type="hidden" name="id_pelanggan" value="<?= $row['id_pelanggan'] ?>">
                                        <input type="hidden" name="biaya_admin" value="2500">
                                        <input type="hidden" name="total_bayar" value="<?= $total_tagihan + 2500 ?>">
                                        
                                        <div class="form-group">
                                            <label>Nama Pelanggan</label>
                                            <input type="text" class="form-control" value="<?= $row['nama_pelanggan'] ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Bulan / Tahun</label>
                                            <input type="text" class="form-control" value="<?= getBulan($row['bulan']) ?> <?= $row['tahun'] ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Jumlah Meter</label>
                                            <input type="text" class="form-control" value="<?= $jumlah_meter ?> kWh" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Total Tagihan</label>
                                            <input type="text" class="form-control" value="<?= formatRupiah($total_tagihan) ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Biaya Admin</label>
                                            <input type="text" class="form-control" value="<?= formatRupiah(2500) ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Total Bayar</label>
                                            <input type="text" class="form-control" value="<?= formatRupiah($total_tagihan + 2500) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" name="bayar" class="btn btn-primary">Proses Pembayaran</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../template/footer.php'; ?>