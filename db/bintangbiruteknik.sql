-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 15, 2026 at 08:37 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bintangbiruteknik`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cashflow`
--

CREATE TABLE `tbl_cashflow` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `tipe` enum('Kredit','Debit') NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_cashflow`
--

INSERT INTO `tbl_cashflow` (`id`, `tanggal`, `tipe`, `total`, `keterangan`, `created_by`, `created_at`) VALUES
(1, '2026-01-13', 'Kredit', 25000.00, '', 'Andi', '2026-02-09 21:15:57'),
(2, '2026-01-19', 'Debit', 10000.00, '', 'Andi', '2026-02-09 21:16:19'),
(3, '2026-02-10', 'Kredit', 123450.00, 'ini keterangan', 'test123', '2026-02-09 22:57:12');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_kapal`
--

CREATE TABLE `tbl_kapal` (
  `kapal_id` int(11) NOT NULL,
  `nama_kapal` varchar(100) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_kapal`
--

INSERT INTO `tbl_kapal` (`kapal_id`, `nama_kapal`, `vendor_id`, `keterangan`) VALUES
(2, 'efg', 6, ''),
(3, 'abc', 5, 'ini keterangan'),
(4, 'crudtest1', 3, 'crud kapal test 1');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_layanan`
--

CREATE TABLE `tbl_layanan` (
  `layanan_id` int(11) NOT NULL,
  `layanan_kode` varchar(50) NOT NULL,
  `layanan_nama` varchar(100) NOT NULL,
  `layanan_harga` decimal(15,2) DEFAULT 0.00,
  `layanan_keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_layanan`
--

INSERT INTO `tbl_layanan` (`layanan_id`, `layanan_kode`, `layanan_nama`, `layanan_harga`, `layanan_keterangan`) VALUES
(1, '', 'Docking', 100000.00, 'ini ket'),
(2, '', 'Perbaikan Mesin', 100000.00, 'ini ket'),
(3, '', 'Cat Kapal', 10000.00, 'ini ket'),
(4, '', 'Ganti Balok', 10000.00, 'ini ket');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pekerja`
--

CREATE TABLE `tbl_pekerja` (
  `pekerja_id` int(11) NOT NULL,
  `pekerja_nama` varchar(100) DEFAULT NULL,
  `pekerja_kontak` varchar(50) DEFAULT NULL,
  `pekerja_alamat` text DEFAULT NULL,
  `pekerja_keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_pekerja`
--

INSERT INTO `tbl_pekerja` (`pekerja_id`, `pekerja_nama`, `pekerja_kontak`, `pekerja_alamat`, `pekerja_keterangan`) VALUES
(4, 'abc', '1234', 'abc', NULL),
(5, 'def', '4567', 'def', NULL),
(9, 'crud test 1', '19283uihabj!@398uj', '18923hUIQFJKAW!(*@UH', 'ini keterangan');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_servis`
--

CREATE TABLE `tbl_servis` (
  `svs_id` int(11) NOT NULL,
  `no_wo` varchar(50) NOT NULL,
  `nama_kapal` varchar(100) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `tgl_masuk` date NOT NULL,
  `tgl_keluar` date DEFAULT NULL,
  `status` enum('On Progress','On Hold','Done','Canceled') DEFAULT 'On Progress',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_harga` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_servis`
--

INSERT INTO `tbl_servis` (`svs_id`, `no_wo`, `nama_kapal`, `vendor_id`, `tgl_masuk`, `tgl_keluar`, `status`, `keterangan`, `created_at`, `total_harga`) VALUES
(1, '12poijhubjas', 'efg', 6, '2026-02-04', '2026-05-14', 'On Progress', 'test crud servis', '2026-02-08 22:16:43', 220.00),
(2, '9128u37y6t1', 'crudtest1', 3, '2025-11-05', '2026-02-19', 'Done', ';koaijcuhb@(*&hbjnqkax', '2026-02-09 15:28:05', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_servis_layanan`
--

CREATE TABLE `tbl_servis_layanan` (
  `svs_id` int(11) NOT NULL,
  `layanan_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_servis_layanan`
--

INSERT INTO `tbl_servis_layanan` (`svs_id`, `layanan_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(2, 1),
(2, 3),
(2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_servis_pekerja`
--

CREATE TABLE `tbl_servis_pekerja` (
  `svs_id` int(11) NOT NULL,
  `pekerja_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_servis_pekerja`
--

INSERT INTO `tbl_servis_pekerja` (`svs_id`, `pekerja_id`) VALUES
(1, 4),
(1, 5),
(2, 4),
(2, 9);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `usr_id` int(11) NOT NULL,
  `usr_nama` varchar(100) DEFAULT NULL,
  `usr_email` varchar(100) DEFAULT NULL,
  `usr_username` varchar(80) DEFAULT NULL,
  `usr_pass` varchar(255) DEFAULT NULL,
  `usr_alamat` text DEFAULT NULL,
  `usr_tlp` varchar(20) DEFAULT NULL,
  `usr_status` varchar(20) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`usr_id`, `usr_nama`, `usr_email`, `usr_username`, `usr_pass`, `usr_alamat`, `usr_tlp`, `usr_status`, `vendor_id`, `created_at`) VALUES
(1, 'Andi', 'andi@bbteknik.com', 'andi', '$2y$10$/PDZGzDYek4GC0076XHyz.0ojaoX1gqgOABO3Cy2Evle.ZScxWrvW', NULL, NULL, 'Admin', NULL, '2026-01-28 16:21:33'),
(2, 'Resha', 'resha@bbteknik.com', 'resha', '$2y$12$6Su36FquPl5hVGGw15gb4ecPBx.wN8KbpNLt/eYBGRHS2iPH/2R2C', NULL, NULL, 'Admin', 1, '2026-01-28 16:21:33'),
(3, 'HM', 'hm@customer.com', 'hm', '$2y$12$6Su36FquPl5hVGGw15gb4ecPBx.wN8KbpNLt/eYBGRHS2iPH/2R2C', NULL, NULL, 'Customer', 7, '2026-01-28 16:21:33'),
(4, 'YP', 'yp@customer.com', 'yp', '$2y$12$6Su36FquPl5hVGGw15gb4ecPBx.wN8KbpNLt/eYBGRHS2iPH/2R2C', NULL, NULL, 'Customer', 2, '2026-01-28 16:21:33'),
(5, 'CongHan', 'conghan@customer.com', 'conghan', '$2y$12$6Su36FquPl5hVGGw15gb4ecPBx.wN8KbpNLt/eYBGRHS2iPH/2R2C', NULL, NULL, 'Customer', 3, '2026-01-28 16:21:33'),
(9, 'A Lo', 'alo@customer.com', 'ALo', '$2y$10$6rjdA8Xderqy1wJfW5YTkeDP9qzYMs3I3FWULucczM54MxGbUO64q', NULL, NULL, 'Customer', 4, '2026-02-13 20:02:33'),
(10, 'Hartono', 'Hartono@customer.com', 'Hartono', '$2y$10$FPX5RMymP3.ssLlQnuLkLODZlQ5jtH19.CGSdKulqfPocaERAvsma', NULL, NULL, 'Customer', 5, '2026-02-13 20:06:46'),
(11, 'CV Baruna Jaya', 'barunajaya@customer.com', 'BarunaJaya', '$2y$10$XutnUFCA3CFSF4bWyuZbje4G35hA.esZr/glXGEoRellg9m8whndi', NULL, NULL, 'Customer', 6, '2026-02-13 20:07:57');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_vendor`
--

CREATE TABLE `tbl_vendor` (
  `vendor_id` int(11) NOT NULL,
  `vendor_name` varchar(150) NOT NULL,
  `vendor_alamat` varchar(255) DEFAULT NULL,
  `vendor_kontak` varchar(100) DEFAULT NULL,
  `vendor_keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_vendor`
--

INSERT INTO `tbl_vendor` (`vendor_id`, `vendor_name`, `vendor_alamat`, `vendor_kontak`, `vendor_keterangan`, `created_at`) VALUES
(1, 'BBTeknik', NULL, NULL, 'Vendor BBTeknik', '2026-02-06 13:15:20'),
(2, 'YP', NULL, NULL, 'Vendor YP', '2026-02-06 13:15:20'),
(3, 'Cong Han', NULL, NULL, 'Vendor Cong Han', '2026-02-06 13:15:20'),
(4, 'A Lo', NULL, NULL, 'Vendor A Lo', '2026-02-06 13:15:20'),
(5, 'Hartono', NULL, NULL, 'Vendor Hartono', '2026-02-06 13:15:20'),
(6, 'CV Baruna Jaya', 'Alamat CV Baruna Jaya', '', 'Vendor Baruna Jaya', '2026-02-06 13:15:20'),
(7, 'HM', 'Alaman HM', '', 'Vendor HM', '2026-02-06 13:15:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_cashflow`
--
ALTER TABLE `tbl_cashflow`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_kapal`
--
ALTER TABLE `tbl_kapal`
  ADD PRIMARY KEY (`kapal_id`),
  ADD KEY `fk_kapal_vendor` (`vendor_id`);

--
-- Indexes for table `tbl_layanan`
--
ALTER TABLE `tbl_layanan`
  ADD PRIMARY KEY (`layanan_id`);

--
-- Indexes for table `tbl_pekerja`
--
ALTER TABLE `tbl_pekerja`
  ADD PRIMARY KEY (`pekerja_id`);

--
-- Indexes for table `tbl_servis`
--
ALTER TABLE `tbl_servis`
  ADD PRIMARY KEY (`svs_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `tbl_servis_layanan`
--
ALTER TABLE `tbl_servis_layanan`
  ADD PRIMARY KEY (`svs_id`,`layanan_id`),
  ADD KEY `layanan_id` (`layanan_id`);

--
-- Indexes for table `tbl_servis_pekerja`
--
ALTER TABLE `tbl_servis_pekerja`
  ADD PRIMARY KEY (`svs_id`,`pekerja_id`),
  ADD KEY `pekerja_id` (`pekerja_id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`usr_id`),
  ADD UNIQUE KEY `usr_email` (`usr_email`),
  ADD UNIQUE KEY `usr_username` (`usr_username`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `tbl_vendor`
--
ALTER TABLE `tbl_vendor`
  ADD PRIMARY KEY (`vendor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_cashflow`
--
ALTER TABLE `tbl_cashflow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_kapal`
--
ALTER TABLE `tbl_kapal`
  MODIFY `kapal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_layanan`
--
ALTER TABLE `tbl_layanan`
  MODIFY `layanan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_pekerja`
--
ALTER TABLE `tbl_pekerja`
  MODIFY `pekerja_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_servis`
--
ALTER TABLE `tbl_servis`
  MODIFY `svs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `usr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_vendor`
--
ALTER TABLE `tbl_vendor`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_kapal`
--
ALTER TABLE `tbl_kapal`
  ADD CONSTRAINT `fk_kapal_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `tbl_vendor` (`vendor_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tbl_servis`
--
ALTER TABLE `tbl_servis`
  ADD CONSTRAINT `tbl_servis_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `tbl_vendor` (`vendor_id`);

--
-- Constraints for table `tbl_servis_layanan`
--
ALTER TABLE `tbl_servis_layanan`
  ADD CONSTRAINT `tbl_servis_layanan_ibfk_1` FOREIGN KEY (`svs_id`) REFERENCES `tbl_servis` (`svs_id`),
  ADD CONSTRAINT `tbl_servis_layanan_ibfk_2` FOREIGN KEY (`layanan_id`) REFERENCES `tbl_layanan` (`layanan_id`);

--
-- Constraints for table `tbl_servis_pekerja`
--
ALTER TABLE `tbl_servis_pekerja`
  ADD CONSTRAINT `tbl_servis_pekerja_ibfk_1` FOREIGN KEY (`svs_id`) REFERENCES `tbl_servis` (`svs_id`),
  ADD CONSTRAINT `tbl_servis_pekerja_ibfk_2` FOREIGN KEY (`pekerja_id`) REFERENCES `tbl_pekerja` (`pekerja_id`);

--
-- Constraints for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD CONSTRAINT `fk_user_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `tbl_vendor` (`vendor_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
