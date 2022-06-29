-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Generation Time: Jun 29, 2022 at 01:53 PM
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
(17, 'Ashutosh Verma', '3/466 street sunburn', 226004, 'a:1:{i:0;O:8:\"stdClass\":2:{s:2:\"id\";s:2:\"15\";s:8:\"quantity\";s:2:\"02\";}}', 'processing');

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
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(10) NOT NULL,
  `api_key` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `username`, `email`, `phone`, `password`, `role`, `api_key`) VALUES
(1, 'Ashutosh', 'Verma', 'dev007', 'ashutoshverma@makewebbetter.com', '9695270087', '9785461320', 'admin', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImN0eSI6ImFwcGxpY2F0aW9uXC9qc29uIn0.eyJhdWQiOlsiaHR0cDpcL1wvbG9jYWxob3N0OjgwODBcLyJdLCJleHAiOjE2NTY1ODk5NjIsImp0aSI6IjEiLCJpYXQiOjE2NTY1MDM1NjIsImlzcyI6Imh0dHBzOlwvXC9waGFsY29uLmlvIiwibmJmIjoxNjU2NTAzNTAyLCJzdWIiOiJhZG1pbiJ9.VRgtl89wXdVrfdmamfTsUQSYO4hXHjKKgorBZlqnkJutOBDlpm20Y0bQGfSdWfrnaGNJ0-UnDrdc3c4X2_qw_w'),
(10, 'Shop', 'Manager', 'subdev', 'ashutoshverma@cedcoss.com', '96695270087', '785461325', 'manager', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImN0eSI6ImFwcGxpY2F0aW9uXC9qc29uIn0.eyJhdWQiOlsiaHR0cDpcL1wvbG9jYWxob3N0OjgwODBcLyJdLCJleHAiOjE2NTY1MTEwMTQsImp0aSI6IjEwIiwiaWF0IjoxNjU2NDI0NjE0LCJpc3MiOiJodHRwczpcL1wvcGhhbGNvbi5pbyIsIm5iZiI6MTY1NjQyNDU1NCwic3ViIjoibWFuYWdlciJ9.XtsAW5yq600vrQqaKHvvMqXXQjNlHqaR0WowHfok9EW98HE9U-S-RnXMr7472dW091dkF2luP3kMopvh7PQFFg');

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_key` (`api_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
