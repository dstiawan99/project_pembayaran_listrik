<?php 
include '../template/header.php';

// Ambil data pelanggan
$id_pelanggan = $_SESSION['user_id'];
$query = mysqli_query($koneksi, "SELECT p.*, t.daya, t.tarifperkwh 
                                FROM pelanggan p 
                                JOIN tarif t ON p.id_tarif = t.id_tarif 
                                WHERE p.id_pelanggan = '$id_pelanggan'");
$data = mysqli_fetch_assoc($query);

// Proses update profile
if(isset($_POST['update_profile'])) {
    $nama_pelanggan = mysqli_real_escape_string($koneksi, $_POST['nama_pelanggan']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    $query = "UPDATE pelanggan SET 
              nama_pelanggan = '$nama_pelanggan', 
              alamat = '$alamat' 
              WHERE id_pelanggan = '$id_pelanggan'";
    if(mysqli_query($koneksi, $query)) {
        $_SESSION['nama'] = $nama_pelanggan;
        $_SESSION['message'] = "Profile berhasil diupdate!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal mengupdate profile!";
        $_SESSION['message_type'] = "danger";
    }
}

// Proses ganti password
if(isset($_POST['change_password'])) {
    $old_password = md5($_POST['old_password']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Cek password lama
    $query_check = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id_pelanggan = '$id_pelanggan' AND password = '$old_password'");
    if(mysqli_num_rows($query_check) == 0) {
        $_SESSION['message'] = "Password lama tidak sesuai!";
        $_SESSION['message_type'] = "danger";
    } else if($new_password != $confirm_password) {
        $_SESSION['message'] = "Konfirmasi password baru tidak sesuai!";
        $_SESSION['message_type'] = "danger";
    } else {
        $new_password = md5($new_password);
        $query = "UPDATE pelanggan SET password = '$new_password' WHERE id_pelanggan = '$id_pelanggan'";
        if(mysqli_query($koneksi, $query)) {
            $_SESSION['message'] = "Password berhasil diubah!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal mengubah password!";
            $_SESSION['message_type'] = "danger";
        }
    }
}
?>

<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800">Profile</h1>

<?php if(isset($_SESSION['message'])): ?>
<div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
    <?= $_SESSION['message'] ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php 
unset($_SESSION['message']);
unset($_SESSION['message_type']);
endif; 
?>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Pelanggan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="150">Nomor Meter</td>
                        <td width="20">:</td>
                        <td><strong><?= $data['nomor_kwh'] ?></strong></td>
                    </tr>
                    <tr>
                        <td>Daya</td>
                        <td>:</td>
                        <td><?= $data['daya'] ?> VA</td>
                    </tr>
                    <tr>
                        <td>Tarif per kWh</td>
                        <td>:</td>
                        <td><?= formatRupiah($data['tarifperkwh']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Update Profile</h6>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" value="<?= $data['username'] ?>" disabled>
                        <small class="text-muted">Username tidak dapat diubah</small>
                    </div>
                    <div class="form-group">
                        <label>Nama Pelanggan</label>
                        <input type="text" class="form-control" name="nama_pelanggan" value="<?= $data['nama_pelanggan'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea class="form-control" name="alamat" required><?= $data['alamat'] ?></textarea>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Ganti Password</h6>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label>Password Lama</label>
                        <input type="password" class="form-control" name="old_password" required>
                    </div>
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Ganti Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../template/footer.php'; ?>