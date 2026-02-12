<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('anggota');

require_once '../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) redirect('index.php');

// Ambil data anggota termasuk kolom foto
$stmt = mysqli_prepare($connection, "SELECT * FROM `anggota` WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$anggota = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$anggota) redirect('index.php');

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_post = trim($_POST['nama'] ?? '');
    $nim_post = trim($_POST['nim'] ?? '');
    $kelas_post = trim($_POST['kelas'] ?? '');
    $foto_post = $anggota['foto'];

    if (!empty($_FILES['foto']['name'])) {
        // Hapus foto lama jika ada dan bukan default.png
        if ($anggota['foto'] !== 'default.png' && file_exists("../assets/img/anggota/" . $anggota['foto'])) {
            unlink("../assets/img/anggota/" . $anggota['foto']);
        }
        
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_post = time() . "_" . $nim_post . "." . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], "../assets/img/anggota/" . $foto_post);
    }

    if (empty($nama_post) || empty($nim_post) || empty($kelas_post)) {
        $error = "Nama, Nim, dan Kelas wajib diisi.";
    }

    if (!$error) {
        $stmt = mysqli_prepare($connection, "UPDATE `anggota` SET `nama` = ?, `nim` = ?, `kelas` = ?, `foto` = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssssi", $nama_post, $nim_post, $kelas_post, $foto_post, $id);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Data anggota berhasil diperbarui.";
            echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 1500);</script>";
        } else {
            $error = "Gagal memperbarui: " . mysqli_error($connection);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/topnav.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>

<h2>Edit Anggota</h2>
<?php if ($error) echo showAlert($error, 'danger'); ?>
<?php if ($success) echo showAlert($success, 'success'); ?>

<div class="white_shd full margin_bottom_30">
    <div class="padding_infor_info">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Nama*</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($anggota['nama']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nim*</label>
                        <input type="text" name="nim" class="form-control" value="<?= htmlspecialchars($anggota['nim']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelas*</label>
                        <input type="text" name="kelas" class="form-control" value="<?= htmlspecialchars($anggota['kelas']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ganti Foto</label>
                        <input type="file" name="foto" id="inputFoto" class="form-control" accept="image/*" onchange="previewImage()">
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
                
                <div class="col-md-4 text-center">
                    <label class="form-label"><strong>Foto Sekarang / Preview</strong></label><br>
                    <div class="p-2 border rounded bg-light">
                        <?php 
                            // Cek fisik file di folder aset
                            $displayFoto = (!empty($anggota['foto']) && file_exists("../assets/img/anggota/" . $anggota['foto'])) 
                                           ? $anggota['foto'] : 'default.png';
                        ?>
                        <img id="imgPreview" src="<?= base_url('assets/img/anggota/' . $displayFoto) ?>" 
                             class="img-fluid rounded shadow-sm" style="max-height: 250px; object-fit: cover;">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage() {
    const file = document.getElementById('inputFoto').files[0];
    const preview = document.getElementById('imgPreview');
    const reader = new FileReader();

    reader.onloadend = function() {
        preview.src = reader.result;
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        // Balikkan ke foto asli jika batal pilih file
        preview.src = "<?= base_url('assets/img/anggota/' . $displayFoto) ?>";
    }
}
</script>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>