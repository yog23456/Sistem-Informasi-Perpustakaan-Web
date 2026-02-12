<?php
session_start();
require_once '../../lib/functions.php';
require_once '../../lib/auth.php';
requireAuth();
requireModuleAccess('peminjaman');
require_once '../../config/database.php';

$error = $success = '';

$peminjaman_id = (int)($_GET['id'] ?? 0);
$tanggal_kembali_aktual = $_GET['tanggal_kembali_aktual'] ?? date('Y-m-d');
$denda_kerusakan = floatval($_GET['denda_kerusakan'] ?? 0);
$status_kondisi = $_GET['status_kondisi'] ?? 'sesuai';
$keterangan = $_GET['keterangan'] ?? '';

if (!$peminjaman_id) {
    $error = "ID Peminjaman tidak valid.";
}

if (!$error) {
    // Ambil data peminjaman untuk menghitung hari terlambat
    $stmt = mysqli_prepare($connection, "SELECT tanggal_kembali_seharusnya FROM peminjaman WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $peminjaman_id);
    mysqli_stmt_execute($stmt);
    $peminjaman = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$peminjaman) {
        $error = "Data peminjaman tidak ditemukan.";
    }
}

if (!$error) {
    $tanggal_seharusnya = $peminjaman['tanggal_kembali_seharusnya'];

    // Hitung keterlambatan
    $seharusnya = new DateTime($tanggal_seharusnya);
    $aktual = new DateTime($tanggal_kembali_aktual);
    $diff = $aktual->diff($seharusnya);
    $hari_terlambat = 0;

    if ($aktual > $seharusnya) {
        $hari_terlambat = $diff->days;
    }

    // Hitung total denda
    $denda_perhari = 5000; // Default
    $total_denda = ($hari_terlambat * $denda_perhari) + $denda_kerusakan;

    // Simpan ke tabel pengembalian
    $stmt = mysqli_prepare($connection, "
        INSERT INTO pengembalian (
            peminjaman_id, tanggal_kembali_aktual, hari_terlambat, 
            denda_perhari, denda_kerusakan, total_denda, status_kondisi, keterangan
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, "isiddsss", 
        $peminjaman_id, $tanggal_kembali_aktual, $hari_terlambat,
        $denda_perhari, $denda_kerusakan, $total_denda, $status_kondisi, $keterangan
    );

    if (mysqli_stmt_execute($stmt)) {
        $success = "Pengembalian berhasil diproses. Stok buku otomatis diperbarui.";
    } else {
        $error = "Gagal menyimpan data pengembalian: " . mysqli_error($connection);
    }
    mysqli_stmt_close($stmt);
}
?>

<?php include '../../views/'.$THEME.'/header.php'; ?>
<?php include '../../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../../views/'.$THEME.'/topnav.php'; ?>
<?php include '../../views/'.$THEME.'/upper_block.php'; ?>

            <h2>Proses Pengembalian</h2>
            <?php if ($error): ?>
                <?= showAlert($error, 'danger') ?>
                <a href="kembali.php" class="btn btn-primary">Kembali ke Form</a>
            <?php endif; ?>
            <?php if ($success): ?>
                <?= showAlert($success, 'success') ?>
                <a href="../peminjaman/index.php" class="btn btn-primary">Lihat Daftar Peminjaman</a>
            <?php endif; ?>

<?php include '../../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../../views/'.$THEME.'/footer.php'; ?>