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
if (!$id) redirect('../peminjaman/index.php');

$stmt = mysqli_prepare($connection, "SELECT * FROM `peminjaman` WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$peminjaman = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$peminjaman) redirect('../peminjaman/index.php');

// PERBAIKAN: Ambil detail peminjaman dengan JOIN ke tabel buku untuk mendapatkan judul DAN cover_buku
$detailsStmt = mysqli_prepare($connection, "
    SELECT pd.id, pd.buku_id, pd.qty, b.judul, b.cover_buku
    FROM `peminjaman_detail` pd
    JOIN `buku` b ON pd.buku_id = b.id
    WHERE pd.peminjaman_id = ?
");
mysqli_stmt_bind_param($detailsStmt, "i", $id);
mysqli_stmt_execute($detailsStmt);
$details = mysqli_stmt_get_result($detailsStmt);
?>

<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/topnav.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Detail Peminjaman #<?= $id ?></h3>
    <?php if ($peminjaman['status'] !== 'selesai'): ?>
        <a href="detailadd.php?peminjaman_id=<?= $id ?>" class="btn btn-primary">+ Tambah Item</a>
    <?php endif; ?>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Nama Peminjam:</strong> <?= htmlspecialchars($peminjaman['nama_peminjam']) ?></p>
                <p><strong>NIM:</strong> <?= htmlspecialchars($peminjaman['nim_peminjam']) ?></p>
                <p><strong>Tanggal Pinjam:</strong> <?= $peminjaman['tanggal_pinjam'] ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Status:</strong> <span class="badge badge-<?= $peminjaman['status'] == 'dipinjam' ? 'warning' : 'success' ?>"><?= ucfirst($peminjaman['status']) ?></span></p>
                <p><strong>Batas Kembali:</strong> <?= $peminjaman['tanggal_kembali_seharusnya'] ?></p>
            </div>
        </div>
    </div>
</div>

<?php if (mysqli_num_rows($details) > 0): ?>
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Cover</th> <th>Buku Id</th>
                <th>Judul Buku</th>
                <th>Qty</th>
                <?php if ($peminjaman['status'] !== 'selesai'): ?><th>Aksi</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($detail = mysqli_fetch_assoc($details)): ?>
                <tr>
                    <td>
                        <?php if (!empty($detail['cover_buku']) && file_exists('../assets/img/buku/' . $detail['cover_buku'])): ?>
                            <img src="<?= base_url('assets/img/buku/' . $detail['cover_buku']) ?>" width="60" class="img-thumbnail">
                        <?php else: ?>
                            <img src="<?= base_url('assets/img/buku/default.jpg') ?>" width="60" class="img-thumbnail">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($detail['buku_id']) ?></td>
                    <td><?= htmlspecialchars($detail['judul']) ?></td>
                    <td><?= htmlspecialchars($detail['qty']) ?></td>
                    <?php if ($peminjaman['status'] !== 'selesai'): ?>
                        <td>
                            <a href="detaildelete.php?id=<?= $detail['id'] ?>&master_id=<?= $id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus item ini?')">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-info">Belum ada buku yang ditambahkan ke peminjaman ini.</div>
<?php endif; ?>

<div class="mt-3">
    <a href="index.php" class="btn btn-secondary">Kembali ke Daftar</a>
</div>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>