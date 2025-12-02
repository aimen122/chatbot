-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2025 at 11:04 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `glaxit_chatbot`
--

-- --------------------------------------------------------

--
-- Table structure for table `marketing_members`
--

CREATE TABLE `marketing_members` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marketing_members`
--

INSERT INTO `marketing_members` (`id`, `name`, `email`, `created_at`) VALUES
(4, 'moeed', 'mxeedahmed522@gmail.com', '2025-10-10 07:50:08'),
(6, 'Abdul Moeed', 'wtf@gmail.com', '2025-10-10 23:05:03'),
(7, 'hello', 'example@gmail.com', '2025-10-14 17:24:57'),
(8, 'uzair', 'uzair@gmail.com', '2025-10-15 13:47:55'),
(9, 'aimen', 'aimenatif080@gmail.com', '2025-10-16 12:18:23'),
(10, 'Nuzhat Hamid', 'nuzhat.glaxit@gmail.com', '2025-10-22 08:23:10'),
(11, 'Pakistan', 'atifaimen21@gmail.com', '2025-10-28 09:38:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `marketing_members`
--
ALTER TABLE `marketing_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `marketing_members`
--
ALTER TABLE `marketing_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
