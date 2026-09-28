-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 04, 2026 at 06:04 AM
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
-- Database: `bmsgraduation2026`
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
(1, 'Invitation', 'mmmirshath@gmail.com', '$2y$10$v.0fQ3SYrE2OJv8Q2touGeZRm8gEUoNwYBxSS102wiej56OvA.naS', 'invitation', '2025-10-13 04:16:07'),
(2, 'Mirshath', 'm@gmail.com', '$2y$10$XgSYipfevLzNLTqzMpSlIudRsn2Fy4zKCZFRy7ncjdAQtyeJPb.AK', 'admin', '2025-10-15 06:41:22'),
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
CREATE TRIGGER `prevent_admin_delete` BEFORE DELETE ON `admin` FOR EACH ROW BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Deletion from admin table is not allowed for security reasons'; END
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
(2, '654321', 'B 02', 'GDM', 'MORNING', 'No', NULL, '2025-11-20 04:41:00', 'Yes', 'D', 'Hasni Nihar'),
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
(7, 'Graduate Diploma in Management - Batch 57', 20000, 2, 3000, 'MORNING', 1, 0, 0),
(8, 'Graduate Diploma in Management - Batch 58', 20000, 2, 3000, 'MORNING', 1, 0, 0),
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
(1, '123456', 'Mirshath', 'mirshath.m@cgs.lk', 'Graduate Diploma in Management - Batch 57', 'B 01', 'MORNING', 'single', 'sent', NULL, 2, '2026-03-03 11:20:23'),
(2, '123456', 'Mirshath', 'mirshath.m@cgs.lk', 'Graduate Diploma in Management - Batch 57', 'B 01', 'MORNING', 'single', 'sent', NULL, 2, '2026-03-03 11:22:11'),
(3, '123456', 'Mirshath', 'mirshath.m@cgs.lk', 'Graduate Diploma in Management - Batch 57', 'B 01', 'MORNING', 'single', 'sent', NULL, 2, '2026-03-03 11:37:06'),
(4, '123456', 'Mirshath', 'mirshath.m@cgs.lk', 'Graduate Diploma in Management - Batch 57', 'B 01', 'MORNING', 'single', 'sent', NULL, 2, '2026-03-03 11:48:05'),
(5, '123456', 'Mirshath', 'mirshath.m@cgs.lk', 'Graduate Diploma in Management - Batch 57', 'B 01', 'MORNING', 'single', 'sent', NULL, 2, '2026-03-03 11:52:14'),
(6, '123456', 'Mirshath', 'mirshath.m@cgs.lk', 'Graduate Diploma in Management - Batch 57', 'B 01', 'MORNING', 'single', 'sent', NULL, 2, '2026-03-03 12:25:23');

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
  `added_tickets` int(11) NOT NULL,
  `ticket_price` decimal(10,2) NOT NULL,
  `total_added` decimal(10,2) NOT NULL,
  `added_by` int(11) DEFAULT NULL,
  `added_on` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `extra_ticket_log`
--

INSERT INTO `extra_ticket_log` (`id`, `student_id`, `added_tickets`, `ticket_price`, `total_added`, `added_by`, `added_on`) VALUES
(1, NULL, 5, 3000.00, 15000.00, 2, '2026-02-25 14:20:37'),
(2, NULL, 6, 3000.00, 18000.00, 2, '2026-02-25 15:55:09'),
(3, NULL, 7, 3000.00, 21000.00, 2, '2026-03-01 10:55:21'),
(4, NULL, 5, 500.00, 2500.00, 2, '2026-03-01 11:05:17'),
(5, NULL, 2, 3000.00, 6000.00, 2, '2026-03-01 11:18:13'),
(6, NULL, 4, 5000.00, 20000.00, 2, '2026-03-01 11:42:59'),
(7, NULL, 1, 3000.00, 3000.00, 3, '2026-03-01 12:28:56'),
(8, NULL, 1, 3000.00, 3000.00, 3, '2026-03-01 12:31:22'),
(9, NULL, 5, 3000.00, 15000.00, 2, '2026-03-03 09:59:46'),
(10, NULL, 5, 3000.00, 15000.00, 2, '2026-03-03 10:52:14');

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
(1, 'registered_students', 5, '123456', 'INSERT', 'New student registered: Mir (Program: GDM)', 'read', '2026-02-23 05:16:11'),
(2, 'registered_students', 6, '123456', 'INSERT', 'New student registered: Mirshath Mohamed Changed Name (Program: GDM)', 'read', '2026-02-23 05:55:01'),
(3, 'registered_students', 7, '123456', 'INSERT', 'New student registered: Mir (Program: GDM)', 'read', '2026-02-23 06:10:39'),
(4, 'registered_students', 8, '123456', 'INSERT', 'New student registered: Mir (Program: GDM)', 'read', '2026-02-23 06:47:49'),
(5, 'registered_students', 9, '123456', 'INSERT', 'New student registered: Mir (Program: GDM)', 'read', '2026-02-23 06:54:51'),
(6, 'registered_students', 10, '123456', 'INSERT', 'New student registered: Mir (Program: GDM)', 'read', '2026-02-23 07:11:40'),
(7, 'registered_students', 11, '123456', 'INSERT', 'New student registered: Mir (Program: GDM)', 'read', '2026-02-23 07:17:30'),
(8, 'registered_students', 11, '123456', 'UPDATE', 'Student record updated for Mir, graduation payment status: Not-Completed', 'read', '2026-02-23 07:26:27'),
(9, 'registered_students', 12, '123456', 'INSERT', 'New student registered: Mirshath (Program: Graduate Diploma in Management - Batch 57)', 'read', '2026-02-23 08:20:42'),
(10, 'registered_students', 13, '654321', 'INSERT', 'New student registered: Mir Test GDM-58 (Program: Graduate Diploma in Management - Batch 58)', 'read', '2026-02-23 08:21:40'),
(11, 'registered_students', 14, '654321', 'INSERT', 'New student registered: Mir Test GDM (Program: Graduate Diploma in Management - Batch 58)', 'read', '2026-02-23 08:26:11'),
(12, 'registered_students', 14, '654321', 'UPDATE', 'Student record updated for Mir Test GDM, graduation payment status: Not-Completed', 'read', '2026-02-23 08:26:27'),
(13, 'registered_students', 12, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: Not-Completed', 'read', '2026-02-23 08:28:48'),
(14, 'registered_students', 15, '333', 'INSERT', 'New student registered: mohamed (Program: BSc (Hons) Global Business Management (Human Resources) - September 2024)', 'read', '2026-02-23 08:29:56'),
(15, 'registered_students', 15, '333', 'UPDATE', 'Student record updated for mohamed, graduation payment status: Not-Completed', 'read', '2026-02-23 08:30:40'),
(16, 'registered_students', 14, '654321', 'UPDATE', 'Student record updated for Hasni Nihar, graduation payment status: Not-Completed', 'read', '2026-02-23 09:09:50'),
(17, 'registered_students', 14, '654321', 'UPDATE', 'Student record updated for GDD, graduation payment status: Not-Completed', 'read', '2026-02-23 09:37:36'),
(18, 'payment_records', 1, '123456', 'INSERT', 'New payment record added for student ID 123456, program: Graduate Diploma in Management - Batch 57, amount: 29000', 'read', '2026-02-23 09:51:02'),
(19, 'registered_students', 12, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'read', '2026-02-23 09:51:02'),
(20, 'payment_records', 2, '654321', 'INSERT', 'New payment record added for student ID 654321, program: Graduate Diploma in Management - Batch 58, amount: 26000', 'read', '2026-02-23 10:00:31'),
(21, 'registered_students', 14, '654321', 'UPDATE', 'Student record updated for GDD, graduation payment status: paid', 'read', '2026-02-23 10:00:31'),
(22, 'payment_records', 2, '654321', 'UPDATE', 'Payment record updated for student ID 654321, receipt number: GC20254309', 'read', '2026-02-23 11:13:31'),
(23, 'extra_ticket_log', 1, '654321', 'INSERT', 'Added 3 extra ticket(s) worth Rs. 9000.00', 'read', '2026-02-23 11:13:31'),
(24, 'extra_ticket_log', 2, NULL, 'INSERT', 'Added 1 extra ticket(s) worth Rs. 3000.00', 'read', '2026-02-23 11:20:11'),
(25, 'registered_students', 16, '123456', 'INSERT', 'New student registered: Mirsha (Program: Graduate Diploma in Management - Batch 57)', 'unread', '2026-02-24 04:51:42'),
(26, 'registered_students', 17, '123456', 'INSERT', 'New student registered: Mirshath (Program: Graduate Diploma in Management - Batch 57)', 'unread', '2026-02-24 04:55:50'),
(27, 'registered_students', 18, '123456', 'INSERT', 'New student registered: Mirs (Program: Graduate Diploma in Management - Batch 57)', 'unread', '2026-02-24 05:11:56'),
(28, 'registered_students', 19, '123456', 'INSERT', 'New student registered: Mirshath (Program: Graduate Diploma in Management - Batch 57)', 'unread', '2026-02-24 05:23:34'),
(29, 'admin', 6, NULL, 'insert', 'New admin added: SSS (Role: finance)', 'unread', '2026-02-24 08:32:12'),
(30, 'admin', 7, NULL, 'insert', 'New admin added: QWQ (Role: admin)', 'unread', '2026-02-24 08:33:49'),
(31, 'admin', 8, NULL, 'insert', 'New admin added: ABC TEsting (Role: admin)', 'unread', '2026-02-24 08:41:44'),
(32, 'admin', 8, NULL, 'update', 'Admin updated: ABC Testing (Role: admin)', 'unread', '2026-02-24 09:04:00'),
(33, 'admin', 2, NULL, 'update', 'Admin updated: Mirshath (Role: admin)', 'unread', '2026-02-24 09:04:25'),
(34, 'admin', 2, NULL, 'update', 'Admin updated: Mirshath (Role: admin)', 'unread', '2026-02-24 09:05:41'),
(35, 'admin', 2, NULL, 'update', 'Admin updated: Mirshath (Role: admin)', 'unread', '2026-02-24 09:06:19'),
(36, 'admin', 6, NULL, 'update', 'Admin updated: AAAA (Role: finance)', 'unread', '2026-02-24 09:07:56'),
(37, 'admin', 1, NULL, 'update', 'Admin updated: Invitation (Role: admin)', 'unread', '2026-02-24 09:10:38'),
(38, 'admin', 1, NULL, 'update', 'Admin updated: Invitation (Role: invitation)', 'unread', '2026-02-24 09:11:01'),
(39, 'payment_records', 1, '123456', 'INSERT', 'New payment record added for student ID 123456, program: Graduate Diploma in Management - Batch 57, amount: 35000', 'unread', '2026-02-24 09:56:33'),
(40, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-02-24 09:56:33'),
(41, 'extra_ticket_log', 1, NULL, 'INSERT', 'Added 1 extra ticket(s) worth Rs. 3000.00', 'unread', '2026-02-24 10:50:38'),
(42, 'extra_ticket_log', 1, NULL, 'UPDATE', 'Updated extra tickets: 2 ticket(s), total Rs. 3000.00', 'unread', '2026-02-24 11:03:15'),
(43, 'extra_ticket_log', 1, NULL, 'UPDATE', 'Updated extra tickets: 2 ticket(s), total Rs. 6000.00', 'unread', '2026-02-24 11:03:17'),
(44, 'extra_ticket_log', 2, NULL, 'INSERT', 'Added 3 extra ticket(s) worth Rs. 9000.00', 'unread', '2026-02-25 06:46:44'),
(45, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paids', 'unread', '2026-02-25 06:55:15'),
(46, 'payment_records', 2, '123456', 'INSERT', 'New payment record added for student ID 123456, program: Graduate Diploma in Management - Batch 57, amount: 20000', 'unread', '2026-02-25 06:55:31'),
(47, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-02-25 06:55:31'),
(48, 'payment_records', 2, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20254755', 'unread', '2026-02-25 06:57:15'),
(49, 'payment_records', 2, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20254755', 'unread', '2026-02-25 06:59:55'),
(50, 'extra_ticket_log', 2, NULL, 'UPDATE', 'Updated extra tickets: 3 ticket(s), total Rs. 9000.00', 'unread', '2026-02-25 07:00:08'),
(51, 'payment_records', 2, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20254755', 'unread', '2026-02-25 07:58:50'),
(52, 'extra_ticket_log', 2, NULL, 'UPDATE', 'Updated extra tickets: 3 ticket(s), total Rs. 9000.00', 'unread', '2026-02-25 07:59:13'),
(53, 'payment_records', 2, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20254755', 'unread', '2026-02-25 07:59:40'),
(54, 'payment_records', 2, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20254755', 'unread', '2026-02-25 08:37:57'),
(55, 'extra_ticket_log', 2, NULL, 'UPDATE', 'Updated extra tickets: 3 ticket(s), total Rs. 9000.00', 'unread', '2026-02-25 08:46:40'),
(56, 'payment_records', 2, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20254755', 'unread', '2026-02-25 08:46:52'),
(57, 'extra_ticket_log', 2, NULL, 'UPDATE', 'Updated extra tickets: 3 ticket(s), total Rs. 9000.00', 'unread', '2026-02-25 08:47:05'),
(58, 'payment_records', 2, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20254755', 'unread', '2026-02-25 08:47:20'),
(59, 'extra_ticket_log', 1, NULL, 'INSERT', 'Added 5 extra ticket(s) worth Rs. 15000.00', 'unread', '2026-02-25 08:50:37'),
(60, 'payment_records', 2, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20254755', 'unread', '2026-02-25 08:52:03'),
(61, 'extra_ticket_log', 1, NULL, 'UPDATE', 'Updated extra tickets: 5 ticket(s), total Rs. 15000.00', 'unread', '2026-02-25 08:53:04'),
(62, 'extra_ticket_log', 1, NULL, 'UPDATE', 'Updated extra tickets: 5 ticket(s), total Rs. 15000.00', 'unread', '2026-02-25 08:53:55'),
(63, 'payment_records', 2, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20254755', 'unread', '2026-02-25 08:54:08'),
(64, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20252529', 'unread', '2026-02-25 09:18:17'),
(65, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20252529', 'unread', '2026-02-25 09:18:23'),
(66, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20252529', 'unread', '2026-02-25 09:18:32'),
(67, 'extra_ticket_log', 1, NULL, 'UPDATE', 'Updated extra tickets: 5 ticket(s), total Rs. 15000.00', 'unread', '2026-02-25 09:32:22'),
(68, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20252529', 'unread', '2026-02-25 09:33:54'),
(69, 'payment_records', 2, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20254755', 'unread', '2026-02-25 09:33:59'),
(70, 'extra_ticket_log', 1, NULL, 'UPDATE', 'Updated extra tickets: 5 ticket(s), total Rs. 15000.00', 'unread', '2026-02-25 09:34:45'),
(71, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20252529', 'unread', '2026-02-25 09:36:23'),
(72, 'extra_ticket_log', 1, NULL, 'UPDATE', 'Updated extra tickets: 5 ticket(s), total Rs. 15000.00', 'unread', '2026-02-25 09:40:43'),
(73, 'payment_records', 2, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20254755', 'unread', '2026-02-25 09:41:45'),
(74, 'extra_ticket_log', 1, NULL, 'UPDATE', 'Updated extra tickets: 5 ticket(s), total Rs. 15000.00', 'unread', '2026-02-25 09:42:10'),
(75, 'extra_ticket_log', 1, NULL, 'UPDATE', 'Updated extra tickets: 5 ticket(s), total Rs. 15000.00', 'unread', '2026-02-25 09:56:06'),
(76, 'extra_ticket_log', 1, NULL, 'UPDATE', 'Updated extra tickets: 5 ticket(s), total Rs. 15000.00', 'unread', '2026-02-25 09:57:35'),
(77, 'payment_records', 2, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20254755', 'unread', '2026-02-25 10:08:53'),
(78, 'payment_records', 2, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20254755', 'unread', '2026-02-25 10:09:19'),
(79, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20252529', 'unread', '2026-02-25 10:13:53'),
(80, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20252529', 'unread', '2026-02-25 10:14:00'),
(81, 'extra_ticket_log', 2, NULL, 'INSERT', 'Added 6 extra ticket(s) worth Rs. 18000.00', 'unread', '2026-02-25 10:25:09'),
(82, 'registered_students', 20, '654321', 'INSERT', 'New student registered: No_ Name (Program: Graduate Diploma in Management - Batch 58)', 'unread', '2026-02-25 10:55:18'),
(83, 'registered_students', 21, '654321', 'INSERT', 'New student registered: No_ Name (Program: Graduate Diploma in Management - Batch 58)', 'unread', '2026-02-25 11:01:10'),
(84, 'registered_students', 22, '654321', 'INSERT', 'New student registered: No_ Name (Program: Graduate Diploma in Management - Batch 58)', 'unread', '2026-02-26 03:54:15'),
(85, 'extra_ticket_log', 3, NULL, 'INSERT', 'Added 7 extra ticket(s) worth Rs. 21000.00', 'unread', '2026-03-01 05:25:21'),
(86, 'extra_ticket_log', 4, NULL, 'INSERT', 'Added 5 extra ticket(s) worth Rs. 2500.00', 'unread', '2026-03-01 05:35:17'),
(87, 'extra_ticket_log', 5, NULL, 'INSERT', 'Added 2 extra ticket(s) worth Rs. 6000.00', 'unread', '2026-03-01 05:48:13'),
(88, 'extra_ticket_log', 6, NULL, 'INSERT', 'Added 4 extra ticket(s) worth Rs. 20000.00', 'unread', '2026-03-01 06:12:59'),
(89, 'extra_ticket_log', 7, NULL, 'INSERT', 'Added 1 extra ticket(s) worth Rs. 3000.00', 'unread', '2026-03-01 06:58:56'),
(90, 'extra_ticket_log', 8, NULL, 'INSERT', 'Added 1 extra ticket(s) worth Rs. 3000.00', 'unread', '2026-03-01 07:01:22'),
(91, 'extra_ticket_log', 8, NULL, 'UPDATE', 'Updated extra tickets: 1 ticket(s), total Rs. 3000.00', 'unread', '2026-03-01 07:02:41'),
(92, 'extra_ticket_log', 7, NULL, 'UPDATE', 'Updated extra tickets: 1 ticket(s), total Rs. 3000.00', 'unread', '2026-03-01 07:02:45'),
(93, 'extra_ticket_log', 9, NULL, 'INSERT', 'Added 5 extra ticket(s) worth Rs. 15000.00', 'unread', '2026-03-03 04:29:46'),
(94, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-03-03 05:08:56'),
(95, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paidd', 'unread', '2026-03-03 05:09:14'),
(96, 'payment_records', 3, '123456', 'INSERT', 'New payment record added for student ID 123456, program: Graduate Diploma in Management - Batch 57, amount: 20000', 'unread', '2026-03-03 05:09:22'),
(97, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-03-03 05:09:22'),
(98, 'extra_ticket_log', 10, NULL, 'INSERT', 'Added 5 extra ticket(s) worth Rs. 15000.00', 'unread', '2026-03-03 05:22:14'),
(99, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paidm', 'unread', '2026-03-03 05:26:15'),
(100, 'payment_records', 4, '123456', 'INSERT', 'New payment record added for student ID 123456, program: Graduate Diploma in Management - Batch 57, amount: 29000', 'unread', '2026-03-03 05:26:38'),
(101, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-03-03 05:26:38'),
(102, 'payment_records', 5, '123456', 'INSERT', 'New payment record added for student ID 123456, program: Graduate Diploma in Management - Batch 57, amount: 29000', 'unread', '2026-03-03 05:28:46'),
(103, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-03-03 05:28:46'),
(104, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paidmd', 'unread', '2026-03-03 05:30:49'),
(105, 'payment_records', 6, '123456', 'INSERT', 'New payment record added for student ID 123456, program: Graduate Diploma in Management - Batch 57, amount: 26000', 'unread', '2026-03-03 05:35:28'),
(106, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-03-03 05:35:28'),
(107, 'payment_records', 4, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20254938', 'unread', '2026-03-03 05:36:43'),
(108, 'payment_records', 5, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20254938', 'unread', '2026-03-03 05:36:48'),
(109, 'payment_records', 6, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20256274', 'unread', '2026-03-03 05:36:50'),
(110, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paids4', 'unread', '2026-03-03 05:37:09'),
(111, 'payment_records', 7, '123456', 'INSERT', 'New payment record added for student ID 123456, program: Graduate Diploma in Management - Batch 57, amount: 26000', 'unread', '2026-03-03 05:37:30'),
(112, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-03-03 05:37:30'),
(113, 'payment_records', 7, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20252203', 'unread', '2026-03-03 05:38:00'),
(114, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paids', 'unread', '2026-03-03 05:46:06'),
(115, 'payment_records', 8, '123456', 'INSERT', 'New payment record added for student ID 123456, program: Graduate Diploma in Management - Batch 57, amount: 26000', 'unread', '2026-03-03 05:46:33'),
(116, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-03-03 05:46:33'),
(117, 'payment_records', 8, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20253997', 'unread', '2026-03-03 05:47:22'),
(118, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paidss', 'unread', '2026-03-03 05:47:29'),
(119, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-03-03 05:47:44'),
(120, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paidss', 'unread', '2026-03-03 05:50:06'),
(121, 'email_log', 1, '123456', 'INSERT', 'Email sent to Mirshath (mirshath.m@cgs.lk)', 'unread', '2026-03-03 05:50:23'),
(122, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-03-03 05:50:23'),
(123, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paids', 'unread', '2026-03-03 05:51:45'),
(124, 'payment_records', 9, '123456', 'INSERT', 'New payment record added for student ID 123456, program: Graduate Diploma in Management - Batch 57, amount: 32000', 'unread', '2026-03-03 05:52:11'),
(125, 'email_log', 2, '123456', 'INSERT', 'Email sent to Mirshath (mirshath.m@cgs.lk)', 'unread', '2026-03-03 05:52:11'),
(126, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-03-03 05:52:11'),
(127, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paidss', 'unread', '2026-03-03 05:52:34'),
(128, 'payment_records', 10, '123456', 'INSERT', 'New payment record added for student ID 123456, program: Graduate Diploma in Management - Batch 57, amount: 38000', 'unread', '2026-03-03 06:07:06'),
(129, 'email_log', 3, '123456', 'INSERT', 'Email sent to Mirshath (mirshath.m@cgs.lk)', 'unread', '2026-03-03 06:07:06'),
(130, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-03-03 06:07:06'),
(131, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paids', 'unread', '2026-03-03 06:17:40'),
(132, 'payment_records', 11, '123456', 'INSERT', 'New payment record added for student ID 123456, program: Graduate Diploma in Management - Batch 57, amount: 26000', 'unread', '2026-03-03 06:18:05'),
(133, 'email_log', 4, '123456', 'INSERT', 'Email sent to Mirshath (mirshath.m@cgs.lk)', 'unread', '2026-03-03 06:18:05'),
(134, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-03-03 06:18:05'),
(135, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paidf', 'unread', '2026-03-03 06:21:58'),
(136, 'payment_records', 12, '123456', 'INSERT', 'New payment record added for student ID 123456, program: Graduate Diploma in Management - Batch 57, amount: 26000', 'unread', '2026-03-03 06:22:14'),
(137, 'email_log', 5, '123456', 'INSERT', 'Email sent to Mirshath (mirshath.m@cgs.lk)', 'unread', '2026-03-03 06:22:14'),
(138, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-03-03 06:22:14'),
(139, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paidfd', 'unread', '2026-03-03 06:54:27'),
(140, 'payment_records', 13, '123456', 'INSERT', 'New payment record added for student ID 123456, program: Graduate Diploma in Management - Batch 57, amount: 26000', 'unread', '2026-03-03 06:55:23'),
(141, 'email_log', 6, '123456', 'INSERT', 'Email sent to Mirshath (mirshath.m@cgs.lk)', 'unread', '2026-03-03 06:55:23'),
(142, 'registered_students', 19, '123456', 'UPDATE', 'Student record updated for Mirshath, graduation payment status: paid', 'unread', '2026-03-03 06:55:23');

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
(1, '123456', 'Mir', '2025-10-30', 'mirshath.mmm@gmail.com', 'Graduate Diploma in Management - Batch 57', 25490455, 'paid', 'registered', 10),
(2, '333', 'mohamed', '2025-10-08', 'yournumplz@gmail.com', 'BSc (Hons) Global Business Management (Human Resources) - September 2024', 778899, 'paid', 'registered', 11),
(3, '654321', 'Mir Test GDM', '2025-10-01', 'bmsmirshath@gmail.com', 'Graduate Diploma in Management - Batch 58', 766158014, 'paid', 'registered', 12);

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
(1, '123456', 'Graduate Diploma in Management - Batch 57', 20000.00, 2, 3, 9000.00, 29000, 'GC20252529', '2026-02-24 09:56:33', 'Mirshath'),
(2, '123456', 'Graduate Diploma in Management - Batch 57', 20000.00, 2, 0, 0.00, 20000, 'GC20254755', '2026-02-24 06:55:31', 'Mirshath'),
(3, '123456', 'Graduate Diploma in Management - Batch 57', 20000.00, 2, 0, 0.00, 20000, 'GC20251969', '2026-03-03 05:09:22', 'Mirshath'),
(4, '123456', 'Graduate Diploma in Management - Batch 57', 20000.00, 2, 3, 9000.00, 29000, 'GC20254938', '2026-02-11 05:26:38', 'Mirshath'),
(5, '123456', 'Graduate Diploma in Management - Batch 57', 20000.00, 2, 3, 9000.00, 29000, 'GC20254938', '2026-02-11 05:26:38', 'Mirshath'),
(6, '123456', 'Graduate Diploma in Management - Batch 57', 20000.00, 2, 2, 6000.00, 26000, 'GC20256274', '2026-02-11 05:26:38', 'Mirshath'),
(7, '123456', 'Graduate Diploma in Management - Batch 57', 20000.00, 2, 2, 6000.00, 26000, 'GC20252203', '2026-02-03 05:37:30', 'Mirshath'),
(8, '123456', 'Graduate Diploma in Management - Batch 57', 20000.00, 2, 2, 6000.00, 26000, 'GC20253997', '2026-02-11 05:46:33', 'Mirshath'),
(9, '123456', 'Graduate Diploma in Management - Batch 57', 20000.00, 2, 4, 12000.00, 32000, 'GC20255606', '2026-03-03 05:52:11', 'Mirshath'),
(10, '123456', 'Graduate Diploma in Management - Batch 57', 20000.00, 2, 6, 18000.00, 38000, 'GC20256586', '2026-03-03 06:07:06', 'Mirshath'),
(11, '123456', 'Graduate Diploma in Management - Batch 57', 20000.00, 2, 2, 6000.00, 26000, 'GC20254929', '2026-03-03 06:18:05', 'Mirshath'),
(12, '123456', 'Graduate Diploma in Management - Batch 57', 20000.00, 2, 2, 6000.00, 26000, 'GC20252910', '2026-03-03 06:22:14', 'Mirshath'),
(13, '123456', 'Graduate Diploma in Management - Batch 57', 20000.00, 2, 2, 6000.00, 26000, 'GC20251669', '2026-03-03 06:55:23', 'Mirshath');

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
  `student_meals` varchar(255) DEFAULT NULL,
  `guest_meals` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registered_students`
--

INSERT INTO `registered_students` (`id`, `student_id`, `in_no`, `dob`, `name_in_full`, `title`, `calling_name`, `confirmation`, `program_name`, `given_email_add`, `email_address`, `phone_no`, `attend`, `crsfee_payment_status`, `graduation_payment_status`, `invitation_collected`, `updated_at_invitation`, `student_meals`, `guest_meals`, `created_at`) VALUES
(19, '123456', NULL, '2025-10-30', 'Mirshath', 'Mr.', 'MMM', 1, 'Graduate Diploma in Management - Batch 57', 'mirshath.mmm@gmail.com', 'mirshath.m@cgs.lk', '25490455', NULL, 'paidd', 'paid', NULL, NULL, NULL, NULL, '2026-02-24 10:53:34'),
(22, '654321', NULL, '2025-10-01', 'No_ Name', 'Mr.', 'ssssssssssssss', 1, 'Graduate Diploma in Management - Batch 58', 'bmsmirshath@gmail.com', 'yournumplz@gmail.com', '+94766158014', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Vegetarian', 'Non-Vegetarian', '2026-02-26 09:24:15');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bulk_data_table`
--
ALTER TABLE `bulk_data_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `clothing_collections`
--
ALTER TABLE `clothing_collections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `data_tables`
--
ALTER TABLE `data_tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `email_log`
--
ALTER TABLE `email_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `extra_ticket_log`
--
ALTER TABLE `extra_ticket_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `old_student_db`
--
ALTER TABLE `old_student_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_records`
--
ALTER TABLE `payment_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `registered_students`
--
ALTER TABLE `registered_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

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
