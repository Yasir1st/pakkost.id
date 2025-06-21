-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Jun 21, 2025 at 05:29 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_majene_kost`
--

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` int NOT NULL,
  `nama_fasilitas` varchar(100) NOT NULL,
  `tipe_fasilitas` enum('kamar','umum') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `nama_fasilitas`, `tipe_fasilitas`) VALUES
(1, 'AC', 'kamar'),
(2, 'Kamar Mandi Dalam', 'kamar'),
(3, 'Kasur', 'kamar'),
(4, 'Lemari', 'kamar'),
(5, 'Meja Belajar', 'kamar'),
(6, 'Wi-Fi', 'umum'),
(7, 'Dapur Bersama', 'umum'),
(8, 'Parkir Motor', 'umum'),
(9, 'Parkir Mobil', 'umum'),
(10, 'Ruang Tamu', 'umum');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int NOT NULL,
  `kost_id` int NOT NULL,
  `nama_pengirim` varchar(100) NOT NULL,
  `email_pengirim` varchar(100) NOT NULL,
  `pertanyaan` text NOT NULL,
  `status` enum('baru','dibalas') DEFAULT 'baru',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `kost_id`, `nama_pengirim`, `email_pengirim`, `pertanyaan`, `status`, `created_at`) VALUES
(1, 1, 'Yasir', 'yasirsaja@gmail.com', 'apakah benar?', 'baru', '2025-06-21 04:22:42');

-- --------------------------------------------------------

--
-- Table structure for table `kosts`
--

CREATE TABLE `kosts` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `nama_kost` varchar(150) NOT NULL,
  `deskripsi` text,
  `tipe_kost` enum('putra','putri','campur') NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `kelurahan` varchar(100) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `harga_harian` int DEFAULT NULL,
  `harga_bulanan` int DEFAULT NULL,
  `harga_tahunan` int DEFAULT NULL,
  `ketersediaan_kamar` int DEFAULT '0',
  `jumlah_total_kamar` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kosts`
--

INSERT INTO `kosts` (`id`, `user_id`, `nama_kost`, `deskripsi`, `tipe_kost`, `alamat`, `kelurahan`, `latitude`, `longitude`, `harga_harian`, `harga_bulanan`, `harga_tahunan`, `ketersediaan_kamar`, `jumlah_total_kamar`, `created_at`, `updated_at`) VALUES
(1, 3, 'KOSTKUNING', 'itulah pokoknya yah', 'campur', 'Majene, Bangge', 'Banggae', '-3.538907', '118.989566', NULL, 500000, 5000000, 10, 10, '2025-06-21 04:19:12', '2025-06-21 05:14:46');

-- --------------------------------------------------------

--
-- Table structure for table `kost_facilities`
--

CREATE TABLE `kost_facilities` (
  `kost_id` int NOT NULL,
  `facility_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kost_facilities`
--

INSERT INTO `kost_facilities` (`kost_id`, `facility_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8);

-- --------------------------------------------------------

--
-- Table structure for table `kost_images`
--

CREATE TABLE `kost_images` (
  `id` int NOT NULL,
  `kost_id` int NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kost_images`
--

INSERT INTO `kost_images` (`id`, `kost_id`, `image_path`) VALUES
(1, 1, '685632c0e13ca-Cuplikan layar 2025-04-27 140334.png');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `id` int NOT NULL,
  `kost_id` int NOT NULL,
  `user_id` int NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('menunggu_konfirmasi','aktif','selesai','dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rentals`
--

INSERT INTO `rentals` (`id`, `kost_id`, `user_id`, `tanggal_mulai`, `tanggal_selesai`, `status`, `created_at`) VALUES
(1, 1, 1, '2025-07-01', '2025-06-21', 'selesai', '2025-06-21 04:40:50');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `kost_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` tinyint NOT NULL,
  `ulasan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `balasan_pemilik` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `kost_id`, `user_id`, `rating`, `ulasan`, `created_at`, `balasan_pemilik`) VALUES
(1, 1, 1, 5, 'biasa aja sih', '2025-06-21 05:16:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `role` enum('admin','pemilik','penyewa') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `password`, `phone_number`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Muhammad Yasir', 'yasir', 'yasir@gmail.com', '$2y$10$ft8NgY5gRhJ.ZV2iPY/L/.hpn7/H54.T.z1siT3kYYoIYzEXCBgXy', '08617126123', 'penyewa', '2025-06-21 03:57:24', '2025-06-21 03:57:24'),
(3, 'Muhammad Yasir', 'yasirpemilik', 'yasinakano2nd@gmail.com', '$2y$10$nPbb5INLTMO6eESSB.im5u73..ptRv9oelfbg.QdG961B3BxO2SX.', '08617126123', 'pemilik', '2025-06-21 04:05:01', '2025-06-21 04:05:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kost_id` (`kost_id`);

--
-- Indexes for table `kosts`
--
ALTER TABLE `kosts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `kost_facilities`
--
ALTER TABLE `kost_facilities`
  ADD PRIMARY KEY (`kost_id`,`facility_id`),
  ADD KEY `facility_id` (`facility_id`);

--
-- Indexes for table `kost_images`
--
ALTER TABLE `kost_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kost_id` (`kost_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kost_id` (`kost_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kost_id` (`kost_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kosts`
--
ALTER TABLE `kosts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kost_images`
--
ALTER TABLE `kost_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`kost_id`) REFERENCES `kosts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kosts`
--
ALTER TABLE `kosts`
  ADD CONSTRAINT `kosts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kost_facilities`
--
ALTER TABLE `kost_facilities`
  ADD CONSTRAINT `kost_facilities_ibfk_1` FOREIGN KEY (`kost_id`) REFERENCES `kosts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kost_facilities_ibfk_2` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kost_images`
--
ALTER TABLE `kost_images`
  ADD CONSTRAINT `kost_images_ibfk_1` FOREIGN KEY (`kost_id`) REFERENCES `kosts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`kost_id`) REFERENCES `kosts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`kost_id`) REFERENCES `kosts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
