<?php include '../template/header.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Pelanggan</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addModal">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Pelanggan
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
        <h6 class="m-0 font-weight-bold text-primary">Daftar Pelanggan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Username</th>
                        <th>Nomor KWH</th>
                        <th>Alamat</th>
                        <th>Tarif/Daya</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($koneksi, "SELECT p.*, t.daya, t.tarifperkwh 
                                                    FROM pelanggan p 
                                                    JOIN tarif t ON p.id_tarif = t.id_tarif
                                                    ORDER BY p.id_pelanggan DESC");
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['nama_pelanggan'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['nomor_kwh'] ?></td>
                        <td><?= $row['alamat'] ?></td>
                        <td><?= $row['daya'] ?> VA / <?= formatRupiah($row['tarifperkwh']) ?> per kWh</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal<?= $row['id_pelanggan'] ?>">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?= $row['id_pelanggan'] ?>">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $row['id_pelanggan'] ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Pelanggan</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="../proses/pelanggan_proses.php" method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="id_pelanggan" value="<?= $row['id_pelanggan'] ?>">
                                        <div class="form-group">
                                            <label>Nama Pelanggan</label>
                                            <input type="text" class="form-control" name="nama_pelanggan" value="<?= $row['nama_pelanggan'] ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input type="text" class="form-control" name="username" value="<?= $row['username'] ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                                        </div>
                                        <div class="form-group">
                                            <label>Nomor KWH</label>
                                            <input type="text" class="form-control" name="nomor_kwh" value="<?= $row['nomor_kwh'] ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Alamat</label>
                                            <textarea class="form-control" name="alamat" required><?= $row['alamat'] ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Tarif/Daya</label>
                                            <select class="form-control" name="id_tarif" required>
                                                <?php
                                                $query_tarif = mysqli_query($koneksi, "SELECT * FROM tarif ORDER BY daya ASC");
                                                while ($tarif = mysqli_fetch_assoc($query_tarif)) {
                                                    $selected = ($tarif['id_tarif'] == $row['id_tarif']) ? 'selected' : '';
                                                    echo '<option value="' . $tarif['id_tarif'] . '" ' . $selected . '>' . $tarif['daya'] . ' VA - ' . formatRupiah($tarif['tarifperkwh']) . ' per kWh</option>';
                                                }
                                                ?>
                                            </select>
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
                    <div class="modal fade" id="deleteModal<?= $row['id_pelanggan'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus pelanggan <strong><?= $row['nama_pelanggan'] ?></strong>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <a href="../proses/pelanggan_proses.php?delete=<?= $row['id_pelanggan'] ?>" class="btn btn-danger">Hapus</a>
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
                <h5 class="modal-title" id="addModalLabel">Tambah Pelanggan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="../proses/pelanggan_proses.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Pelanggan</label>
                        <input type="text" class="form-control" name="nama_pelanggan" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Nomor KWH</label>
                        <input type="text" class="form-control" name="nomor_kwh" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea class="form-control" name="alamat" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Tarif/Daya</label>
                        <select class="form-control" name="id_tarif" required>
                            <option value="">-- Pilih Tarif --</option>
                            <?php
                            $query_tarif = mysqli_query($koneksi, "SELECT * FROM tarif ORDER BY daya ASC");
                            while ($tarif = mysqli_fetch_assoc($query_tarif)) {
                                echo '<option value="' . $tarif['id_tarif'] . '">' . $tarif['daya'] . ' VA - ' . formatRupiah($tarif['tarifperkwh']) . ' per kWh</option>';
                            }
                            ?>
                        </select>
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