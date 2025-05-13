<?php include '../template/header.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Riwayat Pembayaran</h1>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Riwayat Pembayaran Listrik</h6>
    </div>
    <div class="card-body">
        <?php
        $id_pelanggan = $_SESSION['user_id'];
        
        $query = mysqli_query($koneksi, "SELECT pb.*, t.bulan, t.tahun, u.nama_admin 
                                         FROM pembayaran pb 
                                         JOIN tagihan t ON pb.id_tagihan = t.id_tagihan
                                         JOIN user u ON pb.id_user = u.id_user
                                         WHERE t.id_pelanggan = '$id_pelanggan' 
                                         ORDER BY pb.tanggal_pembayaran DESC");
        
        if (mysqli_num_rows($query) > 0) {
        ?>
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
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
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
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
        <?php } else { ?>
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-money-bill-wave fa-4x text-gray-300"></i>
            </div>
            <p>Belum ada riwayat pembayaran.</p>
        </div>
        <?php } ?>
    </div>
</div>

<?php include '../template/footer.php'; ?>