<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('buku');

require_once '../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) redirect('index.php');

$stmt = mysqli_prepare($connection, "SELECT id, judul, pengarang, stok, harga_buku, cover_buku FROM `buku` WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$buku = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$buku) {
    redirect('index.php');
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul_post = trim($_POST['judul'] ?? '');
    $pengarang_post = trim($_POST['pengarang'] ?? '');
    $stok_post = trim($_POST['stok'] ?? '');
    $harga_buku_post = trim($_POST['harga_buku'] ?? '');

    // --- Upload File Cover Baru ---
    $cover_buku_file = $_FILES['cover_buku'] ?? null;
    $cover_buku_post = $buku['cover_buku']; // Tetap gunakan cover lama jika tidak ada upload baru

    if ($cover_buku_file && $cover_buku_file['error'] == 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($cover_buku_file['name'], PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            // Hapus gambar lama jika bukan default
            $old_file_path = '../assets/img/buku/' . $buku['cover_buku'];
            if (file_exists($old_file_path) && $buku['cover_buku'] !== 'no_cover.jpg') {
                unlink($old_file_path);
            }

            $new_filename = 'buku_' . time() . '.' . $file_ext;
            $upload_dir = '../assets/img/buku/';
            $upload_path = $upload_dir . $new_filename;

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($cover_buku_file['tmp_name'], $upload_path)) {
                $cover_buku_post = $new_filename;
            } else {
                $error = "Gagal mengupload gambar baru.";
            }
        } else {
            $error = "Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.";
        }
    }
    // --- End Upload File Cover Baru ---

    if (empty($judul_post)) {
        $error = "Judul wajib diisi.";
    }

    if (!$error) {
        $stmt = mysqli_prepare($connection, "UPDATE `buku` SET `judul` = ?, `pengarang` = ?, `stok` = ?, `harga_buku` = ?, `cover_buku` = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssidsi", $judul_post, $pengarang_post, $stok_post, $harga_buku_post, $cover_buku_post, $id);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Buku berhasil diperbarui.";
            mysqli_stmt_close($stmt);
            // Refresh data dari DB setelah update
            $stmt = mysqli_prepare($connection, "SELECT id, judul, pengarang, stok, harga_buku, cover_buku FROM `buku` WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $buku = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 2000);
            </script>";
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

            <h2>Edit Buku</h2>
            <?php if ($error): ?>
                <?= showAlert($error, 'danger') ?>
            <?php endif; ?>
            <?php if ($success): ?>
                <?= showAlert($success, 'success') ?>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Judul*</label>
                        <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($buku['judul']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pengarang</label>
                        <input type="text" name="pengarang" class="form-control" value="<?= htmlspecialchars($buku['pengarang']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-control" value="<?= $buku['stok'] ?>" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Buku</label>
                        <input type="number" step="0.01" name="harga_buku" class="form-control" value="<?= htmlspecialchars($buku['harga_buku']) ?>" min="0" placeholder="Contoh: 75000.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cover Buku</label>
                        <input type="file" name="cover_buku" class="form-control" accept="image/*" onchange="previewImage(event)">
                        <small class="text-muted">Format: JPG, PNG, GIF. Kosongkan jika tidak ingin ganti gambar.</small>
                    </div>
                    
                    <!-- Pratinjau Gambar Saat Ini -->
                    <div class="mb-3">
                        <label class="form-label">Gambar Saat Ini</label>
                        <br>
                        <?php
                        $current_img_path = '../assets/img/buku/' . $buku['cover_buku'];
                        $current_img_src = file_exists($current_img_path) ? $current_img_path : '../assets/img/buku/no_cover.jpg';
                        ?>
                        <img id="currentImage" src="<?= $current_img_src ?>" alt="Cover Saat Ini" style="max-width: 200px; max-height: 250px; border: 1px solid #ddd; padding: 5px;">
                    </div>

                    <!-- Pratinjau Gambar Baru -->
                    <div class="mb-3">
                        <label class="form-label">Pratinjau Gambar Baru</label>
                        <br>
                        <img id="preview" src="" alt="Pratinjau Cover Baru" style="max-width: 200px; max-height: 250px; display: none; border: 1px solid #ddd; padding: 5px;">
                    </div>

                <button type="submit" class="btn btn-primary">Perbarui</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>

            <!-- Script untuk Pratinjau -->
            <script>
                function previewImage(event) {
                    const reader = new FileReader();
                    const preview = document.getElementById('preview');
                    const currentImage = document.getElementById('currentImage');

                    reader.onload = function() {
                        preview.src = reader.result;
                        preview.style.display = 'block';
                        // Sembunyikan gambar saat ini jika preview muncul
                        currentImage.style.opacity = '0.5'; // Opsional: beri efek visual
                    }

                    if (event.target.files[0]) {
                        reader.readAsDataURL(event.target.files[0]);
                    } else {
                        preview.style.display = 'none';
                        preview.src = '';
                        currentImage.style.opacity = '1'; // Kembalikan efek
                    }
                }
            </script>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>