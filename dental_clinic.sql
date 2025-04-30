-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2025 at 02:59 PM
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
-- Database: `dental_clinic`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `full_name`, `email`, `password_hash`, `created_at`, `last_login`) VALUES
(1, 'admin', NULL, 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-03-28 16:20:37', '2025-04-30 20:28:48'),
(2, 'shane', NULL, 'sagepyke@gmail.com', '$2y$10$4emoPsepYLrhEWQeSwItmOuN.yFPkuZ4djB4w7X7DWs3jOxee5wjy', '2025-03-28 16:56:27', '2025-04-07 19:42:35'),
(5, 'juandelacruz', NULL, 'juandelacruz@gmail.om', '$2y$10$qeOWALHjWDBcAj..4dxrCeR0HY.HMaumrftLZlw1vkRtaYlUzWSTq', '2025-04-04 01:00:27', '2025-04-07 19:42:06');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` int(100) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `service_type` varchar(100) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` int(155) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `name`, `admin_id`, `service_type`, `reason`, `appointment_date`, `appointment_time`, `status`, `created_at`, `updated_at`) VALUES
(36, 9, 0, NULL, 'Cleaning', 'lorem ipsum', '2025-04-20', '09:00:00', 0, '2025-04-15 22:15:31', '2025-04-30 12:31:22');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `password`, `created_at`, `updated_at`, `reset_token`, `reset_expires`) VALUES
(9, 'Shane Michael', 'Tapado', 'diazshane2002@gmail.com', '09673011680', '$2y$10$eG.8uWkN25mlHGzQ7aW5I.O4ANb2ZQNGJTdvWT.nq3cAl73.BFnT6', '2025-04-03 12:53:26', '2025-04-30 12:32:39', NULL, NULL),
(13, 'shing', 'laley', 'ashleydelacruz989@gmail.com', '09673011680', '$2y$10$yVExtKE3rT4SFjGMjdY/LO88HDtJQh98Ji/ET0jYY89yryyM91BaG', '2025-04-30 12:08:22', '2025-04-30 12:08:26', 'a73a305a73e9a44b242018d14ed818962812abf4c4f889f1a6cb79b5a7a67fcf', '2025-04-30 15:08:26'),
(14, 'Evann', 'qq', 'evannjewelavenido09@gmail.com', '09673011680', '$2y$10$9bKGsxCFLXolMgfDmfAn..KmeHrFAX5.1CDqU6jXUiWY/c0c0fkam', '2025-04-30 12:10:15', '2025-04-30 12:15:09', '3f96e923ee7f3d64d6f104109dc71d38f76ae6dd20c936b1c6a974a6529aff48', '2025-04-30 15:15:09'),
(15, 'Jolo', 'ss', 'johnanilouqestrada@gmail.com', '09673011680', '$2y$10$yDmhduKrUU841Wxu8j64He8p0XUwZ.xhzktcvbMN1pz/NnnvFeCxO', '2025-04-30 12:11:01', '2025-04-30 12:14:45', '47666d4c0a4ef9e7b1d59396facc3a469ad469413918514d7426ad9e9f01fd79', '2025-04-30 15:14:45'),
(16, 'Rarog', 'ee', 'christian2ecaldre@gmail.com', '09673011680', '$2y$10$UFqIKlOmp2lHf.LEboPgdug75MbfPOvct40/IAeGcEo1bMx30KKCu', '2025-04-30 12:11:31', '2025-04-30 12:15:00', 'bacf68948b72f5b218360cec096f0d29f5dd187094118ed8bac07262a47311ed', '2025-04-30 15:15:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
