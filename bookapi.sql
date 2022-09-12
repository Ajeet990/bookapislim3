-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 12, 2022 at 06:41 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookapi`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `book_name` varchar(30) NOT NULL,
  `image` varchar(255) NOT NULL,
  `genre` varchar(50) NOT NULL,
  `author` varchar(50) NOT NULL,
  `edition` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` varchar(11) NOT NULL DEFAULT 'active',
  `rating` float NOT NULL DEFAULT 0,
  `owner_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `lattitude` varchar(17) NOT NULL DEFAULT '0',
  `longitude` varchar(17) NOT NULL DEFAULT '0',
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `book_name`, `image`, `genre`, `author`, `edition`, `description`, `status`, `rating`, `owner_id`, `quantity`, `lattitude`, `longitude`, `upload_date`) VALUES
(2, 'Dja', 'Django image', 'beginner', 'Mr. Dev', 4, 'python framework', 'active', 4.8, 6, 10, '', '', '2022-04-05 18:15:00'),
(8, 'PHP book', 'PHP image', 'Beginners for php', 'KIM jong', 2, 'best php book ever', 'active', 4.3, 6, 10, '', '', '2022-04-05 18:15:00'),
(18, 'Python', 'python image', 'beginner', 'Python young', 5, 'best python book ever', 'active', 2.5, 1, 4, '', '', '2022-04-05 18:15:00'),
(23, 'C-Sharp', 'c sharp image', 'programming', 'jhong', 4, 'Good', 'active', 4.3, 8, 8, '', '', '2022-04-05 18:15:00'),
(25, 'PHP latest', 'php image', 'Advance', 'Laxman', 3, 'Best for intermediates', 'active', 4.4, 8, 3, '', '', '2022-04-05 18:15:00'),
(44, 'dummy', 'dummy image', 'dumyness', 'dummy', 2, 'dummy book', 'active', 0, 2, 0, '', '', '2022-05-20 06:41:48'),
(58, 'new book', 'app/img/users/Screenshot (4).png', 'history', 'john', 1, 'world war 1', 'active', 0, 1, 0, '0', '0', '2022-09-09 06:04:15'),
(60, 'social', 'app/img/users/Screenshot (5).png', 'society', 'john', 1, 'about society', 'active', 0, 72, 0, '0', '0', '2022-09-11 05:40:44'),
(61, 'romeo and juliet', 'app/img/users/Screenshot (5).png', 'love story', 'peter', 1, 'best love story book', 'active', 0, 73, 0, '0', '0', '2022-09-11 08:39:08');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `commenter_name` varchar(100) NOT NULL,
  `feedback` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `commenter_name`, `feedback`, `user_id`, `book_id`, `timestamp`) VALUES
(12, 'prepare', 'this is feedback for php book1', 72, 58, '2022-09-11 11:23:42'),
(13, 'rahul', 'i love love stories', 73, 61, '2022-09-11 14:27:30');

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE `permission` (
  `id` int(11) NOT NULL,
  `user_type` int(11) NOT NULL,
  `user_table` int(11) NOT NULL,
  `book_table` int(11) NOT NULL,
  `request` int(11) NOT NULL,
  `settings` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`id`, `user_type`, `user_table`, `book_table`, `request`, `settings`) VALUES
(1, 2, 1, 0, 1, 1),
(2, 3, 0, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `mobile_no` varchar(10) NOT NULL,
  `address` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `lattitude` varchar(20) NOT NULL DEFAULT '0',
  `longitude` varchar(20) NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL,
  `rating` float NOT NULL DEFAULT 0,
  `status` varchar(10) NOT NULL,
  `token` varchar(100) NOT NULL,
  `user_type` int(11) NOT NULL,
  `join_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`id`, `image`, `user_name`, `mobile_no`, `address`, `email`, `lattitude`, `longitude`, `password`, `rating`, `status`, `token`, `user_type`, `join_date`) VALUES
(1, 'C:xampphtdocsookapislim3appControllers/../img/users/Screenshot (7).png', 'pratima', '9811457681', 'patharahiya', 'bhima1@gmail.com', '42342534', '23423534', '$2y$10$tvWO1SNL84XNIkTXULLHlOH1Fw39wleJtQ0e/8KDiMo7T8mK1oyuW', 4.6, 'active', '8c0c28f1989478c670ca993e5dc59bf6', 0, '2022-04-05 18:15:00'),
(2, 'img/Screenshot (7).png', 'Ajeet', '9811505195', 'pratappur-9, pratappur', 'aj@gmail.com', '346498', '65498765', '$2y$10$F9Bw7ZFTX5lYIYxVS6Vf0uGR0N//UEY.IMxkXYhiEUyAnjaFW5FJO', 3.1, 'active', '', 0, '2022-04-05 18:15:00'),
(6, 'img/Screenshot (12).png', 'Sumit subed', '4576387511', 'Birauta-4, Pokhara', 'vanje.sumi1t@gmail.com', '098765789', '980075567', '$2y$10$N0MscdEWEJxHPlp/4CERfuM0G//fDpjN6LFrp0T2RGmrhGrFEEBcO', 3.9, 'active', '', 0, '2022-04-05 18:15:00'),
(8, 'user image', 'Gaurav', '8765456098', 'Gaidahwa - 4, barghat', 'gauri@gmail.com', '', '', '$2y$10$IXRvoLJcNAcmZ2jaXHxngOU4j0xR9zWEsuDx32Ck0dh4zF.NunzD2', 4.3, 'active', '', 0, '2022-04-05 18:15:00'),
(10, 'superAdmin image', 'superadmin', '1234567890', 'superAdmin', 'superadmin@ideaFoundation.in', '', '', '$2y$10$7Xn6K1pGZmfUw7qb3X7bNebi/bYy/jO14Fd/AA1m4ubWyn0ewzCUy', 0, 'active', '', 1, '2022-04-05 18:15:00'),
(11, 'book manager image', 'bookManager', '5678460098', 'bookManager at ideafoundation', 'bookmanager@ideafoundation.in', '', '', '$2y$10$p2xXNP9AyXxHS.FL6KiV3eK.Ecmfqc6oIdERkK.z8wWDHqnXclHhi', 0, 'active', '', 3, '2022-04-05 18:15:00'),
(72, 'app/img/users/Screenshot (3).png', 'prepare', '1212121212', 'CA, America.', 'henrymc@gmail.co', '0', '0', '$2y$10$Qb73ePPY3loDXLwfyn4jvu8NELEIV/Tu1EUbWZllfcnCEbKSNQfoK', 0, 'active', '', 0, '2022-09-10 10:17:22'),
(73, 'app/img/users/Screenshot (28).png', 'rahul', '4564350098', 'UK', 'rahul@gmail.com', '0', '0', '$2y$10$3nnqYN5YmiyWm8e4Cke6a.mP9/4Xvy2b33DsrRITQrVY9UcC6wrl2', 0, 'active', '', 0, '2022-09-11 08:37:20');

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `status` int(20) NOT NULL COMMENT '0=pending,\r\n1=issued,\r\n2=returning,\r\n3=returned,\r\n4=rejected',
  `reason` varchar(255) NOT NULL,
  `rqst_date` date NOT NULL,
  `issued_date` date NOT NULL,
  `return_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `request`
--

INSERT INTO `request` (`id`, `requester_id`, `owner_id`, `book_id`, `status`, `reason`, `rqst_date`, `issued_date`, `return_date`) VALUES
(1, 2, 1, 18, 3, 'Want to study python', '2022-03-16', '2022-05-24', '2022-05-25'),
(4, 8, 1, 18, 4, 'Want to be pythoneer', '2022-03-05', '0000-00-00', '0000-00-00'),
(5, 2, 1, 25, 3, 'Web development using php', '2022-03-12', '2022-03-14', '2022-09-11'),
(11, 72, 6, 2, 0, 'want to learn python', '2022-09-11', '0000-00-00', '0000-00-00'),
(12, 1, 6, 8, 0, 'want to learn python', '2022-09-11', '0000-00-00', '0000-00-00'),
(13, 1, 8, 23, 2, 'want to learn c-sharp', '2022-09-11', '2022-09-11', '0000-00-00'),
(14, 1, 2, 44, 0, 'want to learn c-sharp', '2022-09-11', '0000-00-00', '0000-00-00'),
(15, 73, 1, 18, 4, 'want to learn c-sharp', '2022-09-11', '0000-00-00', '0000-00-00'),
(16, 73, 8, 23, 0, 'want to learn c-sharp', '2022-09-11', '0000-00-00', '0000-00-00'),
(17, 73, 8, 25, 0, 'want to learn c-sharp', '2022-09-11', '0000-00-00', '0000-00-00'),
(18, 73, 1, 18, 1, 'want to learn c-sharp', '2022-09-11', '2022-09-12', '2022-09-11'),
(19, 73, 1, 58, 0, 'want to learn c-sharp', '2022-09-11', '0000-00-00', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `name`, `value`) VALUES
(1, 'site_title', 'bookXchange'),
(2, 'logo', '../img/logo4.jpg'),
(3, 'mail_from', 'ideaFoundation@gmail.com'),
(4, 'welcome_text', 'Admin portal');

-- --------------------------------------------------------

--
-- Table structure for table `user_type`
--

CREATE TABLE `user_type` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_type`
--

INSERT INTO `user_type` (`id`, `name`, `value`) VALUES
(1, 'superadmin', 1),
(2, 'admin', 2),
(3, 'bookManager', 3),
(4, 'user', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `books_ibfk_1` (`owner_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_ibfk_1` (`book_id`),
  ADD KEY `feedback_ibfk_2` (`user_id`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_ibfk_1` (`requester_id`),
  ADD KEY `request_ibfk_2` (`owner_id`),
  ADD KEY `request_ibfk_3` (`book_id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `permission`
--
ALTER TABLE `permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_type`
--
ALTER TABLE `user_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `register` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `register` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `request`
--
ALTER TABLE `request`
  ADD CONSTRAINT `request_ibfk_1` FOREIGN KEY (`requester_id`) REFERENCES `register` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `request_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `register` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `request_ibfk_3` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
