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

$master_id = (int)($_GET['peminjaman_id'] ?? 0);
if (!$master_id) redirect('index.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) die('Invalid CSRF token.');
    
    $buku_id = trim($_POST['buku_id'] ?? '');
    $qty = (int)trim($_POST['qty'] ?? 0);

    if (empty($buku_id)) {
        $error = "Buku Id wajib diisi.";
    } elseif ($qty <= 0) {
        $error = "Jumlah (Qty) minimal adalah 1.";
    }
    
    if (!$error) {
        // --- 1. VALIDASI STOK BUKU ---
        $queryStok = mysqli_prepare($connection, "SELECT stok, judul FROM buku WHERE id = ?");
        mysqli_stmt_bind_param($queryStok, "i", $buku_id);
        mysqli_stmt_execute($queryStok);
        $resStok = mysqli_stmt_get_result($queryStok);
        $dataBuku = mysqli_fetch_assoc($resStok);
        mysqli_stmt_close($queryStok);

        if (!$dataBuku) {
            $error = "Data buku tidak ditemukan.";
        } elseif ($dataBuku['stok'] <= 0) {
            $error = "Gagal! Stok buku '" . $dataBuku['judul'] . "' sudah habis.";
        } elseif ($qty > $dataBuku['stok']) {
            $error = "Gagal! Stok tidak cukup. Tersedia: " . $dataBuku['stok'] . ", ingin pinjam: " . $qty;
        }

        if (!$error) {
            // --- 2. CEK DUPLIKAT ITEM ---
            $check_stmt = mysqli_prepare($connection, "SELECT id FROM `peminjaman_detail` WHERE `peminjaman_id` = ? AND `buku_id` = ?");
            mysqli_stmt_bind_param($check_stmt, "ii", $master_id, $buku_id);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $error = "Buku ini sudah ada di dalam daftar peminjaman ini.";
            }
            mysqli_stmt_close($check_stmt);
            
            if (!$error) {
                // --- 3. SIMPAN KE TABEL DETAIL ---
                $stmt = mysqli_prepare($connection, "INSERT INTO `peminjaman_detail` (`buku_id`, `qty`, `peminjaman_id`) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "iii", $buku_id, $qty, $master_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);

                    // --- 4. UPDATE STOK BUKU SECARA OTOMATIS ---
                    #mysqli_query($connection, "UPDATE buku SET stok = stok - $qty WHERE id = '$buku_id'");

                    redirect(dirname($_SERVER['SCRIPT_NAME']) . "/detail.php?id=$master_id");
                    exit();
                } else {
                    $error = "Gagal menyimpan item: " . mysqli_error($connection);
                }
            }
        }
    }
}
$csrfToken = generateCSRFToken();
?>
<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/topnav.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>

<div class="page-heading">
    <h3>Tambah Item ke Peminjaman</h3>
</div>

<div class="section">
    <div class="card">
        <div class="card-body">
            <?php if ($error): ?>
                <?= showAlert($error, 'danger') ?>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="peminjaman_id" value="<?= $master_id ?>">

                <div class="mb-3">
                    <label class="form-label">Pilih Buku*</label>
                    <?php
                    echo dropdownFromTable(
                        'buku',               
                        'id',                 
                        'judul',              
                        '',                   
                        'buku_id',            
                        '-- Pilih Buku --',   
                        'id'                  
                    ); ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah Pinjam (Qty)</label>
                    <input type="number" name="qty" class="form-control" value="1" min="1">
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Tambah ke Daftar</button>
                    <a href="detail.php?id=<?= $master_id ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>