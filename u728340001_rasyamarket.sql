-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 01, 2026 at 05:40 AM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u728340001_rasyamarket`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 1, 'login', 'Login berhasil', '2001:448a:4090:fb9:f05c:a1d4:6dbf:ad86', '2026-02-23 03:37:06'),
(2, 1, 'forgot_password', 'Request reset password', '2001:448a:4090:fb9:f05c:a1d4:6dbf:ad86', '2026-02-23 03:38:27'),
(3, 1, 'login', 'Login berhasil', '2001:448a:4090:fb9:f05c:a1d4:6dbf:ad86', '2026-02-23 10:27:32'),
(4, 1, 'login', 'Login berhasil', '2001:448a:4090:a2d8:e58e:7522:70e7:5619', '2026-03-02 04:46:37'),
(5, NULL, 'register', 'User rasyaramadhani20q@gmail.com mendaftar', '110.137.38.181', '2026-03-02 04:48:29'),
(6, 2, 'verify_email', 'Email berhasil diverifikasi', '110.137.38.181', '2026-03-02 04:48:36'),
(7, 2, 'login', 'Login berhasil', '110.137.38.181', '2026-03-02 04:48:47'),
(8, 2, 'profile_update', 'Profil diperbarui', '110.137.38.181', '2026-03-02 04:49:42'),
(9, 2, 'profile_update', 'Profil diperbarui', '110.137.38.181', '2026-03-02 04:49:45'),
(10, 2, 'product_create', 'Created product: Angin', '110.137.38.181', '2026-03-02 04:50:32'),
(11, NULL, 'register', 'User mjibril088@gmail.com mendaftar', '2001:448a:4090:a2d8:e58e:7522:70e7:5619', '2026-03-02 04:51:28'),
(12, 3, 'verify_email', 'Email berhasil diverifikasi', '2001:448a:4090:a2d8:e58e:7522:70e7:5619', '2026-03-02 04:51:35'),
(13, 3, 'login', 'Login berhasil', '2001:448a:4090:a2d8:e58e:7522:70e7:5619', '2026-03-02 04:51:44'),
(14, 3, 'profile_update', 'Profil diperbarui', '2001:448a:4090:a2d8:e58e:7522:70e7:5619', '2026-03-02 04:52:20'),
(15, 3, 'checkout', 'Order MS-20260302-BE572C dibuat', '2001:448a:4090:a2d8:e58e:7522:70e7:5619', '2026-03-02 04:52:59'),
(16, 3, 'payment_upload', 'Bukti bayar order #MS-20260302-BE572C', '2001:448a:4090:a2d8:e58e:7522:70e7:5619', '2026-03-02 04:53:06'),
(17, 1, 'login', 'Login berhasil', '2001:448a:4090:a2d8:e58e:7522:70e7:5619', '2026-03-02 04:53:50'),
(18, 1, 'verify_payment', 'Approved order #1', '2001:448a:4090:a2d8:e58e:7522:70e7:5619', '2026-03-02 04:53:58'),
(19, 2, 'login', 'Login berhasil', '110.137.38.181', '2026-03-02 04:56:53'),
(20, 2, 'order_update', 'Order #1 status -> packed', '110.137.38.181', '2026-03-02 04:57:36'),
(21, 2, 'order_update', 'Order #1 status -> shipped', '110.137.38.181', '2026-03-02 04:57:41'),
(22, 3, 'login', 'Login berhasil', '2001:448a:4090:a2d8:e58e:7522:70e7:5619', '2026-03-02 05:00:03'),
(23, 3, 'confirm_order', 'Confirmed order #1', '2001:448a:4090:a2d8:e58e:7522:70e7:5619', '2026-03-02 05:00:09'),
(24, 3, 'product_create', 'Created product: beli', '2001:448a:4090:a2d8:e58e:7522:70e7:5619', '2026-03-02 05:05:48'),
(25, 2, 'checkout', 'Order MS-20260302-70B589 dibuat', '110.137.38.181', '2026-03-02 05:07:51'),
(26, 1, 'login', 'Login berhasil', '2001:448a:4090:a2d8:e58e:7522:70e7:5619', '2026-03-02 05:07:59'),
(27, 3, 'product_update', 'Updated product #2', '2001:448a:4090:a2d8:e58e:7522:70e7:5619', '2026-03-02 05:35:04'),
(28, 1, 'login', 'Login berhasil', '2400:9800:1f7:7d4b:888e:12ff:feab:25b1', '2026-04-02 01:10:07'),
(29, 1, 'login', 'Login berhasil', '2400:9800:152:f9c6:d4d0:c3ff:fec3:8305', '2026-04-05 11:38:51'),
(30, 1, 'login', 'Login berhasil', '114.10.103.131', '2026-04-05 23:09:48'),
(31, NULL, 'register', 'User anselinaurel@gmail.com mendaftar', '121.50.32.203', '2026-04-06 00:54:43'),
(32, 4, 'verify_email', 'Email berhasil diverifikasi', '121.50.32.203', '2026-04-06 00:54:53'),
(33, 4, 'login', 'Login berhasil', '121.50.32.203', '2026-04-06 00:55:10'),
(34, 4, 'profile_update', 'Profil diperbarui', '121.50.32.203', '2026-04-06 00:56:09'),
(35, 4, 'profile_update', 'Profil diperbarui', '121.50.32.203', '2026-04-06 00:56:18'),
(36, 4, 'profile_update', 'Profil diperbarui', '121.50.32.203', '2026-04-06 00:56:29'),
(37, 4, 'checkout', 'Order MS-20260406-60A499 dibuat', '121.50.32.203', '2026-04-06 00:57:26'),
(38, NULL, 'register', 'User bagasandri51@gmail.com mendaftar', '2404:c0:5a20::3eb:126d', '2026-04-06 01:00:27'),
(39, 5, 'verify_email', 'Email berhasil diverifikasi', '2404:c0:5a20::3eb:126d', '2026-04-06 01:00:39'),
(40, 5, 'login', 'Login berhasil', '2404:c0:5a20::3eb:126d', '2026-04-06 01:00:54'),
(41, 5, 'profile_update', 'Profil diperbarui', '2404:c0:5a20::3eb:126d', '2026-04-06 01:03:38'),
(42, 5, 'checkout', 'Order MS-20260406-408D03 dibuat', '2404:c0:5a20::3eb:126d', '2026-04-06 01:04:36'),
(43, 2, 'forgot_password', 'Request reset password', '121.50.32.200', '2026-04-06 01:44:22'),
(44, 2, 'reset_password', 'Password berhasil direset', '121.50.32.200', '2026-04-06 01:44:43'),
(45, 2, 'login', 'Login berhasil', '121.50.32.200', '2026-04-06 01:44:53'),
(46, 2, 'checkout', 'Order MS-20260406-47492C dibuat', '121.50.32.200', '2026-04-06 01:45:24'),
(47, 2, 'payment_upload', 'Bukti bayar order #MS-20260406-47492C', '121.50.32.200', '2026-04-06 01:46:17'),
(48, 1, 'login', 'Login berhasil', '2400:9800:158:b712:88e4:aff:fec0:abf3', '2026-04-06 01:52:02'),
(49, 1, 'login', 'Login berhasil', '2400:9800:158:b712:88e4:aff:fec0:abf3', '2026-04-06 01:53:14'),
(50, 1, 'checkout', 'Order MS-20260406-76CEB7 dibuat', '2400:9800:158:b712:88e4:aff:fec0:abf3', '2026-04-06 01:55:03'),
(51, 1, 'payment_upload', 'Bukti bayar order #MS-20260406-76CEB7', '2400:9800:158:b712:88e4:aff:fec0:abf3', '2026-04-06 01:55:40'),
(52, 4, 'login', 'Login berhasil', '121.50.32.204', '2026-04-06 01:57:27'),
(53, 4, 'login', 'Login berhasil', '121.50.32.203', '2026-04-06 01:58:22'),
(54, 1, 'login', 'Login berhasil', '2400:9800:fb:376c:34bc:1b1f:be04:d4e0', '2026-04-09 02:59:19'),
(55, 3, 'forgot_password', 'Request reset password', '112.215.45.183', '2026-04-12 16:27:46'),
(56, 3, 'reset_password', 'Password berhasil direset', '140.213.14.181', '2026-04-12 16:28:41'),
(57, 3, 'login', 'Login berhasil', '140.213.14.181', '2026-04-12 16:28:54'),
(58, 3, 'profile_update', 'Profil diperbarui', '140.213.14.181', '2026-04-12 16:29:43'),
(59, 3, 'product_create', 'Created product: Mie Ayam', '140.213.14.181', '2026-04-12 16:30:24'),
(60, 1, 'login', 'Login berhasil', '140.213.14.181', '2026-04-12 16:31:04'),
(61, 1, 'profile_update', 'Profil diperbarui', '140.213.14.181', '2026-04-12 16:31:53'),
(62, 4, 'login', 'Login berhasil', '114.10.103.178', '2026-04-13 14:43:15'),
(63, 1, 'login', 'Login berhasil', '2400:9800:f4:dbe4:6096:9119:1f22:ec2', '2026-04-14 02:12:36'),
(64, 1, 'checkout', 'Order MS-20260414-7B12FA dibuat', '2400:9800:f4:dbe4:6096:9119:1f22:ec2', '2026-04-14 02:13:11'),
(65, 1, 'login', 'Login berhasil', '2400:9800:174:70aa:e55c:305c:5cd6:fafd', '2026-04-19 12:58:30'),
(66, 1, 'checkout', 'Order MS-20260419-871431 dibuat', '2400:9800:174:70aa:e55c:305c:5cd6:fafd', '2026-04-19 12:58:48');

-- --------------------------------------------------------

--
-- Table structure for table `carousel_ads`
--

CREATE TABLE `carousel_ads` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `bg_color_from` varchar(7) DEFAULT '#061B45',
  `bg_color_to` varchar(7) DEFAULT '#065A82',
  `icon` varchar(50) DEFAULT 'fa-solid fa-bullhorn',
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carousel_ads`
--

INSERT INTO `carousel_ads` (`id`, `product_id`, `title`, `description`, `bg_color_from`, `bg_color_to`, `icon`, `is_active`, `sort_order`, `created_at`) VALUES
(3, 3, 'Mie Ayam', 'Dapatkan disokn 50 %', '#061b45', '#065a82', 'fa-solid fa-bullhorn', 1, 0, '2026-04-14 02:25:07');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `service_fee` decimal(12,2) NOT NULL DEFAULT 3500.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','paid','verified','packed','shipped','done','rejected','cancelled') DEFAULT 'pending',
  `payment_proof` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payment_deadline` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `buyer_id`, `subtotal`, `service_fee`, `total`, `status`, `payment_proof`, `notes`, `payment_deadline`, `created_at`, `updated_at`) VALUES
(1, 'MS-20260302-BE572C', 3, 2000.00, 3500.00, 5500.00, 'done', 'uploads/payments/69a517b2612cc.png', '', NULL, '2026-03-02 04:52:59', '2026-03-02 05:00:09'),
(2, 'MS-20260302-70B589', 2, 40000.00, 3500.00, 43500.00, 'pending', NULL, '', NULL, '2026-03-02 05:07:51', '2026-03-02 05:07:51'),
(3, 'MS-20260406-60A499', 4, 40000.00, 3500.00, 43500.00, 'cancelled', NULL, '', '2026-04-07 00:57:26', '2026-04-06 00:57:26', '2026-04-14 02:12:57'),
(4, 'MS-20260406-408D03', 5, 40000.00, 3500.00, 43500.00, 'cancelled', NULL, '', '2026-04-07 01:04:36', '2026-04-06 01:04:36', '2026-04-14 02:12:57'),
(5, 'MS-20260406-47492C', 2, 40000.00, 3500.00, 43500.00, 'paid', 'uploads/payments/69d310692bd17.jpg', '', '2026-04-07 01:45:24', '2026-04-06 01:45:24', '2026-04-06 01:46:17'),
(6, 'MS-20260406-76CEB7', 1, 40000.00, 3500.00, 43500.00, 'paid', 'uploads/payments/69d3129bf34f8.jpg', '', '2026-04-07 01:55:03', '2026-04-06 01:55:03', '2026-04-06 01:55:40'),
(7, 'MS-20260414-7B12FA', 1, 15000.00, 3500.00, 18500.00, 'cancelled', NULL, '', '2026-04-15 02:13:11', '2026-04-14 02:13:11', '2026-04-19 12:58:44'),
(8, 'MS-20260419-871431', 1, 15000.00, 3500.00, 18500.00, 'pending', NULL, '', '2026-04-20 12:58:48', '2026-04-19 12:58:48', '2026-04-19 12:58:48');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `item_status` enum('pending','packed','shipped','done') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `seller_id`, `quantity`, `price`, `item_status`, `created_at`) VALUES
(7, 7, 3, 3, 1, 15000.00, 'pending', '2026-04-14 02:13:11'),
(8, 8, 3, 3, 1, 15000.00, 'pending', '2026-04-19 12:58:48');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `category` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `seller_id`, `title`, `description`, `price`, `stock`, `category`, `image`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 3, 'Mie Ayam', '', 15000.00, 8, 'Makanan &amp; Minuman', 'uploads/products/69dbc8a0323bd.jpg', 1, '2026-04-12 16:30:24', '2026-04-19 12:58:48');

-- --------------------------------------------------------

--
-- Table structure for table `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `action` varchar(50) NOT NULL,
  `attempts` int(11) DEFAULT 1,
  `first_attempt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rate_limits`
--

INSERT INTO `rate_limits` (`id`, `ip_address`, `action`, `attempts`, `first_attempt`) VALUES
(1, '110.137.38.181', 'register', 1, '2026-03-02 04:48:28'),
(2, '2001:448a:4090:a2d8:e58e:7522:70e7:5619', 'register', 1, '2026-03-02 04:51:27'),
(3, '121.50.32.203', 'register', 1, '2026-04-06 00:54:43'),
(4, '2404:c0:5a20::3eb:126d', 'register', 1, '2026-04-06 01:00:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verify_code` varchar(10) DEFAULT NULL,
  `verify_expires` datetime DEFAULT NULL,
  `profile_completed` tinyint(1) DEFAULT 0,
  `role` enum('admin','member') DEFAULT 'member',
  `occupation_type` enum('mahasiswa','pekerja') DEFAULT NULL,
  `program_studi` varchar(100) DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `instansi` varchar(100) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `kota` varchar(100) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `is_banned` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `email_verified`, `verify_code`, `verify_expires`, `profile_completed`, `role`, `occupation_type`, `program_studi`, `pekerjaan`, `instansi`, `avatar`, `address`, `kota`, `reset_token`, `reset_expires`, `is_banned`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@MarketStudent.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '083831134040', 1, NULL, NULL, 1, 'admin', NULL, NULL, NULL, 'FT', 'uploads/avatars/69dbc8f99b240.jpg', 'ketek', 'Kota Metro', '2f9cfe172c8ed64bbcaf71830772aeac6e5ffa4ef0fe512bca3f39027d29649b', '2026-02-23 04:38:27', 0, '2026-02-23 03:36:29', '2026-04-12 16:31:53'),
(2, 'Rasya Ramadhani', 'rasyaramadhani20q@gmail.com', '$2y$10$wR8mwkX.ba7ePfMpaldk.OAw4IxVA32PSc1Jvucxmq7qlhMW5TfpS', '087869110535', 1, NULL, NULL, 1, 'member', 'mahasiswa', 'FEB', NULL, NULL, NULL, 'Bandar lampung, Z.A pagar alam', NULL, NULL, NULL, 0, '2026-03-02 04:48:28', '2026-04-06 01:44:43'),
(3, 'Mohammad Jibril', 'mjibril088@gmail.com', '$2y$10$kVu56/7qB0dpeNAm8HveduvRcEq9v2ZteEO2x7JVOBuS.dVGvrcaS', '083831134041', 1, NULL, NULL, 1, 'member', 'mahasiswa', 'FT', NULL, 'FT', 'uploads/avatars/69dbc877300cb.jpg', 'ketek', 'Kab. Lampung Tengah', NULL, NULL, 0, '2026-03-02 04:51:27', '2026-04-12 16:29:43'),
(4, 'Anselin Aurel', 'anselinaurel@gmail.com', '$2y$10$xJeHWkyFsqvTbhidzVwug.AEBUYyBdNjQ4n1dxL4zq4b2HKJUvwke', '089526471140', 1, NULL, NULL, 1, 'member', NULL, NULL, NULL, 'Universitas Lampung', NULL, 'Bandar Lampung', 'Kota Bandar Lampung', NULL, NULL, 0, '2026-04-06 00:54:43', '2026-04-06 00:56:09'),
(5, 'Andri Bagas Irawan', 'bagasandri51@gmail.com', '$2y$10$tdDuELfPe.kX/eaB9Zcu9OOhG5f2r0fcy8G0YSfpoVzhB5S9MM0B2', '081282311752', 1, NULL, NULL, 1, 'member', NULL, NULL, NULL, 'Universitas Lampung', NULL, 'Jl. gg anggur no 6a, Rajabasa, Bandar Lampung', 'Kota Bandar Lampung', NULL, NULL, 0, '2026-04-06 01:00:27', '2026-04-06 01:03:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `carousel_ads`
--
ALTER TABLE `carousel_ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_action` (`ip_address`,`action`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `carousel_ads`
--
ALTER TABLE `carousel_ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `carousel_ads`
--
ALTER TABLE `carousel_ads`
  ADD CONSTRAINT `carousel_ads_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
