-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2022 at 05:30 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

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
  `image` varchar(50) NOT NULL,
  `genre` varchar(50) NOT NULL,
  `author` varchar(50) NOT NULL,
  `edition` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` varchar(11) NOT NULL DEFAULT 'active',
  `rating` float NOT NULL,
  `owner_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `lattitude` varchar(17) NOT NULL,
  `longitude` varchar(17) NOT NULL,
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
(24, 'Loksewa', 'loksewa image', 'Government job', 'Aditya nath', 4, 'government job', 'active', 4.3, 9, 10, '', '', '2022-04-05 18:15:00'),
(25, 'PHP latest', 'php image', 'Advance', 'Laxman', 3, 'Best for intermediates', 'active', 4.4, 8, 3, '', '', '2022-04-05 18:15:00'),
(42, 'Math', 'img/a.jpg', 'Readmore', 'Narad Thakur', 2, 'Math book for 10th standard', 'active', 0, 9, 0, '', '', '2022-05-02 11:12:42'),
(44, 'dummy', 'dummy image', 'dumyness', 'dummy', 2, 'dummy book', 'active', 0, 2, 0, '', '', '2022-05-20 06:41:48'),
(49, 'Computer2', 'C:xampphtdocsookapislim3appControllers/../img/boo', 'technical', 'Mt joshef', 2, 'COmputer book for begginers', 'active', 0, 2, 0, '', '', '2022-05-20 08:02:55'),
(50, 'name edited1', 'C:xampphtdocsookapislim3appControllers/../img/boo', 'editedgenere', 'edited author', 3, 'edited', 'active', 0, 6, 0, '', '', '2022-05-20 08:58:32'),
(51, 'python', 'C:xampphtdocsookapislim3appControllers/../img/boo', 'technical', 'Mt joshef', 2, 'COmputer book for begginers', 'active', 0, 6, 0, '', '', '2022-05-20 09:50:28'),
(54, 'python Auth edited', 'C:xampphtdocsookapislim3appControllers/../img/boo', 'editedgenere', 'edited author', 3, 'edited', 'active', 0, 1, 0, '', '', '2022-05-26 01:14:38'),
(55, 'python Auth1', 'C:xampphtdocsookapislim3appControllers/../img/boo', 'technical', 'Mt joshef', 2, 'COmputer book for begginers', 'active', 0, 1, 0, '', '', '2022-05-26 01:57:30');

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
(3, 'Ajeet', 'Best computer book for begginers', 2, 51, '2022-05-24 09:58:11'),
(7, 'Ajeet', 'Best computer book for begginers', 2, 50, '2022-05-24 10:54:14'),
(8, 'Ajeet', 'this is feedback for edited book', 2, 50, '2022-05-24 10:54:28'),
(9, 'Gaurav', 'this is feedback for edited book, baurab', 8, 50, '2022-05-24 11:04:24');

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
  `lattitude` varchar(20) NOT NULL,
  `longitude` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rating` float NOT NULL,
  `status` varchar(10) NOT NULL,
  `token` varchar(100) NOT NULL,
  `user_type` int(11) NOT NULL,
  `join_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`id`, `image`, `user_name`, `mobile_no`, `address`, `email`, `lattitude`, `longitude`, `password`, `rating`, `status`, `token`, `user_type`, `join_date`) VALUES
(1, 'C:xampphtdocsookapislim3appControllers/../img/users/Screenshot (7).png', 'pratima', '9811457681', 'patharahiya', 'bhima1@gmail.com', '42342534', '23423534', '$2y$10$tvWO1SNL84XNIkTXULLHlOH1Fw39wleJtQ0e/8KDiMo7T8mK1oyuW', 4.6, 'active', 'a2993c7d3cab358c6701faf4ba76f9ba', 0, '2022-04-05 18:15:00'),
(2, 'img/Screenshot (7).png', 'Ajeet', '9811505195', 'pratappur-9, pratappur', 'aj@gmail.com', '346498', '65498765', '$2y$10$F9Bw7ZFTX5lYIYxVS6Vf0uGR0N//UEY.IMxkXYhiEUyAnjaFW5FJO', 3.1, 'active', '', 0, '2022-04-05 18:15:00'),
(6, 'img/Screenshot (12).png', 'Sumit subed', '4576387511', 'Birauta-4, Pokhara', 'vanje.sumi1t@gmail.com', '098765789', '980075567', '$2y$10$N0MscdEWEJxHPlp/4CERfuM0G//fDpjN6LFrp0T2RGmrhGrFEEBcO', 3.9, 'active', '', 0, '2022-04-05 18:15:00'),
(8, 'user image', 'Gaurav', '8765456098', 'Gaidahwa - 4, barghat', 'gauri@gmail.com', '', '', '$2y$10$IXRvoLJcNAcmZ2jaXHxngOU4j0xR9zWEsuDx32Ck0dh4zF.NunzD2', 4.3, 'active', '', 0, '2022-04-05 18:15:00'),
(9, 'C:xampphtdocsookapislim3appControllers/../img/users/', '', '5647098324', '', 'milan@gmail.com', '', '', '$2y$10$iLKFdydS6/HD5SkQgeV94eI8PDeEdXQbUvy918UThEz8Fa3wd2pcu', 4.3, 'active', '', 0, '2022-04-05 18:15:00'),
(10, 'superAdmin image', 'superadmin', '1234567890', 'superAdmin', 'superadmin@ideaFoundation.in', '', '', '$2y$10$7Xn6K1pGZmfUw7qb3X7bNebi/bYy/jO14Fd/AA1m4ubWyn0ewzCUy', 0, 'active', '', 1, '2022-04-05 18:15:00'),
(11, 'book manager image', 'bookManager', '5678460098', 'bookManager at ideafoundation', 'bookmanager@ideafoundation.in', '', '', '$2y$10$p2xXNP9AyXxHS.FL6KiV3eK.Ecmfqc6oIdERkK.z8wWDHqnXclHhi', 0, 'active', '', 3, '2022-04-05 18:15:00'),
(12, 'User manager image', 'userManager', '2345680969', 'user manager at idea Foundation', 'usermanger@ideaFoundation.in', '', '', '$2y$10$ZQdw.Li4JqYCaA76bMo2.OPrOFaMJ0/i8HswM2V09vPcILLZLn6QG', 0, 'active', '', 2, '2022-04-05 18:15:00'),
(16, 'img/logo4.jpg', 'Sagar', '9811505050', 'pratappu-3, pratappur', 'sagar@gmail.com', '', '', '$2y$10$tezlmIw3wsbWEJr24TFqVuvTUh4/ZuuXh5iQdz5euLj90d.1vR/9u', 0, '', '', 0, '2022-04-28 10:35:40'),
(52, 'C:xampphtdocsookapislim3appControllers/../img/users/Screenshot (7).png', 'seru1', '8888888888', 'hamro gaun 1', 'sere@gmail.com', '', '', '$2y$10$vGbMCZxIVhVwVg7TBiry7O8u.5M7K/EsKaqwp.mB/K3EwITBhTXym', 0, '', '', 0, '2022-05-23 00:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `isReturning` int(11) NOT NULL,
  `rqst_date` date NOT NULL,
  `issued_date` date NOT NULL,
  `return_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `request`
--

INSERT INTO `request` (`id`, `requester_id`, `owner_id`, `book_id`, `status`, `reason`, `isReturning`, `rqst_date`, `issued_date`, `return_date`) VALUES
(1, 2, 1, 18, 'returned', 'Want to study python', 1, '2022-03-16', '2022-05-24', '2022-05-25'),
(2, 6, 9, 24, 'issued', 'Goverment job', 0, '2022-03-15', '2022-03-16', '0000-00-00'),
(3, 8, 9, 24, 'returned', 'Want to be government employee', 0, '2022-03-01', '2022-03-02', '2022-03-14'),
(4, 8, 1, 18, 'pending', 'Want to be pythoneer', 0, '2022-03-05', '0000-00-00', '0000-00-00'),
(5, 1, 8, 25, 'pending', 'Web development using php', 0, '2022-03-12', '2022-03-14', '0000-00-00'),
(7, 8, 9, 42, 'rejected', '', 0, '2022-05-24', '0000-00-00', '0000-00-00'),
(8, 9, 2, 44, 'pending', '', 0, '2022-05-24', '0000-00-00', '0000-00-00'),
(9, 6, 2, 49, 'pending', '', 0, '2022-05-24', '0000-00-00', '0000-00-00');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `permission`
--
ALTER TABLE `permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
