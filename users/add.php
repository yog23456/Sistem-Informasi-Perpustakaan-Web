<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';
requireAuth();

// Hanya admin yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    die('Access Denied');
}

require_once '../config/database.php';

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');

    // Upload Foto
    $foto_profil = 'default.png';
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../assets/img/users/";
        $imageFileType = strtolower(pathinfo($_FILES["foto_profil"]["name"], PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png", "gif"];

        if (in_array($imageFileType, $allowed_types)) {
            $filename = 'user_' . time() . '_' . uniqid() . '.' . $imageFileType;
            $target_file = $target_dir . $filename;

            if (move_uploaded_file($_FILES["foto_profil"]["tmp_name"], $target_file)) {
                $foto_profil = $filename;
            } else {
                $error = "Gagal mengupload foto.";
            }
        } else {
            $error = "Format foto tidak valid. Gunakan JPG, JPEG, PNG, atau GIF.";
        }
    }

    if (empty($username) || empty($password) || empty($nama_lengkap)) {
        $error = "Semua field wajib diisi.";
    } else {
        // Periksa apakah username sudah digunakan
        $check_stmt = mysqli_prepare($connection, "SELECT id FROM users WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($check_stmt, "s", $username);
        mysqli_stmt_execute($check_stmt);
        if (mysqli_num_rows(mysqli_stmt_get_result($check_stmt)) > 0) {
            $error = "Username sudah digunakan.";
        }
        mysqli_stmt_close($check_stmt);

        if (!$error) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($connection, "INSERT INTO users (username, password, nama_lengkap, foto_profil, role) VALUES (?, ?, ?, ?, 'petugas')");
            mysqli_stmt_bind_param($stmt, "ssss", $username, $hashed_password, $nama_lengkap, $foto_profil);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Petugas berhasil ditambahkan.";
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 2000);
                </script>";
            } else {
                $error = "Gagal menyimpan: " . mysqli_error($connection);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/topnav.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>

            <h2>Tambah Petugas</h2>
            <?php if ($error): ?>
                <?= showAlert($error, 'danger') ?>
            <?php endif; ?>
            <?php if ($success): ?>
                <?= showAlert($success, 'success') ?>
                <a href="index.php" class="btn btn-secondary">Kembali ke Daftar</a>
            <?php else: ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Username*</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password*</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap*</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Profil</label>
                        <input type="file" name="foto_profil" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, GIF.</small>
                    </div>
                    <button type="submit" class="btn btn-save-petugas">Simpan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            <?php endif; ?>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>