<?php
session_start();
require_once '../config/database.php';
require_once '../lib/auth.php';
require_once '../lib/functions.php';
require_once '../lib/functions_laporan.php';

requireRoleAccess(['admin', 'petugas']);

$where_clauses = ["1=1"];

if (!empty($_GET['tgl_mulai']) && !empty($_GET['tgl_selesai'])) {
    $mulai = mysqli_real_escape_string($connection, $_GET['tgl_mulai']);
    $selesai = mysqli_real_escape_string($connection, $_GET['tgl_selesai']);
    $where_clauses[] = "pg.tanggal_kembali_aktual BETWEEN '$mulai' AND '$selesai'";
}

if (!empty($_GET['id_pinjam'])) {
    $id_pinjam = mysqli_real_escape_string($connection, $_GET['id_pinjam']);
    $where_clauses[] = "pg.peminjaman_id = '$id_pinjam'";
}

// Tambahkan filter Bulan & Tahun ke query manual
if (!empty($_GET['bulan'])) {
    $bulan = mysqli_real_escape_string($connection, $_GET['bulan']);
    $where_clauses[] = "MONTH(pg.tanggal_kembali_aktual) = '$bulan'";
}

if (!empty($_GET['tahun'])) {
    $tahun = mysqli_real_escape_string($connection, $_GET['tahun']);
    $where_clauses[] = "YEAR(pg.tanggal_kembali_aktual) = '$tahun'";
}

$where = "WHERE " . implode(" AND ", $where_clauses);

$sql = "SELECT p.id AS peminjaman_id, p.nama_peminjam, p.nim_peminjam, 
               pg.tanggal_kembali_aktual, pg.hari_terlambat, pg.total_denda, 
               pg.status_kondisi, pg.keterangan
        FROM pengembalian pg
        JOIN peminjaman p ON pg.peminjaman_id = p.id
        $where ORDER BY pg.id DESC";

$data = mysqli_query($connection, $sql);

include '../views/'.$THEME.'/header.php';
include '../views/'.$THEME.'/sidebar.php';
include '../views/'.$THEME.'/topnav.php';
include '../views/'.$THEME.'/upper_block.php';
?>

<div class="container-fluid">
    <h2 class="mb-4">Laporan Pengembalian Buku</h2>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-success text-white">
            <h6 class="m-0 font-weight-bold">Filter & Cetak Pengembalian</h6>
        </div>
        <div class="card-body">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">ID Peminjaman</label>
                        <input type="number" name="id_pinjam" class="form-control" value="<?= $_GET['id_pinjam'] ?? '' ?>" placeholder="Cari ID...">
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
                    <a href="cetak_pengembalian.php?<?= http_build_query($_GET) ?>" target="_blank" class="btn btn-success"><i class="bi bi-printer"></i> Cetak Laporan</a>
                    <a href="laporan_pengembalian.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Pinjam</th>
                            <th>Nama Peminjam</th>
                            <th>Tgl Kembali</th>
                            <th>Terlambat</th>
                            <th>Denda</th>
                            <th>Kondisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($data) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td><?= $row['peminjaman_id'] ?></td>
                                <td><?= htmlspecialchars($row['nama_peminjam']) ?></td>
                                <td><?= $row['tanggal_kembali_aktual'] ?></td>
                                <td><?= $row['hari_terlambat'] ?> Hari</td>
                                <td>Rp <?= number_format($row['total_denda'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge <?= ($row['status_kondisi'] == 'Baik') ? 'bg-info' : 'bg-danger' ?>">
                                        <?= $row['status_kondisi'] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">Belum ada data pengembalian.</td></tr>
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