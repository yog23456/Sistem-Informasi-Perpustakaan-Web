<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('anggota');

require_once '../config/database.php';

$result = mysqli_query($connection, "SELECT * FROM `anggota` ORDER BY id DESC");
?>

<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/topnav.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Daftar Anggota</h2>
                <a href="add.php" class="btn btn-primary">+ Tambah Anggota</a>
                
            </div>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Nim</th>
                                <th>Kelas</th>
                                <th>Aksi</th>
                                <th>Kartu</th>
                                <th>foto</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['nim']) ?></td>
                                    <td><?= htmlspecialchars($row['kelas']) ?></td>
                                    <td>
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus anggota ini?')">Hapus</a>
                                    </td>
                                    <td>
                                        <a href="cetak_kartu.php?id=<?= $row['id']; ?>" target="_blank" class="btn btn-info btn-sm">
                                            <i class="fa fa-id-card"></i> Cetak
                                        </a>
                                    </td>
                                    <td>
                                        <?php 
                                        $foto_path = '../assets/img/anggota/' . $row['foto'];
                                        // Cek jika file foto ada, jika tidak pakai default.png
                                        if (!empty($row['foto']) && file_exists($foto_path)): ?>
                                            <img src="<?= base_url('assets/img/anggota/' . $row['foto']) ?>" width="50" height="50" class="rounded-circle shadow-sm">
                                        <?php else: ?>
                                            <img src="<?= base_url('assets/img/anggota/default.png') ?>" width="50" height="50" class="rounded-circle shadow-sm">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Belum ada data anggota.</div>
            <?php endif; ?>
            


<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>
