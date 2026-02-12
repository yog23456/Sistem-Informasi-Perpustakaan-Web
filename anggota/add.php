<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('anggota');

require_once '../config/database.php';

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_post = trim($_POST['nama'] ?? '');
    $nim_post = trim($_POST['nim'] ?? '');
    $kelas_post = trim($_POST['kelas'] ?? '');
    
    // Logika Upload Foto ke folder assets/img/anggota/
    $foto_post = 'default.png'; 
    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        // Penamaan file: timestamp_NIM.ekstensi agar unik
        $foto_post = time() . "_" . $nim_post . "." . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], "../assets/img/anggota/" . $foto_post);
    }

    if (empty($nama_post) || empty($nim_post) || empty($kelas_post)) {
        $error = "Semua field wajib diisi.";
    }

    if (!$error) {
        // Pastikan kolom 'foto' sudah ada di tabel 'anggota' database kamu
        $stmt = mysqli_prepare($connection, "INSERT INTO `anggota` (nama, nim, kelas, foto) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $nama_post, $nim_post, $kelas_post, $foto_post);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Anggota berhasil ditambahkan.";
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 1500);
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

<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>Tambah Anggota Baru</h2>
        </div>
    </div>
</div>

<div class="white_shd full margin_bottom_30">
    <div class="padding_infor_info">
        <?php if ($error) echo showAlert($error, 'danger'); ?>
        <?php if ($success) echo showAlert($success, 'success'); ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Nama Lengkap*</label>
                <input type="text" name="nama" class="form-control" placeholder="Masukkan nama anggota" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">NIM (Nomor Induk Mahasiswa)*</label>
                <input type="text" name="nim" class="form-control" placeholder="Masukkan NIM" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Kelas / Jurusan*</label>
                <input type="text" name="kelas" class="form-control" placeholder="Contoh: TI-2A" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Foto Profil Anggota</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
                <small class="text-muted">Gunakan file JPG atau PNG. Jika kosong, sistem akan menggunakan foto default.</small>
            </div>
            
            <hr>
            <div class="d-flex justify-content-start">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fa fa-save"></i> Simpan Anggota
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>