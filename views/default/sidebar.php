<?php
$menuConfig = loadMenuConfig();
$user_role = $_SESSION['role'] ?? 'guest';
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="#" class="sidebar-logo">
            <i class="bi bi-grid-3x3-gap-fill"></i>
            <span>Perpustakaan</span>
        </a>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>
    <nav class="sidebar-menu">
        <div class="sidebar-item">
            <a href="<?= base_url('dashboard.php') ?>" class="sidebar-link">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <?php if ($user_role === 'admin' || $user_role === 'petugas'): ?>
            <div class="sidebar-item">
                <a href="<?= base_url('buku/index.php') ?>" class="sidebar-link">
                    <i class="bi bi-book"></i>
                    <span>Data Buku</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="<?= base_url('peminjaman/index.php') ?>" class="sidebar-link">
                    <i class="bi bi-journal-plus"></i>
                    <span>Peminjaman</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="<?= base_url('pengembalian/index.php') ?>" class="sidebar-link">
                    <i class="bi bi-journal-check"></i>
                    <span>Pengembalian</span>
                </a>
            </div>
            
            <hr class="sidebar-divider" style="border-top: 1px solid rgba(255,255,255,0.1)">
            <div class="sidebar-item">
                <a href="<?= base_url('admin/laporan_peminjaman.php') ?>" class="sidebar-link">
                    <i class="bi bi-file-earmark-pdf"></i>
                    <span>Laporan Peminjaman</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="<?= base_url('admin/laporan_pengembalian.php') ?>" class="sidebar-link">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    <span>Laporan Pengembalian</span>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($user_role === 'admin'): ?>
            <div class="sidebar-item">
                <a href="<?= base_url('users/index.php') ?>" class="sidebar-link">
                    <i class="bi bi-people"></i>
                    <span>Manajemen Petugas</span>
                </a>
            </div>
        <?php endif; ?>
        <?php if ($user_role === 'admin'): ?>
            <div class="sidebar-item">
                <a href="<?= base_url('anggota/index.php') ?>" class="sidebar-link">
                    <i class="bi bi-people"></i>
                    <span>Manajemen anggota</span>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($user_role === 'admin'): ?>
            <div class="sidebar-item">
                <a href="<?= base_url('admin/themes.php') ?>" class="sidebar-link">
                    <i class="bi bi-palette"></i>
                    <span>Ganti Tema</span>
                </a>
            </div>
        <?php endif; ?>

        <div class="sidebar-item">
            <a href="<?= base_url('logout.php') ?>" class="sidebar-link">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</aside>