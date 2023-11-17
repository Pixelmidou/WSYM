-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2023 at 09:57 PM
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
-- Table structure for table `adminusers`
--

CREATE TABLE `adminusers` (
  `username` varchar(30) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `rank` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `adminusers`
--

INSERT INTO `adminusers` (`username`, `pass`, `email`, `rank`) VALUES
('salah', '$2y$10$arEluj.rGqgZCK.eG.gw4ubYCzLM.Yz676o/jb09Ow2bGnYLZCNL.', 'salah@gmail.com', 'admin');

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
('pixel', 'pixel@gmail.com', '10.0'),
('yessine', 'yessine@gmail.com', '10.0');

-- --------------------------------------------------------

--
-- Table structure for table `deposit`
--

CREATE TABLE `deposit` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `deposit_date` datetime NOT NULL,
  `deposit_amount` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `deposit`
--

INSERT INTO `deposit` (`id`, `username`, `deposit_date`, `deposit_amount`) VALUES
(1, 'pixel', '2023-11-17 08:24:15', '45.00'),
(2, 'yessine', '2023-11-16 12:22:00', '88.00'),
(3, 'yessine', '2023-11-17 00:00:00', '448.00');

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
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `login_credentials`
--

INSERT INTO `login_credentials` (`username`, `pass`, `email`) VALUES
('pixel', '$2y$10$arEluj.rGqgZCK.eG.gw4ubYCzLM.Yz676o/jb09Ow2bGnYLZCNL.', 'pixel@gmail.com'),
('yessine', '$2y$10$ECxdedkPizxS2HKw.ntDh.QG/0BVNqKTrVTyWpN7.LtS0.oXh8GEe', 'yessine@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `username` varchar(30) NOT NULL,
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
  `wire_date` datetime NOT NULL,
  `wire_amount` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `withdraw`
--

CREATE TABLE `withdraw` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `withdraw_date` datetime NOT NULL,
  `withdraw_amount` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminusers`
--
ALTER TABLE `adminusers`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `balance`
--
ALTER TABLE `balance`
  ADD PRIMARY KEY (`username`),
  ADD KEY `email` (`email`);

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
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`username`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
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
  ADD CONSTRAINT `balance_ibfk_2` FOREIGN KEY (`email`) REFERENCES `login_credentials` (`email`);

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
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`username`) REFERENCES `login_credentials` (`username`);

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
