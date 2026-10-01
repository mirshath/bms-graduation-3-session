-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2026 at 02:09 PM
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
-- Database: `bms_graduation_3_session`
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
(1, 'Admin', 'admin@bms.ac.lk', '$2y$10$ygq.jvBdwcXgUKMq0wW.3.gPrvrGQGN.mD0de9Qj3aZMKmDMYPn0O', 'admin', '2026-09-28 06:20:39');

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
(1, '123456', 'S001', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 'No', NULL, '2026-09-30 05:16:34', 'Yes', '', 'MMM Mirshath');

--
-- Triggers `bulk_data_table`
--
DELIMITER $$
CREATE TRIGGER `bulk_data_table_prevent_delete` BEFORE DELETE ON `bulk_data_table` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Security Notice: Deletion of student data is not allowed for security reasons.';
END
$$
DELIMITER ;

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
(1, '123456', 'Minzar Mirshath', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-30 10:01:50', NULL, NULL, NULL);

--
-- Triggers `clothing_collections`
--
DELIMITER $$
CREATE TRIGGER `prevent_clothing_collections_delete` BEFORE DELETE ON `clothing_collections` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Deletion from clothing_collections table is not allowed.';
END
$$
DELIMITER ;

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
  `hats` tinyint(4) NOT NULL DEFAULT 0,
  `active` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_tables`
--

INSERT INTO `data_tables` (`id`, `programName`, `graduationFee`, `freeTicket`, `extraTicketFee`, `session`, `cloak`, `slashes`, `hats`, `active`) VALUES
(1, 'BTEC Higher National Diploma in Business - Batch 18', 12500, 1, 2500, 'SESSION_01', 1, 1, 1, 1),
(2, 'Higher Diploma in Biomedical Science - Batch 29', 12500, 1, 2500, 'SESSION_01', 1, 0, 0, 1),
(3, 'Higher Diploma In Biotechnology - Batch 23', 12500, 1, 2500, 'SESSION_02', 1, 0, 0, 1),
(4, 'Higher Diploma in Food Science and Nutrition- Batch 05', 12500, 1, 2500, 'SESSION_02', 1, 0, 0, 1),
(5, 'Higher Diploma in Medical Biotechnology - Batch 03', 12500, 1, 2500, 'SESSION_03', 1, 0, 0, 1);

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
  `student_id` varchar(50) DEFAULT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `program_name` varchar(255) DEFAULT NULL,
  `session` varchar(255) DEFAULT NULL,
  `added_tickets` int(11) NOT NULL,
  `ticket_price` decimal(10,2) NOT NULL,
  `total_added` decimal(10,2) NOT NULL,
  `added_by` varchar(255) DEFAULT NULL,
  `added_on` datetime DEFAULT current_timestamp(),
  `issued_adExtra_ticket` varchar(255) DEFAULT NULL,
  `issued_adExtra_ticket_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `extra_ticket_log`
--

INSERT INTO `extra_ticket_log` (`id`, `student_id`, `student_name`, `program_name`, `session`, `added_tickets`, `ticket_price`, `total_added`, `added_by`, `added_on`, `issued_adExtra_ticket`, `issued_adExtra_ticket_by`) VALUES
(1, '123456', 'Minzar Mohamadhu Mohamed Mirshath', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 3, 2500.00, 7500.00, 'Admin', '2026-10-01 16:34:30', 'issued', 'Admin'),
(2, '123456', 'Minzar Mohamadhu Mohamed Mirshath', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 4, 2500.00, 10000.00, 'Admin', '2026-10-01 16:35:00', 'issued', 'Admin'),
(3, '123456', 'Minzar Mohamadhu Mohamed Mirshath', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 4, 2500.00, 10000.00, 'Admin', '2026-10-01 16:36:19', 'issued', 'Admin'),
(4, '12345', 'Hasni Nihar', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 5, 2500.00, 12500.00, 'Admin', '2026-10-01 16:39:17', 'issued', 'Admin'),
(5, '12345', 'Hasni Nihar', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 5, 2500.00, 12500.00, 'Admin', '2026-10-01 17:35:12', 'issued', 'Admin');

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
(1, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: Not-Complated', 'read', '2026-09-30 11:15:48'),
(2, 'registered_students', 2, '12345', 'INSERT', 'New student registered: Hasni Nihar (Program: BTEC Higher National Diploma in Business - Batch 18)', 'read', '2026-10-01 03:52:29'),
(3, 'registered_students', 1, '123456', 'INSERT', 'New student registered: Minzar Mohamadhu Mohamed Mirshath (Program: BTEC Higher National Diploma in Business - Batch 18)', 'read', '2026-10-01 03:57:29'),
(4, 'registered_students', 2, '12345', 'INSERT', 'New student registered: Hasni Nihar (Program: BTEC Higher National Diploma in Business - Batch 18)', 'read', '2026-10-01 04:29:46'),
(5, 'registered_students', 1, '12345', 'INSERT', 'New student registered: Hasni Nihar (Program: BTEC Higher National Diploma in Business - Batch 18)', 'read', '2026-10-01 05:04:22'),
(6, 'registered_students', 2, '123456', 'INSERT', 'New student registered: No_ Name (Program: BTEC Higher National Diploma in Business - Batch 18)', 'read', '2026-10-01 05:04:53'),
(7, 'registered_students', 1, '123456', 'INSERT', 'New student registered: Minzar Mohamadhu Mohamed Mirshath (Program: BTEC Higher National Diploma in Business - Batch 18)', 'read', '2026-10-01 05:24:19'),
(8, 'registered_students', 2, '12345', 'INSERT', 'New student registered: No_ Name (Program: BTEC Higher National Diploma in Business - Batch 18)', 'read', '2026-10-01 05:28:45'),
(9, 'registered_students', 1, '123456', 'INSERT', 'New student registered: Minzar Mohamadhu Mohamed Mirshath (Program: BTEC Higher National Diploma in Business - Batch 18)', 'read', '2026-10-01 05:35:10'),
(10, 'registered_students', 1, '123456', 'INSERT', 'New student registered: Minzar Mohamadhu Mohamed Mirshath (Program: BTEC Higher National Diploma in Business - Batch 18)', 'read', '2026-10-01 05:36:23'),
(11, 'registered_students', 2, '12345', 'INSERT', 'New student registered: Hasni Nihar (Program: BTEC Higher National Diploma in Business - Batch 18)', 'unread', '2026-10-01 06:32:55'),
(12, 'registered_students', 2, '12345', 'UPDATE', 'Student record updated for Hasni Nihar, graduation payment status: Not-Completed', 'unread', '2026-10-01 07:03:35'),
(13, 'registered_students', 2, '12345', 'UPDATE', 'Student record updated for Hasni Nihar, graduation payment status: Not-Completed', 'unread', '2026-10-01 07:03:39'),
(14, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: Not-Completed', 'unread', '2026-10-01 07:03:45'),
(15, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: Not-Completed', 'unread', '2026-10-01 07:04:11'),
(16, 'payment_records', 4, '123456', 'INSERT', 'New payment record added for student ID 123456, program: BTEC Higher National Diploma in Business - Batch 18, amount: 15000', 'unread', '2026-10-01 07:19:57'),
(17, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: paid', 'unread', '2026-10-01 07:19:57'),
(18, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: paid', 'unread', '2026-10-01 07:23:20'),
(19, 'registered_students', 2, '12345', 'UPDATE', 'Student record updated for Hasni Nihar, graduation payment status: Not-Completed', 'unread', '2026-10-01 07:23:24'),
(20, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: paid', 'unread', '2026-10-01 07:23:35'),
(21, 'registered_students', 2, '12345', 'UPDATE', 'Student record updated for Hasni Nihar, graduation payment status: paid', 'unread', '2026-10-01 07:23:45'),
(22, 'registered_students', 2, '12345', 'UPDATE', 'Student record updated for Hasni Nihar, graduation payment status: paid', 'unread', '2026-10-01 07:23:49'),
(23, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: paid', 'unread', '2026-10-01 07:23:50'),
(24, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: Not Completed', 'unread', '2026-10-01 07:24:01'),
(25, 'registered_students', 2, '12345', 'UPDATE', 'Student record updated for Hasni Nihar, graduation payment status: Not Completed', 'unread', '2026-10-01 07:24:03'),
(26, 'payment_records', 5, '123456', 'INSERT', 'New payment record added for student ID 123456, program: BTEC Higher National Diploma in Business - Batch 18, amount: 15000', 'unread', '2026-10-01 07:27:36'),
(27, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: paid', 'unread', '2026-10-01 07:27:36'),
(28, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: paid', 'unread', '2026-10-01 08:05:52'),
(29, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: Not Completed', 'unread', '2026-10-01 08:05:56'),
(30, 'payment_records', 1, '123456', 'INSERT', 'New payment record added for student ID 123456, program: BTEC Higher National Diploma in Business - Batch 18, amount: 20000', 'unread', '2026-10-01 08:23:20'),
(31, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: paid', 'unread', '2026-10-01 08:23:20'),
(32, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20262753', 'unread', '2026-10-01 08:58:25'),
(33, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20262753', 'unread', '2026-10-01 08:58:47'),
(34, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20262753', 'unread', '2026-10-01 08:59:20'),
(35, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20262753', 'unread', '2026-10-01 08:59:56'),
(36, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20262753', 'unread', '2026-10-01 09:00:12'),
(37, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20262753', 'unread', '2026-10-01 09:01:36'),
(38, 'payment_records', 2, '12345', 'INSERT', 'New payment record added for student ID 12345, program: BTEC Higher National Diploma in Business - Batch 18, amount: 17500', 'unread', '2026-10-01 09:06:48'),
(39, 'registered_students', 2, '12345', 'UPDATE', 'Student record updated for Hasni Nihar, graduation payment status: paid', 'unread', '2026-10-01 09:06:48'),
(40, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20262753', 'unread', '2026-10-01 09:10:12'),
(41, 'extra_ticket_log', 1, NULL, 'INSERT', 'Added 2 extra ticket(s) worth Rs. 5000.00', 'unread', '2026-10-01 09:20:46'),
(42, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: djsfhg', 'unread', '2026-10-01 09:24:10'),
(43, 'registered_students', 2, '12345', 'UPDATE', 'Student record updated for Hasni Nihar, graduation payment status: paiddsgfsag', 'unread', '2026-10-01 09:24:12'),
(44, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: djsfhg', 'unread', '2026-10-01 09:24:15'),
(45, 'registered_students', 2, '12345', 'UPDATE', 'Student record updated for Hasni Nihar, graduation payment status: paiddsgfsag', 'unread', '2026-10-01 09:24:17'),
(46, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: djsfhg', 'unread', '2026-10-01 09:24:26'),
(47, 'registered_students', 2, '12345', 'UPDATE', 'Student record updated for Hasni Nihar, graduation payment status: paiddsgfsag', 'unread', '2026-10-01 09:24:28'),
(48, 'payment_records', 1, '123456', 'INSERT', 'New payment record added for student ID 123456, program: BTEC Higher National Diploma in Business - Batch 18, amount: 20000', 'unread', '2026-10-01 09:24:49'),
(49, 'registered_students', 1, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: paid', 'unread', '2026-10-01 09:24:49'),
(50, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20269878', 'unread', '2026-10-01 09:28:27'),
(51, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20269878', 'unread', '2026-10-01 09:37:35'),
(52, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20269878', 'unread', '2026-10-01 09:37:44'),
(53, 'extra_ticket_log', 2, '123456', 'INSERT', 'Added 4 extra ticket(s) worth Rs. 10000.00', 'unread', '2026-10-01 10:13:24'),
(54, 'extra_ticket_log', 1, '123456', 'INSERT', 'Added 4 extra ticket(s) worth Rs. 10000.00', 'unread', '2026-10-01 10:28:28'),
(55, 'extra_ticket_log', 2, '123456', 'INSERT', 'Added 2 extra ticket(s) worth Rs. 5000.00', 'unread', '2026-10-01 10:28:42'),
(56, 'extra_ticket_log', 1, '123456', 'INSERT', 'Added 3 extra ticket(s) worth Rs. 7500.00', 'unread', '2026-10-01 10:37:11'),
(57, 'extra_ticket_log', 2, '123456', 'INSERT', 'Added 5 extra ticket(s) worth Rs. 12500.00', 'unread', '2026-10-01 10:37:25'),
(58, 'extra_ticket_log', 3, '123456', 'INSERT', 'Added 4 extra ticket(s) worth Rs. 10000.00', 'unread', '2026-10-01 10:37:46'),
(59, 'extra_ticket_log', 4, '123456', 'INSERT', 'Added 4 extra ticket(s) worth Rs. 10000.00', 'unread', '2026-10-01 10:37:57'),
(60, 'extra_ticket_log', 5, '123456', 'INSERT', 'Added 4 extra ticket(s) worth Rs. 10000.00', 'unread', '2026-10-01 10:39:45'),
(61, 'extra_ticket_log', 6, '123456', 'INSERT', 'Added 5 extra ticket(s) worth Rs. 12500.00', 'unread', '2026-10-01 10:39:53'),
(62, 'extra_ticket_log', 1, '123456', 'INSERT', 'Added 4 extra ticket(s) worth Rs. 10000.00', 'unread', '2026-10-01 10:44:17'),
(63, 'extra_ticket_log', 1, '123456', 'INSERT', 'Added 5 extra ticket(s) worth Rs. 12500.00', 'unread', '2026-10-01 10:54:13'),
(64, 'extra_ticket_log', 1, '123456', 'UPDATE', 'Updated extra tickets: 5 ticket(s), total Rs. 12500.00', 'unread', '2026-10-01 10:54:35'),
(65, 'extra_ticket_log', 2, '123456', 'INSERT', 'Added 3 extra ticket(s) worth Rs. 7500.00', 'unread', '2026-10-01 10:54:54'),
(66, 'extra_ticket_log', 2, '123456', 'UPDATE', 'Updated extra tickets: 3 ticket(s), total Rs. 7500.00', 'unread', '2026-10-01 10:55:30'),
(67, 'extra_ticket_log', 3, '123456', 'INSERT', 'Added 2 extra ticket(s) worth Rs. 5000.00', 'unread', '2026-10-01 10:55:45'),
(68, 'extra_ticket_log', 3, '123456', 'UPDATE', 'Updated extra tickets: 2 ticket(s), total Rs. 5000.00', 'unread', '2026-10-01 11:03:03'),
(69, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20269878', 'unread', '2026-10-01 11:03:51'),
(70, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20269878', 'unread', '2026-10-01 11:03:54'),
(71, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20269878', 'unread', '2026-10-01 11:04:11'),
(72, 'extra_ticket_log', 1, '123456', 'INSERT', 'Added 3 extra ticket(s) worth Rs. 7500.00', 'unread', '2026-10-01 11:04:30'),
(73, 'extra_ticket_log', 1, '123456', 'UPDATE', 'Updated extra tickets: 3 ticket(s), total Rs. 7500.00', 'unread', '2026-10-01 11:04:41'),
(74, 'extra_ticket_log', 2, '123456', 'INSERT', 'Added 4 extra ticket(s) worth Rs. 10000.00', 'unread', '2026-10-01 11:05:00'),
(75, 'extra_ticket_log', 2, '123456', 'UPDATE', 'Updated extra tickets: 4 ticket(s), total Rs. 10000.00', 'unread', '2026-10-01 11:05:15'),
(76, 'extra_ticket_log', 3, '123456', 'INSERT', 'Added 4 extra ticket(s) worth Rs. 10000.00', 'unread', '2026-10-01 11:06:19'),
(77, 'extra_ticket_log', 3, '123456', 'UPDATE', 'Updated extra tickets: 4 ticket(s), total Rs. 10000.00', 'unread', '2026-10-01 11:06:25'),
(78, 'payment_records', 2, '12345', 'INSERT', 'New payment record added for student ID 12345, program: BTEC Higher National Diploma in Business - Batch 18, amount: 12500', 'unread', '2026-10-01 11:08:16'),
(79, 'registered_students', 2, '12345', 'UPDATE', 'Student record updated for Hasni Nihar, graduation payment status: paid', 'unread', '2026-10-01 11:08:16'),
(80, 'extra_ticket_log', 4, '12345', 'INSERT', 'Added 5 extra ticket(s) worth Rs. 12500.00', 'unread', '2026-10-01 11:09:17'),
(81, 'extra_ticket_log', 4, '12345', 'UPDATE', 'Updated extra tickets: 5 ticket(s), total Rs. 12500.00', 'unread', '2026-10-01 11:09:23'),
(82, 'extra_ticket_log', 5, '12345', 'INSERT', 'Added 5 extra ticket(s) worth Rs. 12500.00', 'unread', '2026-10-01 12:05:12'),
(83, 'extra_ticket_log', 5, '12345', 'UPDATE', 'Updated extra tickets: 5 ticket(s), total Rs. 12500.00', 'unread', '2026-10-01 12:06:10');

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
(1, '123456', 'Minzar Mohamadhu Mohamed Mirshath', '1999-01-19', 'mirshath.mmm@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', 766158014, 'paid', 'registered', 1),
(2, '12345', 'Hasni Nihar', '1999-02-19', 'mirshath.mmm@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', 72265421, 'paid', 'registered', 2);

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
-- Table structure for table `payment_email_log`
--

CREATE TABLE `payment_email_log` (
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
-- Dumping data for table `payment_email_log`
--

INSERT INTO `payment_email_log` (`id`, `student_id`, `name_in_full`, `email_address`, `program_name`, `seat_no`, `session_time`, `email_type`, `status`, `error_message`, `sent_by`, `sent_at`) VALUES
(1, '123456', 'Minzar Mohamadhu Mohamed Mirshath', 'yournumplz@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', 'S001', 'SESSION_01', 'single', 'sent', NULL, 1, '2026-09-30 12:47:08'),
(2, '12345', 'Hasni Nihar', 'yournumplz@gmail.com', 'Higher Diploma in Medical Biotechnology - Batch 03', NULL, NULL, 'single', 'sent', NULL, 1, '2026-09-30 12:48:05'),
(3, '123456', 'Minzar Mohamadhu Mohamed Mirshath', 'yournumplz@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', 'S001', 'SESSION_01', 'single', 'sent', NULL, 1, '2026-09-30 15:53:20'),
(4, '123456', 'Minzar Mohamadhu Mohamed Mirshath', 'yournumplz@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', 'S001', 'SESSION_01', 'single', 'sent', NULL, 1, '2026-10-01 12:50:04'),
(5, '123456', 'Minzar Mohamadhu Mohamed Mirshath', 'yournumplz@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', 'S001', 'SESSION_01', 'single', 'sent', NULL, 1, '2026-10-01 12:57:41'),
(6, '123456', 'Minzar Mohamadhu Mohamed Mirshath', 'yournumplz@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', 'S001', 'SESSION_01', 'single', 'sent', NULL, 1, '2026-10-01 13:53:25'),
(7, '12345', 'Hasni Nihar', 'yournumplz@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', NULL, NULL, 'single', 'sent', NULL, 1, '2026-10-01 14:36:52'),
(8, '123456', 'Minzar Mohamadhu Mohamed Mirshath', 'yournumplz@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', 'S001', 'SESSION_01', 'single', 'sent', NULL, 1, '2026-10-01 14:54:52'),
(9, '12345', 'Hasni Nihar', 'yournumplz@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', NULL, NULL, 'single', 'sent', NULL, 1, '2026-10-01 16:38:20');

-- --------------------------------------------------------

--
-- Table structure for table `payment_records`
--

CREATE TABLE `payment_records` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `program_name` varchar(255) NOT NULL,
  `graduation_fee` decimal(10,2) NOT NULL,
  `free_ticket_count` int(5) DEFAULT 0,
  `extra_ticket_count` int(5) DEFAULT 0,
  `extra_ticket_fee` decimal(10,2) DEFAULT 0.00,
  `issued_ex_ticket` varchar(50) DEFAULT NULL,
  `issued_by` varchar(255) DEFAULT NULL,
  `total_amount` int(11) DEFAULT NULL,
  `receipt_number` varchar(20) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_records`
--

INSERT INTO `payment_records` (`id`, `student_id`, `program_name`, `graduation_fee`, `free_ticket_count`, `extra_ticket_count`, `extra_ticket_fee`, `issued_ex_ticket`, `issued_by`, `total_amount`, `receipt_number`, `payment_date`, `created_by`) VALUES
(1, '123456', 'BTEC Higher National Diploma in Business - Batch 18', 12500.00, 1, 3, 7500.00, 'issued', 'Admin', 20000, 'GC20269878', '2026-10-01 09:24:49', 'Admin'),
(2, '12345', 'BTEC Higher National Diploma in Business - Batch 18', 12500.00, 1, 0, 0.00, NULL, NULL, 12500, 'GC20269374', '2026-10-01 11:08:16', 'Admin');

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
  `session` varchar(255) DEFAULT NULL,
  `given_email_add` varchar(50) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `phone_no` varchar(15) DEFAULT NULL,
  `attend` varchar(50) DEFAULT NULL,
  `crsfee_payment_status` varchar(50) DEFAULT NULL,
  `graduation_payment_status` varchar(50) DEFAULT 'Not-Completed',
  `invitation_collected` varchar(255) DEFAULT NULL,
  `updated_at_invitation` datetime DEFAULT NULL,
  `student_meals` varchar(255) DEFAULT NULL,
  `guest_meals` varchar(255) DEFAULT NULL,
  `guest_meals_02` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registered_students`
--

INSERT INTO `registered_students` (`id`, `student_id`, `in_no`, `dob`, `name_in_full`, `title`, `calling_name`, `confirmation`, `program_name`, `session`, `given_email_add`, `email_address`, `phone_no`, `attend`, `crsfee_payment_status`, `graduation_payment_status`, `invitation_collected`, `updated_at_invitation`, `student_meals`, `guest_meals`, `guest_meals_02`, `created_at`) VALUES
(1, '123456', 1, '1999-01-19', 'Minzar Mohamadhu Mohamed Mirshath', 'Ms.', 'Dilmi Navanjana', 1, 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 'mirshath.mmm@gmail.com', 'yournumplz@gmail.com', '+94766158014', NULL, 'paid', 'paid', 'collected', '2026-10-01 14:54:49', 'Non-Vegetarian', 'Non-Vegetarian', 'Non-Vegetarian', '2026-10-01 11:06:23'),
(2, '12345', 2, '1999-02-19', 'Hasni Nihar', 'Ms.', 'Dilmi Navanjana', 1, 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 'mirshath.mmm@gmail.com', 'yournumplz@gmail.com', '+94722654213', NULL, 'paid', 'paid', 'collected', '2026-10-01 16:38:16', 'Vegetarian', 'Non-Vegetarian', 'Vegetarian', '2026-10-01 12:02:55');

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
DELIMITER $$
CREATE TRIGGER `prevent_registered_students_delete` BEFORE DELETE ON `registered_students` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Deletion from registered_students table is not allowed for security reasons';
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
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `payment_email_log`
--
ALTER TABLE `payment_email_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_email` (`email_address`),
  ADD KEY `idx_program` (`program_name`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_sent_at` (`sent_at`),
  ADD KEY `fk_sent_by` (`sent_by`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bulk_data_table`
--
ALTER TABLE `bulk_data_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `clothing_collections`
--
ALTER TABLE `clothing_collections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `data_tables`
--
ALTER TABLE `data_tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `email_log`
--
ALTER TABLE `email_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `extra_ticket_log`
--
ALTER TABLE `extra_ticket_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `old_student_db`
--
ALTER TABLE `old_student_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_email_log`
--
ALTER TABLE `payment_email_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payment_records`
--
ALTER TABLE `payment_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `registered_students`
--
ALTER TABLE `registered_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
