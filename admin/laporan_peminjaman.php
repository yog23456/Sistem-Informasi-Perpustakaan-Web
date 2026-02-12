<?php
session_start();
require_once '../config/database.php';
require_once '../lib/auth.php';
require_once '../lib/functions.php';
require_once '../lib/functions_laporan.php';

requireRoleAccess(['admin', 'petugas']);

// Ambil data berdasarkan filter jika ada, jika tidak ambil semua
$data = getDataLaporan('peminjaman', $_GET);

include '../views/'.$THEME.'/header.php';
include '../views/'.$THEME.'/sidebar.php';
include '../views/'.$THEME.'/topnav.php';
include '../views/'.$THEME.'/upper_block.php';
?>

<div class="container-fluid">
    <h2 class="mb-4">Laporan Peminjaman Buku</h2>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Filter & Cetak</h6>
        </div>
        <div class="card-body">
            <form action="" method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Berdasarkan ID</label>
                        <input type="number" name="id" class="form-control" value="<?= $_GET['id'] ?? '' ?>" placeholder="ID...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" class="form-control" value="<?= $_GET['tgl_mulai'] ?? '' ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" class="form-control" value="<?= $_GET['tgl_selesai'] ?? '' ?>">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Bulan</label>
                        <select name="bulan" class="form-control">
                            <option value="">-- Semua Bulan --</option>
                            <?php
                            $months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                            foreach ($months as $k => $v) {
                                $s = (isset($_GET['bulan']) && $_GET['bulan'] == ($k+1)) ? 'selected' : '';
                                echo "<option value='".($k+1)."' $s>$v</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="<?= $_GET['tahun'] ?? date('Y') ?>">
                    </div>
                </div>
                <div class="mt-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Tampilkan Data</button>
                    <a href="cetak_peminjaman.php?<?= http_build_query($_GET) ?>" target="_blank" class="btn btn-success"><i class="bi bi-printer"></i> Cetak Hasil Filter</a>
                    <a href="laporan_peminjaman.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nama Peminjam</th>
                            <th>NIM</th>
                            <th>Kelas</th>
                            <th>Tgl Pinjam</th>
                            <th>Batas Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($data) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['nama_peminjam']) ?></td>
                                <td><?= htmlspecialchars($row['nim_peminjam']) ?></td>
                                <td><?= htmlspecialchars($row['kelas_peminjam']) ?></td>
                                <td><?= $row['tanggal_pinjam'] ?></td>
                                <td><?= $row['tanggal_kembali_seharusnya'] ?></td>
                                <td><span class="badge <?= ($row['status'] == 'selesai') ? 'bg-success' : 'bg-warning' ?>"><?= $row['status'] ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center">Data tidak ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
include '../views/'.$THEME.'/lower_block.php';
include '../views/'.$THEME.'/footer.php'; 
?>