-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Generation Time: Jun 24, 2022 at 10:51 AM
-- Server version: 8.0.19
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `cust_name` varchar(50) NOT NULL,
  `cust_addr` varchar(500) NOT NULL,
  `cust_zipcode` int NOT NULL,
  `line_items` varchar(5000) NOT NULL,
  `status` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `cust_name`, `cust_addr`, `cust_zipcode`, `line_items`, `status`) VALUES
(4, 'Make Web Better Upsell test', '514 street sunburn', 45254, 'a:2:{s:2:\"id\";a:4:{i:1;s:2:\"12\";i:2;s:2:\"13\";i:3;s:0:\"\";i:4;s:0:\"\";}s:8:\"quantity\";a:4:{i:1;s:2:\"10\";i:2;s:2:\"12\";i:3;s:0:\"\";i:4;s:0:\"\";}}', 'refunded'),
(8, 'Make Web Better Upsell test', '514 street sunburn', 226004, 'a:2:{s:2:\"id\";a:2:{i:1;s:2:\"12\";i:2;s:2:\"12\";}s:8:\"quantity\";a:2:{i:1;s:2:\"12\";i:2;s:3:\"263\";}}', 'pending'),
(9, 'Make Web Better Upsell test', '514 street sunburn', 201545, 'a:2:{s:2:\"id\";a:2:{i:1;s:2:\"12\";i:2;s:2:\"13\";}s:8:\"quantity\";a:2:{i:1;s:1:\"5\";i:2;s:1:\"4\";}}', 'processing'),
(10, 'I am testing', '514 street sunburn', 90001, 'a:2:{s:2:\"id\";a:3:{i:1;s:2:\"12\";i:2;s:2:\"12\";i:3;s:0:\"\";}s:8:\"quantity\";a:3:{i:1;s:2:\"12\";i:2;s:2:\"56\";i:3;s:0:\"\";}}', 'processing');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(150) NOT NULL,
  `price` int NOT NULL,
  `description` varchar(500) NOT NULL,
  `tag` varchar(20) NOT NULL,
  `stock` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `tag`, `stock`) VALUES
(12, 'Polo', 20, 'Create the next big solution for more than 4.2 million QuickBooks Online small business users', 'clothing', '100'),
(13, 'Dracula', 20, 'Lorem Ipsum is simply dummy text.', 'monster', '50'),
(14, 'DIno ( cloths ) ', 10, 'Optimization is set to &quot;With Tags&quot; then update product name as &quot;Name+Tags&quot;\r\nIf Default price is set to any number say &quot;10&quot; and product price is empty or 0 then set product price as default price\r\nIf Default Stock is set to any number say &quot;10&quot; and product stock is empty or 0 then set product stock as default stock\r\nWhenever any order is being created the', 'cloths', '200'),
(15, 'T-shirt Polo ( cloths ) ', 100, 'a fdfdsfsf  sfds fdsfdsf', 'cloths', '200');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `title` varchar(20) NOT NULL,
  `price` int NOT NULL,
  `stock` int NOT NULL,
  `zipcode` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `title`, `price`, `stock`, `zipcode`) VALUES
(6, 'with_tags', 10, 200, '201545');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `username`, `email`, `phone`, `password`, `role`) VALUES
(1, 'Ashutosh', 'Verma', 'devrocks', 'vermaa947@gmail.com', '+918545088530', '123456', 'admin'),
(2, 'Make Web Better', 'test', 'devrockss', 'ashutoshverma@mwb.com', '01234567890', '123456', 'customer'),
(3, 'Ashutosh', 'Verma', 'mwbdevhere', 'mwbdev13@gmail.com', '+918545088530', 'qwerty', 'guest'),
(4, 'Harshit', 'Agrawal', 'fuckboi@69', 'harshitagrawal@mwb.com', '9785461320', '123456', 'manager'),
(5, 'Make Web Better', 'test', 'devro', 'dsfdsfdsashutoshverma@mwb.com', '01234567890', '123456', 'guest');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
