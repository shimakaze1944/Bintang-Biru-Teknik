-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 28, 2026 at 05:22 PM
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
-- Table structure for table `tbl_kapal`
--

CREATE TABLE `tbl_kapal` (
  `kapal_id` int(11) NOT NULL,
  `nama_kapal` varchar(100) DEFAULT NULL,
  `nama_pemilik` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_servis`
--

CREATE TABLE `tbl_servis` (
  `svs_id` int(11) NOT NULL,
  `nama_kapal` varchar(100) DEFAULT NULL,
  `nama_pekerja` varchar(100) DEFAULT NULL,
  `usr_email` varchar(100) DEFAULT NULL,
  `tgl_masuk` date DEFAULT NULL,
  `tgl_keluar` date DEFAULT NULL,
  `total_service` decimal(15,2) DEFAULT 0.00,
  `biaya_pekerja` decimal(15,2) DEFAULT 0.00,
  `biaya_bahan` decimal(15,2) DEFAULT 0.00,
  `status` enum('canceled','on progress','on hold','done') DEFAULT 'on progress',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `usr_id` int(11) NOT NULL,
  `usr_nama` varchar(100) DEFAULT NULL,
  `usr_email` varchar(100) DEFAULT NULL,
  `usr_pass` varchar(255) DEFAULT NULL,
  `usr_alamat` text DEFAULT NULL,
  `usr_tlp` varchar(20) DEFAULT NULL,
  `usr_status` enum('Admin','Customer') DEFAULT 'Customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`usr_id`, `usr_nama`, `usr_email`, `usr_pass`, `usr_alamat`, `usr_tlp`, `usr_status`, `created_at`) VALUES
(1, 'Andi', 'andi@bbteknik.com', '$2y$10$Q4DncajX/hoLJf1d8fRzOehKtm0I7sPBKdpVTT9q2Qv0b7nOEFuHq', NULL, NULL, 'Admin', '2026-01-28 16:21:33'),
(2, 'Resha', 'resha@bbteknik.com', '$2y$10$Q4DncajX/hoLJf1d8fRzOehKtm0I7sPBKdpVTT9q2Qv0b7nOEFuHq', NULL, NULL, 'Admin', '2026-01-28 16:21:33'),
(3, 'HM', 'hm@customer.com', '$2y$10$Q4DncajX/hoLJf1d8fRzOehKtm0I7sPBKdpVTT9q2Qv0b7nOEFuHq', NULL, NULL, 'Customer', '2026-01-28 16:21:33'),
(4, 'YP', 'yp@customer.com', '$2y$10$Q4DncajX/hoLJf1d8fRzOehKtm0I7sPBKdpVTT9q2Qv0b7nOEFuHq', NULL, NULL, 'Customer', '2026-01-28 16:21:33'),
(5, 'CongHan', 'conghan@customer.com', '$2y$10$Q4DncajX/hoLJf1d8fRzOehKtm0I7sPBKdpVTT9q2Qv0b7nOEFuHq', NULL, NULL, 'Customer', '2026-01-28 16:21:33');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_vendor`
--

CREATE TABLE `tbl_vendor` (
  `vendor_id` int(11) NOT NULL,
  `vendor_nama` varchar(100) DEFAULT NULL,
  `vendor_kontak` varchar(50) DEFAULT NULL,
  `vendor_alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_kapal`
--
ALTER TABLE `tbl_kapal`
  ADD PRIMARY KEY (`kapal_id`);

--
-- Indexes for table `tbl_servis`
--
ALTER TABLE `tbl_servis`
  ADD PRIMARY KEY (`svs_id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`usr_id`),
  ADD UNIQUE KEY `usr_email` (`usr_email`);

--
-- Indexes for table `tbl_vendor`
--
ALTER TABLE `tbl_vendor`
  ADD PRIMARY KEY (`vendor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_kapal`
--
ALTER TABLE `tbl_kapal`
  MODIFY `kapal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_servis`
--
ALTER TABLE `tbl_servis`
  MODIFY `svs_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `usr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_vendor`
--
ALTER TABLE `tbl_vendor`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
