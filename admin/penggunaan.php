<?php include '../template/header.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Penggunaan</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addModal">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Penggunaan
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
        <h6 class="m-0 font-weight-bold text-primary">Daftar Penggunaan Listrik</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Bulan/Tahun</th>
                        <th>Meter Awal</th>
                        <th>Meter Akhir</th>
                        <th>Jumlah Meter</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($koneksi, "SELECT p.*, pel.nama_pelanggan 
                                                    FROM penggunaan p 
                                                    JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
                                                    ORDER BY p.id_penggunaan DESC");
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['nama_pelanggan'] ?></td>
                        <td><?= getBulan($row['bulan']) ?> <?= $row['tahun'] ?></td>
                        <td><?= $row['meter_awal'] ?></td>
                        <td><?= $row['meter_akhir'] ?></td>
                        <td><?= $row['meter_akhir'] - $row['meter_awal'] ?></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal<?= $row['id_penggunaan'] ?>">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?= $row['id_penggunaan'] ?>">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $row['id_penggunaan'] ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Penggunaan</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="../proses/penggunaan_proses.php" method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="id_penggunaan" value="<?= $row['id_penggunaan'] ?>">
                                        <div class="form-group">
                                            <label>Pelanggan</label>
                                            <select class="form-control" name="id_pelanggan" required>
                                                <?php
                                                $query_pel = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY nama_pelanggan ASC");
                                                while ($pel = mysqli_fetch_assoc($query_pel)) {
                                                    $selected = ($pel['id_pelanggan'] == $row['id_pelanggan']) ? 'selected' : '';
                                                    echo '<option value="' . $pel['id_pelanggan'] . '" ' . $selected . '>' . $pel['nama_pelanggan'] . ' - ' . $pel['nomor_kwh'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Bulan</label>
                                            <select class="form-control" name="bulan" required>
                                                <?php
                                                $bulan = array(
                                                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', 
                                                    '04' => 'April', '05' => 'Mei', '06' => 'Juni', 
                                                    '07' => 'Juli', '08' => 'Agustus', '09' => 'September', 
                                                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                                );
                                                foreach ($bulan as $key => $value) {
                                                    $selected = ($key == $row['bulan']) ? 'selected' : '';
                                                    echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Tahun</label>
                                            <select class="form-control" name="tahun" required>
                                                <?php
                                                $tahun_sekarang = date('Y');
                                                for ($i = $tahun_sekarang; $i >= $tahun_sekarang - 2; $i--) {
                                                    $selected = ($i == $row['tahun']) ? 'selected' : '';
                                                    echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Meter Awal</label>
                                            <input type="number" class="form-control" name="meter_awal" value="<?= $row['meter_awal'] ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Meter Akhir</label>
                                            <input type="number" class="form-control" name="meter_akhir" value="<?= $row['meter_akhir'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal<?= $row['id_penggunaan'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus data penggunaan <strong><?= $row['nama_pelanggan'] ?></strong> untuk bulan <strong><?= getBulan($row['bulan']) ?> <?= $row['tahun'] ?></strong>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <a href="../proses/penggunaan_proses.php?delete=<?= $row['id_penggunaan'] ?>" class="btn btn-danger">Hapus</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Tambah Penggunaan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="../proses/penggunaan_proses.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pelanggan</label>
                        <select class="form-control" name="id_pelanggan" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            <?php
                            $query_pel = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY nama_pelanggan ASC");
                            while ($pel = mysqli_fetch_assoc($query_pel)) {
                                echo '<option value="' . $pel['id_pelanggan'] . '">' . $pel['nama_pelanggan'] . ' - ' . $pel['nomor_kwh'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Bulan</label>
                        <select class="form-control" name="bulan" required>
                            <option value="">-- Pilih Bulan --</option>
                            <?php
                            $bulan = array(
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', 
                                '04' => 'April', '05' => 'Mei', '06' => 'Juni', 
                                '07' => 'Juli', '08' => 'Agustus', '09' => 'September', 
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            );
                            foreach ($bulan as $key => $value) {
                                echo '<option value="' . $key . '">' . $value . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tahun</label>
                        <select class="form-control" name="tahun" required>
                            <option value="">-- Pilih Tahun --</option>
                            <?php
                            $tahun_sekarang = date('Y');
                            for ($i = $tahun_sekarang; $i >= $tahun_sekarang - 2; $i--) {
                                echo '<option value="' . $i . '">' . $i . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Meter Awal</label>
                        <input type="number" class="form-control" name="meter_awal" required>
                    </div>
                    <div class="form-group">
                        <label>Meter Akhir</label>
                        <input type="number" class="form-control" name="meter_akhir" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="add" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../template/footer.php'; ?>