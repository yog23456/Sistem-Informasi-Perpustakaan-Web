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

$id = (int) ($_GET['id'] ?? 0);
if (!$id) redirect('index.php');

$stmt = mysqli_prepare($connection, "SELECT id, username, nama_lengkap, foto_profil FROM users WHERE id = ? AND role = 'petugas'");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    redirect('index.php');
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');

    // Upload Foto Baru (jika ada)
    $foto_profil = $user['foto_profil']; // Gunakan foto lama sebagai default
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../assets/img/users/";
        $imageFileType = strtolower(pathinfo($_FILES["foto_profil"]["name"], PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png", "gif"];

        if (in_array($imageFileType, $allowed_types)) {
            // Hapus foto lama jika bukan default
            if ($user['foto_profil'] !== 'default.png') {
                $old_file = $target_dir . $user['foto_profil'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            $filename = 'user_' . time() . '_' . uniqid() . '.' . $imageFileType;
            $target_file = $target_dir . $filename;

            if (move_uploaded_file($_FILES["foto_profil"]["tmp_name"], $target_file)) {
                $foto_profil = $filename;
            } else {
                $error = "Gagal mengupload foto baru.";
            }
        } else {
            $error = "Format foto tidak valid. Gunakan JPG, JPEG, PNG, atau GIF.";
        }
    }

    if (empty($username) || empty($nama_lengkap)) {
        $error = "Username dan Nama Lengkap wajib diisi.";
    } else {
        $check_stmt = mysqli_prepare($connection, "SELECT id FROM users WHERE username = ? AND id != ?");
        mysqli_stmt_bind_param($check_stmt, "si", $username, $id);
        mysqli_stmt_execute($check_stmt);
        if (mysqli_num_rows(mysqli_stmt_get_result($check_stmt)) > 0) {
            $error = "Username sudah digunakan.";
        }
        mysqli_stmt_close($check_stmt);

        if (!$error) {
            $update_sql = "UPDATE users SET username = ?, nama_lengkap = ?, foto_profil = ? WHERE id = ?";
            $params = [$username, $nama_lengkap, $foto_profil, $id];

            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE users SET username = ?, password = ?, nama_lengkap = ?, foto_profil = ? WHERE id = ?";
                $params = [$username, $hashed_password, $nama_lengkap, $foto_profil, $id];
            }

            $stmt = mysqli_prepare($connection, $update_sql);
            mysqli_stmt_bind_param($stmt, str_repeat("s", count($params) - 1) . "i", ...$params);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Data petugas berhasil diperbarui.";
                // Refresh data
                $stmt = mysqli_prepare($connection, "SELECT id, username, nama_lengkap, foto_profil FROM users WHERE id = ? AND role = 'petugas'");
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            } else {
                $error = "Gagal memperbarui: " . mysqli_error($connection);
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

            <h2>Edit Petugas</h2>
            <?php if ($error): ?>
                <?= showAlert($error, 'danger') ?>
            <?php endif; ?>
            <?php if ($success): ?>
                <?= showAlert($success, 'success') ?>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Username*</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password (kosongkan jika tidak ingin diubah)</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap*</label>
                    <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Foto Profil Saat Ini</label><br>
                    <img src="../assets/img/users/<?= htmlspecialchars($user['foto_profil']) ?>" alt="Foto Profil" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                </div>
                <div class="mb-3">
                    <label class="form-label">Ganti Foto Profil</label>
                    <input type="file" name="foto_profil" class="form-control" accept="image/*">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengganti.</small>
                </div>
                <button type="submit" class="btn btn-primary">Perbarui</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>