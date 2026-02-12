<?php
session_start();
require_once '../config/database.php';
require_once '../lib/functions.php';
require_once '../lib/auth.php';

// Proteksi: Hanya Admin yang boleh ganti tema
requireAuth();
if (getUserRole() !== 'admin') {
    redirect('../login.php');
}

if (isset($_GET['set'])) {
    $newTheme = sanitize($_GET['set']); // Misal: 'mazer', 'default', atau 'pluto'
    
    // Update ke tabel settings
    $stmt = mysqli_prepare($connection, "UPDATE settings SET setting_value = ? WHERE setting_key = 'active_theme'");
    mysqli_stmt_bind_param($stmt, "s", $newTheme);
    
    if (mysqli_stmt_execute($stmt)) {
        // Update session agar perubahan langsung terasa di halaman ini
        $_SESSION['theme'] = $newTheme;
        header("Location: themes.php?success=Tema berhasil diubah menjadi " . ucfirst($newTheme));
    } else {
        header("Location: themes.php?error=Gagal memperbarui database");
    }
    exit();
}