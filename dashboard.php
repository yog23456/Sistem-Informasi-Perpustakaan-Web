<?php
session_start();
require_once 'lib/functions.php';
require_once 'lib/auth.php';
requireAuth();
require_once 'config/database.php';

// Query untuk menghitung statistik
$stats = [];

// Total jumlah judul buku
$result = mysqli_query($connection, "SELECT COUNT(*) as total FROM buku");
$stats['total_judul_buku'] = (int) mysqli_fetch_assoc($result)['total'];

// Total jumlah Buku (stok keseluruhan)
$result = mysqli_query($connection, "SELECT SUM(stok) as total FROM buku");
$fetched = mysqli_fetch_assoc($result);
$stats['total_stok_buku'] = (int) $fetched['total'];

// Total Users
$result = mysqli_query($connection, "SELECT COUNT(*) as total FROM users");
$stats['total_users'] = (int) mysqli_fetch_assoc($result)['total'];

// Total Buku yang Sedang Dipinjam
$result = mysqli_query($connection, "
    SELECT SUM(pd.qty) as total
    FROM peminjaman_detail pd
    JOIN peminjaman p ON pd.peminjaman_id = p.id
    WHERE p.status = 'dipinjam'
");
$fetched = mysqli_fetch_assoc($result);
$stats['total_buku_dipinjam'] = (int) $fetched['total'];

// Total uang hasil Denda
$result = mysqli_query($connection, "SELECT SUM(total_denda) as total FROM pengembalian");
$fetched = mysqli_fetch_assoc($result);
$stats['total_denda'] = (float) $fetched['total'];

// Data grafik peminjaman per bulan
$monthly_peminjaman = [];
$months = [];
for ($i = 11; $i >= 0; $i--) {
    $date = new DateTime();
    $date->modify("-$i months");
    $month = $date->format('Y-m');
    $months[] = $date->format('M Y');
    $stmt = mysqli_prepare($connection, "SELECT COUNT(*) as total FROM peminjaman WHERE DATE_FORMAT(tanggal_pinjam, '%Y-%m') = ?");
    mysqli_stmt_bind_param($stmt, "s", $month);
    mysqli_stmt_execute($stmt);
    $res = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    $monthly_peminjaman[] = (int) $res['total'];
    mysqli_stmt_close($stmt);
}

// Data grafik status pengembalian untuk bulan ini saja
$current_month = date('Y-m');
$labels = ['Sesuai', 'Terlambat', 'Rusak', 'Hilang'];
$data_status = [];

foreach (['sesuai', 'terlambat', 'rusak', 'hilang'] as $status) {
    $stmt = mysqli_prepare($connection, "SELECT COUNT(*) as total FROM pengembalian WHERE DATE_FORMAT(tanggal_kembali_aktual, '%Y-%m') = ? AND status_kondisi = ?");
    mysqli_stmt_bind_param($stmt, "ss", $current_month, $status);
    mysqli_stmt_execute($stmt);
    $res = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    $data_status[] = (int) $res['total'];
    mysqli_stmt_close($stmt);
}

?>

<?php include 'views/'.$THEME.'/header.php'; ?>
<?php include 'views/'.$THEME.'/sidebar.php'; ?>
<?php include 'views/'.$THEME.'/topnav.php'; ?>
<?php include 'views/'.$THEME.'/upper_block.php'; ?>

            <div class="container-fluid">
                <h2 class="my-4 text-dark-alt">Dashboard Perpustakaan</h2>

                <!-- Cards Statistik -->
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Judul Buku</h5>
                                <h3><?= $stats['total_judul_buku'] ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title">Total Stok Buku</h5>
                                <h3><?= $stats['total_stok_buku'] ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <h3><?= $stats['total_users'] ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Buku Dipinjam</h5>
                                <h3><?= $stats['total_buku_dipinjam'] ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-danger">
                            <div class="card-body">
                                <h5 class="card-title">Total Denda</h5>
                                <h3>Rp <?= number_format($stats['total_denda'], 0, ',', '.') ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grafik -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                            <div class="card-body">
                                <h5 class="card-title text-dark-alt">Jumlah Peminjaman per Bulan</h5>
                                <canvas id="chartPeminjaman"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                            <div class="card-body">
                                <h5 class="card-title text-dark-alt">Status Pengembalian Bulan Ini</h5>
                                <canvas id="chartStatusBulanIni"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
            // Grafik Peminjaman
            const ctx1 = document.getElementById('chartPeminjaman').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: <?= json_encode($months) ?>,
                    datasets: [{
                        label: 'Jumlah Peminjaman',
                        data: <?= json_encode($monthly_peminjaman) ?>,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Grafik Status Pengembalian Bulan Ini (Histogram)
            const ctx2 = document.getElementById('chartStatusBulanIni').getContext('2d');
            new Chart(ctx2, {
                type: 'bar', // Histogram adalah bar chart
                data: {
                    labels: <?= json_encode($labels) ?>,
                    datasets: [{
                        label: 'Jumlah',
                        data: <?= json_encode($data_status) ?>,
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.7)', // Sesuai - Hijau
                            'rgba(255, 193, 7, 0.7)', // Terlambat - Kuning
                            'rgba(220, 53, 69, 0.7)', // Rusak - Merah
                            'rgba(108, 117, 125, 0.7)' // Hilang - Abu-abu
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(220, 53, 69, 1)',
                            'rgba(108, 117, 125, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            </script>

<?php include 'views/'.$THEME.'/lower_block.php'; ?>
<?php include 'views/'.$THEME.'/footer.php'; ?>