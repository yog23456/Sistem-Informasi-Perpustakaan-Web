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

// Ambil data user dengan role petugas
$queryPetugas = mysqli_query($connection, "SELECT id, nama_lengkap FROM users WHERE role = 'petugas' ORDER BY nama_lengkap ASC");

// PERBAIKAN: Ambil data dari folder anggota yang baru kamu buat
$queryAnggota = mysqli_query($connection, "SELECT * FROM anggota ORDER BY nama ASC");

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token.');
    }

    $anggota_id_post = trim($_POST['anggota_id'] ?? ''); 
    $nama_peminjam_post = trim($_POST['nama_peminjam_hidden'] ?? '');
    $nim_peminjam_post = trim($_POST['nim_peminjam'] ?? '');
    $kelas_peminjam_post = trim($_POST['kelas_peminjam'] ?? '');
    $petugas_id_post = trim($_POST['petugas_id'] ?? '');
    $tanggal_pinjam_post = trim($_POST['tanggal_pinjam'] ?? '');
    $tanggal_kembali_seharusnya_post = trim($_POST['tanggal_kembali_seharusnya'] ?? '');
    $status_val = 'dipinjam';

    if (empty($nama_peminjam_post) || empty($nim_peminjam_post) || empty($petugas_id_post) || empty($tanggal_pinjam_post) || empty($tanggal_kembali_seharusnya_post)) {
        $error = "Semua field wajib diisi.";
    }

    if (!$error) {
        $stmt = mysqli_prepare($connection, "INSERT INTO `peminjaman` (`anggota_id`,`nama_peminjam`, `nim_peminjam`, `kelas_peminjam`, `petugas_id`, `tanggal_pinjam`, `tanggal_kembali_seharusnya`, `status`) VALUES (?,   ?, ?, ?, ?, ?, ?, ?)");
        
        mysqli_stmt_bind_param($stmt, "isssisss",$anggota_id_post, $nama_peminjam_post, $nim_peminjam_post, $kelas_peminjam_post, $petugas_id_post, $tanggal_pinjam_post, $tanggal_kembali_seharusnya_post, $status_val);
        
        if (mysqli_stmt_execute($stmt)) {
            $new_id = mysqli_insert_id($connection);
            header("Location: detail.php?id=$new_id");
            exit();
        } else {
            $error = "Gagal menyimpan peminjaman.";
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

<h2>Tambah Peminjaman</h2>

<?php if ($error): ?>
    <?= showAlert($error, 'danger') ?>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    
    <div class="mb-3">
        <label class="form-label">Nama Peminjam*</label>
        <select name="anggota_id" id="pilih_anggota" class="form-control" required onchange="isiDataAnggota()">
            <option value="">-- Pilih Anggota --</option>
            <?php while ($rowAg = mysqli_fetch_assoc($queryAnggota)): ?>
                <option value="<?= $rowAg['id'] ?>" 
                        data-nama="<?= htmlspecialchars($rowAg['nama']) ?>"
                        data-nim="<?= htmlspecialchars($rowAg['nim']) ?>" 
                        data-kelas="<?= htmlspecialchars($rowAg['kelas']) ?>">
                    <?= htmlspecialchars($rowAg['nama']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <input type="hidden" name="nama_peminjam_hidden" id="nama_peminjam_hidden">
    </div>

    <div class="mb-3">
        <label class="form-label">Nim Peminjam*</label>
        <input type="text" name="nim_peminjam" id="nim_peminjam" class="form-control" readonly required>
    </div>

    <div class="mb-3">
        <label class="form-label">Kelas Peminjam</label>
        <input type="text" name="kelas_peminjam" id="kelas_peminjam" class="form-control" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label">Petugas*</label>
        <select name="petugas_id" class="form-select" required>
            <option value="">-- Pilih Petugas --</option>
            <?php mysqli_data_seek($queryPetugas, 0); while ($row = mysqli_fetch_assoc($queryPetugas)): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama_lengkap']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Tanggal Pinjam*</label>
        <input type="date" name="tanggal_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Tanggal Kembali Seharusnya*</label>
        <input type="date" name="tanggal_kembali_seharusnya" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">Simpan Peminjaman</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>

<script>
// Fungsi untuk mengisi NIM dan Kelas secara otomatis saat Nama dipilih
function isiDataAnggota() {
    const select = document.getElementById('pilih_anggota');
    const selectedOption = select.options[select.selectedIndex];
    
    // Ambil semua data dari atribut 'data-'
    const nama = selectedOption.getAttribute('data-nama');
    const nim = selectedOption.getAttribute('data-nim');
    const kelas = selectedOption.getAttribute('data-kelas');
    
    // Isi ke input masing-masing
    document.getElementById('nama_peminjam_hidden').value = nama || "";
    document.getElementById('nim_peminjam').value = nim || "";
    document.getElementById('kelas_peminjam').value = kelas || "";
}
</script>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>