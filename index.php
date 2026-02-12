<?php
// Jika sudah login, arahkan ke dashboard
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
require_once 'config/database.php';

// Ambil buku untuk ditampilkan
$buku_list = mysqli_query($connection, "SELECT id, judul, pengarang, cover_buku FROM buku ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1F2A44;
            --secondary: #6B7280;
            --light: #FFFFFF;
            --accent: #E5E7EB;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light);
            color: var(--primary);
        }

        /* Navbar Transparan */
        .navbar {
            background-color: transparent !important;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 15px 0;
        }

        .navbar-brand span:nth-child(1) {
            color: #eeff9e;
            font-size: 1.7rem;
        }

        .navbar-brand span:nth-child(2) {
            color: #eeff9e;
            font-weight: bold;
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
        }

        .navbar-nav .nav-link:hover {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        /* Hero Section Full Background Image */
        .hero-section {
            height: 100vh;
            background-image: url('assets/img/hero-bg.jpg'); /* Ganti dengan gambar kamu */
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: flex-end;
            padding-bottom: 80px;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4); /* Agar teks tetap terbaca */
        }

        .hero-content {
            position: relative;
            z-index: 10;
            color: white;
        }

        .hero-text-left {
            text-align: left;
        }

        .hero-text-right {
            text-align: right;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            color: white;
            margin-bottom: 80px;
        }

        .center-image {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 5;
        }

        .center-image img {
            max-width: 450px;
            border-radius: 0px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .btn-transparent {
            background-color: rgba(255,255,255,0.2);
            border: 2px solid white;
            color: white;
            padding: 10px 25px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-transparent:hover {
            background-color: white;
            color: black;
        }

        /* Section Titles */
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c2c2c; /* Warna teks besar diubah */
            margin-bottom: 40px;
            text-align: center;
        }

        .about-section {
            padding: 80px 0;
            background-color: #f9fafb;
        }

        .about-text {
            font-size: 1.1rem;
            color: var(--secondary);
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Book Card Background Changed */
        .book-card {
            background-color: #f1f1f1; /* Warna abu muda */
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            align-items: stretch;
        }

        .book-cover {
            width: 180px;
            height: 100%;
            object-fit: cover;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .book-info {
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .book-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .book-author {
            font-size: 0.9rem;
            color: var(--secondary);
            font-style: italic;
        }

        .login-section {
            padding: 80px 0;
            text-align: center;
            background-color: #ffffff;
        }

        /* Login Button Color Changed */
        .login-btn {
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            background-color: #2c2c2c; /* Warna tombol login */
            color: white;
            border: none;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background-color: #1a1a1a;
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .book-cover {
                width: 120px;
            }

            .book-info {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <span>📚</span>
                <span class="ms-2">Perpustakaan</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#books">Koleksi Buku</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Tentang Kami</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="hero-overlay"></div>

        <!-- Gambar di tengah -->
        <div class="center-image">
            <img src="assets/img/hero-book2.jpg" alt="Buku" class="img-fluid">
        </div>

        <div class="container hero-content">
            <div class="row">
                <div class="col-lg-6 hero-text-left">
                    <h1 class="hero-title">Sistem Informasi Perpustakaan</h1>
                </div>
                <div class="col-lg-6 hero-text-right">
                    <a href="#books" class="btn btn-transparent">Jelajahi Koleksi</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Books Section -->
    <section id="books" class="container py-5">
        <h2 class="section-title">Koleksi Buku Terbaru</h2>
        <?php if (mysqli_num_rows($buku_list) > 0): ?>
            <div class="row">
                <?php while ($buku = mysqli_fetch_assoc($buku_list)): ?>
                    <div class="col-md-6 mb-4">
                        <div class="book-card">
                            <?php
                            $cover_path = 'assets/img/buku/' . $buku['cover_buku'];
                            $img_src = file_exists($cover_path) ? $cover_path : 'assets/img/buku/no_cover.jpg';
                            ?>
                            <img src="<?= $img_src ?>" class="book-cover" alt="Cover <?= htmlspecialchars($buku['judul']) ?>">
                            <div class="book-info">
                                <div>
                                    <h3 class="book-title"><?= htmlspecialchars($buku['judul']) ?></h3>
                                    <p class="book-author">Oleh: <?= htmlspecialchars($buku['pengarang']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">Belum ada buku yang tersedia.</div>
        <?php endif; ?>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <h2 class="section-title">Tentang Kami</h2>
            <div class="about-text">
                <p>
                    Perpustakaan kami didirikan dengan tujuan memberikan akses informasi dan pengetahuan kepada seluruh komunitas. 
                    Kami menyediakan koleksi buku fisik yang beragam dan lengkap, mulai dari fiksi, nonfiksi, buku ilmiah, sastra, hingga referensi pendidikan lainnya.
                </p>
                <p>
                    Kami percaya bahwa membaca adalah jendela dunia. Oleh karena itu, kami berkomitmen untuk:
                </p>
                <ul>
                    <li>Meningkatkan literasi melalui penyediaan koleksi buku yang mudah diakses oleh masyarakat.</li>
                    <li>Menyediakan ruang baca dan belajar yang nyaman, tenang, dan kondusif.</li>
                    <li>Mendukung kegiatan pembelajaran dan pengembangan pengetahuan melalui layanan perpustakaan yang tertata dan terkelola dengan baik.</li>
                </ul>
                <p>
                    Mari kunjungi perpustakaan kami dan jadilah bagian dari lingkungan belajar yang aktif, cerdas, dan berwawasan luas.
                </p>
            </div>
        </div>
    </section>

    <!-- Login Section -->
    <section id="login" class="login-section">
        <div class="container">
            <h2 class="section-title">Petugas/Admin</h2>
            <a href="login.php" class="btn login-btn">Masuk ke Sistem</a>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>