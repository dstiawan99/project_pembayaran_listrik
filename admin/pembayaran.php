<?php include '../template/header.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Pembayaran</h1>
    <a href="../cetak/laporan_pembayaran.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" target="_blank">
        <i class="fas fa-download fa-sm text-white-50"></i> Cetak Laporan
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
        <h6 class="m-0 font-weight-bold text-primary">Riwayat Pembayaran Listrik</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Tanggal Bayar</th>
                        <th>Bulan/Tahun</th>
                        <th>Biaya Admin</th>
                        <th>Total Bayar</th>
                        <th>Petugas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($koneksi, "SELECT pb.*, t.bulan, t.tahun, 
                                                    p.nama_pelanggan, u.nama_admin
                                                    FROM pembayaran pb 
                                                    JOIN tagihan t ON pb.id_tagihan = t.id_tagihan
                                                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                                                    JOIN user u ON pb.id_user = u.id_user
                                                    ORDER BY pb.tanggal_pembayaran DESC");
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['nama_pelanggan'] ?></td>
                        <td><?= tanggal_indo($row['tanggal_pembayaran']) ?></td>
                        <td><?= getBulan($row['bulan']) ?> <?= $row['tahun'] ?></td>
                        <td><?= formatRupiah($row['biaya_admin']) ?></td>
                        <td><?= formatRupiah($row['total_bayar']) ?></td>
                        <td><?= $row['nama_admin'] ?></td>
                        <td>
                            <a href="../cetak/invoice.php?id=<?= $row['id_tagihan'] ?>" class="btn btn-sm btn-info" target="_blank">
                                <i class="fas fa-print"></i> Cetak
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../template/footer.php'; ?>