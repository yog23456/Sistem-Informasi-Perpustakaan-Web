-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 12, 2026 at 08:02 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `kelas` varchar(50) NOT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id`, `nama`, `nim`, `kelas`, `foto`) VALUES
(1, 'Ahmad Rizki', '230511001', 'TI-2A', NULL),
(2, 'Siti Nurhaliza', '230511002', 'TI-2A', NULL),
(3, 'Budi Santoso', '230511003', 'TI-2B', NULL),
(4, 'Dewi Lestari', '230511004', 'TI-2B', NULL),
(5, 'Andi Pratama', '230511005', 'TI-2C', '1769485285_230511005.png'),
(6, 'Putri Ayu', '230511006', 'TI-2C', NULL),
(7, 'Fajar Hidayat', '230511007', 'TI-2A', NULL),
(8, 'Nabila Zahra', '230511008', 'TI-2B', NULL),
(9, 'Rizky Ramadhan', '230511009', 'TI-2C', '1769485449_230511009.png'),
(11, 'Yogi Saputra', '23051101', 'TI-2A', 'default.png');

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id` int NOT NULL,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pengarang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stok` int DEFAULT '0',
  `harga_buku` decimal(10,2) DEFAULT '0.00',
  `cover_buku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'no_cover.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id`, `judul`, `pengarang`, `stok`, `harga_buku`, `cover_buku`) VALUES
(1, 'UI UX V2', 'Rina Kurniawati', 70, 75000.00, 'no_cover.jpg'),
(2, 'Aplikasi Komputer', 'Dwi Krisbiantoro', 54, 400000.00, 'buku_1769160207.jpg'),
(3, 'The Wealth of Nations', 'Adam Smith', 10, 2000000.00, 'buku_1769175489.jpg'),
(4, 'The Psychology of Money', 'Morgan Housel', 15, 800000.00, 'buku_1769175643.jpg'),
(5, 'The Richest Man in Babylon', 'George S. Clason', 20, 1000000.00, 'buku_1769175796.jpg'),
(6, 'The Intelligent Investor', 'Benjamin Graham', 20, 1500000.00, 'buku_1769175923.jpg'),
(7, 'Security Analysis', 'Benjamin Graham and David L. Dodd', 25, 1000000.00, 'buku_1769176062.jpg'),
(8, 'Harry Potter and the Sorcerer\'s Stone', 'J.K. Rowling', 40, 200000.00, 'buku_1769176292.jpg'),
(9, 'Dasar-Dasar Teknik Informatika', 'Novega Pratama Adiputra', 50, 70000.00, 'buku_1769176360.jpg'),
(10, 'Pengantar Teknologi Informatika', 'Bagaskoro, S.Kom', 69, 80000.00, 'buku_1769176456.jpg'),
(11, 'Kapita Selekta Neurologi (Edisi Kedua)', 'Prof (Ret). dr. Harsono, Sp.S(K)', 60, 100000.00, 'buku_1769176619.png'),
(12, 'Buku Ajar Pemantauan Janin Dan Kontraksi Uterus Secara Elektronik (Kardiotokografi)', 'Dr. dr. Anak Agung Ngurah Jaya Kusuma, Sp.OG(K)-KFM, MARS dan dr. Pande Kadek Aditya Prayudi, Sp.OG', 50, 150000.00, 'buku_1769176689.jpg'),
(13, 'Buku Instrumen Bedah Jantung Pediatri', 'Yudha Krisnadia', 40, 85000.00, 'buku_1769176760.jpg'),
(14, 'The Alchemist', 'Paulo Coelho', 50, 70000.00, 'buku_1769176924.jpg'),
(15, 'As Long As the Lemon Trees Grow', 'Zoulfa Katouh', 60, 100000.00, 'buku_1769177131.jpg'),
(16, 'The Old Man and the Sea', 'Ernest Hemingway', 40, 200000.00, 'buku_1769177212.jpg'),
(17, 'Game of Thrones', 'George R. R. Martin', 59, 100000.00, 'buku_1769177329.jpg'),
(18, 'Komik Dragon Ball', 'Akira Toriyama', 10, 70000.00, 'no_cover.jpg'),
(19, 'Laskar Pelangi', 'Akira Toriyama', 50, 50000.00, 'no_cover.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int NOT NULL,
  `anggota_id` int DEFAULT NULL,
  `nama_peminjam` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nim_peminjam` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelas_peminjam` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `petugas_id` int NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_seharusnya` date NOT NULL,
  `status` enum('dipinjam','selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'dipinjam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `anggota_id`, `nama_peminjam`, `nim_peminjam`, `kelas_peminjam`, `petugas_id`, `tanggal_pinjam`, `tanggal_kembali_seharusnya`, `status`) VALUES
(1, NULL, 'Alfi Imtiaz', '230511022', 'TI23A', 1, '2026-01-23', '2026-01-30', 'selesai'),
(2, NULL, 'Putriyah', '237376425', 'TI23A', 1, '2026-01-23', '2026-01-30', 'selesai'),
(3, NULL, 'Yogi', '230511000', 'TI23A', 1, '2026-01-18', '2026-01-20', 'selesai'),
(4, NULL, 'Charlie Puth', '223098462', 'TI23V', 1, '2026-01-19', '2026-01-23', 'selesai'),
(5, NULL, 'Sainz', '32827624', 'TI23V', 1, '2026-01-20', '2026-01-23', 'selesai'),
(6, NULL, 'Alfi Imtiaz', '230511022', 'TI23A', 1, '2026-01-14', '2026-01-22', 'selesai'),
(7, NULL, 'Alfi Imtiaz', '230511022', 'TI23A', 1, '2026-01-23', '2026-01-30', 'selesai'),
(8, NULL, 'Asep', '89023973', 'TI23V', 1, '2026-01-23', '2026-01-30', 'selesai'),
(9, NULL, 'Asep', '89023973', 'TI23M', 1, '2026-01-24', '2026-01-31', 'selesai'),
(10, NULL, 'Wahyu', '969', 'TI23A', 5, '2026-01-26', '2026-02-02', 'selesai'),
(11, NULL, 'OWI', '1234', 'TI23A', 2, '2026-01-26', '2026-02-02', 'selesai'),
(15, NULL, 'agus', '122222', '122222', 4, '2026-01-26', '2026-02-02', 'selesai'),
(16, NULL, 'Kirman', '1111122222', '1111122222', 4, '2026-01-26', '2026-02-02', 'selesai'),
(17, NULL, 'asep', '1234567', '1111', 4, '2026-01-27', '2026-02-03', 'selesai'),
(18, NULL, 'asep', '1234567', '1111', 5, '2026-01-27', '2026-02-03', 'selesai'),
(19, NULL, 'Nadia', '23098326', '1111', 2, '2026-01-27', '2026-02-05', 'selesai'),
(20, NULL, 'Dzikri', '239083677', 'TI23A', 1, '2026-01-27', '2026-02-03', 'selesai'),
(21, NULL, 'asep', '23908363', '1111', 2, '2026-01-27', '2026-02-03', 'selesai'),
(22, NULL, 'Yogi Saputra', '23051101', 'TI-2A', 5, '2026-01-27', '2026-02-03', 'selesai'),
(23, NULL, 'Budi Santoso', '230511003', 'TI-2B', 4, '2026-01-27', '2026-02-03', 'selesai'),
(24, NULL, 'Nabila Zahra', '230511008', 'TI-2B', 2, '2026-01-27', '2026-02-03', 'selesai'),
(25, NULL, 'Fajar Hidayat', '230511007', 'TI-2A', 2, '2026-01-27', '2026-02-03', 'selesai'),
(26, NULL, 'Andi Pratama', '230511005', 'TI-2C', 4, '2026-01-27', '2026-02-03', 'selesai'),
(27, 11, 'Yogi Saputra', '23051101', 'TI-2A', 4, '2026-02-10', '2026-02-17', 'selesai'),
(28, 11, 'Yogi Saputra', '23051101', 'TI-2A', 5, '2026-02-11', '2026-02-18', 'selesai'),
(29, 3, 'Budi Santoso', '230511003', 'TI-2B', 4, '2026-02-11', '2026-02-18', 'selesai'),
(30, 11, 'Yogi Saputra', '23051101', 'TI-2A', 5, '2026-02-11', '2026-02-18', 'dipinjam'),
(31, 11, 'Yogi Saputra', '23051101', 'TI-2A', 5, '2026-02-11', '2026-02-18', 'dipinjam'),
(32, 11, 'Yogi Saputra', '23051101', 'TI-2A', 5, '2026-02-11', '2026-02-18', 'dipinjam'),
(33, 11, 'Yogi Saputra', '23051101', 'TI-2A', 5, '2026-02-11', '2026-02-18', 'dipinjam'),
(34, 11, 'Yogi Saputra', '23051101', 'TI-2A', 5, '2026-02-12', '2026-02-19', 'dipinjam'),
(35, 11, 'Yogi Saputra', '23051101', 'TI-2A', 5, '2026-02-12', '2026-02-19', 'dipinjam'),
(36, 11, 'Yogi Saputra', '23051101', 'TI-2A', 5, '2026-02-12', '2026-02-19', 'dipinjam'),
(37, 11, 'Yogi Saputra', '23051101', 'TI-2A', 5, '2026-02-12', '2026-02-19', 'dipinjam'),
(38, 11, 'Yogi Saputra', '23051101', 'TI-2A', 5, '2026-02-12', '2026-02-19', 'dipinjam'),
(39, 11, 'Yogi Saputra', '23051101', 'TI-2A', 5, '2026-02-12', '2026-02-19', 'dipinjam'),
(40, 11, 'Yogi Saputra', '23051101', 'TI-2A', 5, '2026-02-12', '2026-02-19', 'dipinjam'),
(41, 11, 'Yogi Saputra', '23051101', 'TI-2A', 5, '2026-02-12', '2026-02-19', 'dipinjam');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman_detail`
--

CREATE TABLE `peminjaman_detail` (
  `id` int NOT NULL,
  `peminjaman_id` int NOT NULL,
  `buku_id` int NOT NULL,
  `qty` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `peminjaman_detail`
--

INSERT INTO `peminjaman_detail` (`id`, `peminjaman_id`, `buku_id`, `qty`) VALUES
(1, 1, 2, 1),
(2, 2, 1, 2),
(3, 3, 2, 3),
(4, 4, 2, 3),
(5, 5, 2, 1),
(6, 6, 2, 4),
(7, 6, 1, 1),
(8, 7, 4, 2),
(9, 7, 16, 1),
(10, 8, 12, 1),
(11, 8, 14, 1),
(12, 9, 7, 1),
(13, 10, 17, 1),
(14, 10, 18, 13),
(15, 11, 17, 1),
(19, 15, 18, 1),
(20, 16, 18, 1),
(21, 17, 18, 1),
(22, 18, 18, 1),
(23, 19, 14, 1),
(24, 19, 3, 1),
(25, 20, 10, 1),
(26, 21, 8, 1),
(27, 21, 12, 1),
(28, 22, 18, 1),
(29, 23, 18, 5),
(30, 24, 19, 20),
(31, 25, 19, 1),
(32, 26, 12, 1),
(33, 26, 3, 1),
(34, 27, 19, 10),
(36, 27, 8, 30);

--
-- Triggers `peminjaman_detail`
--
DELIMITER $$
CREATE TRIGGER `update_stok_after_insert_detail` AFTER INSERT ON `peminjaman_detail` FOR EACH ROW BEGIN
    UPDATE `buku` 
    SET `stok` = `stok` - NEW.qty 
    WHERE `id` = NEW.buku_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id` int NOT NULL,
  `peminjaman_id` int NOT NULL,
  `tanggal_kembali_aktual` date NOT NULL,
  `hari_terlambat` int DEFAULT '0',
  `denda_perhari` decimal(10,2) DEFAULT '5000.00',
  `denda_kerusakan` decimal(10,2) DEFAULT '0.00',
  `total_denda` decimal(10,2) DEFAULT '0.00',
  `status_kondisi` enum('sesuai','terlambat','rusak','hilang') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengembalian`
--

INSERT INTO `pengembalian` (`id`, `peminjaman_id`, `tanggal_kembali_aktual`, `hari_terlambat`, `denda_perhari`, `denda_kerusakan`, `total_denda`, `status_kondisi`, `keterangan`) VALUES
(1, 1, '2026-01-23', 0, 5000.00, 0.00, 0.00, 'hilang', 'hilang'),
(5, 2, '2026-01-23', 0, 5000.00, 0.00, 0.00, 'sesuai', ''),
(6, 3, '2026-01-23', 3, 5000.00, 0.00, 15000.00, 'terlambat', 'hehehe'),
(7, 4, '2026-01-23', 0, 5000.00, 0.00, 120000.00, 'rusak', 'basah kuyup'),
(8, 5, '2026-01-23', 0, 5000.00, 0.00, 0.00, 'terlambat', 'sempat hilang'),
(9, 6, '2026-01-23', 1, 5000.00, 0.00, 5000.00, 'terlambat', ''),
(10, 7, '2026-01-23', 0, 5000.00, 0.00, 0.00, 'sesuai', ''),
(11, 10, '2026-01-26', 0, 5000.00, 0.00, 1010000.00, 'hilang', 'Kelas King'),
(12, 11, '2026-01-26', 0, 5000.00, 5000.00, 0.00, 'sesuai', 'Kena Tumpahan minuman alhasil kena denda 5000'),
(14, 8, '2026-01-26', 0, 5000.00, 5000.00, 0.00, 'sesuai', 'Kena Tumpahan minuman alhasil kena denda 5000'),
(16, 9, '2026-01-26', 0, 5000.00, 5000.00, 5000.00, 'sesuai', 'Terdapat coretan'),
(17, 15, '2026-01-26', 0, 5000.00, 1000.00, 1000.00, 'sesuai', 'Dia Hutang 1000'),
(18, 16, '2026-01-26', 0, 5000.00, 0.00, 0.00, 'sesuai', ''),
(19, 17, '2026-01-27', 0, 5000.00, 5000.00, 5000.00, 'sesuai', ''),
(20, 18, '2026-01-27', 0, 5000.00, 0.00, 70000.00, 'hilang', ''),
(21, 19, '2026-01-27', 0, 5000.00, 0.00, 0.00, 'sesuai', ''),
(22, 20, '2026-01-27', 0, 5000.00, 0.00, 80000.00, 'hilang', 'lupa naroh'),
(23, 21, '2026-01-27', 0, 5000.00, 0.00, 35000.00, 'rusak', 'basah'),
(24, 22, '2026-02-05', 2, 5000.00, 5000.00, 10000.00, 'terlambat', 'coretan'),
(25, 23, '2026-02-20', 17, 5000.00, 5000.00, 85000.00, 'terlambat', ''),
(27, 24, '2026-02-06', 3, 5000.00, 10000.00, 25000.00, 'terlambat', ''),
(28, 25, '2026-01-27', 0, 5000.00, 0.00, 0.00, 'sesuai', ''),
(29, 26, '2026-02-11', 8, 5000.00, 0.00, 40000.00, 'terlambat', ''),
(30, 27, '2026-02-12', 0, 5000.00, 0.00, 0.00, 'sesuai', ''),
(31, 28, '2026-02-12', 0, 5000.00, 0.00, 0.00, 'sesuai', ''),
(32, 29, '2026-02-12', 0, 5000.00, 0.00, 0.00, 'sesuai', '');

--
-- Triggers `pengembalian`
--
DELIMITER $$
CREATE TRIGGER `update_stok_after_return` AFTER INSERT ON `pengembalian` FOR EACH ROW BEGIN
    -- Stok balik hanya jika status BUKAN hilang
    IF NEW.status_kondisi != 'hilang' THEN
        UPDATE buku b
        JOIN peminjaman_detail pd ON b.id = pd.buku_id
        SET b.stok = b.stok + pd.qty
        WHERE pd.peminjaman_id = NEW.peminjaman_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('active_theme', 'default');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','petugas') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_lengkap` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `foto_profil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `nama_lengkap`, `created_at`, `foto_profil`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Super Admin', '2026-01-23 08:44:15', 'default.png'),
(2, 'petugas1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'petugas', 'Petugas Perpus', '2026-01-23 08:44:15', 'default.png'),
(4, 'petugas3', '$2y$10$9x1jweF4vbST6VTiVlOtFe33R8Qm7cm/pe8E3wIW4bX8fsnVCGdF6', 'petugas', 'Asep Raja Jawa', '2026-01-23 11:36:05', 'default.png'),
(5, 'petugas2', '$2y$10$4oM26QPCOdmA4kpEFC9N1ugygjvxwfBzIGZ5KgY6/kEeAHZxG2Xti', 'petugas', 'Yogi', '2026-01-23 11:37:01', 'default.png'),
(6, 'admin2', '$2y$10$zFM7LhQnUCJtPWJMpDQ3iu6OMBEaT6U.M6GAIvPuuWTj5dyn2ZrAu', 'admin', NULL, '2026-01-24 09:00:14', 'default.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `petugas_id` (`petugas_id`),
  ADD KEY `fk_anggota_peminjaman` (`anggota_id`);

--
-- Indexes for table `peminjaman_detail`
--
ALTER TABLE `peminjaman_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peminjaman_id` (`peminjaman_id`),
  ADD KEY `buku_id` (`buku_id`);

--
-- Indexes for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_peminjaman` (`peminjaman_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `peminjaman_detail`
--
ALTER TABLE `peminjaman_detail`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `fk_anggota_peminjaman` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_peminjaman_petugas` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `peminjaman_detail`
--
ALTER TABLE `peminjaman_detail`
  ADD CONSTRAINT `fk_detail_buku` FOREIGN KEY (`buku_id`) REFERENCES `buku` (`id`),
  ADD CONSTRAINT `fk_detail_peminjaman` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `fk_pengembalian_peminjaman` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
