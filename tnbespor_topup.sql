-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 17, 2026 at 11:31 PM
-- Server version: 11.4.9-MariaDB
-- PHP Version: 8.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tnbespor_topup`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$12$ccTkGD7B5V2LGOs9IkV8VusOd1SfUSFEhYExLs5LesqtQ6G1DvicO');

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `wallet_number` varchar(50) DEFAULT NULL,
  `trx_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `deposits`
--

INSERT INTO `deposits` (`id`, `user_id`, `amount`, `method`, `wallet_number`, `trx_id`, `status`, `created_at`) VALUES
(1, 7, 50.00, 'Nagad ', 'à¦†à¦®à¦¿ à¦¤à§‹à¦®à¦°à¦¾ à¦¬à¦¿à¦— à¦¸à¦¿à¦²à¦¿à¦', 'Adfguhvv', 'pending', '2025-11-28 17:38:14'),
(2, 9, 50.00, 'Nagad ', 'à¦•à¦¿à¦°à§‡ à¦­à¦¾à¦‡ à¦à¦Ÿà¦¾ à¦¤à§‹ PhP', 'à¦à¦Ÿà¦¾ à¦ à¦¿à¦• à¦¹à§Ÿ à¦¨à¦¾à¦‡, html code à¦•à¦‡', 'pending', '2025-11-28 17:45:28'),
(3, 16, 500.00, 'Bkash', '01858609070', 'DEV-HAMIM', 'pending', '2025-11-29 00:24:34'),
(4, 22, 10.00, 'Bkash', '01333600774', 'YX', 'pending', '2025-11-29 03:09:35'),
(5, 24, 20.00, 'Bkash', 'Hjh', 'Uiggg', 'pending', '2025-11-29 03:38:00'),
(6, 25, 200.00, 'Bkash', '01992242059', 'Mshjsksh', 'pending', '2025-11-29 04:00:17'),
(7, 6, 100.00, 'Bkash', '01858609070', '743TRB7H', 'approved', '2025-11-29 04:28:35'),
(8, 22, 8800.00, 'Bkash', '01333600774', 'vsjsjssbshsb', 'pending', '2025-11-29 10:44:13'),
(9, 3, 50.00, 'Bkash', '01822335566', 'DJAJFECC', 'pending', '2025-11-30 10:48:33'),
(10, 46, 1000.00, 'Bkash', '01700000000', 'CKJHGFDSAER', 'pending', '2025-12-02 10:52:45'),
(11, 48, 500.00, 'Bkash', '555', '50', 'pending', '2025-12-02 17:58:35'),
(12, 50, 1000.00, 'Bkash', '01921417215', 'Nzjzizgdgsjsidj', 'pending', '2025-12-04 00:59:13'),
(13, 57, 200.00, 'Bkash', '01787878777', 'TXJKVYKC', 'pending', '2025-12-13 03:46:47'),
(14, 63, 100.00, 'Bkash', '100', 'TX Id', 'pending', '2025-12-13 12:04:49'),
(15, 76, 1.00, 'Bkash', 'Check ', 'Check âœ…', 'approved', '2025-12-14 08:43:32'),
(16, 78, 280.00, 'Nagad ', '01960097047', 'GXJGXFHFUCF', 'pending', '2025-12-14 13:05:13'),
(17, 81, 50.00, 'Bkash', '0199999999999', 'Di nai vai accept koro', 'pending', '2025-12-17 15:06:22'),
(18, 82, 2.00, 'Bkash', '01471559413', 'KG52719', 'pending', '2025-12-18 09:38:25'),
(19, 25, 500.00, 'Bkash', '859965896577', 'Mshjsksh', 'pending', '2025-12-22 07:56:45'),
(20, 83, 500.00, 'Bkash', '012464348941', 'jsgdlhahvx', 'pending', '2025-12-22 08:38:19'),
(21, 84, 500.00, 'Bkash', '01774496408', 'hy5e4573fgg5', 'approved', '2025-12-25 12:46:58'),
(22, 84, 99999999.99, 'Bkash', '01774496408', 'hy5e4573fgg5', 'approved', '2025-12-26 04:26:03'),
(23, 85, 100.00, 'Bkash', '01986590416', 'Ejshdksgh', 'pending', '2025-12-27 09:12:44'),
(24, 85, 500.00, 'Bkash', '01986590416', 'Gritdiydiut', 'pending', '2025-12-27 18:45:00'),
(25, 86, 100.00, 'Bkash', '01742505601', 'Bhai,takapatainai,ha,ha', 'pending', '2025-12-28 14:11:45'),
(26, 88, 50.00, 'Nagad ', '01773925477', 'WWJAJJS229', 'pending', '2025-12-30 04:29:27'),
(27, 93, 200.00, 'Nagad ', '01858609070', 'Juuu', 'pending', '2025-12-30 17:28:03'),
(28, 96, 100.00, 'Bkash', '01858609070', 'xxx', 'pending', '2025-12-31 19:40:40'),
(29, 104, 200.00, 'Bkash', '01883037715', 'Vvcdvfrd', 'pending', '2026-01-03 05:05:18'),
(30, 110, 1000.00, 'Bkash', '+44134174900', 'mubat1@gmail.com', 'pending', '2026-01-05 09:59:06'),
(31, 114, 1000.00, 'Nagad ', '01858609070', '01858609070', 'pending', '2026-01-06 08:00:45'),
(32, 42, 500.00, 'Bkash', '01858609070', '01858609070', 'pending', '2026-01-06 14:49:31'),
(33, 124, 5555.00, 'Nagad ', '01751485429', '258ygicydu', 'pending', '2026-01-10 04:13:50'),
(34, 126, 500.00, 'Bkash', '01858609070', 'à¦¯à§Žà§Žà§à¦¯à¦¥à¦°à§Ž', 'pending', '2026-01-10 20:01:06'),
(35, 127, 500.00, 'Bkash', '01981966788', 'Vhgjhgkhgu', 'pending', '2026-01-11 10:25:22'),
(36, 131, 500.00, 'Bkash', '01604343616', 'Rihcbo', 'pending', '2026-01-13 00:29:13'),
(37, 132, 5.00, 'Bkash', '01858609070', 'J', 'pending', '2026-01-13 16:45:58'),
(38, 134, 200.00, 'Bkash', '01788745776', 'EDSCXG', 'pending', '2026-01-16 09:22:13'),
(39, 135, 500.00, 'Bkash', '0172638937', 'Snskkbw ma', 'pending', '2026-01-17 17:10:12'),
(40, 142, 1000.00, 'Bkash', '01921416279', 'Zbksjdgddbkd', 'pending', '2026-01-20 17:02:45'),
(41, 150, 50.00, 'Bkash', '01858609070', 'stvlphvsa', 'pending', '2026-01-24 18:05:23'),
(42, 161, 10.00, 'Bkash', '01858609070', '01858609070', 'pending', '2026-01-30 17:17:00'),
(43, 179, 2000.00, 'Bkash', '01611792405', 'Snsnmdn', 'pending', '2026-02-07 16:58:11'),
(44, 196, 20.00, 'Bkash', '01858609070', 'Jabsbddge', 'pending', '2026-02-16 15:10:29'),
(45, 2, 40.00, 'Bkash', 'Ycugvgvi', 'Gcivgigv', 'approved', '2026-05-10 17:49:40'),
(46, 2, 10.00, 'bKash', '01814155176', '5096', 'approved', '2026-05-10 20:11:40'),
(47, 2, 0.00, 'Wallet', 'Wallet Deducted', 'WALLET_6a01e46c9a263', 'approved', '2026-05-11 14:15:08'),
(48, 2, 10.00, 'Wallet', 'Wallet Deducted', 'WALLET_6a01e4813f8eb', 'approved', '2026-05-11 14:15:29'),
(49, 2, 10.00, 'Wallet', 'Wallet Deducted', 'WALLET_6a01e52859d2c', 'approved', '2026-05-11 14:18:16'),
(50, 2, 0.00, 'Wallet', 'Wallet Deducted', 'WALLET_6a01e921b7fc2', 'approved', '2026-05-11 14:35:13'),
(51, 2, 0.00, 'Wallet', 'Wallet Deducted', 'WALLET_6a01e983f35b4', 'approved', '2026-05-11 14:36:51'),
(52, 2, 0.00, 'Wallet', '', '', 'pending', '2026-05-14 06:59:34'),
(53, 2, 0.00, 'Wallet', '', '', 'pending', '2026-05-14 07:00:31'),
(54, 2, 1000.00, 'bKash', '01814155176', 'Bsjsjsj', 'approved', '2026-05-14 07:10:18'),
(55, 2, 999999.00, 'bKash', '01814155176', 'Bsjsjsj', 'approved', '2026-05-14 07:45:04'),
(56, 2, 100.00, 'bKash', '01814155176', 'Bbbbb', 'approved', '2026-05-14 19:18:35'),
(57, 2, 30.00, 'Auto Pay', NULL, 'AUTO-6A09D89B7671B', '', '2026-05-17 15:02:51'),
(58, 2, 304.00, 'Auto Pay', NULL, 'AUTO-6A09FA4425586', '', '2026-05-17 17:26:28');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` enum('uid','voucher') DEFAULT 'uid',
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `name`, `type`, `description`, `image`) VALUES
(9, 'UID TOPUP BD ', 'uid', 'No rules', 'uploads/game_1779021030.jpg'),
(10, 'Weekly', 'uid', 'Nk Rules', 'uploads/game_1779021053.jpg'),
(11, 'Monthly ', 'uid', 'No Rules', 'uploads/game_1779021086.jpg'),
(12, 'Weekly & Monthly', 'uid', 'No Rules', 'uploads/game_1779021102.jpg'),
(13, 'Weekly Lite', 'uid', 'Null', 'uploads/game_1779021123.jpg'),
(14, 'Evo Access ', 'uid', 'Null', 'uploads/game_1779021141.jpg'),
(15, 'UniPin Code', 'voucher', 'Null', 'uploads/game_1779031734.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `game_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `player_id` varchar(100) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `game_id`, `product_id`, `amount`, `status`, `player_id`, `transaction_id`, `payment_method`, `created_at`) VALUES
(1, 12, 1, 1, 22.00, 'completed', '3055837579', 'DSFXVCGGV', 'Bkash', '2025-11-28 19:18:38'),
(2, 18, 1, 1, 22.00, 'completed', '7454101043', 'Vai takar obabðŸ˜¢', 'Bkash', '2025-11-29 01:58:28'),
(3, 12, 2, 9, 20.00, 'completed', 'Voucher Request', 'DSFXVCGGV', 'Bkash', '2025-11-29 03:11:10'),
(4, 19, 1, 1, 22.00, 'completed', '283682632', 'jsktblsf', 'Bkash', '2025-11-29 04:45:12'),
(5, 29, 3, 8, 5.00, 'completed', '3618274647', 'afasf542', 'Bkash', '2025-11-29 06:33:57'),
(6, 28, 3, 8, 5.00, 'completed', '9597978', '58484848', 'Bkash', '2025-11-29 06:34:58'),
(7, 22, 1, 1, 22.00, 'completed', 'https://prime-topup.iceiy.com/index.php', 'vsjsjssbshsbb', 'Bkash', '2025-11-29 10:43:14'),
(8, 12, 1, 1, 22.00, 'completed', '3055837579', 'DSFXVCGGV', 'Bkash', '2025-11-29 13:37:17'),
(9, 35, 3, 8, 5.00, 'completed', 'Api add korcho ? ', 'Kalke bikale line a thako ', 'Bkash', '2025-11-29 14:58:41'),
(10, 36, 3, 8, 5.00, 'completed', 'Bkknbuibb', 'Gkkbbuiikk', 'Bkash', '2025-11-29 15:41:49'),
(11, 42, 1, 7, 30.00, 'pending', '2512222907', '01858609070', 'Bkash', '2025-12-01 16:18:53'),
(12, 42, 2, 11, 420.00, 'pending', 'Voucher Request', '01858609070', 'Bkash', '2025-12-01 16:20:08'),
(13, 10, 3, 8, 5.00, 'pending', '7283560779', 'CLA7190GSX', 'Bkash', '2025-12-10 16:32:23'),
(14, 60, 2, 11, 70.00, 'pending', 'Voucher Request', 'Part 2 asar por 100 taka gift korbo', 'Bkash', '2025-12-13 05:46:21'),
(15, 65, 1, 1, 22.00, 'pending', '6918754740', 'ðŸŒŸ Bunny Esports Tournament Rules (Special Match)  âœ… à¦…à¦‚à¦¶à¦—à§à¦°à¦¹à¦£à§‡à¦° à¦¶à¦°à§à¦¤', 'Bkash', '2025-12-13 12:25:17'),
(16, 74, 3, 8, 5.00, 'pending', '6666666666', '888', 'Bkash', '2025-12-14 05:44:33'),
(17, 75, 1, 1, 44.00, 'pending', '6677', 'Yy', 'Bkash', '2025-12-14 08:39:06'),
(18, 78, 1, 1, 22.00, 'pending', '276726762', 'GXHTXHFXUT', 'Nagad ', '2025-12-14 13:04:27'),
(19, 82, 1, 5, 150.00, 'pending', '5802369269', 'KG52719', 'Bkash', '2025-12-18 09:40:12'),
(20, 82, 2, 11, 70.00, 'pending', 'Voucher Request', 'KG52715', 'Bkash', '2025-12-18 09:42:27'),
(21, 82, 1, 6, 750.00, 'pending', '5372916353', '79SU278', 'Nagad ', '2025-12-18 09:43:35'),
(22, 82, 4, 13, 35.00, 'completed', '1726382953', 'GY52823', 'Nagad ', '2025-12-18 09:45:31'),
(23, 25, 2, 9, 20.00, 'completed', 'Voucher Request', 'Mshjsksh', 'Bkash', '2025-12-22 07:57:33'),
(24, 83, 1, 6, 144000.00, 'pending', '10917522', 'Jfcdghdgjfd', 'Bkash', '2025-12-22 08:40:33'),
(25, 89, 2, 9, 20.00, 'pending', 'Voucher Request', '8VXJD6945FH', 'Bkash', '2025-12-30 13:32:55'),
(26, 89, 3, 8, 5.00, 'pending', '01019292929', 'Jjjj$+$+$(', 'Bkash', '2025-12-30 13:34:53'),
(27, 91, 1, 1, 22.00, 'pending', '7720547860', 'Werr', 'Bkash', '2025-12-30 15:19:25'),
(28, 93, 2, 9, 20.00, 'pending', 'Voucher Request', 'Uu', 'Nagad ', '2025-12-30 17:26:58'),
(29, 95, 4, 13, 35.00, 'pending', '76744664478', 'Teggdy7dd', 'Bkash', '2025-12-31 12:49:24'),
(30, 103, 1, 1, 22.00, 'pending', 'agt437', 'gk', 'Nagad ', '2026-01-03 02:10:43'),
(31, 108, 1, 6, 108000.00, 'pending', '12345678', 'AHJSJDKOW', 'Bkash', '2026-01-04 01:35:34'),
(32, 109, 1, 1, 22.00, 'pending', '1961717221', 'FJVV4GJ6', 'Bkash', '2026-01-05 09:33:38'),
(33, 109, 2, 9, 20.00, 'pending', 'Voucher Request', 'H4CKER', 'Bkash', '2026-01-05 09:35:36'),
(34, 109, 2, 9, 20.00, 'pending', 'Voucher Request', 'Gg', 'Bkash', '2026-01-05 09:36:07'),
(35, 110, 1, 1, 22.00, 'pending', '8687676354', 'mubat1@gmail.com', 'Bkash', '2026-01-05 09:59:45'),
(36, 111, 1, 1, 22.00, 'pending', '22', 'mdjahidpro578', 'Bkash', '2026-01-05 10:28:17'),
(37, 116, 1, 6, 750.00, 'pending', '7635607867', 'TR6B5WHA', 'Nagad ', '2026-01-06 11:08:18'),
(38, 125, 4, 13, 35.00, 'pending', '23563727', 'Njsjsjdj', 'Nagad ', '2026-01-10 10:48:08'),
(39, 126, 2, 9, 20.00, 'pending', 'Voucher Request', 'à¦¤à¦¤à¦¯à§Ž', 'Bkash', '2026-01-10 19:59:50'),
(40, 132, 2, 11, 700.00, 'pending', 'Voucher Request', 'WWW.XXXX.COM', 'Nagad ', '2026-01-14 04:15:16'),
(41, 133, 1, 1, 22.00, 'pending', 'UID', 'vKTHKKoVk', 'Bkash', '2026-01-14 13:55:26'),
(42, 138, 1, 1, 66.00, 'pending', '47443469', 'Shiens', 'Bkash', '2026-01-18 20:58:03'),
(43, 139, 1, 1, 22.00, 'pending', '7353853972', 'E6HOO9PLI', 'Nagad ', '2026-01-19 17:21:59'),
(44, 143, 1, 1, 22.00, 'pending', '47865885', 'Ivvthb', 'Nagad ', '2026-01-21 06:20:29'),
(45, 148, 1, 7, 30.00, 'pending', '940232228', 'HTHKNBGD', 'Nagad ', '2026-01-24 08:06:27'),
(46, 154, 1, 5, 150.00, 'pending', '11411062776', 'DAS8JOEOWK', 'Bkash', '2026-01-28 09:54:18'),
(47, 155, 4, 13, 35.00, 'pending', '65456', 'Fffff', 'Bkash', '2026-01-28 14:06:35'),
(48, 158, 1, 1, 22.00, 'pending', '49749643844864', '97979797597497', 'Nagad ', '2026-01-30 07:37:31'),
(49, 160, 1, 6, 1500.00, 'pending', '4333333564565', 'rhfrgeu', 'Bkash', '2026-01-30 14:51:19'),
(50, 164, 1, 1, 22.00, 'pending', '2818855466', 'RWESHG$RH', 'Bkash', '2026-02-01 08:23:19'),
(51, 164, 1, 1, 22.00, 'pending', '2818855466', 'Hidden', 'Bkash', '2026-02-02 14:50:38'),
(52, 166, 4, 13, 35.00, 'pending', '933836388', 'Nsjshsdhdb', 'Bkash', '2026-02-02 15:37:03'),
(53, 168, 1, 6, 750.00, 'pending', '7478530833', '74QY58QP', 'Nagad ', '2026-02-04 07:09:31'),
(54, 169, 1, 1, 66.00, 'pending', '131313', 'Skdjdjdi', 'Bkash', '2026-02-04 13:16:22'),
(55, 182, 1, 1, 22.00, 'pending', '11747492061', 'FHTYFJDHUJ', 'Bkash', '2026-02-09 22:05:53'),
(56, 2, NULL, 16, 10.00, 'completed', '2535040417', NULL, NULL, '2026-05-11 13:21:33'),
(57, 2, NULL, 16, 0.00, 'completed', '2535040417', 'WALLET_6a01e46c9a263', 'Wallet Balance', '2026-05-11 14:15:08'),
(58, 2, NULL, 16, 10.00, 'completed', '25', 'WALLET_6a01e4813f8eb', 'Wallet Balance', '2026-05-11 14:15:29'),
(59, 2, NULL, 16, 10.00, 'completed', '2535040417', 'WALLET_6a01e52859d2c', 'Wallet Balance', '2026-05-11 14:18:16'),
(60, 2, NULL, 14, 0.00, 'completed', '253504040469', 'WALLET_6a01e921b7fc2', 'Wallet Balance', '2026-05-11 14:35:13'),
(61, 2, NULL, 15, 0.00, 'completed', NULL, 'WALLET_6a01e983f35b4', 'Wallet Balance', '2026-05-11 14:36:51'),
(62, 2, 5, 14, 0.00, 'completed', '58168181', 'WAL6A0574507B468', 'Wallet', '2026-05-14 07:05:52'),
(63, 2, 5, 16, 10.00, 'pending', '97649', '5096', 'bKash', '2026-05-14 07:08:18'),
(64, 2, 7, 15, 0.00, 'pending', '', 'WAL6A057580336D5', 'Wallet', '2026-05-14 07:10:56'),
(65, 2, 7, 15, 0.00, 'completed', '', 'WAL6A05768DEC6AD', 'Wallet', '2026-05-14 07:15:25'),
(66, 2, 7, 15, 0.00, 'pending', '', 'WAL6A0577B9624C5', 'Wallet', '2026-05-14 07:20:25'),
(67, 2, 7, 15, 0.00, 'pending', '', 'WAL6A0579F3AB98A', 'Wallet', '2026-05-14 07:29:55'),
(68, 2, 7, 17, 200.00, 'pending', '', 'WAL6A057A3690914', 'Wallet', '2026-05-14 07:31:02'),
(69, 2, 7, 17, 200.00, 'pending', '', 'WAL6A057B3AA4E7D', 'Wallet', '2026-05-14 07:35:22'),
(70, 2, 7, 17, 200.00, 'pending', '', 'WAL6A057C53BFBB6', 'Wallet', '2026-05-14 07:40:03'),
(71, 2, 7, 17, 200.00, 'completed', '', 'WAL6A057D44170D2', 'Wallet', '2026-05-14 07:44:04'),
(72, 2, 7, 17, 200.00, 'completed', '', 'WAL6A057D896E7B7', 'Wallet', '2026-05-14 07:45:13'),
(73, 2, NULL, 0, 11.00, 'completed', NULL, 'AUTO-6A0629AECEFD3', NULL, '2026-05-14 19:59:42'),
(74, 2, NULL, 16, 10.00, 'pending', '20', 'AUTO-6A062A129B10B', NULL, '2026-05-14 20:01:22'),
(75, 2, 5, 16, 10.00, 'pending', '80', 'WAL6A062A699F184', 'Wallet', '2026-05-14 20:02:49'),
(76, 2, 0, 16, 10.00, 'pending', '846464', 'AUTO-6A0634600FD43', 'Auto Payment', '2026-05-14 20:45:20'),
(77, 2, 0, 16, 10.00, 'pending', '8', 'AUTO-6A06346FD35E2', 'Auto Payment', '2026-05-14 20:45:35'),
(78, 2, 0, 17, 200.00, 'pending', '', 'AUTO-6A0819D61426B', 'Auto Payment', '2026-05-16 07:16:38'),
(79, 2, 0, 16, 10.00, 'pending', '588', 'AUTO-6A0819EF61D34', 'Auto Payment', '2026-05-16 07:17:03'),
(80, 2, 0, 16, 10.00, 'pending', '588', 'AUTO-6A081A207823C', 'Auto Payment', '2026-05-16 07:17:52'),
(81, 2, 0, 0, 50.00, 'completed', NULL, 'AUTO-6A081AE5EFFF5', 'Auto Payment', '2026-05-16 07:21:09'),
(82, 2, 0, 0, 350.00, 'pending', 'Wallet Balance', 'AUTO-6A0824E01EAA5', 'Instant Auto', '2026-05-16 08:03:44'),
(83, 2, 5, 16, 10.00, 'completed', '28', 'WAL-6A085377E4F07', 'Wallet', '2026-05-16 11:22:31'),
(84, 2, 0, 0, 20.00, 'pending', 'Wallet Balance', 'AUTO-6A09A4382D077', 'Instant Auto', '2026-05-17 11:19:20'),
(85, 2, 0, 0, 304.00, 'pending', 'Wallet Balance', 'AUTO-6A09A45DA545A', 'Instant Auto', '2026-05-17 11:19:57'),
(86, 8, 9, 18, 23.00, 'cancelled', '3077799485', 'WAL-6A09C5EF58491', 'Wallet', '2026-05-17 13:43:11'),
(87, 8, 9, 18, 23.00, 'pending', '12345678', 'WAL-6A09C712BD010', 'Wallet', '2026-05-17 13:48:02'),
(88, 2, 9, 18, 23.00, 'pending', '28', 'WAL-6A09D8D238901', 'Wallet', '2026-05-17 15:03:46'),
(89, 8, 9, 18, 23.00, 'completed', '12345678', 'WAL-6A09DA979D3F1', 'Wallet', '2026-05-17 15:11:19'),
(90, 2, 15, 19, 10000.00, 'completed', '', 'WAL-6A09E0C9B5E85', 'Wallet', '2026-05-17 15:37:45'),
(91, 2, 15, 19, 10000.00, 'completed', '', 'WAL-6A09EB8D847A7', 'Wallet', '2026-05-17 16:23:41'),
(92, 2, 15, 19, 10000.00, 'completed', '', 'WAL-6A09F13C2C3AC', 'Wallet', '2026-05-17 16:47:56'),
(93, 2, 9, 18, 23.00, 'pending', '9999', 'WAL-6A09F1F896DE8', 'Wallet', '2026-05-17 16:51:04'),
(94, 2, 15, 19, 10000.00, 'completed', '', 'WAL-6A09F49FED0A1', 'Wallet', '2026-05-17 17:02:23'),
(95, 8, 9, 18, 23.00, 'pending', '12345678', 'WAL-6A09FAB2B5D5F', 'Wallet', '2026-05-17 17:28:18');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `qr_image` varchar(255) DEFAULT NULL,
  `number` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `short_desc` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `logo`, `qr_image`, `number`, `description`, `short_desc`) VALUES
(5, 'Bkash', 'uploads/pay_logo_1779024271.png', '', '01814155105', '', 'Send Monry');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `game_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `game_id`, `name`, `price`) VALUES
(1, 1, '25 Diamond ', 22.00),
(2, 1, '50 Diamond ', 40.00),
(3, 1, '100 Diamond ', 75.00),
(4, 1, '500 Diamond ', 200.00),
(5, 1, 'Weekly', 150.00),
(6, 1, 'Monthly ', 750.00),
(7, 1, 'Weekly Lite ', 30.00),
(8, 3, '100 Like ', 5.00),
(9, 2, '25 Diamond ', 20.00),
(10, 2, '50 Diamond ', 35.00),
(11, 2, '100 Diamond ', 70.00),
(12, 2, '50 Diamond ', 35.00),
(13, 4, 'Weekly Lite', 35.00),
(14, 5, 'Test', 0.00),
(15, 7, 'Test', 0.00),
(16, 5, 'Test 2', 10.00),
(17, 7, 'Khankir pola', 200.00),
(18, 9, '25 Daimond', 23.00),
(19, 15, 'Test', 10000.00);

-- --------------------------------------------------------

--
-- Table structure for table `redeem_codes`
--

CREATE TABLE `redeem_codes` (
  `id` int(11) NOT NULL,
  `game_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `status` enum('active','used','expired') DEFAULT 'active',
  `order_id` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `redeem_codes`
--

INSERT INTO `redeem_codes` (`id`, `game_id`, `product_id`, `code`, `status`, `order_id`) VALUES
(1, NULL, 9, 'Up-BD-9977777777877777', 'used', 3),
(2, NULL, 15, 'Wfywuwhwh', 'used', 65),
(3, NULL, 17, 'Jzjsksiieiw', 'used', 68),
(4, NULL, 17, 'Hjxjdjdjdj', 'used', 69),
(5, NULL, 17, 'H', 'used', 70),
(6, NULL, 17, 'Vzjzjhsjsh', 'used', 71),
(7, NULL, 17, 'Xvhddhdhh', 'used', 72),
(8, NULL, 17, 'Vszhjshsh', 'active', 0),
(9, NULL, 19, 'Bssjbsjs', 'active', 0),
(10, NULL, 19, 'Dbbdbdndn', 'active', 0);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `value` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`) VALUES
(1, 'site_name', 'TN TOPUP'),
(2, 'site_desc', 'Best Gaming Top Up Shop'),
(3, 'currency', 'à§³'),
(4, 'marquee_text', 'à¦†à¦®à¦¾à¦¦à§‡à¦° à¦“à¦¯à¦¼à§‡à¦¬à¦¸à¦¾à¦‡à¦Ÿà§‡ à¦¦à¦¿à¦¨à¦°à¦¾à¦¤ à§¨à§ª à¦˜à¦¨à§à¦Ÿà¦¾ à¦…à¦°à§à¦¡à¦¾à¦° à¦•à¦°à¦¤à§‡ à¦ªà¦¾à¦°à¦¬à§‡à¦¨, à¦®à¦¾à¦¤à§à¦° à§©à§¦ à¦¸à§‡à¦•à§‡à¦¨à§à¦¡à§‡ à¦°à§‹à¦¬à¦Ÿà§‡à¦° à¦®à¦¾à¦§à§à¦¯à¦®à§‡ à¦¡à§‡à¦²à¦¿à¦­à¦¾à¦°à¦¿ à¦¦à§‡à¦“à¦¯à¦¼à¦¾ à¦¹à¦¯à¦¼à¥¤ '),
(9, 'logo_url', 'logo_1779021013.png'),
(5, 'marquee_active', '1'),
(6, 'fab_link', 'https://t.me/BanglaBhai_FF'),
(7, 'add_money_video', 'https://youtu.be/GaBzRioOdsc?si=YfOyFQpbM_ZOleZO'),
(8, 'new_pass', ''),
(10, 'fb_url', 'https://t.me/BanglaBhai_FF'),
(11, 'yt_url', 'https://t.me/BanglaBhai_FF'),
(12, 'messenger_url', 'https://t.me/BanglaBhai_FF'),
(13, 'whatsapp_url', 'https://wa.me/8801XXXXXXXXX'),
(14, 'site_logo', 'logo_1778930883.jpg'),
(15, 'name_checking_api_key', 'API-8EUX8QRW'),
(16, 'shohoj_api_key', 'NjsorKw8VH5FUJcIuAdNa7q4ljutGRdkEC3YI3RjGUBHCJ0tya'),
(17, 'shohoj_secret_key', 'NjsorKw8VH5FUJcIuAdNa7q4ljutGRdkEC3YI3RjGUBHCJ0tya'),
(18, 'shohoj_brand_key', 'NjsorKw8VH5FUJcIuAdNa7q4ljutGRdkEC3YI3RjGUBHCJ0tya'),
(19, 'site_url', 'https://pay.shohojworldpay.com');

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sliders`
--

INSERT INTO `sliders` (`id`, `image`, `link`) VALUES
(10, 'uploads/slider_1779022875.webp', 'https://t.me/gfxrahulvai');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `phone`, `email`, `password`, `balance`, `created_at`) VALUES
(1, 'TeamNano', '018141551', 'theansplzhindi@gmail.com', '$2y$12$OzxsNC8t9OSV11FHIWoMCu0A1cAe.sXb78ruFUO8hJ7X5mcqXpjsu', 0.00, '2026-05-10 13:38:47'),
(2, 'Hamim', '01814155197', 'hamim@gmail.com', '$2y$12$ADFI98j8zJUSRyQB7.kVqOm7NrYy88de.39857hpisS9oTYoD7bSK', 960428.00, '2026-05-10 13:40:56'),
(3, 'Gfx Bhai', '01733500833', 'eb6908645@gmail.com', '$2y$12$kXslW7lgYYNDPVZViC4GlO8mwre3Pt7tqtl.nQ6BW4b7avhoKxDJm', 0.00, '2026-05-16 11:45:36'),
(4, 'Dev Habib', '1877167186', 'devhabib9bd@gmail.com', '$2y$12$2hskimN2/ZBuaVfkMbQB/.UVCWhfclv8D8XqycHoqz/umxsOLxXAi', 0.00, '2026-05-16 11:46:56'),
(5, 'Gf', '02345687901', 'gghgg6322@gmail.com', '$2y$12$Ttf7VXtVkQNiIWowS9IWnOqTAy0NFsq8AEpPNhb.8Nii7dFGpMg.y', 0.00, '2026-05-16 12:04:35'),
(6, 'Namhh', '01814165105', 'theannnnsplzhindi@gmail.com', '$2y$12$Gsxu86pjiVRuvm8zG4trturUTUnS7.YA35xTT1UqRU9FBpNlEgDju', 0.00, '2026-05-16 12:26:55'),
(7, 'Sadim Arafat', '01605226829', 'foysal2025sara@gmail.com', '$2y$12$Fjrb8dxX5kc3.xOFu6DiW.YT72Vxr4Hzt9Zt9KghU7FyuvGF0eSky', 0.00, '2026-05-16 17:10:07'),
(8, 'Digi Creative', '01733500887', 'digicreative10@gmail.com', '$2y$12$LBhP8htQdic9f2yLp97Z.uQcYLFbEf/H9be/O.8UQWcwBZWmXqTsy', 2019.00, '2026-05-16 18:08:44'),
(9, 'Fj', '12345678901', 'ih@gamil.com', '$2y$12$QOepj056ts1l.wlZKB.dKO2nrercnIXq76X1FBwWgZ3York30VYVm', 0.00, '2026-05-17 16:00:52'),
(10, 'ZST', '01754044290', 'zot32612@gmail.com', '$2y$12$nehvBaEZ0oFUy6fFaFrrqO7nouYy./6oWGvrD.HuhEw7lOGsQQqwG', 0.00, '2026-05-17 16:05:27'),
(11, 'Rghg', '01989577280', 'rhanmkind@gmail.com', '$2y$12$76wwFr6WTnyKTeibiaNb9eIIU5.zunh.DayGOKf9I5mTj3WoFkVqq', 0.00, '2026-05-17 16:08:31'),
(12, 'Shuhan', '0194757656', 'shuh@gmail.com', '$2y$12$E89tDCjLLZ9b0V2YJq9.OOvpTnh8yyTr5Fp4QRz05xZdwdnyeXB1u', 0.00, '2026-05-17 16:17:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `redeem_codes`
--
ALTER TABLE `redeem_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `redeem_codes`
--
ALTER TABLE `redeem_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
