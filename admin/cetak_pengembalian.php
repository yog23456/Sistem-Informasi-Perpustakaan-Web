<?php
session_start();
require_once '../config/database.php';
require_once '../lib/functions.php';
require_once '../lib/functions_laporan.php';

requireRoleAccess(['admin', 'petugas']);

// Query yang sama dengan filter di halaman preview
$where = "WHERE 1=1";
if (!empty($_GET['tgl_mulai']) && !empty($_GET['tgl_selesai'])) {
    $where .= " AND pg.tanggal_kembali_aktual BETWEEN '{$_GET['tgl_mulai']}' AND '{$_GET['tgl_selesai']}'";
}
if (!empty($_GET['id_pinjam'])) {
    $where .= " AND pg.peminjaman_id = '{$_GET['id_pinjam']}'";
}

$sql = "SELECT p.id AS peminjaman_id, p.nama_peminjam, p.nim_peminjam, 
               pg.tanggal_kembali_aktual, pg.hari_terlambat, pg.total_denda, 
               pg.status_kondisi, pg.keterangan
        FROM pengembalian pg
        JOIN peminjaman p ON pg.peminjaman_id = p.id
        $where ORDER BY pg.id DESC";

$data = mysqli_query($connection, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengembalian</title>
    <style>
        body { font-family: 'Times New Roman', serif; padding: 40px; }
        .kop { text-align: center; border-bottom: 3px double #000; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; font-size: 12px; }
        th { background: #f2f2f2; text-transform: uppercase; }
        .footer-sign { float: right; width: 200px; text-align: center; margin-top: 40px; }
    </style>
</head>
<body onload="window.print()">
    <div class="kop">
        <h2>SISTEM PERPUSTAKAAN DIGITAL</h2>
        <p>Laporan Data Pengembalian Buku</p>
    </div>

    <p>Dicetak pada: <?= date('d/m/Y H:i') ?></p>

    <table>
        <thead>
            <tr>
                <th>ID Pinjam</th>
                <th>Nama Peminjam</th>
                <th>NIM</th>
                <th>Tgl Kembali</th>
                <th>Terlambat</th>
                <th>Denda</th>
                <th>Kondisi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $grand_total_denda = 0;
            while($row = mysqli_fetch_assoc($data)): 
                $grand_total_denda += $row['total_denda'];
            ?>
            <tr>
                <td align="center"><?= $row['peminjaman_id'] ?></td>
                <td><?= htmlspecialchars($row['nama_peminjam']) ?></td>
                <td><?= htmlspecialchars($row['nim_peminjam']) ?></td>
                <td align="center"><?= $row['tanggal_kembali_aktual'] ?></td>
                <td align="center"><?= $row['hari_terlambat'] ?> Hari</td>
                <td align="right">Rp <?= number_format($row['total_denda'], 0, ',', '.') ?></td>
                <td align="center"><?= $row['status_kondisi'] ?></td>
            </tr>
            <?php endwhile; ?>
            <tr style="font-weight: bold; background: #eee;">
                <td colspan="5" align="right">TOTAL PENDAPATAN DENDA :</td>
                <td align="right">Rp <?= number_format($grand_total_denda, 0, ',', '.') ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer-sign">
        <p>Cirebon, <?= date('d F Y') ?></p>
        <p>Petugas,</p>
        <div style="height: 70px;"></div>
        <p><strong>( <?= $_SESSION['username'] ?> )</strong></p>
    </div>
</body>
</html>