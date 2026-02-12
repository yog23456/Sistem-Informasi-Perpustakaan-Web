<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('buku');

require_once '../config/database.php';

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul_post = trim($_POST['judul'] ?? '');
    $pengarang_post = trim($_POST['pengarang'] ?? '');
    $stok_post = trim($_POST['stok'] ?? '');
    $harga_buku_post = trim($_POST['harga_buku'] ?? '');

    // --- Upload File Cover ---
    $cover_buku_file = $_FILES['cover_buku'] ?? null;
    $cover_buku_post = 'no_cover.jpg'; // Default jika tidak ada upload

    if ($cover_buku_file && $cover_buku_file['error'] == 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($cover_buku_file['name'], PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = 'buku_' . time() . '.' . $file_ext;
            $upload_dir = '../assets/img/buku/';
            $upload_path = $upload_dir . $new_filename;

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($cover_buku_file['tmp_name'], $upload_path)) {
                $cover_buku_post = $new_filename;
            } else {
                $error = "Gagal mengupload gambar.";
            }
        } else {
            $error = "Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.";
        }
    }
    // --- End Upload File Cover ---

    if (empty($judul_post)) {
        $error = "Judul wajib diisi.";
    }

    if (!$error) {
        $stmt = mysqli_prepare($connection, "INSERT INTO `buku` (judul, pengarang, stok, harga_buku, cover_buku) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssids", $judul_post, $pengarang_post, $stok_post, $harga_buku_post, $cover_buku_post);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Buku berhasil ditambahkan.";
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
?>

<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/topnav.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>

            <h2>Tambah Buku</h2>
            <?php if ($error): ?>
                <?= showAlert($error, 'danger') ?>
            <?php endif; ?>
            <?php if ($success): ?>
                <?= showAlert($success, 'success') ?>
                <a href="index.php" class="btn btn-secondary mt-3">Kembali ke Daftar</a>
            <?php else: ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Judul*</label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pengarang</label>
                        <input type="text" name="pengarang" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-control" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Buku</label>
                        <input type="number" step="0.01" name="harga_buku" class="form-control" min="0" placeholder="Contoh: 75000.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cover Buku</label>
                        <input type="file" name="cover_buku" class="form-control" accept="image/*" onchange="previewImage(event)">
                        <small class="text-muted">Format: JPG, PNG, GIF. Kosongkan jika tidak ingin upload.</small>
                    </div>
                    
                    <!-- Pratinjau Gambar -->
                    <div class="mb-3">
                        <label class="form-label">Pratinjau Gambar</label>
                        <br>
                        <img id="preview" src="" alt="Pratinjau Cover" style="max-width: 200px; max-height: 250px; display: none; border: 1px solid #ddd; padding: 5px;">
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>

                <!-- Script untuk Pratinjau -->
                <script>
                    function previewImage(event) {
                        const reader = new FileReader();
                        const preview = document.getElementById('preview');

                        reader.onload = function() {
                            preview.src = reader.result;
                            preview.style.display = 'block';
                        }

                        if (event.target.files[0]) {
                            reader.readAsDataURL(event.target.files[0]);
                        } else {
                            preview.style.display = 'none';
                            preview.src = '';
                        }
                    }
                </script>
            <?php endif; ?>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>