<?php
session_start();
require_once '../config/database.php';
require_once '../lib/functions.php';
require_once '../lib/functions_laporan.php';

requireRoleAccess(['admin', 'petugas']);
$data = getDataLaporan('peminjaman', $_GET);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Peminjaman</title>
    <style>
        body { font-family: 'Times New Roman', serif; margin: 0; padding: 40px; color: #333; }
        .kop-surat { text-align: center; border-bottom: 3px double #000; margin-bottom: 20px; padding-bottom: 10px; }
        .kop-surat h2 { margin: 0; text-transform: uppercase; }
        .kop-surat p { margin: 5px 0; font-style: italic; }
        .info-laporan { margin-bottom: 20px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table, th, td { border: 1px solid #000; padding: 8px; }
        th { background-color: #f2f2f2; text-transform: uppercase; font-size: 12px; }
        td { font-size: 12px; }
        .tanda-tangan { float: right; width: 250px; text-align: center; margin-top: 20px; }
        .tanda-tangan p { margin: 0; }
        .space { height: 80px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="kop-surat">
        <h2>Sistem Informasi Perpustakaan</h2>
        <p>Jl. Contoh No. 123, Kota Cirebon - Jawa Barat</p>
    </div>

    <div class="info-laporan">
        <h3 style="text-align: center; text-decoration: underline;">LAPORAN PEMINJAMAN BUKU</h3>
        <p>Dicetak Oleh: <strong><?= $_SESSION['username'] ?></strong></p>
        <p>Tanggal Cetak: <?= date('d F Y H:i') ?></p>
    </div>

    <table>
        <thead>
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
            <?php while($row = mysqli_fetch_assoc($data)): ?>
            <tr>
                <td align="center"><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['nama_peminjam']) ?></td>
                <td><?= htmlspecialchars($row['nim_peminjam']) ?></td>
                <td align="center"><?= htmlspecialchars($row['kelas_peminjam']) ?></td>
                <td align="center"><?= $row['tanggal_pinjam'] ?></td>
                <td align="center"><?= $row['tanggal_kembali_seharusnya'] ?></td>
                <td align="center"><?= strtoupper($row['status']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="tanda-tangan">
        <p>Cirebon, <?= date('d F Y') ?></p>
        <p>Petugas Perpustakaan,</p>
        <div class="space"></div>
        <p><strong>( <?= $_SESSION['username'] ?> )</strong></p>
    </div>
</body>
</html>