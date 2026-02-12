<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';
requireAuth();
requireModuleAccess('peminjaman');
require_once '../config/database.php';

$error = $success = '';

// Ambil daftar peminjaman yang belum selesai (dan belum dikembalikan) untuk form
$peminjaman_list = mysqli_query($connection, "
    SELECT id, nama_peminjam, nim_peminjam, tanggal_pinjam, tanggal_kembali_seharusnya 
    FROM peminjaman 
    WHERE status = 'dipinjam' 
    AND id NOT IN (SELECT peminjaman_id FROM pengembalian WHERE peminjaman_id IS NOT NULL)
    ORDER BY id ASC
");

// Ambil daftar riwayat pengembalian
$riwayat_pengembalian = mysqli_query($connection, "
    SELECT 
        p.id AS peminjaman_id,
        p.nama_peminjam,
        p.nim_peminjam,
        pg.tanggal_kembali_aktual,
        pg.hari_terlambat,
        pg.total_denda,
        pg.status_kondisi,
        pg.keterangan
    FROM pengembalian pg
    JOIN peminjaman p ON pg.peminjaman_id = p.id
    ORDER BY pg.id DESC
");

// Proses AJAX untuk mengambil detail peminjaman (harga total)
// Proses AJAX untuk mengambil detail peminjaman (harga total)
if (isset($_GET['action']) && $_GET['action'] === 'get_peminjaman_details') {
    ob_clean(); // Hapus output apapun yang tidak sengaja keluar sebelumnya
    header('Content-Type: application/json');
    $peminjaman_id = (int)($_GET['peminjaman_id'] ?? 0);

    if ($peminjaman_id > 0) {
        // Query yang lebih aman dan ringkas
        $sql = "SELECT p.tanggal_kembali_seharusnya, 
                (SELECT SUM(b.harga_buku * pd.qty) FROM peminjaman_detail pd JOIN buku b ON pd.buku_id = b.id WHERE pd.peminjaman_id = p.id) AS total_nilai 
                FROM peminjaman p WHERE p.id = ?";
        
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "i", $peminjaman_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        echo json_encode([
            'tanggal_seharusnya' => $result['tanggal_kembali_seharusnya'] ?? null,
            'total_nilai' => (float)($result['total_nilai'] ?? 0)
        ]);
    } else {
        echo json_encode(['tanggal_seharusnya' => null, 'total_nilai' => 0]);
    }
    exit; // Wajib exit agar kode HTML di bawahnya tidak ikut terkirim
}

// Proses submit form pengembalian
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $peminjaman_id = (int)($_POST['peminjaman_id'] ?? 0);
    $tanggal_kembali_aktual = $_POST['tanggal_kembali_aktual'] ?? date('Y-m-d');
    $denda_kerusakan = floatval($_POST['denda_kerusakan'] ?? 0);
    $status_kondisi = $_POST['status_kondisi'] ?? 'sesuai';
    $keterangan = $_POST['keterangan'] ?? '';

    if (!$peminjaman_id) {
        $error = "Peminjaman harus dipilih.";
    }

    if (!$error) {
        // Ambil data peminjaman untuk menghitung hari terlambat
        $stmt = mysqli_prepare($connection, "SELECT tanggal_kembali_seharusnya FROM peminjaman WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $peminjaman_id);
        mysqli_stmt_execute($stmt);
        $peminjaman = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$peminjaman) {
            $error = "Data peminjaman tidak ditemukan.";
        }
    }

    if (!$error) {
        $tanggal_seharusnya = $peminjaman['tanggal_kembali_seharusnya'];

        // Hitung keterlambatan
        $seharusnya = new DateTime($tanggal_seharusnya);
        $aktual = new DateTime($tanggal_kembali_aktual);
        $diff = $aktual->diff($seharusnya);
        $hari_terlambat = 0;

        if ($aktual > $seharusnya) {
            $hari_terlambat = $diff->days;
        }

        // Hitung total denda berdasarkan status_kondisi
        // Hitung total denda berdasarkan status_kondisi
        $denda_perhari = 5000;
        $total_denda = 0;

        switch ($status_kondisi) {
            case 'sesuai':
                $total_denda = $denda_kerusakan;
                break;
            case 'terlambat':
                // Rumus: (Hari x 5000) + Denda Manual
                $total_denda = ($hari_terlambat * $denda_perhari) + $denda_kerusakan;
                break;
            case 'rusak':
                $harga_buku_stmt = mysqli_prepare($connection, "SELECT SUM(b.harga_buku * pd.qty) AS total_nilai FROM peminjaman_detail pd JOIN buku b ON pd.buku_id = b.id WHERE pd.peminjaman_id = ?");
                mysqli_stmt_bind_param($harga_buku_stmt, "i", $peminjaman_id);
                mysqli_stmt_execute($harga_buku_stmt);
                $harga_row = mysqli_fetch_assoc(mysqli_stmt_get_result($harga_buku_stmt));
                mysqli_stmt_close($harga_buku_stmt);

                $nilai_buku = $harga_row['total_nilai'] ?? 0;
                // Rumus: (10% Harga Buku) + Denda Manual
                $total_denda = ($nilai_buku * 0.10) + $denda_kerusakan;
                break;
            case 'hilang':
                $harga_buku_stmt = mysqli_prepare($connection, "SELECT SUM(b.harga_buku * pd.qty) AS total_nilai FROM peminjaman_detail pd JOIN buku b ON pd.buku_id = b.id WHERE pd.peminjaman_id = ?");
                mysqli_stmt_bind_param($harga_buku_stmt, "i", $peminjaman_id);
                mysqli_stmt_execute($harga_buku_stmt);
                $harga_row = mysqli_fetch_assoc(mysqli_stmt_get_result($harga_buku_stmt));
                mysqli_stmt_close($harga_buku_stmt);

                $nilai_buku = $harga_row['total_nilai'] ?? 0;
                // Rumus: Harga Buku + Denda Manual
                $total_denda = $nilai_buku + $denda_kerusakan;
                break;
            default:
                $total_denda = $denda_kerusakan;
        }

        // Simpan ke tabel pengembalian
        $stmt = mysqli_prepare($connection, "
            INSERT INTO pengembalian (
                peminjaman_id, tanggal_kembali_aktual, hari_terlambat, 
                denda_perhari, denda_kerusakan, total_denda, status_kondisi, keterangan
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        mysqli_stmt_bind_param($stmt, "isiddsss", 
            $peminjaman_id, $tanggal_kembali_aktual, $hari_terlambat,
            $denda_perhari, $denda_kerusakan, $total_denda, $status_kondisi, $keterangan
        );

        if (mysqli_stmt_execute($stmt)) {
            // -- 1. UPDATE STATUS PEMINJAMAN MENJADI SELESAI --
            $update_status_stmt = mysqli_prepare($connection, "UPDATE peminjaman SET status = 'selesai' WHERE id = ?");
            mysqli_stmt_bind_param($update_status_stmt, "i", $peminjaman_id);
            mysqli_stmt_execute($update_status_stmt);
            mysqli_stmt_close($update_status_stmt);

            // -- 2. LOGIKA OTOMATISASI STOK BALIK (KECUALI HILANG) --
            
            // ----------------------------------------------

            $success = "Pengembalian berhasil diproses. " . ($status_kondisi === 'hilang' ? "Stok tidak bertambah karena status Hilang." : "Stok buku otomatis diperbarui.");
            
            $riwayat_pengembalian = mysqli_query($connection, "
                SELECT 
                    p.id AS peminjaman_id,
                    p.nama_peminjam,
                    p.nim_peminjam,
                    pg.tanggal_kembali_aktual,
                    pg.hari_terlambat,
                    pg.total_denda,
                    pg.status_kondisi,
                    pg.keterangan
                FROM pengembalian pg
                JOIN peminjaman p ON pg.peminjaman_id = p.id
                ORDER BY pg.id DESC
            ");

            // Refresh data dropdown agar peminjaman yang sudah dikembalikan tidak muncul lagi
            $peminjaman_list = mysqli_query($connection, "
                SELECT id, nama_peminjam, nim_peminjam, tanggal_pinjam, tanggal_kembali_seharusnya 
                FROM peminjaman 
                WHERE status = 'dipinjam' 
                AND id NOT IN (SELECT peminjaman_id FROM pengembalian WHERE peminjaman_id IS NOT NULL)
                ORDER BY id ASC
            ");
        } else {
            $error = "Gagal menyimpan data pengembalian: " . mysqli_error($connection);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/topnav.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>

            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Modul Pengembalian Buku</h2>
                <div class="btn-group">
                    <a href="../admin/laporan_pengembalian.php" class="btn btn-outline-success me-2">
                        <i class="bi bi-printer"></i> Cetak Laporan
                    </a>
                    <button class="btn btn-add-pengembalian" type="button" onclick="toggleForm()">+ Tambah Pengembalian</button>
                </div>
            </div>
            <!-- Form Pengembalian -->
            <div id="formPengembalian" class="card mb-4" style="display: none;">
                <div class="card-body">
                    <h5>Form Pengembalian</h5>
                    <?php if ($error): ?>
                        <?= showAlert($error, 'danger') ?>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <?= showAlert($success, 'success') ?>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Pilih Peminjaman*</label>
                            <select name="peminjaman_id" class="form-control" required onchange="loadPeminjamanDetails(this.value)">
                                <option value="">-- Pilih Peminjaman --</option>
                                <?php while ($row = mysqli_fetch_assoc($peminjaman_list)): ?>
                                    <option value="<?= $row['id'] ?>" data-tgl-seharusnya="<?= $row['tanggal_kembali_seharusnya'] ?>">
                                        [<?= $row['id'] ?>] <?= htmlspecialchars($row['nama_peminjam']) ?> - <?= $row['tanggal_pinjam'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Kembali Seharusnya</label>
                            <input type="date" id="tgl_seharusnya_display" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Kembali Aktual</label>
                            <input type="date" name="tanggal_kembali_aktual" class="form-control" value="<?= date('Y-m-d') ?>" required onchange="calculateFine()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Denda Kerusakan Manual (Opsional)</label>
                            <input type="number" name="denda_kerusakan" class="form-control" step="0.01" value="0" min="0" disabled id="dendaKerusakanInput" oninput="calculateFine()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status Kondisi</label>
                            <select name="status_kondisi" class="form-control" required onchange="toggleDendaInput()">
                                <option value="sesuai">--pilih status--</option>
                                <option value="sesuai">Sesuai (Tepat Waktu)</option>
                                <option value="terlambat">Terlambat</option>
                                <option value="rusak">Rusak</option>
                                <option value="hilang">Hilang</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Denda (Otomatis)</label>
                            <input type="text" class="form-control" value="Rp 0" readonly id="totalDendaDisplay">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Proses Pengembalian</button>
                        <button type="button" class="btn btn-secondary" onclick="toggleForm()">Batal</button>
                    </form>
                </div>
            </div>

            <!-- Riwayat Pengembalian -->
            <div id="riwayatPengembalian">
                <h4>Riwayat Pengembalian</h4>
                <?php if (mysqli_num_rows($riwayat_pengembalian) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID Peminjaman</th>
                                    <th>Nama Peminjam</th>
                                    <th>Tgl Kembali</th>
                                    <th>Hari Terlambat</th>
                                    <th>Total Denda</th>
                                    <th>Status Kondisi</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($riwayat_pengembalian)): ?>
                                    <tr>
                                        <td><?= $row['peminjaman_id'] ?></td>
                                        <td><?= htmlspecialchars($row['nama_peminjam']) ?></td>
                                        <td><?= $row['tanggal_kembali_aktual'] ?></td>
                                        <td><?= $row['hari_terlambat'] ?></td>
                                        <td>Rp <?= number_format($row['total_denda'], 2, ',', '.') ?></td>
                                        <td><?= htmlspecialchars($row['status_kondisi']) ?></td>
                                        <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Belum ada data pengembalian.</div>
                <?php endif; ?>
            </div>

            <script>
            let selectedPeminjamanId = null;
            let hargaBukuTotal = 0;
            let tanggalSeharusnya = null;

            function loadPeminjamanDetails(id) {
                selectedPeminjamanId = id;
                const select = document.querySelector('select[name="peminjaman_id"]');
                const option = select.options[select.selectedIndex];
                
                // Ambil tanggal dari atribut data-tgl-seharusnya di option
                tanggalSeharusnya = option.dataset.tglSeharusnya;
                
                // TAMPILKAN KE KOLOM YANG KAMU BUAT
                document.getElementById('tgl_seharusnya_display').value = tanggalSeharusnya || "";

                if (id) {
                    fetch(`?action=get_peminjaman_details&peminjaman_id=${id}`)
                        .then(response => response.json())
                        .then(data => {
                            hargaBukuTotal = data.total_nilai || 0;
                            calculateFine(); // Hitung denda setelah data harga buku didapat
                        });
                }
            }

            function calculateFine() {
                if (!tanggalSeharusnya) return;

                const tanggalAktual = document.querySelector('input[name="tanggal_kembali_aktual"]').value;
                const statusKondisi = document.querySelector('select[name="status_kondisi"]').value;
                
                // Ambil nilai denda manual dari input
                const dendaManual = parseFloat(document.getElementById('dendaKerusakanInput').value) || 0;

                const seharusnyaDate = new Date(tanggalSeharusnya);
                const aktualDate = new Date(tanggalAktual);
                let hariTerlambat = 0;

                if (aktualDate > seharusnyaDate) {
                    const diffTime = Math.abs(aktualDate - seharusnyaDate);
                    hariTerlambat = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                }

                let dendaSistem = 0;
                const dendaPerHari = 5000;

                // Tentukan denda dasar berdasarkan status
                switch(statusKondisi) {
                    case 'terlambat':
                        dendaSistem = hariTerlambat * dendaPerHari;
                        break;
                    case 'rusak':
                        dendaSistem = hargaBukuTotal * 0.10;
                        break;
                    case 'hilang':
                        dendaSistem = hargaBukuTotal;
                        break;
                    default:
                        dendaSistem = 0;
                }

                // Penjumlahan akhir: Sistem + Manual
                let totalAkhir = dendaSistem + dendaManual;

                document.getElementById('totalDendaDisplay').value = 'Rp ' + totalAkhir.toLocaleString('id-ID');
            }

            function toggleDendaInput() {
                const status = document.querySelector('select[name="status_kondisi"]').value;
                const dendaInput = document.getElementById('dendaKerusakanInput');
                if (status === 'rusak' || status === 'hilang') {
                    dendaInput.disabled = true;
                    dendaInput.value = 0; // Reset jika disable
                } else {
                    dendaInput.disabled = false;
                }
                calculateFine(); // Recalculate after status change
            }

            function toggleForm() {
                const form = document.getElementById('formPengembalian');
                const riwayat = document.getElementById('riwayatPengembalian');
                const button = document.querySelector('button[onclick="toggleForm()"]');

                if (form.style.display === 'none') {
                    form.style.display = 'block';
                    riwayat.style.display = 'none';
                    button.textContent = 'X Tutup Form';
                    button.onclick = toggleForm;
                } else {
                    form.style.display = 'none';
                    riwayat.style.display = 'block';
                    button.textContent = '+ Tambah Pengembalian';
                    button.onclick = toggleForm;
                }
            }

            // Panggil saat halaman dimuat
            document.addEventListener('DOMContentLoaded', function () {
                // Tidak perlu tambahan logika di sini
            });
            </script>
            <script>
                $(document).ready(function() {
                    // Fungsi untuk menghitung total denda
                    function hitungTotalDenda() {
                        // Ambil nilai denda otomatis (sistem) - hapus format 'Rp' dan titik
                        let dendaSistemText = $('#denda_otomatis_display').val() || "0";
                        let dendaSistem = parseInt(dendaSistemText.replace(/[^0-9]/g, '')) || 0;

                        // Ambil nilai denda kerusakan manual
                        let dendaManual = parseInt($('#denda_kerusakan_manual').val()) || 0;

                        // Penjumlahan denda
                        let total = dendaSistem + dendaManual;

                        // Tampilkan hasil ke input Total Denda dengan format rupiah
                        $('#total_denda_akhir').val('Rp ' + total.toLocaleString('id-ID'));
                    }

                    // Jalankan fungsi setiap kali input denda manual berubah
                    $('#denda_kerusakan_manual').on('input', function() {
                        hitungTotalDenda();
                    });
                });
            </script>
            
<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>