<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.use_strict_mode', 1);
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';
requireAuth();
requireModuleAccess('peminjaman');
require_once '../config/database.php';
$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('index.php');
$stmt = mysqli_prepare($connection, "SELECT * FROM `peminjaman` WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$peminjaman = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if (!$peminjaman) redirect('index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) die('Invalid CSRF token.');
    $nama_peminjam_post = trim($_POST['nama_peminjam'] ?? '');
    $nim_peminjam_post = trim($_POST['nim_peminjam'] ?? '');
    $kelas_peminjam_post = trim($_POST['kelas_peminjam'] ?? '');
    $petugas_id_post = trim($_POST['petugas_id'] ?? '');
    $tanggal_pinjam_post = trim($_POST['tanggal_pinjam'] ?? '');
    $tanggal_kembali_seharusnya_post = trim($_POST['tanggal_kembali_seharusnya'] ?? '');
    if (empty($nama_peminjam_post) || empty($nim_peminjam_post) || empty($petugas_id_post) || empty($tanggal_pinjam_post) || empty($tanggal_kembali_seharusnya_post)) {
        $error = "Semua field wajib diisi.";
    }
    if (!$error) {
        $stmt = mysqli_prepare($connection, "UPDATE `peminjaman` SET `nama_peminjam` = ?, `nim_peminjam` = ?, `kelas_peminjam` = ?, `petugas_id` = ?, `tanggal_pinjam` = ?, `tanggal_kembali_seharusnya` = ?  WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssissi", $nama_peminjam_post, $nim_peminjam_post, $kelas_peminjam_post, $petugas_id_post, $tanggal_pinjam_post, $tanggal_kembali_seharusnya_post, $id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            $stmt = mysqli_prepare($connection, "SELECT * FROM `peminjaman` WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $peminjaman = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        } else {
            $error = "Gagal memperbarui peminjaman.";
        }
        mysqli_stmt_close($stmt);
    }
}
$csrfToken = generateCSRFToken();
?>
<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/topnav.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>
<h2>Edit Peminjaman</h2>
<?php if ($error): ?>
<?= showAlert($error, 'danger') ?>
<?php endif; ?>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <div class="mb-3">
<label class="form-label">Nama Peminjam*</label>
<input type="text" name="nama_peminjam" class="form-control" value="<?= htmlspecialchars($peminjaman['nama_peminjam']) ?>" required>
</div>
                <div class="mb-3">
<label class="form-label">Nim Peminjam*</label>
<input type="text" name="nim_peminjam" class="form-control" value="<?= htmlspecialchars($peminjaman['nim_peminjam']) ?>" required>
</div>
                <div class="mb-3">
<label class="form-label">Kelas Peminjam</label>
<input type="text" name="kelas_peminjam" class="form-control" value="<?= htmlspecialchars($peminjaman['kelas_peminjam']) ?>">
</div>
                <div class="mb-3">
<label class="form-label">Petugas Id*</label>
<input type="number" name="petugas_id" class="form-control" value="<?= $peminjaman['petugas_id'] ?>" required>
</div>
                <div class="mb-3">
<label class="form-label">Tanggal Pinjam*</label>
<input type="date" name="tanggal_pinjam" class="form-control" value="<?= $peminjaman['tanggal_pinjam'] ?>" required>
</div>
                <div class="mb-3">
<label class="form-label">Tanggal Kembali Seharusnya*</label>
<input type="date" name="tanggal_kembali_seharusnya" class="form-control" value="<?= $peminjaman['tanggal_kembali_seharusnya'] ?>" required>
</div>
                <div class="mb-3">

</select>
</div>
<button type="submit" class="btn btn-primary">Perbarui</button>
<a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>
