<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';
requireAuth();

// Hanya admin yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    die('Access Denied');
}

require_once '../config/database.php';

$result = mysqli_query($connection, "SELECT id, username, nama_lengkap, foto_profil, created_at FROM users WHERE role = 'petugas'");
?>

<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/topnav.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Manajemen Petugas</h2>
                <a href="add.php" class="btn btn-add-petugas">+ Tambah Petugas</a>
            </div>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Dibuat Pada</th>
                                <th>Aksi</th>
                                <th>Kartu</th>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <img src="../assets/img/users/<?= htmlspecialchars($row['foto_profil']) ?>" alt="Foto" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                    </td>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                    <td><?= $row['created_at'] ?></td>
                                    <td>
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus petugas ini?')">Hapus</a>
                                    </td>
                                     <td>
                                <a href="cetak_kartu_petugas.php?id=<?= $row['id']; ?>" target="_blank" class="btn btn-info btn-sm">
                                    <i class="fa fa-id-card"></i> Cetak
                                </a>
                            </td>
                            </tr>   
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Belum ada data petugas.</div>
            <?php endif; ?>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>