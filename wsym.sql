-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 13, 2023 at 05:58 PM
-- Server version: 5.7.17
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
  `balance` decimal(6,1) DEFAULT '0.0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `balance`
--

INSERT INTO `balance` (`username`, `email`, `balance`) VALUES
('pixel', 'pixel@gmail.com', '0.0'),
('salah', 'salah@gmail.com', '0.0'),
('yessine', 'yessine@gmail.com', '0.0');

-- --------------------------------------------------------

--
-- Table structure for table `blacklist`
--

CREATE TABLE `blacklist` (
  `username` varchar(30) NOT NULL,
  `account` tinyint(1) NOT NULL DEFAULT '1',
  `deposit` tinyint(1) NOT NULL DEFAULT '1',
  `withdraw` tinyint(1) NOT NULL DEFAULT '1',
  `wire` tinyint(1) NOT NULL DEFAULT '1',
  `ticket` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blacklist`
--

INSERT INTO `blacklist` (`username`, `account`, `deposit`, `withdraw`, `wire`, `ticket`) VALUES
('pixel', 1, 1, 1, 1, 1),
('salah', 1, 1, 1, 1, 1),
('yessine', 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `deposit`
--

CREATE TABLE `deposit` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `deposit_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deposit_amount` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forgot_password`
--

CREATE TABLE `forgot_password` (
  `email` varchar(100) NOT NULL,
  `forgotpass` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `login_credentials`
--

CREATE TABLE `login_credentials` (
  `username` varchar(30) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `timecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastlogin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rank` varchar(30) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `login_credentials`
--

INSERT INTO `login_credentials` (`username`, `pass`, `email`, `timecreated`, `lastlogin`, `rank`) VALUES
('pixel', '$2y$10$WycMX9tDWKT8e2D1NIXlYOWBiH1F58Knvh4APEmjIvIvawQR6xJNC', 'pixel@gmail.com', '2023-12-10 20:25:53', '2023-12-11 17:36:52', 'none'),
('salah', '$2y$10$sxX6zAoESOPf.HzMcD60rOjxoVWnA66OH9QSByfXQuiql79VFpRpK', 'salah@gmail.com', '2023-12-10 20:26:37', '2023-12-11 17:42:14', 'superadmin'),
('yessine', '$2y$10$S9K46zTXio80TovI/2CG9uixvlIIBZNobT4G22F3nTxAp1LXmTS5S', 'yessine@gmail.com', '2023-12-10 20:26:58', '2023-12-11 17:34:42', 'none');

-- --------------------------------------------------------

--
-- Table structure for table `ranks`
--

CREATE TABLE `ranks` (
  `rank` varchar(30) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `time_opened` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_closed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ticket_content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wire`
--

CREATE TABLE `wire` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `receiver` varchar(30) NOT NULL,
  `wire_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `wire_amount` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `withdraw`
--

CREATE TABLE `withdraw` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `withdraw_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `withdraw_amount` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `wire`
--
ALTER TABLE `wire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `withdraw`
--
ALTER TABLE `withdraw`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
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
-- Constraints for table `deposit`
--
ALTER TABLE `deposit`
  ADD CONSTRAINT `deposit_ibfk_1` FOREIGN KEY (`username`) REFERENCES `login_credentials` (`username`);

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

--
-- Constraints for table `wire`
--
ALTER TABLE `wire`
  ADD CONSTRAINT `wire_ibfk_1` FOREIGN KEY (`username`) REFERENCES `login_credentials` (`username`),
  ADD CONSTRAINT `wire_ibfk_2` FOREIGN KEY (`receiver`) REFERENCES `login_credentials` (`email`);

--
-- Constraints for table `withdraw`
--
ALTER TABLE `withdraw`
  ADD CONSTRAINT `withdraw_ibfk_1` FOREIGN KEY (`username`) REFERENCES `login_credentials` (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
