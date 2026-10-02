-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2026 at 01:42 PM
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
(1, '123456', 'Minzar Mohamadhu Mohamed Mirshath', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 4, 4000.00, 16000.00, 'Admin', '2026-09-27 14:40:37', 'issued', 'Admin'),
(2, '123456', 'Minzar Mohamadhu Mohamed Mirshath', 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 5, 4000.00, 20000.00, 'Admin', '2026-10-02 14:46:05', 'issued', 'Admin'),
(3, '123', 'Ahamaed Khan', 'Higher Diploma in Medical Biotechnology - Batch 03', 'SESSION_03', 3, 4000.00, 12000.00, 'Admin', '2026-10-02 14:53:00', NULL, NULL);

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
(1, 'payment_records', 1, '123456', 'INSERT', 'New payment record added for student ID 123456, program: BTEC Higher National Diploma in Business - Batch 18, amount: 38000', 'read', '2026-09-27 09:10:02'),
(2, 'registered_students', 2, '123456', 'UPDATE', 'Student record updated for Minzar Mohamadhu Mohamed Mirshath, graduation payment status: paid', 'read', '2026-09-27 09:10:02'),
(3, 'payment_records', 1, '123456', 'UPDATE', 'Payment record updated for student ID 123456, receipt number: GC20264422', 'read', '2026-09-27 09:10:26'),
(4, 'extra_ticket_log', 1, '123456', 'INSERT', 'Added 4 extra ticket(s) worth Rs. 16000.00', 'read', '2026-09-27 09:10:37'),
(5, 'extra_ticket_log', 1, '123456', 'UPDATE', 'Updated extra tickets: 4 ticket(s), total Rs. 16000.00', 'read', '2026-09-27 09:10:45'),
(6, 'extra_ticket_log', 2, '123456', 'INSERT', 'Added 5 extra ticket(s) worth Rs. 20000.00', 'read', '2026-10-02 09:16:05'),
(7, 'extra_ticket_log', 2, '123456', 'UPDATE', 'Updated extra tickets: 5 ticket(s), total Rs. 20000.00', 'read', '2026-10-02 09:18:38'),
(8, 'payment_records', 2, '123', 'INSERT', 'New payment record added for student ID 123, program: Higher Diploma in Medical Biotechnology - Batch 03, amount: 46000', 'read', '2026-10-02 09:22:12'),
(9, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Ahamaed Khan, graduation payment status: paid', 'read', '2026-10-02 09:22:12'),
(10, 'extra_ticket_log', 3, '123', 'INSERT', 'Added 3 extra ticket(s) worth Rs. 12000.00', 'read', '2026-10-02 09:23:00');

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
(2, '12345', 'Hasni Nihar', '1999-02-19', 'mirshath.mmm@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', 72265421, 'paid', 'registered', 2),
(3, '678', 'Thilshath Khan', '1999-02-19', 'mirshath.mmm@gmail.com', 'Higher Diploma In Biotechnology - Batch 23', 722548901, 'paid', NULL, 3),
(4, '123', 'Ahamaed Khan', '1999-02-19', 'mirshath.mmm@gmail.com', 'Higher Diploma in Medical Biotechnology - Batch 03', 723540901, 'paid', 'registered', 4);

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
(1, '123456', 'Minzar Mohamadhu Mohamed Mirshath', 'yournumplz@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', 'S001', 'SESSION_01', 'single', 'sent', NULL, 1, '2026-10-02 10:36:09'),
(2, '123', 'Ahamaed Khan', 'yournumplz@gmail.com', 'Higher Diploma in Medical Biotechnology - Batch 03', NULL, NULL, 'single', 'sent', NULL, 1, '2026-10-02 10:39:29'),
(3, '123456', 'Minzar Mohamadhu Mohamed Mirshath', 'yournumplz@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', 'S001', 'SESSION_01', 'single', 'sent', NULL, 1, '2026-09-27 14:40:05'),
(4, '123', 'Ahamaed Khan', 'yournumplz@gmail.com', 'Higher Diploma in Medical Biotechnology - Batch 03', NULL, NULL, 'single', 'sent', NULL, 1, '2026-10-02 14:52:16');

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
(1, '123456', 'BTEC Higher National Diploma in Business - Batch 18', 30000.00, 2, 2, 8000.00, 'issued', 'Admin', 38000, 'GC20264422', '2026-09-27 09:10:02', 'Admin'),
(2, '123', 'Higher Diploma in Medical Biotechnology - Batch 03', 30000.00, 2, 4, 16000.00, NULL, NULL, 46000, 'GC20266809', '2026-10-02 09:22:12', 'Admin');

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
(1, '123', 4, '1999-02-19', 'Ahamaed Khan', 'Ms.', 'Dilmi Navanjana', 1, 'Higher Diploma in Medical Biotechnology - Batch 03', 'SESSION_03', 'mirshath.mmm@gmail.com', 'yournumplz@gmail.com', '+94723540901', NULL, 'paid', 'paid', 'collected', '2026-10-02 14:52:12', 'Vegetarian', 'Non-Vegetarian', 'Non-Vegetarian', '2026-10-02 10:15:56'),
(2, '123456', 1, '1999-01-19', 'Minzar Mohamadhu Mohamed Mirshath', 'Mrs.', 'Dilmi Navanjana', 1, 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 'mirshath.mmm@gmail.com', 'yournumplz@gmail.com', '+94766158014', NULL, 'paid', 'paid', 'collected', '2026-09-27 14:40:02', 'Non-Vegetarian', 'Non-Vegetarian', 'Non-Vegetarian', '2026-10-02 10:17:40'),
(3, '12345', NULL, '1999-02-19', 'Hasni Nihar', 'Ms.', 'Dilmi Navanjana', 1, 'BTEC Higher National Diploma in Business - Batch 18', 'SESSION_01', 'mirshath.mmm@gmail.com', 'yournumplz@gmail.com', '+94722654213', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', 'Non-Vegetarian', '2026-10-02 10:18:24');

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
-- AUTO_INCREMENT for table `extra_ticket_log`
--
ALTER TABLE `extra_ticket_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `old_student_db`
--
ALTER TABLE `old_student_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment_email_log`
--
ALTER TABLE `payment_email_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment_records`
--
ALTER TABLE `payment_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
