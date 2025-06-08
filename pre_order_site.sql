-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 04:14 AM
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
-- Database: `pre_order_site`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `order_details` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `status` varchar(50) DEFAULT 'جديد'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `username`, `order_details`, `created_at`, `updated_at`, `status`) VALUES
(9, 12, 'haitham', '{\"1\":{\"name\":\"4G\",\"price\":323,\"quantity\":1},\"2\":{\"name\":\"card 5\",\"price\":4.8,\"quantity\":1},\"3\":{\"name\":\"adsl\",\"price\":617.5,\"quantity\":1},\"4\":{\"name\":\"fwa\",\"price\":590,\"quantity\":1},\"5\":{\"name\":\"card 10\",\"price\":9.4,\"quantity\":1}}', '2025-06-08 04:28:42', '2025-06-08 04:48:22', 'ملغي'),
(10, 12, 'haitham', '{\"1\":{\"name\":\"4G\",\"price\":323,\"quantity\":1}}', '2025-06-08 04:36:01', '2025-06-08 04:43:18', 'معلق'),
(11, 12, 'haitham', '{\"4\":{\"name\":\"fwa\",\"price\":590,\"quantity\":1}}', '2025-06-08 04:52:32', '2025-06-08 04:53:19', 'قيد التنفيذ'),
(12, 11, 'haya', '{\"1\":{\"name\":\"4G\",\"price\":323,\"quantity\":24}}', '2025-06-08 04:55:05', '2025-06-08 04:55:25', 'تم التسليم'),
(13, 8, 'hh@hh.ly', '{\"3\":{\"name\":\"adsl\",\"price\":617.5,\"quantity\":1}}', '2025-06-08 05:05:31', '2025-06-08 05:06:00', 'قيد التنفيذ');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`) VALUES
(1, '4G', 323.00, 'uploads/6844c1719f5e2.png'),
(2, 'card 5', 4.80, 'uploads/6844c69688979.png'),
(3, 'adsl', 617.50, 'uploads/6844c8f4c8c8a.png'),
(4, 'fwa', 590.00, 'uploads/6844dd5a609dd.png'),
(5, 'card 10', 9.40, 'uploads/6844dd72eecbb.png');

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `is_admin`) VALUES
(8, 'hh@hh.ly', '$2y$10$oaJ/xrSdLtCx1qg0ObzJFO.GT08m9pJ3YB1U4IGOBn6bBIESo/O9q', 1),
(11, 'haya', '$2y$10$nkIYZW0WQ6fJIdSeU3ucre.DIdtJgK70RaJthKWyUzCThxpKkkQWC', 0),
(12, 'haitham', '$2y$10$bVEZFosGMxi8E4FZbMxARemIdmnslINWAjFebfz.Ti0KuTvj5LXle', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
