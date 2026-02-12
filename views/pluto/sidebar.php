<?php $user_role = $_SESSION['role'] ?? 'petugas'; ?>
<nav id="sidebar">
   <div class="sidebar_blog_1">
      <div class="sidebar-header">
         <div class="logo_section">
            <a href="<?= base_url('dashboard.php') ?>"><img class="logo_icon img-responsive" src="<?= base_url('assets/'.$THEME.'/images/logo/logo_icon.png'); ?>" alt="#" /></a>
         </div>
      </div>
      
      <div class="sidebar_user_info">
         <div class="user_profle_side">
            <div class="user_img">
               <img class="img-responsive rounded-circle" src="https://ui-avatars.com/api/?name=<?= $_SESSION['username'] ?>&background=random" alt="#" />
            </div>
            <div class="user_info">
               <h6><?= htmlspecialchars($_SESSION['username'] ?? 'User'); ?></h6>
               <p><span class="online_animation"></span> Online</p>
            </div>
         </div>
      </div>
   </div>
   <div class="sidebar_blog_2">
      <h4>General Menu</h4>
      <ul class="list-unstyled components">
         <li><a href="<?= base_url('dashboard.php') ?>"><i class="fa fa-dashboard yellow_color"></i> <span>Dashboard</span></a></li>
         <li><a href="<?= base_url('buku/index.php') ?>"><i class="fa fa-book blue1_color"></i> <span>Data Buku</span></a></li>
         <li><a href="<?= base_url('peminjaman/index.php') ?>"><i class="fa fa-clock-o orange_color"></i> <span>Peminjaman</span></a></li>
         <li><a href="<?= base_url('pengembalian/index.php') ?>"><i class="fa fa-check-square green_color"></i> <span>Pengembalian</span></a></li>
         
         <?php if ($user_role === 'admin'): ?>
            <li><a href="<?= base_url('admin/themes.php') ?>"><i class="fa fa-paint-brush purple_color"></i> <span>Ganti Tema</span></a></li>
            <li><a href="<?= base_url('users/index.php') ?>"><i class="fa fa-users yellow_color"></i> <span>Manajemen Petugas</span></a></li>
         <?php endif; ?>

         <li><a href="<?= base_url('logout.php') ?>"><i class="fa fa-sign-out red_color"></i> <span>Log Out</span></a></li>
      </ul>
   </div>
</nav>
<div id="content">