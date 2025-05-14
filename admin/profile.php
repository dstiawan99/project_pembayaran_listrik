<?php 
include '../template/header.php';

// Ambil data user
$id_user = $_SESSION['user_id'];
$query = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = '$id_user'");
$data = mysqli_fetch_assoc($query);

// Proses update profile
if(isset($_POST['update_profile'])) {
    $nama_admin = mysqli_real_escape_string($koneksi, $_POST['nama_admin']);
    
    $query = "UPDATE user SET nama_admin = '$nama_admin' WHERE id_user = '$id_user'";
    if(mysqli_query($koneksi, $query)) {
        $_SESSION['nama'] = $nama_admin;
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
    $query_check = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = '$id_user' AND password = '$old_password'");
    if(mysqli_num_rows($query_check) == 0) {
        $_SESSION['message'] = "Password lama tidak sesuai!";
        $_SESSION['message_type'] = "danger";
    } else if($new_password != $confirm_password) {
        $_SESSION['message'] = "Konfirmasi password baru tidak sesuai!";
        $_SESSION['message_type'] = "danger";
    } else {
        $new_password = md5($new_password);
        $query = "UPDATE user SET password = '$new_password' WHERE id_user = '$id_user'";
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
                        <label>Nama Admin</label>
                        <input type="text" class="form-control" name="nama_admin" value="<?= $data['nama_admin'] ?>" required>
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