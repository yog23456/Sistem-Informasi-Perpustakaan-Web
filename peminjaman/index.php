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
$result = mysqli_query($connection, "SELECT * FROM `peminjaman` WHERE status = 'dipinjam' ORDER BY id DESC");
?>
<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/topnav.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Daftar Peminjaman</h2>
    <div class="btn-group">
        <a href="../admin/laporan_peminjaman.php" class="btn btn-outline-info me-2">
            <i class="bi bi-printer"></i> Cetak Laporan
        </a>
        <a href="add.php" class="btn btn-add-peminjaman">+ Tambah Peminjaman</a>
    </div>
</div>
<?php if (mysqli_num_rows($result) > 0): ?>
<div class="table-responsive">
<table class="table table-striped">
<thead>
<tr>
<th>ID</th>
<th>Nama Peminjam</th>
<th>Nim Peminjam</th>
<th>Kelas Peminjam</th>
<th>Petugas Id</th>
<th>Tanggal Pinjam</th>
<th>Tanggal Kembali Seharusnya</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['nama_peminjam']) ?></td>
<td><?= htmlspecialchars($row['nim_peminjam']) ?></td>
<td><?= htmlspecialchars($row['kelas_peminjam']) ?></td>
<td><?= htmlspecialchars($row['petugas_id']) ?></td>
<td><?= $row['tanggal_pinjam'] ?></td>
<td><?= $row['tanggal_kembali_seharusnya'] ?></td>
<td><?= htmlspecialchars($row['status']) ?></td>
<td>
<a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Detail</a>
<?php if ($row['status'] !== 'selesai'): ?>
<a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
<a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus peminjaman ini?')">Hapus</a>
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php else: ?>
<div class="alert alert-info">Belum ada data peminjaman.</div>
<?php endif; ?>
<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>