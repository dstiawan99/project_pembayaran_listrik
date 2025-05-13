<?php include '../template/header.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data User</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addModal">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah User
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
        <h6 class="m-0 font-weight-bold text-primary">Daftar User</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Admin</th>
                        <th>Username</th>
                        <th>Level</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($koneksi, "SELECT u.*, l.nama_level
                                                    FROM user u
                                                    JOIN user_level l ON u.id_level = l.id_level
                                                    ORDER BY u.id_user DESC");
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['nama_admin'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['nama_level'] ?></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal<?= $row['id_user'] ?>">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($row['id_user'] != $_SESSION['user_id']) : ?>
                            <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?= $row['id_user'] ?>">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $row['id_user'] ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="../proses/user_proses.php" method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="id_user" value="<?= $row['id_user'] ?>">
                                        <div class="form-group">
                                            <label>Nama Admin</label>
                                            <input type="text" class="form-control" name="nama_admin" value="<?= $row['nama_admin'] ?>" required>
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
                                            <label>Level</label>
                                            <select class="form-control" name="id_level" required>
                                                <?php
                                                $query_level = mysqli_query($koneksi, "SELECT * FROM user_level ORDER BY id_level ASC");
                                                while ($level = mysqli_fetch_assoc($query_level)) {
                                                    $selected = ($level['id_level'] == $row['id_level']) ? 'selected' : '';
                                                    echo '<option value="' . $level['id_level'] . '" ' . $selected . '>' . $level['nama_level'] . '</option>';
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
                    <div class="modal fade" id="deleteModal<?= $row['id_user'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus user <strong><?= $row['nama_admin'] ?></strong>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <a href="../proses/user_proses.php?delete=<?= $row['id_user'] ?>" class="btn btn-danger">Hapus</a>
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
                <h5 class="modal-title" id="addModalLabel">Tambah User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="../proses/user_proses.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Admin</label>
                        <input type="text" class="form-control" name="nama_admin" required>
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
                        <label>Level</label>
                        <select class="form-control" name="id_level" required>
                            <option value="">-- Pilih Level --</option>
                            <?php
                            $query_level = mysqli_query($koneksi, "SELECT * FROM user_level ORDER BY id_level ASC");
                            while ($level = mysqli_fetch_assoc($query_level)) {
                                echo '<option value="' . $level['id_level'] . '">' . $level['nama_level'] . '</option>';
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