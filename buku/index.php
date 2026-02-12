<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('buku');

require_once '../config/database.php';

$result = mysqli_query($connection, "SELECT * FROM `buku` ORDER BY id DESC");

// Dapatkan role user
$user_role = $_SESSION['role'] ?? 'guest';
?>

<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/topnav.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Daftar Buku</h2>
                <?php if ($user_role === 'admin'): ?>
                    <a href="add.php" class="btn btn-add-book">+ Tambah Buku</a>
                <?php endif; ?>
            </div>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cover</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Stok</th>
                                <th>Harga Buku</th>
                                <?php if ($user_role === 'admin'): ?>
                                    <th>Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td>
                                        <?php
                                        $cover_path = '../assets/img/buku/' . $row['cover_buku'];
                                        $img_src = file_exists($cover_path) ? $cover_path : '../assets/img/buku/no_cover.jpg';
                                        ?>
                                        <img src="<?= $img_src ?>" alt="Cover Buku" style="width: 60px; height: 80px; object-fit: cover;" onerror="this.onerror=null; this.src='../assets/img/buku/no_cover.jpg';">
                                    </td>
                                    <td><?= htmlspecialchars($row['judul']) ?></td>
                                    <td><?= htmlspecialchars($row['pengarang']) ?></td>
                                    <td><?= htmlspecialchars($row['stok']) ?></td>
                                    <td><?= htmlspecialchars($row['harga_buku']) ?></td>
                                    <?php if ($user_role === 'admin'): ?>
                                        <td>
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus buku ini?')">Hapus</a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Belum ada data buku.</div>
            <?php endif; ?>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>