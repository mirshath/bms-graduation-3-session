-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 03, 2026 at 01:12 PM
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
(1, 'Admin', 'admin@bms.ac.lk', '$2y$10$oiMRQMf.tomVqqx10T5OQeivN.GpDKajHfnde0KWpDCq4TCyTcNPW', 'admin', '2026-09-28 06:20:39');

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
(1, 'BTEC Higher National Diploma in Business - Batch 18', 30000, 2, 4000, 'SESSION_01', 1, 1, 1, 1),
(2, 'Higher Diploma in Biomedical Science - Batch 29', 30000, 2, 4000, 'SESSION_01', 1, 0, 0, 1),
(3, 'Higher Diploma In Biotechnology - Batch 23', 30000, 2, 4000, 'SESSION_02', 1, 1, 1, 1),
(4, 'Higher Diploma in Food Science and Nutrition- Batch 05', 30000, 2, 4000, 'SESSION_02', 1, 0, 0, 1),
(5, 'Higher Diploma in Medical Biotechnology - Batch 03', 30000, 2, 4000, 'SESSION_03', 1, 0, 0, 1);

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
-- Table structure for table `extra_guest_meals`
--

CREATE TABLE `extra_guest_meals` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `program_name` varchar(255) NOT NULL,
  `session` varchar(255) DEFAULT NULL,
  `receipt_number` varchar(20) NOT NULL COMMENT 'links the guests to the payment',
  `extra_ticket_log_id` int(11) NOT NULL DEFAULT 0 COMMENT '0 = bought with the graduation payment; otherwise extra_ticket_log.id',
  `guest_type` varchar(20) NOT NULL COMMENT 'Guest 01, Guest 02 ...',
  `meal_type` enum('Vegetarian','Non-Vegetarian') NOT NULL,
  `entered_by` varchar(255) NOT NULL COMMENT 'logged-in admin (from session)',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `extra_guest_meals`
--

INSERT INTO `extra_guest_meals` (`id`, `student_id`, `student_name`, `program_name`, `session`, `receipt_number`, `extra_ticket_log_id`, `guest_type`, `meal_type`, `entered_by`, `created_at`) VALUES
(1, '12345', 'Hasni Nihar', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 'GC20263498', 0, 'Guest 01', 'Non-Vegetarian', 'Admin', '2026-10-03 15:48:04'),
(2, '12345', 'Hasni Nihar', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 'GC20263498', 0, 'Guest 02', 'Vegetarian', 'Admin', '2026-10-03 15:48:04'),
(3, '12345', 'Hasni Nihar', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 'GC20263498', 1, 'Guest 01', 'Vegetarian', 'Admin', '2026-10-03 15:49:15'),
(4, '12345', 'Hasni Nihar', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 'GC20263498', 1, 'Guest 02', 'Non-Vegetarian', 'Admin', '2026-10-03 15:49:15'),
(5, '12345', 'Hasni Nihar', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 'GC20263498', 1, 'Guest 03', 'Non-Vegetarian', 'Admin', '2026-10-03 15:49:15'),
(6, '12345', 'Hasni Nihar', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 'GC20263498', 1, 'Guest 04', 'Non-Vegetarian', 'Admin', '2026-10-03 15:49:15'),
(7, '12345', 'Hasni Nihar', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 'GC20263498', 2, 'Guest 01', 'Vegetarian', 'Admin', '2026-10-03 15:49:39');

--
-- Triggers `extra_guest_meals`
--
DELIMITER $$
CREATE TRIGGER `prevent_extra_guest_meals_delete` BEFORE DELETE ON `extra_guest_meals` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Deletion from extra_guest_meals table is not allowed.';
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
(1, '12345', 'Hasni Nihar', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 4, 4000.00, 16000.00, 'Admin', '2026-10-03 15:49:15', 'issued', 'Admin'),
(2, '12345', 'Hasni Nihar', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 1, 4000.00, 4000.00, 'Admin', '2026-10-03 15:49:39', 'issued', 'Admin');

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
(1, 'payment_records', 1, '12345', 'INSERT', 'New payment record added for student ID 12345, program: BTEC Higher National Diploma in Business - Batch 18, amount: 38000', 'read', '2026-10-03 10:18:00'),
(2, 'registered_students', 3, '12345', 'UPDATE', 'Student record updated for Hasni Nihar, graduation payment status: paid', 'read', '2026-10-03 10:18:01'),
(3, 'extra_ticket_log', 1, '12345', 'INSERT', 'Added 4 extra ticket(s) worth Rs. 16000.00', 'read', '2026-10-03 10:19:15'),
(4, 'extra_ticket_log', 2, '12345', 'INSERT', 'Added 1 extra ticket(s) worth Rs. 4000.00', 'read', '2026-10-03 10:19:39'),
(5, 'payment_records', 1, '12345', 'UPDATE', 'Payment record updated for student ID 12345, receipt number: GC20263498', 'read', '2026-10-03 10:21:19'),
(6, 'extra_ticket_log', 1, '12345', 'UPDATE', 'Updated extra tickets: 4 ticket(s), total Rs. 16000.00', 'read', '2026-10-03 10:21:26'),
(7, 'extra_ticket_log', 2, '12345', 'UPDATE', 'Updated extra tickets: 1 ticket(s), total Rs. 4000.00', 'read', '2026-10-03 10:21:26'),
(8, 'registered_students', 4, '678', 'INSERT', 'New student registered: web master (Program: Higher Diploma In Biotechnology - Batch 23)', 'read', '2026-10-03 10:28:17');

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
  `in_no` int(11) DEFAULT NULL,
  `active` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `old_student_db`
--

INSERT INTO `old_student_db` (`id`, `student_id`, `name`, `DOB`, `given_email`, `program`, `mobile_no`, `payment_status`, `status`, `in_no`, `active`) VALUES
(1, '123456', 'Minzar Mohamadhu Mohamed Mirshath', '1999-01-19', 'mirshath.mmm@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', 766158014, 'paid', 'registered', 1, 'completed'),
(2, '12345', 'Hasni Nihar', '1999-01-19', 'mirshath.mmm@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', 72265421, 'paid', 'registered', 2, 'completed'),
(3, '678', 'Thilshath Khan', '1999-01-19', 'mirshath.mmm@gmail.com', 'Higher Diploma In Biotechnology - Batch 23', 722548901, 'paid', 'registered', 3, 'completed'),
(4, '123', 'Ahamaed Khan', '1999-01-19', 'mirshath.mmm@gmail.com', 'Higher Diploma in Medical Biotechnology - Batch 03', 723540901, 'paid', 'registered', 4, 'completed');

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
(1, '12345', 'Hasni Nihar', 'yournumplz@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', NULL, NULL, 'single', 'sent', NULL, 1, '2026-10-03 15:48:04');

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
(1, '12345', 'BTEC Higher National Diploma in Business - Batch 18', 30000.00, 2, 2, 8000.00, 'issued', 'Admin', 38000, 'GC20263498', '2026-10-03 10:18:00', 'Admin');

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
(1, '123', 4, '1999-02-19', 'Ahamaed Khan', 'Ms.', 'Dilmi Navanjana', 1, 'Higher Diploma in Medical Biotechnology - Batch 03', 'SESSION_03', 'mirshath.mmm@gmail.com', 'yournumplz@gmail.com', '+94723540901', NULL, 'paid', 'fff', 'collected', '2026-10-02 14:52:12', 'Vegetarian', 'Non-Vegetarian', 'Non-Vegetarian', '2026-10-02 10:15:56'),
(2, '123456', 1, '1999-01-19', 'Minzar Mohamadhu Mohamed Mirshath', 'Mrs.', 'Dilmi Navanjana', 1, 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 'mirshath.mmm@gmail.com', 'yournumplz@gmail.com', '+94766158014', NULL, 'paid', 'fff', 'collected', '2026-10-03 12:20:33', 'Non-Vegetarian', 'Non-Vegetarian', 'Non-Vegetarian', '2026-10-02 10:17:40'),
(3, '12345', 2, '1999-02-19', 'Hasni Nihar', 'Ms.', 'Dilmi Navanjana', 1, 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 'mirshath.mmm@gmail.com', 'yournumplz@gmail.com', '+94722654213', NULL, 'paid', 'paid', 'collected', '2026-10-03 15:48:01', 'Non-Vegetarian', 'Non-Vegetarian', 'Non-Vegetarian', '2026-10-02 10:18:24'),
(4, '678', NULL, '1999-01-19', 'web master', 'Mr.', 'Thilshath Khan', 1, 'Higher Diploma In Biotechnology - Batch 23', 'SESSION_02', 'mirshath.mmm@gmail.com', 'bmswebmaster2@gmail.com', '+94798642033', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Vegetarian', 'Non-Vegetarian', 'Vegetarian', '2026-10-03 15:58:17');

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
-- Indexes for table `extra_guest_meals`
--
ALTER TABLE `extra_guest_meals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_student_receipt_log_guest` (`student_id`,`receipt_number`,`extra_ticket_log_id`,`guest_type`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_session` (`session`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clothing_collections`
--
ALTER TABLE `clothing_collections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `extra_guest_meals`
--
ALTER TABLE `extra_guest_meals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `extra_ticket_log`
--
ALTER TABLE `extra_ticket_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `old_student_db`
--
ALTER TABLE `old_student_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment_email_log`
--
ALTER TABLE `payment_email_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_records`
--
ALTER TABLE `payment_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `registered_students`
--
ALTER TABLE `registered_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
