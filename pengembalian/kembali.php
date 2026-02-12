<?php
session_start();
require_once '../../lib/functions.php';
require_once '../../lib/auth.php';
requireAuth();
requireModuleAccess('peminjaman');
require_once '../../config/database.php';

$error = $success = '';

// Ambil daftar peminjaman yang belum selesai
$peminjaman_list = mysqli_query($connection, "SELECT id, nama_peminjam, nim_peminjam, tanggal_pinjam, tanggal_kembali_seharusnya FROM peminjaman WHERE status = 'dipinjam' ORDER BY id ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $peminjaman_id = (int)($_POST['peminjaman_id'] ?? 0);
    $tanggal_kembali_aktual = $_POST['tanggal_kembali_aktual'] ?? date('Y-m-d');
    $denda_kerusakan = floatval($_POST['denda_kerusakan'] ?? 0);
    $status_kondisi = $_POST['status_kondisi'] ?? 'sesuai';
    $keterangan = $_POST['keterangan'] ?? '';

    if (!$peminjaman_id) {
        $error = "Peminjaman harus dipilih.";
    }

    if (!$error) {
        // Redirect ke halaman simpan untuk memproses lebih lanjut
        $params = http_build_query([
            'id' => $peminjaman_id,
            'tanggal_kembali_aktual' => $tanggal_kembali_aktual,
            'denda_kerusakan' => $denda_kerusakan,
            'status_kondisi' => $status_kondisi,
            'keterangan' => $keterangan
        ]);
        header("Location: kembali_save.php?" . $params);
        exit;
    }
}
?>

<?php include '../../views/'.$THEME.'/header.php'; ?>
<?php include '../../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../../views/'.$THEME.'/topnav.php'; ?>
<?php include '../../views/'.$THEME.'/upper_block.php'; ?>

            <h2>Form Pengembalian Buku</h2>
            <?php if ($error): ?>
                <?= showAlert($error, 'danger') ?>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Pilih Peminjaman*</label>
                    <select name="peminjaman_id" class="form-control" required>
                        <option value="">-- Pilih Peminjaman --</option>
                        <?php while ($row = mysqli_fetch_assoc($peminjaman_list)): ?>
                            <option value="<?= $row['id'] ?>">
                                [<?= $row['id'] ?>] <?= htmlspecialchars($row['nama_peminjam']) ?> (<?= $row['nim_peminjam'] ?>) - <?= $row['tanggal_pinjam'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Kembali Aktual</label>
                    <input type="date" name="tanggal_kembali_aktual" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Denda Kerusakan</label>
                    <input type="number" name="denda_kerusakan" class="form-control" step="0.01" value="0" min="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">Status Kondisi</label>
                    <select name="status_kondisi" class="form-control" required onchange="toggleDendaInput()">
                        <option value="sesuai">Sesuai (Tepat Waktu)</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="rusak">Rusak</option>
                        <option value="hilang">Hilang</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Proses Pengembalian</button>
                <a href="../peminjaman/index.php" class="btn btn-secondary">Batal</a>
            </form>

            <script>
            function toggleDendaInput() {
                const status = document.querySelector('select[name="status_kondisi"]').value;
                const dendaInput = document.querySelector('input[name="denda_kerusakan"]');
                if (status === 'rusak' || status === 'hilang') {
                    dendaInput.disabled = false;
                } else {
                    dendaInput.disabled = true;
                }
            }
            // Panggil saat halaman dimuat
            document.addEventListener('DOMContentLoaded', toggleDendaInput);
            </script>

<?php include '../../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../../views/'.$THEME.'/footer.php'; ?>