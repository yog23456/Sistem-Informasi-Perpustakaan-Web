<?php
session_start();
require_once '../config/database.php';
require_once '../lib/functions.php';
require_once '../lib/auth.php';

// Pastikan yang akses adalah admin
requireAuth();
if (getUserRole() !== 'admin') {
    redirect('../login.php');
}

include '../views/'.$THEME.'/header.php';
include '../views/'.$THEME.'/sidebar.php';
include '../views/'.$THEME.'/topnav.php';
include '../views/'.$THEME.'/upper_block.php';

// Memindai folder di dalam direktori /views/ secara otomatis
$availableThemes = getAvailableThemes(); 
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Ganti Tema Aplikasi</h2>
        <span class="badge bg-primary">Tema Aktif: <?= ucfirst($THEME) ?></span>
    </div>

    <?php if (isset($_GET['success'])) showAlert($_GET['success'], 'success'); ?>
    <?php if (isset($_GET['error'])) showAlert($_GET['error'], 'danger'); ?>

    <div class="row">
        <?php foreach ($availableThemes as $folder => $name): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm <?= ($THEME === $folder) ? 'border-primary border-3' : '' ?>">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="mb-3">
                            <i class="bi bi-palette2 fs-1 text-secondary"></i>
                        </div>
                        <h5 class="card-title"><?= $name ?></h5>
                        <p class="card-text text-muted small">Lokasi: views/<?= $folder ?></p>
                        
                        <div class="mt-auto">
                            <?php if ($THEME === $folder): ?>
                                <button class="btn btn-success w-100" disabled>
                                    <i class="bi bi-check-circle"></i> Sedang Digunakan
                                </button>
                            <?php else: ?>
                                <a href="themes_action.php?set=<?= $folder ?>" class="btn btn-primary w-100" 
                                   onclick="return confirm('Apakah Anda yakin ingin mengganti tema ke <?= $name ?>?')">
                                    <i class="bi bi- paintbrush"></i> Gunakan Tema Ini
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php 
include '../views/'.$THEME.'/lower_block.php';
include '../views/'.$THEME.'/footer.php'; 
?>