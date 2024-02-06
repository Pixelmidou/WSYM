-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 15, 2024 at 01:21 PM
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
-- Database: `wsym`
--

-- --------------------------------------------------------

--
-- Table structure for table `balance`
--

CREATE TABLE `balance` (
  `username` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `balance` decimal(6,1) DEFAULT 0.0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `balance`
--

INSERT INTO `balance` (`username`, `email`, `balance`) VALUES
('mohamed', 'mohamed@gmail.com', 10.0),
('salah', 'salah@gmail.com', 0.0),
('yessine', 'yessine@gmail.com', 0.0);

-- --------------------------------------------------------

--
-- Table structure for table `blacklist`
--

CREATE TABLE `blacklist` (
  `username` varchar(30) NOT NULL,
  `account` tinyint(1) NOT NULL DEFAULT 1,
  `deposit` tinyint(1) NOT NULL DEFAULT 1,
  `withdraw` tinyint(1) NOT NULL DEFAULT 1,
  `wire` tinyint(1) NOT NULL DEFAULT 1,
  `ticket` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `blacklist`
--

INSERT INTO `blacklist` (`username`, `account`, `deposit`, `withdraw`, `wire`, `ticket`) VALUES
('mohamed', 1, 1, 1, 1, 1),
('salah', 1, 1, 1, 1, 1),
('yessine', 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `deposit`
--

CREATE TABLE `deposit` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `deposit_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `deposit_amount` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `deposit`
--

INSERT INTO `deposit` (`id`, `username`, `deposit_date`, `deposit_amount`) VALUES
(39, 'mohamed', '2024-01-10 18:52:58', 10.00),
(40, 'mohamed', '2024-01-13 20:01:21', 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `forgot_password`
--

CREATE TABLE `forgot_password` (
  `email` varchar(100) NOT NULL,
  `forgotpass` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_credentials`
--

CREATE TABLE `login_credentials` (
  `username` varchar(30) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `email_verif` tinyint(1) NOT NULL DEFAULT 0,
  `email_verif_token` varchar(64) DEFAULT NULL,
  `email_verif_expire` datetime DEFAULT NULL,
  `timecreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `lastlogin` timestamp NULL DEFAULT NULL,
  `pfp` varchar(40) NOT NULL DEFAULT 'favicon.ico',
  `rank` varchar(30) DEFAULT NULL,
  `token` varchar(64) DEFAULT NULL,
  `token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `login_credentials`
--

INSERT INTO `login_credentials` (`username`, `pass`, `email`, `email_verif`, `email_verif_token`, `email_verif_expire`, `timecreated`, `lastlogin`, `pfp`, `rank`, `token`, `token_expire`) VALUES
('mohamed', '$2y$10$CyhidsrA4/bYrQEtFy39i.S5HSWBqWhKwVJm/pLqyoDFMWJ1mS4pe', 'mohamed@gmail.com', 1, NULL, NULL, '2024-01-13 20:00:04', '2024-01-13 20:00:29', 'favicon.ico', NULL, NULL, NULL),
('salah', '$2y$10$sxX6zAoESOPf.HzMcD60rOjxoVWnA66OH9QSByfXQuiql79VFpRpK', 'salah@gmail.com', 1, NULL, NULL, '2023-12-10 20:26:37', '2024-01-09 22:00:11', 'favicon.ico', 'superadmin', NULL, NULL),
('yessine', '$2y$10$S9K46zTXio80TovI/2CG9uixvlIIBZNobT4G22F3nTxAp1LXmTS5S', 'yessine@gmail.com', 1, NULL, NULL, '2023-12-10 20:26:58', '2023-12-11 17:34:42', 'favicon.ico', 'none', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ranks`
--

CREATE TABLE `ranks` (
  `rank` varchar(30) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ranks`
--

INSERT INTO `ranks` (`rank`, `description`) VALUES
('admin', NULL),
('none', NULL),
('superadmin', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `username` varchar(30) NOT NULL,
  `status` varchar(6) NOT NULL,
  `time_opened` timestamp NOT NULL DEFAULT current_timestamp(),
  `time_closed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ticket_content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wire`
--

CREATE TABLE `wire` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `receiver` varchar(30) NOT NULL,
  `wire_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `wire_amount` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `wire`
--

INSERT INTO `wire` (`id`, `username`, `receiver`, `wire_date`, `wire_amount`) VALUES
(2, 'mohamed', 'yessine@gmail.com', '2024-01-05 21:18:26', 96.00);

-- --------------------------------------------------------

--
-- Table structure for table `withdraw`
--

CREATE TABLE `withdraw` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `withdraw_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `withdraw_amount` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `withdraw`
--

INSERT INTO `withdraw` (`id`, `username`, `withdraw_date`, `withdraw_amount`) VALUES
(4, 'mohamed', '2024-01-05 21:07:25', 8.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `balance`
--
ALTER TABLE `balance`
  ADD PRIMARY KEY (`username`),
  ADD KEY `balance_ibfk_2` (`email`);

--
-- Indexes for table `blacklist`
--
ALTER TABLE `blacklist`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `deposit`
--
ALTER TABLE `deposit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `forgot_password`
--
ALTER TABLE `forgot_password`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `login_credentials`
--
ALTER TABLE `login_credentials`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `rank` (`rank`);

--
-- Indexes for table `ranks`
--
ALTER TABLE `ranks`
  ADD PRIMARY KEY (`rank`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`username`,`time_opened`);

--
-- Indexes for table `wire`
--
ALTER TABLE `wire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`),
  ADD KEY `wire_ibfk_2` (`receiver`);

--
-- Indexes for table `withdraw`
--
ALTER TABLE `withdraw`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `deposit`
--
ALTER TABLE `deposit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `wire`
--
ALTER TABLE `wire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `withdraw`
--
ALTER TABLE `withdraw`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `balance`
--
ALTER TABLE `balance`
  ADD CONSTRAINT `balance_ibfk_1` FOREIGN KEY (`username`) REFERENCES `login_credentials` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `balance_ibfk_2` FOREIGN KEY (`email`) REFERENCES `login_credentials` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `blacklist`
--
ALTER TABLE `blacklist`
  ADD CONSTRAINT `blacklist_ibfk_1` FOREIGN KEY (`username`) REFERENCES `login_credentials` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `forgot_password`
--
ALTER TABLE `forgot_password`
  ADD CONSTRAINT `forgot_password_ibfk_1` FOREIGN KEY (`email`) REFERENCES `login_credentials` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `login_credentials`
--
ALTER TABLE `login_credentials`
  ADD CONSTRAINT `login_credentials_ibfk_1` FOREIGN KEY (`rank`) REFERENCES `ranks` (`rank`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
