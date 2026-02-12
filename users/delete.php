<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';
requireAuth();

// Hanya admin yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    die('Access Denied');
}

require_once '../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id) {
    $stmt = mysqli_prepare($connection, "DELETE FROM users WHERE id = ? AND role = 'petugas'");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

redirect('index.php');
?>