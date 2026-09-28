-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 23, 2026 at 05:11 AM
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
-- Database: `bmsgraduation205`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `admin_name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Invitation', 'mmirshath@gmail.com', '$2y$10$v.0fQ3SYrE2OJv8Q2touGeZRm8gEUoNwYBxSS102wiej56OvA.naS', 'invitation', '2025-10-13 04:16:07'),
(2, 'Mirshath', 'm@gmail.com', '$2y$10$v.0fQ3SYrE2OJv8Q2touGeZRm8gEUoNwYBxSS102wiej56OvA.naS', 'admin', '2025-10-15 06:41:22'),
(3, 'finance', 'finance@gmail.com', '$2y$10$PMCjct9jnXAMxMST6LqxcePkazUA79V3UidG3.JSLl9kYuO58d.Dm', 'finance', '2025-10-26 07:18:55'),
(4, '123', 'registration@gmail.com', '$2y$10$eXfhBw2M6ekRWgAU0HEEaO39B3yf//X3B2/kH3.oTnrd9CHn7HBWO', 'registrationDesk', '2025-11-24 03:47:56'),
(5, 'cloak user', 'cloak@gmail.com', '$2y$10$yEMOoSnb0jtuH9cX1OerJuTL/scmnS4rJSOXNx1fMquIDyFwYVg92', 'clothCollectReturn', '2025-11-24 04:19:53');

--
-- Triggers `admin`
--
DELIMITER $$
CREATE TRIGGER `after_admin_insert` AFTER INSERT ON `admin` FOR EACH ROW BEGIN
    INSERT INTO `notifications` (table_name, record_id, student_id, action_type, description)
    VALUES (
        'admin',
        NEW.id,
        NULL,
        'insert',
        CONCAT('New admin added: ', NEW.admin_name, ' (Role: ', NEW.role, ')')
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_admin_update` AFTER UPDATE ON `admin` FOR EACH ROW BEGIN
    INSERT INTO `notifications` (table_name, record_id, student_id, action_type, description)
    VALUES (
        'admin',
        NEW.id,
        NULL,
        'update',
        CONCAT('Admin updated: ', NEW.admin_name, ' (Role: ', NEW.role, ')')
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `prevent_admin_delete` BEFORE DELETE ON `admin` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Deletion from admin table is not allowed for security reasons';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_data_table`
--

CREATE TABLE `bulk_data_table` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `seat_no` varchar(50) NOT NULL,
  `program_name` varchar(100) NOT NULL,
  `session_time` varchar(255) DEFAULT NULL,
  `email_sent_yes_no` enum('Yes','No') DEFAULT 'No',
  `email_sent_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `graduated_status` varchar(10) DEFAULT NULL,
  `student_result` varchar(255) DEFAULT NULL,
  `calling_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bulk_data_table`
--

INSERT INTO `bulk_data_table` (`id`, `student_id`, `seat_no`, `program_name`, `session_time`, `email_sent_yes_no`, `email_sent_time`, `created_at`, `graduated_status`, `student_result`, `calling_name`) VALUES
(1, '123456', 'B 01', 'GDM', 'MORNING', 'No', NULL, '2025-11-20 04:41:00', 'Yes', 'D', 'MMM Mirshath CALLING NAME'),
(2, '990190984', 'B 02', 'GDM', 'MORNING', 'No', NULL, '2025-11-20 04:41:00', 'Yes', 'D', 'Hasni Nihar'),
(3, '333', 'B 01', 'BSc (Hons) Global Business Management (Human Resources) - September 2024', 'EVENING', 'No', NULL, '2025-11-20 04:41:00', 'Yes', 'D', 'Hasni Nihar');

-- --------------------------------------------------------

--
-- Table structure for table `clothing_collections`
--

CREATE TABLE `clothing_collections` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `program_name` varchar(255) NOT NULL,
  `collect_cloak` varchar(255) DEFAULT NULL,
  `collect_slashes` varchar(255) DEFAULT NULL,
  `collect_hats` varchar(255) DEFAULT NULL,
  `collected_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `return_cloak` varchar(255) DEFAULT NULL,
  `return_slashes` varchar(255) DEFAULT NULL,
  `return_hats` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clothing_collections`
--

INSERT INTO `clothing_collections` (`id`, `student_id`, `student_name`, `program_name`, `collect_cloak`, `collect_slashes`, `collect_hats`, `collected_at`, `return_cloak`, `return_slashes`, `return_hats`) VALUES
(32, '333', 'Hasni Nihar', 'BSc (Hons) Global Business Management (Human Resources) - September 2024', 'collected', 'collected', 'collected', '2025-11-24 08:43:46', 'returned', 'returned', NULL),
(33, '123456', 'MMM Mirshath CALLING NAME', 'GDM', 'collected', NULL, NULL, '2025-11-24 08:45:16', NULL, NULL, NULL),
(34, '990190984', 'Hasni Nihar', 'GDM', 'collected', NULL, NULL, '2025-11-24 10:01:45', 'returned', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `data_tables`
--

CREATE TABLE `data_tables` (
  `id` int(11) NOT NULL,
  `programName` varchar(255) DEFAULT NULL,
  `graduationFee` int(20) DEFAULT NULL,
  `freeTicket` int(50) DEFAULT NULL,
  `extraTicketFee` int(50) DEFAULT NULL,
  `session` varchar(50) DEFAULT NULL,
  `cloak` tinyint(4) NOT NULL DEFAULT 1,
  `slashes` tinyint(4) NOT NULL DEFAULT 0,
  `hats` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_tables`
--

INSERT INTO `data_tables` (`id`, `programName`, `graduationFee`, `freeTicket`, `extraTicketFee`, `session`, `cloak`, `slashes`, `hats`) VALUES
(1, 'Higher National Diploma in Business - Batch 15', 15000, 1, 3000, 'MORNING', 1, 1, 1),
(2, 'Higher National Diploma in Business - Batch 16', 15000, 1, 3000, 'MORNING', 0, 0, 0),
(3, 'Higher Diploma in Biomedical Science - Batch 27', 15000, 1, 3000, 'MORNING', 0, 0, 0),
(4, 'Higher Diploma in Biotechnology - Batch 21', 15000, 1, 3000, 'MORNING', 0, 0, 0),
(5, 'Higher Diploma in Food Science and Nutrition - Batch 4', 15000, 1, 3000, 'MORNING', 0, 0, 0),
(6, 'Higher Diploma in Medical Biotechnology - Batch 1', 15000, 1, 3000, 'MORNING', 0, 0, 0),
(7, 'Graduate Diploma in Management - Batch 57', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(8, 'Graduate Diploma in Management - Batch 58', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(9, 'Graduate Diploma in Management - Batch 62', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(10, 'Graduate Diploma in Management - Batch 63', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(11, 'Graduate Diploma in Management - Batch 66', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(12, 'Graduate Diploma in Management - Batch 68', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(13, 'Graduate Diploma in Management - Batch 70', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(14, 'Graduate Diploma in Management - Batch 71', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(15, 'Graduate Diploma in Management - Batch 73', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(16, 'Graduate Diploma in Management - Batch 75', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(17, 'Graduate Diploma in Management - Batch 76', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(18, 'Graduate Diploma in Management - Batch 77', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(19, 'Graduate Diploma in Management - Batch 78', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(20, 'Graduate Diploma in Management - Batch 79', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(21, 'Graduate Diploma in Management - Batch 80', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(22, 'Graduate Diploma in Management - Batch 81', 20000, 2, 3000, 'MORNING', 0, 0, 0),
(23, 'Teesside MBA - COHORT 07', 25000, 2, 3000, 'MORNING', 0, 0, 0),
(24, 'MSc Management Northumbria University - COHORT 01', 25000, 2, 3000, 'EVENING', 0, 0, 0),
(25, 'BSc (Hons) Global Business Management - May 2024', 25000, 2, 3000, 'EVENING', 0, 0, 0),
(26, 'BSc (Hons) Global Business Management - September 2024', 25000, 2, 3000, 'EVENING', 0, 0, 0),
(27, 'BSc (Hons) Accounting and Finance - September 2024', 25000, 2, 3000, 'EVENING', 0, 0, 0),
(28, 'BSc (Hons) Global Business Management (Human Resources) - September 2024', 25000, 2, 3000, 'EVENING', 1, 1, 1),
(29, 'BSc (Hons) Global Business Management (Marketing) - September 2024', 25000, 2, 3000, 'EVENING', 0, 0, 0),
(30, 'BSc (Hons) Global Business Management (Marketing) - January 2025', 25000, 2, 3000, 'EVENING', 0, 0, 0),
(31, 'BSc (Hons) Global Business Management - January 2025', 25000, 2, 3000, 'EVENING', 0, 0, 0),
(32, 'BSc (Hons) Global Business Management (Human Resources) - January 2025', 25000, 2, 3000, 'EVENING', 0, 0, 0),
(33, 'BSc (Hons) Accounting and Finance - January 2025', 25000, 2, 3000, 'EVENING', 0, 0, 0),
(34, 'BSc (Hons) Biomedical Science - September 2024', 25000, 2, 3000, 'EVENING', 0, 0, 0),
(35, 'BSc (Hons) Biotechnology - September 2024', 25000, 2, 3000, 'EVENING', 0, 0, 0),
(36, 'BSc (Hons) Biomedical Science - January 2025', 25000, 2, 3000, 'EVENING', 0, 0, 0),
(37, 'BSc (Hons) Biotechnology - January 2025', 25000, 2, 3000, 'EVENING', 0, 0, 0),
(50, 'GDM', 20000, 2, 3000, 'MORNING', 1, 0, 0),
(60, 'ECMS', 30000, 3, 4500, 'EVENING', 0, 0, 0);

--
-- Triggers `data_tables`
--
DELIMITER $$
CREATE TRIGGER `prevent_data_tables_delete` BEFORE DELETE ON `data_tables` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Deletion from data_tables table is not allowed for security reasons';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `email_log`
--

CREATE TABLE `email_log` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `name_in_full` varchar(255) DEFAULT NULL,
  `email_address` varchar(255) NOT NULL,
  `program_name` varchar(255) DEFAULT NULL,
  `seat_no` varchar(50) DEFAULT NULL,
  `session_time` varchar(255) DEFAULT NULL,
  `email_type` enum('single','bulk') DEFAULT 'single',
  `status` enum('sent','failed') DEFAULT 'sent',
  `error_message` text DEFAULT NULL,
  `sent_by` int(11) DEFAULT NULL COMMENT 'Admin ID who triggered the email',
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_log`
--

INSERT INTO `email_log` (`id`, `student_id`, `name_in_full`, `email_address`, `program_name`, `seat_no`, `session_time`, `email_type`, `status`, `error_message`, `sent_by`, `sent_at`) VALUES
(1, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed', 'yournumplz@gmail.com', 'GDM', 'S002', 'MORNING', 'single', 'sent', NULL, 2, '2025-11-16 10:29:57'),
(2, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed', 'mirshath.mmm@gmail.com', 'GDM', 'S002', 'MORNING', 'single', 'sent', NULL, 2, '2025-11-16 10:29:59'),
(3, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'yournumplz@gmail.com', 'GDM', 's001', 'MORNING', 'bulk', 'sent', NULL, 2, '2025-11-20 10:12:18'),
(4, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'mirshath.mmm@gmail.com', 'GDM', 's001', 'MORNING', 'bulk', 'sent', NULL, 2, '2025-11-20 10:12:21'),
(5, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'yournumplz@gmail.com', 'GDM', 's001', 'MORNING', 'bulk', 'sent', NULL, 2, '2025-11-20 10:18:22'),
(6, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'mirshath.mmm@gmail.com', 'GDM', 's001', 'MORNING', 'bulk', 'sent', NULL, 2, '2025-11-20 10:18:25'),
(7, '990190984', 'Mir Test GDM', 'mirshath.m@cgs.lk', 'GDM', 's002', 'MORNING', 'bulk', 'sent', NULL, 2, '2025-11-20 10:18:27'),
(8, '990190984', 'Mir Test GDM', 'mirshath.m@cgs.lk', 'GDM', 's002', 'MORNING', 'single', 'sent', NULL, 2, '2025-11-20 10:36:46'),
(9, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'yournumplz@gmail.com', 'GDM', 's001', 'MORNING', 'bulk', 'sent', NULL, 2, '2025-11-20 10:38:39'),
(10, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'mirshath.mmm@gmail.com', 'GDM', 's001', 'MORNING', 'bulk', 'sent', NULL, 2, '2025-11-20 10:38:41'),
(11, '990190984', 'Mir Test GDM', 'mirshath.m@cgs.lk', 'GDM', 's002', 'MORNING', 'bulk', 'sent', NULL, 2, '2025-11-20 10:38:44'),
(12, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'yournumplz@gmail.com', 'GDM', 's001', 'MORNING', 'bulk', 'sent', NULL, 2, '2025-11-20 10:43:22'),
(13, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'mirshath.mmm@gmail.com', 'GDM', 's001', 'MORNING', 'bulk', 'sent', NULL, 2, '2025-11-20 10:43:25'),
(14, '990190984', 'Mir Test GDM', 'mirshath.m@cgs.lk', 'GDM', 's002', 'MORNING', 'bulk', 'sent', NULL, 2, '2025-11-20 10:43:28'),
(15, '990190984', 'Mir Test GDM', 'mirshath.m@cgs.lk', 'GDM', 's002', 'MORNING', 'single', 'sent', NULL, 2, '2025-11-20 10:43:53'),
(16, '990190984', 'TEST CALLING NAME', 'mirshath.m@cgs.lk', 'GDM', 's002', 'MORNING', 'single', 'sent', NULL, 2, '2025-11-20 10:58:07'),
(17, '990190984', 'Hasni Nihar', 'webmaster@bms.ac.lk', 'GDM', '2', 'MORNING', 'single', 'sent', NULL, 2, '2025-11-20 14:46:04'),
(18, '990190984', 'Hasni Nihar', 'mirshath.m@cgs.lk', 'GDM', '2', 'MORNING', 'single', 'sent', NULL, 2, '2025-11-20 14:46:06'),
(19, '123456', 'MMM Mirshath CALLING NAME', 'yournumplz@gmail.com', 'GDM', 'B 01', 'MORNING', 'single', 'sent', NULL, 2, '2025-11-22 15:40:03'),
(20, '123456', 'MMM Mirshath CALLING NAME', 'mirshath.mmm@gmail.com', 'GDM', 'B 01', 'MORNING', 'single', 'sent', NULL, 2, '2025-11-22 15:40:06'),
(21, '123456', 'MMM Mirshath CALLING NAME', 'yournumplz@gmail.com', 'GDM', 'B 01', 'MORNING', 'single', 'sent', NULL, 2, '2025-11-22 15:42:20'),
(22, '123456', 'MMM Mirshath CALLING NAME', 'mirshath.mmm@gmail.com', 'GDM', 'B 01', 'MORNING', 'single', 'sent', NULL, 2, '2025-11-22 15:42:23'),
(23, '123456', 'MMM Mirshath CALLING NAME', 'yournumplz@gmail.com', 'GDM', 'B 01', 'MORNING', 'single', 'sent', NULL, 2, '2025-11-22 15:43:22'),
(24, '123456', 'MMM Mirshath CALLING NAME', 'mirshath.mmm@gmail.com', 'GDM', 'B 01', 'MORNING', 'single', 'sent', NULL, 2, '2025-11-22 15:43:24'),
(25, '123456', 'MMM Mirshath CALLING NAME', 'yournumplz@gmail.com', 'GDM', 'B 01', 'Session 1 ( 10am - 1pm)', 'single', 'sent', NULL, 2, '2025-11-22 15:47:14'),
(26, '123456', 'MMM Mirshath CALLING NAME', 'mirshath.mmm@gmail.com', 'GDM', 'B 01', 'Session 1 ( 10am - 1pm)', 'single', 'sent', NULL, 2, '2025-11-22 15:47:16'),
(27, '990190984', 'Hasni Nihar', 'webmaster@bms.ac.lk', 'GDM', 'B 02', 'Session 1 ( 10am - 1pm)', 'single', 'sent', NULL, 2, '2025-11-22 15:47:37'),
(28, '990190984', 'Hasni Nihar', 'mirshath.m@cgs.lk', 'GDM', 'B 02', 'Session 1 ( 10am - 1pm)', 'single', 'sent', NULL, 2, '2025-11-22 15:47:40'),
(29, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'yournumplz@gmail.com', 'GDM', 'B 01', 'Session 1 ( 10am - 1pm)', 'bulk', 'sent', NULL, 2, '2025-11-22 16:01:21'),
(30, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'mirshath.mmm@gmail.com', 'GDM', 'B 01', 'Session 1 ( 10am - 1pm)', 'bulk', 'sent', NULL, 2, '2025-11-22 16:01:23'),
(31, '990190984', 'Mohamed Mirshath', 'webmaster@bms.ac.lk', 'GDM', 'B 02', 'Session 1 ( 10am - 1pm)', 'bulk', 'sent', NULL, 2, '2025-11-22 16:01:26'),
(32, '990190984', 'Mohamed Mirshath', 'mirshath.m@cgs.lk', 'GDM', 'B 02', 'Session 1 ( 10am - 1pm)', 'bulk', 'sent', NULL, 2, '2025-11-22 16:01:29'),
(33, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'yournumplz@gmail.com', 'GDM', 'B 01', 'Session 1 ( 10.00am - 1.00pm)', 'bulk', 'sent', NULL, 2, '2025-11-22 16:05:01'),
(34, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'mirshath.mmm@gmail.com', 'GDM', 'B 01', 'Session 1 ( 10.00am - 1.00pm)', 'bulk', 'sent', NULL, 2, '2025-11-22 16:05:05'),
(35, '990190984', 'Mohamed Mirshath', 'webmaster@bms.ac.lk', 'GDM', 'B 02', 'Session 1 ( 10.00am - 1.00pm)', 'bulk', 'sent', NULL, 2, '2025-11-22 16:05:07'),
(36, '990190984', 'Mohamed Mirshath', 'mirshath.m@cgs.lk', 'GDM', 'B 02', 'Session 1 ( 10.00am - 1.00pm)', 'bulk', 'sent', NULL, 2, '2025-11-22 16:05:10'),
(37, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'yournumplz@gmail.com', 'GDM', 'B 01', 'Session 1 ( 10.00am - 1.00pm)', 'bulk', 'sent', NULL, 2, '2025-11-22 16:06:23'),
(38, '123456', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'mirshath.mmm@gmail.com', 'GDM', 'B 01', 'Session 1 ( 10.00am - 1.00pm)', 'bulk', 'sent', NULL, 2, '2025-11-22 16:06:26'),
(39, '990190984', 'Mohamed Mirshath', 'webmaster@bms.ac.lk', 'GDM', 'B 02', 'Session 1 ( 10.00am - 1.00pm)', 'bulk', 'sent', NULL, 2, '2025-11-22 16:06:32'),
(40, '990190984', 'Mohamed Mirshath', 'mirshath.m@cgs.lk', 'GDM', 'B 02', 'Session 1 ( 10.00am - 1.00pm)', 'bulk', 'sent', NULL, 2, '2025-11-22 16:06:34');

--
-- Triggers `email_log`
--
DELIMITER $$
CREATE TRIGGER `after_email_log_insert` AFTER INSERT ON `email_log` FOR EACH ROW BEGIN
    IF NEW.status = 'sent' THEN
        INSERT INTO `notifications` (table_name, record_id, student_id, action_type, description)
        VALUES (
            'email_log',
            NEW.id,
            NEW.student_id,
            'INSERT',
            CONCAT('Email sent to ', NEW.name_in_full, ' (', NEW.email_address, ')')
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `prevent_email_log_delete` BEFORE DELETE ON `email_log` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Deletion from email_log table is not allowed for security reasons';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `extra_ticket_log`
--

CREATE TABLE `extra_ticket_log` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `added_tickets` int(11) NOT NULL,
  `ticket_price` decimal(10,2) NOT NULL,
  `total_added` decimal(10,2) NOT NULL,
  `added_by` int(11) DEFAULT NULL,
  `added_on` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `extra_ticket_log`
--
DELIMITER $$
CREATE TRIGGER `trg_extra_ticket_insert` AFTER INSERT ON `extra_ticket_log` FOR EACH ROW BEGIN
    INSERT INTO notifications (
        table_name,
        record_id,
        student_id,
        action_type,
        description
    ) VALUES (
        'extra_ticket_log',
        NEW.id,
        NEW.student_id,
        'INSERT',
        CONCAT('Added ', NEW.added_tickets, ' extra ticket(s) worth Rs. ', NEW.total_added)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_extra_ticket_update` AFTER UPDATE ON `extra_ticket_log` FOR EACH ROW BEGIN
    INSERT INTO notifications (
        table_name,
        record_id,
        student_id,
        action_type,
        description
    ) VALUES (
        'extra_ticket_log',
        NEW.id,
        NEW.student_id,
        'UPDATE',
        CONCAT('Updated extra tickets: ', NEW.added_tickets, ' ticket(s), total Rs. ', NEW.total_added)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_prevent_delete_extra_ticket_log` BEFORE DELETE ON `extra_ticket_log` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' 
    SET MESSAGE_TEXT = 'DELETE is not allowed on extra_ticket_log table.';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `table_name`, `record_id`, `student_id`, `action_type`, `description`, `status`, `created_at`) VALUES
(1, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-10 04:33:28'),
(2, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'read', '2025-11-10 04:33:31'),
(3, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'read', '2025-11-10 05:19:28'),
(4, 'email_log', 1, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed (yournumplz@gmail.com)', 'read', '2025-11-16 04:59:57'),
(5, 'email_log', 2, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed (mirshath.mmm@gmail.com)', 'read', '2025-11-16 04:59:59'),
(6, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-16 05:51:21'),
(7, 'email_log', 3, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed   (yournumplz@gmail.com)', 'read', '2025-11-20 04:42:18'),
(8, 'email_log', 4, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed   (mirshath.mmm@gmail.com)', 'read', '2025-11-20 04:42:21'),
(9, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-20 04:47:52'),
(10, 'email_log', 5, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed   (yournumplz@gmail.com)', 'read', '2025-11-20 04:48:22'),
(11, 'email_log', 6, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed   (mirshath.mmm@gmail.com)', 'read', '2025-11-20 04:48:25'),
(12, 'email_log', 7, '990190984', 'INSERT', 'Email sent to Mir Test GDM (mirshath.m@cgs.lk)', 'read', '2025-11-20 04:48:27'),
(13, 'email_log', 8, '990190984', 'INSERT', 'Email sent to Mir Test GDM (mirshath.m@cgs.lk)', 'read', '2025-11-20 05:06:46'),
(14, 'email_log', 9, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed   (yournumplz@gmail.com)', 'read', '2025-11-20 05:08:39'),
(15, 'email_log', 10, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed   (mirshath.mmm@gmail.com)', 'read', '2025-11-20 05:08:41'),
(16, 'email_log', 11, '990190984', 'INSERT', 'Email sent to Mir Test GDM (mirshath.m@cgs.lk)', 'read', '2025-11-20 05:08:44'),
(17, 'email_log', 12, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed   (yournumplz@gmail.com)', 'read', '2025-11-20 05:13:22'),
(18, 'email_log', 13, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed   (mirshath.mmm@gmail.com)', 'read', '2025-11-20 05:13:25'),
(19, 'email_log', 14, '990190984', 'INSERT', 'Email sent to Mir Test GDM (mirshath.m@cgs.lk)', 'read', '2025-11-20 05:13:28'),
(20, 'email_log', 15, '990190984', 'INSERT', 'Email sent to Mir Test GDM (mirshath.m@cgs.lk)', 'read', '2025-11-20 05:13:53'),
(21, 'email_log', 16, '990190984', 'INSERT', 'Email sent to TEST CALLING NAME (mirshath.m@cgs.lk)', 'read', '2025-11-20 05:28:07'),
(22, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-20 09:15:03'),
(23, 'email_log', 17, '990190984', 'INSERT', 'Email sent to Hasni Nihar (webmaster@bms.ac.lk)', 'read', '2025-11-20 09:16:04'),
(24, 'email_log', 18, '990190984', 'INSERT', 'Email sent to Hasni Nihar (mirshath.m@cgs.lk)', 'read', '2025-11-20 09:16:06'),
(25, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-20 09:54:51'),
(26, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-20 09:57:50'),
(27, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'read', '2025-11-22 06:05:08'),
(28, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 06:05:11'),
(29, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 06:08:49'),
(30, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 06:09:25'),
(31, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 06:09:31'),
(32, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 06:10:08'),
(33, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:11:29'),
(34, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:14:21'),
(35, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 06:14:33'),
(36, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 06:15:29'),
(37, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 06:15:35'),
(38, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:19:04'),
(39, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:20:09'),
(40, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 06:20:11'),
(41, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:20:15'),
(42, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:24:20'),
(43, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:24:24'),
(44, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 06:33:27'),
(45, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 06:33:53'),
(46, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:33:55'),
(47, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:34:23'),
(48, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:34:41'),
(49, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:34:48'),
(50, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 06:35:57'),
(51, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:43:20'),
(52, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 06:43:23'),
(53, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:43:32'),
(54, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:44:35'),
(55, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:44:41'),
(56, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:49:21'),
(57, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 06:49:25'),
(58, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 08:01:18'),
(59, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 08:28:37'),
(60, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 08:28:39'),
(61, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 08:28:44'),
(62, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 08:29:06'),
(63, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 08:30:20'),
(64, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 08:30:21'),
(65, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 08:30:28'),
(66, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2025-11-22 08:34:30'),
(67, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 08:35:24'),
(68, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'read', '2025-11-22 08:35:38'),
(69, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'read', '2025-11-22 08:35:43'),
(70, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'read', '2025-11-22 08:35:48'),
(71, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'read', '2025-11-22 08:36:13'),
(72, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'read', '2025-11-22 08:36:19'),
(73, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'read', '2025-11-22 08:37:07'),
(74, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'read', '2025-11-22 08:37:12'),
(75, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'read', '2025-11-22 08:37:24'),
(76, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'read', '2025-11-22 08:37:30'),
(77, 'email_log', 19, '123456', 'INSERT', 'Email sent to MMM Mirshath CALLING NAME (yournumplz@gmail.com)', 'read', '2025-11-22 10:10:03'),
(78, 'email_log', 20, '123456', 'INSERT', 'Email sent to MMM Mirshath CALLING NAME (mirshath.mmm@gmail.com)', 'read', '2025-11-22 10:10:06'),
(79, 'email_log', 21, '123456', 'INSERT', 'Email sent to MMM Mirshath CALLING NAME (yournumplz@gmail.com)', 'read', '2025-11-22 10:12:20'),
(80, 'email_log', 22, '123456', 'INSERT', 'Email sent to MMM Mirshath CALLING NAME (mirshath.mmm@gmail.com)', 'read', '2025-11-22 10:12:23'),
(81, 'email_log', 23, '123456', 'INSERT', 'Email sent to MMM Mirshath CALLING NAME (yournumplz@gmail.com)', 'read', '2025-11-22 10:13:22'),
(82, 'email_log', 24, '123456', 'INSERT', 'Email sent to MMM Mirshath CALLING NAME (mirshath.mmm@gmail.com)', 'read', '2025-11-22 10:13:24'),
(83, 'email_log', 25, '123456', 'INSERT', 'Email sent to MMM Mirshath CALLING NAME (yournumplz@gmail.com)', 'read', '2025-11-22 10:17:14'),
(84, 'email_log', 26, '123456', 'INSERT', 'Email sent to MMM Mirshath CALLING NAME (mirshath.mmm@gmail.com)', 'read', '2025-11-22 10:17:16'),
(85, 'email_log', 27, '990190984', 'INSERT', 'Email sent to Hasni Nihar (webmaster@bms.ac.lk)', 'read', '2025-11-22 10:17:37'),
(86, 'email_log', 28, '990190984', 'INSERT', 'Email sent to Hasni Nihar (mirshath.m@cgs.lk)', 'read', '2025-11-22 10:17:40'),
(87, 'email_log', 29, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed   (yournumplz@gmail.com)', 'read', '2025-11-22 10:31:21'),
(88, 'email_log', 30, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed   (mirshath.mmm@gmail.com)', 'read', '2025-11-22 10:31:23'),
(89, 'email_log', 31, '990190984', 'INSERT', 'Email sent to Mohamed Mirshath (webmaster@bms.ac.lk)', 'read', '2025-11-22 10:31:26'),
(90, 'email_log', 32, '990190984', 'INSERT', 'Email sent to Mohamed Mirshath (mirshath.m@cgs.lk)', 'read', '2025-11-22 10:31:29'),
(91, 'email_log', 33, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed   (yournumplz@gmail.com)', 'read', '2025-11-22 10:35:01'),
(92, 'email_log', 34, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed   (mirshath.mmm@gmail.com)', 'read', '2025-11-22 10:35:05'),
(93, 'email_log', 35, '990190984', 'INSERT', 'Email sent to Mohamed Mirshath (webmaster@bms.ac.lk)', 'read', '2025-11-22 10:35:07'),
(94, 'email_log', 36, '990190984', 'INSERT', 'Email sent to Mohamed Mirshath (mirshath.m@cgs.lk)', 'read', '2025-11-22 10:35:10'),
(95, 'email_log', 37, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed   (yournumplz@gmail.com)', 'read', '2025-11-22 10:36:23'),
(96, 'email_log', 38, '123456', 'INSERT', 'Email sent to Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed   (mirshath.mmm@gmail.com)', 'read', '2025-11-22 10:36:26'),
(97, 'email_log', 39, '990190984', 'INSERT', 'Email sent to Mohamed Mirshath (webmaster@bms.ac.lk)', 'read', '2025-11-22 10:36:32'),
(98, 'email_log', 40, '990190984', 'INSERT', 'Email sent to Mohamed Mirshath (mirshath.m@cgs.lk)', 'read', '2025-11-22 10:36:34'),
(99, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'read', '2025-11-22 13:00:12'),
(100, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'read', '2025-11-22 13:00:55'),
(101, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'read', '2025-11-22 13:00:57'),
(102, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'read', '2025-11-22 13:01:24'),
(103, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'read', '2025-11-22 13:02:18'),
(104, 'admin', 4, NULL, 'insert', 'New admin added: 123 (Role: registrationDesk)', 'unread', '2025-11-24 03:47:56'),
(105, 'admin', 5, NULL, 'insert', 'New admin added: cloth (Role: clothCollectReturn)', 'unread', '2025-11-24 04:19:53'),
(106, 'admin', 5, NULL, 'update', 'Admin updated: cloak user (Role: clothCollectReturn)', 'unread', '2025-11-24 04:27:33'),
(107, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'unread', '2025-11-24 06:16:37'),
(108, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'unread', '2025-11-24 06:16:39'),
(109, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'unread', '2025-11-24 06:16:46'),
(110, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'unread', '2025-11-24 06:17:18'),
(111, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'unread', '2025-11-24 06:17:34'),
(112, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'unread', '2025-11-24 07:41:18'),
(113, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'unread', '2025-11-24 07:41:20'),
(114, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'unread', '2025-11-24 07:41:22'),
(115, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'unread', '2025-11-24 07:41:27'),
(116, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'unread', '2025-11-24 07:41:36'),
(117, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'unread', '2025-11-24 07:41:42'),
(118, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'unread', '2025-11-24 08:44:14'),
(119, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'unread', '2025-11-24 08:44:16'),
(120, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'unread', '2025-11-24 08:44:18'),
(121, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'unread', '2025-11-24 08:45:08'),
(122, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'unread', '2025-11-24 10:01:11'),
(123, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'unread', '2026-02-23 03:52:49'),
(124, 'registered_students', 3, '990190984', 'UPDATE', 'Student record updated for Mohamed Mirshath, graduation payment status: Not-Completed', 'unread', '2026-02-23 03:52:51'),
(125, 'registered_students', 2, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: paid', 'unread', '2026-02-23 03:54:23');

--
-- Triggers `notifications`
--
DELIMITER $$
CREATE TRIGGER `prevent_notifications_delete` BEFORE DELETE ON `notifications` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Deletion from notifications table is not allowed for security reasons';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `old_student_db`
--

CREATE TABLE `old_student_db` (
  `id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `DOB` date NOT NULL,
  `given_email` varchar(50) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `mobile_no` int(15) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `in_no` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `old_student_db`
--

INSERT INTO `old_student_db` (`id`, `student_id`, `name`, `DOB`, `given_email`, `program`, `mobile_no`, `payment_status`, `status`, `in_no`) VALUES
(1, '123456', 'Mir', '2025-10-30', 'mirshath.mmm@gmail.com', 'GDM', 25490455, 'paid', 'registered', 10),
(2, '333', 'mohamed', '2025-10-08', 'yournumplz@gmail.com', 'BSc (Hons) Global Business Management (Human Resources) - September 2024', 778899, 'paid', 'registered', 11),
(3, '990190984', 'Mir Test GDM', '2025-11-02', 'bmsmirshath@gmail.com', 'GDM', 766158014, 'paid', 'registered', 12);

--
-- Triggers `old_student_db`
--
DELIMITER $$
CREATE TRIGGER `prevent_old_student_delete` BEFORE DELETE ON `old_student_db` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Deletion from old_student_db table is not allowed for security reasons';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `payment_records`
--

CREATE TABLE `payment_records` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `program_name` varchar(255) NOT NULL,
  `graduation_fee` decimal(10,2) DEFAULT NULL,
  `free_ticket_count` int(5) DEFAULT 0,
  `extra_ticket_count` int(5) DEFAULT 0,
  `extra_ticket_fee` decimal(10,2) DEFAULT 0.00,
  `total_amount` int(11) DEFAULT NULL,
  `receipt_number` varchar(20) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_records`
--

INSERT INTO `payment_records` (`id`, `student_id`, `program_name`, `graduation_fee`, `free_ticket_count`, `extra_ticket_count`, `extra_ticket_fee`, `total_amount`, `receipt_number`, `payment_date`, `created_by`) VALUES
(1, '123456', 'GDM', 20000.00, 2, 11, 33000.00, 53000, 'GC20256990', '2025-10-31 04:04:42', NULL),
(2, '333', 'BSc (Hons) Global Business Management (Human Resources) - September 2024', 25000.00, 2, 15, 45000.00, 70000, 'GC20255138', '2025-10-31 05:06:28', NULL),
(3, '123456', 'GDM', 20000.00, 2, 4, 12000.00, 32000, 'GC20256297', '2025-11-01 08:29:32', 'Mirshath');

--
-- Triggers `payment_records`
--
DELIMITER $$
CREATE TRIGGER `log_payment_insert` AFTER INSERT ON `payment_records` FOR EACH ROW BEGIN
    INSERT INTO notifications (table_name, record_id, student_id, action_type, description)
    VALUES (
        'payment_records',
        NEW.id,
        NEW.student_id,
        'INSERT',
        CONCAT('New payment record added for student ID ', NEW.student_id,
               ', program: ', NEW.program_name,
               ', amount: ', NEW.total_amount)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_payment_update` AFTER UPDATE ON `payment_records` FOR EACH ROW BEGIN
    INSERT INTO notifications (table_name, record_id, student_id, action_type, description)
    VALUES (
        'payment_records',
        NEW.id,
        NEW.student_id,
        'UPDATE',
        CONCAT('Payment record updated for student ID ', NEW.student_id,
               ', receipt number: ', NEW.receipt_number)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `prevent_payment_records_delete` BEFORE DELETE ON `payment_records` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Deletion from payment_records table is not allowed for security reasons';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `registered_students`
--

CREATE TABLE `registered_students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `in_no` int(11) DEFAULT NULL,
  `dob` date NOT NULL,
  `name_in_full` varchar(255) NOT NULL,
  `title` varchar(10) DEFAULT NULL,
  `calling_name` varchar(255) DEFAULT NULL,
  `confirmation` tinyint(4) NOT NULL DEFAULT 0,
  `program_name` varchar(255) DEFAULT NULL,
  `given_email_add` varchar(50) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `phone_no` varchar(15) DEFAULT NULL,
  `attend` varchar(50) DEFAULT NULL,
  `crsfee_payment_status` varchar(50) DEFAULT NULL,
  `graduation_payment_status` varchar(50) DEFAULT 'Not-Completed',
  `invitation_collected` varchar(255) DEFAULT NULL,
  `updated_at_invitation` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registered_students`
--

INSERT INTO `registered_students` (`id`, `student_id`, `in_no`, `dob`, `name_in_full`, `calling_name`, `confirmation`, `program_name`, `given_email_add`, `email_address`, `phone_no`, `attend`, `crsfee_payment_status`, `graduation_payment_status`, `invitation_collected`, `updated_at_invitation`, `created_at`) VALUES
(2, '333', NULL, '2026-02-23', 'mohamed', '', 0, 'BSc (Hons) Global Business Management (Human Resources) - September 2024', 'yournumplz@gmail.com', 'yournumplz@gmail.com', '778899', NULL, 'paid', 'paid', NULL, NULL, '2025-10-31 10:35:56'),
(3, '990190984', NULL, '2025-11-02', 'Mohamed Mirshath', '', 0, 'GDM', 'mirshath.m@cgs.lk', 'webmaster@bms.ac.lk', '+94766158014', NULL, 'paid', 'Not-Completed', NULL, NULL, '2025-11-03 10:26:51');

--
-- Triggers `registered_students`
--
DELIMITER $$
CREATE TRIGGER `log_registered_insert` AFTER INSERT ON `registered_students` FOR EACH ROW BEGIN
    INSERT INTO notifications (table_name, record_id, student_id, action_type, description)
    VALUES (
        'registered_students',
        NEW.id,
        NEW.student_id,
        'INSERT',
        CONCAT('New student registered: ', NEW.name_in_full,
               ' (Program: ', NEW.program_name, ')')
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_registered_update` AFTER UPDATE ON `registered_students` FOR EACH ROW BEGIN
    INSERT INTO notifications (table_name, record_id, student_id, action_type, description)
    VALUES (
        'registered_students',
        NEW.id,
        NEW.student_id,
        'UPDATE',
        CONCAT('Student record updated for ', NEW.name_in_full,
               ', graduation payment status: ', NEW.graduation_payment_status)
    );
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bulk_data_table`
--
ALTER TABLE `bulk_data_table`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `clothing_collections`
--
ALTER TABLE `clothing_collections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student` (`student_id`);

--
-- Indexes for table `data_tables`
--
ALTER TABLE `data_tables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_log`
--
ALTER TABLE `email_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_email` (`email_address`),
  ADD KEY `idx_program` (`program_name`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_sent_at` (`sent_at`),
  ADD KEY `fk_sent_by` (`sent_by`);

--
-- Indexes for table `extra_ticket_log`
--
ALTER TABLE `extra_ticket_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_added_by` (`added_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `old_student_db`
--
ALTER TABLE `old_student_db`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `payment_records`
--
ALTER TABLE `payment_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registered_students`
--
ALTER TABLE `registered_students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bulk_data_table`
--
ALTER TABLE `bulk_data_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `clothing_collections`
--
ALTER TABLE `clothing_collections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `data_tables`
--
ALTER TABLE `data_tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `email_log`
--
ALTER TABLE `email_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `extra_ticket_log`
--
ALTER TABLE `extra_ticket_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `old_student_db`
--
ALTER TABLE `old_student_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_records`
--
ALTER TABLE `payment_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `registered_students`
--
ALTER TABLE `registered_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `email_log`
--
ALTER TABLE `email_log`
  ADD CONSTRAINT `fk_sent_by` FOREIGN KEY (`sent_by`) REFERENCES `admin` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
