-- ============================================================================
--  WSYM - database schema (MySQL / MariaDB, stock XAMPP)
--
--  phpMyAdmin > New > database name: wsym > Import > choose this file.
--
--  Ten tables and three demo accounts whose passwords are the ones printed on the
--  login page:
--        Admin            / Admin123
--        VerifiedUser     / VerifiedUser123
--        nonVerifiedUser  / nonVerifiedUser123    (email not verified yet)
--
--  No procedures, no triggers, no migration runner: plain SQL.
--  Re-importing this file rebuilds every table from scratch.
-- ============================================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `ticket_message`;
DROP TABLE IF EXISTS `ticket`;
DROP TABLE IF EXISTS `wire`;
DROP TABLE IF EXISTS `withdraw`;
DROP TABLE IF EXISTS `deposit`;
DROP TABLE IF EXISTS `blacklist`;
DROP TABLE IF EXISTS `balance`;
DROP TABLE IF EXISTS `login_credentials`;
DROP TABLE IF EXISTS `ranks`;
DROP TABLE IF EXISTS `admin_audit`;

-- ---- who changed what (written by lib/audit.php) ---------------------------
CREATE TABLE IF NOT EXISTS `admin_audit` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `actor` varchar(30) NOT NULL,
  `action` varchar(40) NOT NULL,
  `target` varchar(100) NOT NULL DEFAULT '',
  `details` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---- ranks (login_credentials.rankname points here) -----------------------
CREATE TABLE `ranks` (
  `rankname` varchar(30) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`rankname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ranks` (`rankname`, `description`) VALUES
('admin', NULL),
('none', NULL),
('superadmin', NULL);

-- ---- accounts -------------------------------------------------------------
CREATE TABLE `login_credentials` (
  `username` varchar(30) NOT NULL,
  `pass` varchar(255) NOT NULL,                 -- password_hash() output
  `email` varchar(100) NOT NULL,
  `email_verif` tinyint(1) NOT NULL DEFAULT 0,
  `email_verif_token` varchar(64) DEFAULT NULL,
  `email_verif_expire` datetime DEFAULT NULL,
  `timecreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `lastlogin` timestamp NULL DEFAULT NULL,
  `pfp` varchar(40) NOT NULL DEFAULT 'favicon.ico',
  `rankname` varchar(30) NOT NULL DEFAULT 'none',
  `pass_reset_token` varchar(64) DEFAULT NULL,
  `pass_reset_token_expire` datetime DEFAULT NULL,
  -- bumped by the admin ("Info management" > Expire session). The value is copied into
  -- the user's session at login; when it stops matching, their next click logs them out.
  `session_epoch` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `token` (`pass_reset_token`),
  KEY `rank` (`rankname`),
  CONSTRAINT `login_credentials_ibfk_1` FOREIGN KEY (`rankname`) REFERENCES `ranks` (`rankname`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- demo accounts, hashed once with password_hash(..., PASSWORD_BCRYPT).
-- If you change a password here, change the text printed in index.php too.
INSERT INTO `login_credentials`
  (`username`,`pass`,`email`,`email_verif`,`pfp`,`rankname`,`session_epoch`) VALUES
('Admin',          '$2y$12$GLJszFCHrK4suK0nXYYUmeFyfm9FrjHJhc/ARXN5kBkudG5SOMKA2', 'Admin@example.com',          1, 'favicon.ico', 'superadmin', 0),
('VerifiedUser',   '$2y$12$Z/B4x1Pnfsisl75.GxD06.JuNr700kGjV.LqppaaOY4VMq0vR3EwC', 'VerifiedUser@example.com',   1, 'favicon.ico', 'none',       0),
('nonVerifiedUser','$2y$12$CIQaf5nGEUTn5fD6tmtHoe3EBCrU3r1AMWWBcXTVK.A4kDxqSVn0K', 'nonVerifiedUser@example.com',0, 'favicon.ico', 'none',       0);

-- ---- balances -------------------------------------------------------------
CREATE TABLE `balance` (
  `username` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `balance` decimal(9,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`username`),
  KEY `balance_ibfk_2` (`email`),
  CONSTRAINT `balance_ibfk_1` FOREIGN KEY (`username`) REFERENCES `login_credentials` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `balance_ibfk_2` FOREIGN KEY (`email`) REFERENCES `login_credentials` (`email`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `balance` (`username`,`email`,`balance`) VALUES
('Admin',          'Admin@example.com',           0.00),
('VerifiedUser',   'VerifiedUser@example.com',    0.00),
('nonVerifiedUser','nonVerifiedUser@example.com', 0.00);

-- ---- per-user permissions (1 = allowed) -----------------------------------
CREATE TABLE `blacklist` (
  `username` varchar(30) NOT NULL,
  `account` tinyint(1) NOT NULL DEFAULT 1,
  `deposit` tinyint(1) NOT NULL DEFAULT 1,
  `withdraw` tinyint(1) NOT NULL DEFAULT 1,
  `wire` tinyint(1) NOT NULL DEFAULT 1,
  `ticket` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`username`),
  CONSTRAINT `blacklist_ibfk_1` FOREIGN KEY (`username`) REFERENCES `login_credentials` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `blacklist` (`username`,`account`,`deposit`,`withdraw`,`wire`,`ticket`) VALUES
('Admin',1,1,1,1,1),
('VerifiedUser',1,1,1,1,1),
('nonVerifiedUser',1,1,1,1,1);

-- ---- ledger: deposits, withdrawals and wires -------------------------------
CREATE TABLE `deposit` (
  `username` varchar(30) NOT NULL,
  `deposit_date` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `deposit_amount` decimal(9,2) NOT NULL,
  PRIMARY KEY (`username`,`deposit_date`),
  CONSTRAINT `deposit_positive` CHECK (`deposit_amount` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `withdraw` (
  `username` varchar(30) NOT NULL,
  `withdraw_date` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `withdraw_amount` decimal(9,2) NOT NULL,
  PRIMARY KEY (`username`,`withdraw_date`),
  CONSTRAINT `withdraw_positive` CHECK (`withdraw_amount` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `wire` (
  `username` varchar(30) NOT NULL,
  `receiver` varchar(30) NOT NULL,
  `wire_date` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `wire_amount` decimal(9,2) NOT NULL,
  PRIMARY KEY (`username`,`wire_date`),
  KEY `wire_ibfk_2` (`receiver`),
  CONSTRAINT `wire_positive` CHECK (`wire_amount` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---- support = one conversation per customer ------------------------------
-- `ticket`         the thread and its state (who owns it, is it open, which conversation)
-- `ticket_message` what was said inside it. The `conversation` number is copied on every
--                  message, so closing and starting a new conversation keeps the old
--                  messages readable instead of losing them.
CREATE TABLE `ticket` (
  `username` varchar(30) NOT NULL,
  `status` varchar(6) NOT NULL DEFAULT 'opened',
  `subject` varchar(120) NOT NULL DEFAULT '',
  `time_opened` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `time_closed` datetime DEFAULT NULL,
  `last_message` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `conversation` int unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`username`),
  KEY `last_message` (`last_message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `ticket_message` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `sender` varchar(30) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `body` text NOT NULL,
  `sent_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `conversation` int unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `thread` (`username`,`conversation`,`sent_at`),
  CONSTRAINT `ticket_message_ibfk_1` FOREIGN KEY (`username`) REFERENCES `ticket` (`username`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET foreign_key_checks = 1;
