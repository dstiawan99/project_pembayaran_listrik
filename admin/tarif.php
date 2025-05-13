<?php include '../template/header.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Tarif</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addModal">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Tarif
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
        <h6 class="m-0 font-weight-bold text-primary">Daftar Tarif Listrik</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Daya</th>
                        <th>Tarif per kWh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($koneksi, "SELECT * FROM tarif ORDER BY daya ASC");
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['daya'] ?> VA</td>
                        <td><?= formatRupiah($row['tarifperkwh']) ?></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal<?= $row['id_tarif'] ?>">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?= $row['id_tarif'] ?>">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $row['id_tarif'] ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Tarif</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="../proses/tarif_proses.php" method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="id_tarif" value="<?= $row['id_tarif'] ?>">
                                        <div class="form-group">
                                            <label>Daya (VA)</label>
                                            <input type="number" class="form-control" name="daya" value="<?= $row['daya'] ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Tarif per kWh (Rp)</label>
                                            <input type="number" class="form-control" name="tarifperkwh" value="<?= $row['tarifperkwh'] ?>" required>
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
                    <div class="modal fade" id="deleteModal<?= $row['id_tarif'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus tarif <strong><?= $row['daya'] ?> VA</strong>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <a href="../proses/tarif_proses.php?delete=<?= $row['id_tarif'] ?>" class="btn btn-danger">Hapus</a>
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
                <h5 class="modal-title" id="addModalLabel">Tambah Tarif</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="../proses/tarif_proses.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Daya (VA)</label>
                        <input type="number" class="form-control" name="daya" required>
                    </div>
                    <div class="form-group">
                        <label>Tarif per kWh (Rp)</label>
                        <input type="number" class="form-control" name="tarifperkwh" required>
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