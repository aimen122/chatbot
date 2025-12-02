-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 27, 2025 at 09:43 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `contact`, `company`, `created_at`) VALUES
(139, 'subhan', 'jonnysins12190@gamil.com', '03203167821', '', '2025-10-20 11:46:17'),
(140, 'moeed', 'example@gmail.com', '003232323444', '', '2025-10-20 11:55:18'),
(141, 'uzair', 'uzair@gmail.com', '032323444', '', '2025-10-20 12:00:15'),
(142, 'moiz', 'example123@gmail.com', '003232323444', '', '2025-10-21 07:05:50'),
(143, 'arslan', 'arslan@gmail.com', '12123304344', '', '2025-10-21 07:10:17'),
(144, 'aleez', 'atifaimen21@gmail.com', '03180696392', 'Glaxit', '2025-10-21 07:11:50'),
(145, 'shan', 'sharthaj@gmail.com', '032323444', '', '2025-10-21 07:16:00'),
(146, 'shataj', 'shataj@gmail.com', '1234567', '', '2025-10-21 07:19:38'),
(147, 'herisss', 'wdwewe@gmail.com', '12345678', '', '2025-10-21 07:25:09'),
(148, 'shah', 'shah@gmail.com', '12345678', '', '2025-10-21 07:29:07'),
(149, 'shani', 'shani@gmail.com', '097775', '', '2025-10-21 07:33:25'),
(150, 'charli', 'charli@gmail.com', '1234567', '', '2025-10-21 07:37:09'),
(151, 'hashir', 'ashir@gmail.com', '12345678', '', '2025-10-21 07:48:08'),
(152, 'ddadd', 'asas@gmail.com', '76745646', '', '2025-10-21 08:08:31'),
(153, 'shatat', 'shata@gmail.com', '7867564534', '', '2025-10-21 08:48:48'),
(154, 'shahwaiz', 'sahwaiz@gmail.com', '333873678638', '', '2025-10-22 06:07:11'),
(155, 'shyad', 'sasa@gmail.com', '5656465646', '', '2025-10-22 07:07:28'),
(156, 'Nuzhat Hamid', 'nuzhat.glaxit@gmail.com', '123456784567', 'Glaxit', '2025-10-22 08:04:34'),
(157, 'mnmb', 'mnmbjm@gmail.com', '7678688', '', '2025-10-22 09:35:38'),
(158, 'moeed', 'mxeed12@gmail.com', '03125675945', 'glaxit', '2025-10-22 13:09:40'),
(159, 'moeed', 'ahmed12@gmail.com', '03125675976', 'glaxit', '2025-10-22 17:47:00'),
(160, 'ahmed', 'ahmedshah@gmail.com', '03125675879', 'glaxit', '2025-10-22 17:51:59'),
(161, 'Faraz Rafique', 'farazrafiquef@gmail.com', '00', '', '2025-10-22 18:32:30'),
(162, 'uzair', 'uzair123@gmail.com', '03124567678', 'Glaxit', '2025-10-22 19:56:26'),
(163, 'ahmed', 'ahmed34@gmail.com', '03124567678', 'Glaxit', '2025-10-22 20:04:50'),
(164, 'raghab', 'raghab123@gmail.com', '03125675987', 'Glaxit', '2025-10-22 20:07:48'),
(165, 'talha', 'talha123@gmail.com', '03125675987', 'Glaxit', '2025-10-22 20:17:28'),
(166, 'tabish', 'tabish123@gmail.com', '03125675989', 'Glaxit', '2025-10-22 20:26:58'),
(167, 'dsds', 'rahss@gmail.com', '121312344', 'Glaxit', '2025-10-22 21:26:52'),
(168, 'hammad', 'hammad123@gmail.com', '03125675987', 'Glaxit', '2025-10-22 21:36:48'),
(169, 'hamid', 'hamid123@gmail.com', '03125675976', 'Glaxit', '2025-10-22 22:24:44'),
(170, 'Ashir', 'Ashir123@gmail.com', '03125675945', 'Glaxit', '2025-10-22 22:32:31'),
(171, 'wewewe', 'qwqw123@gmail.com', '0312456789', 'rapinov', '2025-10-23 09:34:31'),
(172, 'hammadshah', 'hammasd@gmail.com', '03125676867', 'Glaxit', '2025-10-23 09:37:28'),
(173, 'aimen', 'aimenatif080@gmail.com', '03180696392', 'glaxit', '2025-10-27 07:18:16'),
(174, 'moiz', 'moiz123@gmail.com', '32423554535', 'glaxit', '2025-10-27 07:37:53'),
(175, 'Nuzhat', 'app@spmetesting.com', '23690-9821332', 'glaxit', '2025-10-27 07:52:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
