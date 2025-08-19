-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 19, 2025 at 08:29 PM
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
-- Database: `fitzone`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `appointment_date` datetime NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `id`, `trainer_id`, `class_id`, `appointment_date`, `status`, `created_at`) VALUES
(22, 1, 24, 34, '2025-08-20 08:30:00', 'pending', '2025-08-19 02:53:13'),
(23, 1, 24, 34, '2025-08-19 07:55:00', 'pending', '2025-08-19 17:50:21');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `schedule` datetime DEFAULT NULL,
  `type` enum('cardio Training','strength Training','yoga & flexibility') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `name`, `description`, `trainer_id`, `schedule`, `type`, `created_at`) VALUES
(33, 'Cardio Training', 'High-energy sessions to improve endurance and burn calories', 21, '2025-08-13 20:41:00', 'cardio Training', '2025-08-14 03:41:52'),
(34, 'Strength Training', 'Build your strength with specialized weight training programs', 17, '2025-08-13 20:42:00', 'strength Training', '2025-08-14 03:42:47'),
(35, 'Yoga & Flexibility', 'Relax your body and mind with professional yoga sessions', 18, '2025-08-13 20:43:00', 'yoga & flexibility', '2025-08-14 03:43:59'),
(37, ' General fitness', 'A full-body workout designed to improve strength, endurance, and overall fitness for all levels.', NULL, '2025-08-13 22:43:00', 'cardio Training', '2025-08-14 05:43:37');

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE `memberships` (
  `membership_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Duration in months',
  `benefits` text DEFAULT NULL,
  `special_promotions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `memberships`
--

INSERT INTO `memberships` (`membership_id`, `name`, `price`, `duration`, `benefits`, `special_promotions`, `created_at`) VALUES
(12, 'Basic Package', 2500.00, 3, 'Access the gym equipment only', 'Get 15% off when you sign up for 12 months', '2025-08-16 22:57:48'),
(13, 'Standard Package', 5000.00, 2, 'Access all classes and gym equipment', 'Free Personal Training session for new member', '2025-08-16 22:59:52'),
(14, 'Premium Package', 8000.00, 4, 'Includes  personal trainer sessions, classes ', 'Get 2 free guest passes per month', '2025-08-16 23:02:41'),
(15, 'Family Package', 12000.00, 10, 'Access for up to 4 family members, all benefits of', 'Save 15% of families signing up together', '2025-08-16 23:06:06');

-- --------------------------------------------------------

--
-- Table structure for table `membership_registrations`
--

CREATE TABLE `membership_registrations` (
  `id` int(11) NOT NULL,
  `status` enum('active','accept','cancel','pending') DEFAULT 'pending',
  `full_name` varchar(255) DEFAULT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `age` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text NOT NULL,
  `package` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `membership_registrations`
--

INSERT INTO `membership_registrations` (`id`, `status`, `full_name`, `gender`, `age`, `email`, `phone`, `address`, `package`, `password`) VALUES
(62, 'pending', 'Maxwel', 'male', 34, 'max@gmail.com', '0786345937', 'Kurunegala', 'Basic Package', '$2y$10$JH6xPEYfsSDsTTRSFnR66eQrY4r76yz5uczb3JKdkD9nfE682X.8K');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` varchar(500) NOT NULL,
  `status` enum('pending','read','closed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `query`
--

CREATE TABLE `query` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `query`
--

INSERT INTO `query` (`id`, `user_id`, `subject`, `message`, `status`, `created_at`) VALUES
(17, 35, 'Yoga Class start', 'When does the Yoga class start', 'read', '2025-08-16 23:14:26'),
(18, 38, 'dfghj', 'rtj6il8 kyk', 'Pending', '2025-08-19 02:52:22'),
(19, 37, 'fjftk', '5rti5ri', 'Pending', '2025-08-19 17:50:27');

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `trainer_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `proficiency` varchar(50) DEFAULT NULL,
  `experience_years` int(10) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`trainer_id`, `name`, `proficiency`, `experience_years`, `email`, `image`) VALUES
(23, 'Maya', 'Cardio Expert', 6, 'maya@gmail.com', 'uploads/trainers/Maya.jpg'),
(24, 'Max', 'General Fitness', 3, 'max@gmail.com', 'uploads/trainers/Max.jpg'),
(25, 'Kara', 'Yoga & Flexibility', 6, 'kara@gmail.com', 'uploads/trainers/kara.jpg'),
(26, 'Jake', 'Strength Training', 7, 'jake@gmail.com', 'uploads/trainers/jake.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('customer','staff','admin') DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`) VALUES
(32, 'ABC', 'abc@gmail.com', '$2y$10$AKOnanIhpMpkvHn9ByAfQOopIfHDFiWOAnaFCfPrstdDL9qiaTDmm', 'admin'),
(33, 'John Doe', 'john@gmail.com', '$2y$10$8UMLjGpGxzUdCZZoQ7WijexhmXOhuHmpg/YdyQh5c31DmhXRJj.lS', 'staff'),
(34, 'Maya', 'maya@gmail.com', '$2y$10$YyznTb9UJTVYSXlq7niQP.ornz0noLaiOU4bQ0j3D/Zqj1JOQcgdi', 'customer'),
(35, 'Tharindu Bandara', 'tharindu@gmail.com', '$2y$10$3Qu6a7eO./nKXyUwD6gnjOtZqvnELSSx0F7UUXjnklsCok8B2G6p6', 'customer'),
(36, 'Chathuri', 'chathuri@gmail.com', '$2y$10$ofz47IMAq0ZFK/ixEkSJuuMBYdwdm3KuzzcO7OpEPp/5Zq9DYh96e', 'customer'),
(37, 'Maxwel', 'max@gmail.com', '$2y$10$C.30BvkehdUE3P6ZTrO..O9G7xXBI.36iVF7eCzmvwVqF0Q/K0OqW', 'customer'),
(38, 'Virat Kholi', 'virat@gmail.com', '$2y$10$5pc72TmhJ8oKglUD0lKIbufjKhl1CACeU0Xzv/DLyO9r/ElBTn80m', 'customer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`membership_id`);

--
-- Indexes for table `membership_registrations`
--
ALTER TABLE `membership_registrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `query`
--
ALTER TABLE `query`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`trainer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `memberships`
--
ALTER TABLE `memberships`
  MODIFY `membership_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `membership_registrations`
--
ALTER TABLE `membership_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `query`
--
ALTER TABLE `query`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `trainer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
