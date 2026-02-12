<?php $user_role = $_SESSION['role'] ?? 'guest'; ?>
<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="<?= base_url('dashboard.php') ?>" class="d-flex align-items-center text-decoration-none">
                        <i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>
                        <span class="text-dark">PerpusPanel</span>
                    </a>
                </div>
                <div class="sidebar-toggler x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
            
            <div class="theme-toggle d-flex gap-2 align-items-center mt-4 justify-content-start">
                <i class="bi bi-sun-fill fs-6 text-warning"></i>
                <div class="form-check form-switch fs-6 mb-0">
                    <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                </div>
                <i class="bi bi-moon-fill fs-6 text-primary"></i>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu Utama</li>
                <li class="sidebar-item">
                    <a href="<?= base_url('dashboard.php') ?>" class="sidebar-link">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <?php if ($user_role === 'admin' || $user_role === 'petugas'): ?>
                <li class="sidebar-item">
                    <a href="<?= base_url('buku/index.php') ?>" class="sidebar-link">
                        <i class="bi bi-book"></i>
                        <span>Data Buku</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= base_url('peminjaman/index.php') ?>" class="sidebar-link">
                        <i class="bi bi-journal-plus"></i>
                        <span>Peminjaman</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= base_url('pengembalian/index.php') ?>" class="sidebar-link">
                        <i class="bi bi-journal-check"></i>
                        <span>Pengembalian</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if ($user_role === 'admin'): ?>
                <li class="sidebar-title">Sistem</li>
                <li class="sidebar-item">
                    <a href="<?= base_url('users/index.php') ?>" class="sidebar-link">
                        <i class="bi bi-people"></i>
                        <span>Manajemen Petugas</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= base_url('admin/themes.php') ?>" class="sidebar-link">
                        <i class="bi bi-palette"></i>
                        <span>Ganti Tema</span>
                    </a>
                </li>
                <?php endif; ?>

                <li class="sidebar-item mt-4">
                    <a href="<?= base_url('logout.php') ?>" class="sidebar-link text-danger">
                        <i class="bi bi-box-arrow-right text-danger"></i>
                        <span>Keluar</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div id="main">