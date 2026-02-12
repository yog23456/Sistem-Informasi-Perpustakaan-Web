<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';
requireAuth();
requireModuleAccess('peminjaman');
require_once '../config/database.php';

$peminjaman_id = (int)($_GET['peminjaman_id'] ?? 0);

if ($peminjaman_id) {
    $stmt = mysqli_prepare($connection, "
        SELECT SUM(b.harga_buku * pd.qty) AS total_nilai
        FROM peminjaman_detail pd
        JOIN buku b ON pd.buku_id = b.id
        WHERE pd.peminjaman_id = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $peminjaman_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    echo json_encode(['total_nilai' => (float)$result['total_nilai']]);
} else {
    echo json_encode(['total_nilai' => 0]);
}
?>