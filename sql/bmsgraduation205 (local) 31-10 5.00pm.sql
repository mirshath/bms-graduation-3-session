-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2025 at 12:37 PM
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
(3, 'finance', 'finance@gmail.com', '$2y$10$PMCjct9jnXAMxMST6LqxcePkazUA79V3UidG3.JSLl9kYuO58d.Dm', 'finance', '2025-10-26 07:18:55');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bulk_data_table`
--

INSERT INTO `bulk_data_table` (`id`, `student_id`, `seat_no`, `program_name`, `session_time`, `email_sent_yes_no`, `email_sent_time`, `created_at`) VALUES
(1, '123456', 'S001', 'GDM', 'MORNING', 'No', NULL, '2025-10-30 07:46:02');

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
  `session` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_tables`
--

INSERT INTO `data_tables` (`id`, `programName`, `graduationFee`, `freeTicket`, `extraTicketFee`, `session`) VALUES
(1, 'Higher National Diploma in Business - Batch 15', 15000, 1, 3000, 'MORNING'),
(2, 'Higher National Diploma in Business - Batch 16', 15000, 1, 3000, 'MORNING'),
(3, 'Higher Diploma in Biomedical Science - Batch 27', 15000, 1, 3000, 'MORNING'),
(4, 'Higher Diploma in Biotechnology - Batch 21', 15000, 1, 3000, 'MORNING'),
(5, 'Higher Diploma in Food Science and Nutrition - Batch 4', 15000, 1, 3000, 'MORNING'),
(6, 'Higher Diploma in Medical Biotechnology - Batch 1', 15000, 1, 3000, 'MORNING'),
(7, 'Graduate Diploma in Management - Batch 57', 20000, 2, 3000, 'MORNING'),
(8, 'Graduate Diploma in Management - Batch 58', 20000, 2, 3000, 'MORNING'),
(9, 'Graduate Diploma in Management - Batch 62', 20000, 2, 3000, 'MORNING'),
(10, 'Graduate Diploma in Management - Batch 63', 20000, 2, 3000, 'MORNING'),
(11, 'Graduate Diploma in Management - Batch 66', 20000, 2, 3000, 'MORNING'),
(12, 'Graduate Diploma in Management - Batch 68', 20000, 2, 3000, 'MORNING'),
(13, 'Graduate Diploma in Management - Batch 70', 20000, 2, 3000, 'MORNING'),
(14, 'Graduate Diploma in Management - Batch 71', 20000, 2, 3000, 'MORNING'),
(15, 'Graduate Diploma in Management - Batch 73', 20000, 2, 3000, 'MORNING'),
(16, 'Graduate Diploma in Management - Batch 75', 20000, 2, 3000, 'MORNING'),
(17, 'Graduate Diploma in Management - Batch 76', 20000, 2, 3000, 'MORNING'),
(18, 'Graduate Diploma in Management - Batch 77', 20000, 2, 3000, 'MORNING'),
(19, 'Graduate Diploma in Management - Batch 78', 20000, 2, 3000, 'MORNING'),
(20, 'Graduate Diploma in Management - Batch 79', 20000, 2, 3000, 'MORNING'),
(21, 'Graduate Diploma in Management - Batch 80', 20000, 2, 3000, 'MORNING'),
(22, 'Graduate Diploma in Management - Batch 81', 20000, 2, 3000, 'MORNING'),
(23, 'Teesside MBA - COHORT 07', 25000, 2, 3000, 'MORNING'),
(24, 'MSc Management Northumbria University - COHORT 01', 25000, 2, 3000, 'EVENING'),
(25, 'BSc (Hons) Global Business Management - May 2024', 25000, 2, 3000, 'EVENING'),
(26, 'BSc (Hons) Global Business Management - September 2024', 25000, 2, 3000, 'EVENING'),
(27, 'BSc (Hons) Accounting and Finance - September 2024', 25000, 2, 3000, 'EVENING'),
(28, 'BSc (Hons) Global Business Management (Human Resources) - September 2024', 25000, 2, 3000, 'EVENING'),
(29, 'BSc (Hons) Global Business Management (Marketing) - September 2024', 25000, 2, 3000, 'EVENING'),
(30, 'BSc (Hons) Global Business Management (Marketing) - January 2025', 25000, 2, 3000, 'EVENING'),
(31, 'BSc (Hons) Global Business Management - January 2025', 25000, 2, 3000, 'EVENING'),
(32, 'BSc (Hons) Global Business Management (Human Resources) - January 2025', 25000, 2, 3000, 'EVENING'),
(33, 'BSc (Hons) Accounting and Finance - January 2025', 25000, 2, 3000, 'EVENING'),
(34, 'BSc (Hons) Biomedical Science - September 2024', 25000, 2, 3000, 'EVENING'),
(35, 'BSc (Hons) Biotechnology - September 2024', 25000, 2, 3000, 'EVENING'),
(36, 'BSc (Hons) Biomedical Science - January 2025', 25000, 2, 3000, 'EVENING'),
(37, 'BSc (Hons) Biotechnology - January 2025', 25000, 2, 3000, 'EVENING'),
(50, 'GDM', 20000, 2, 3000, 'MORNING'),
(60, 'ECMS', 30000, 3, 4500, 'EVENING');

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
-- Dumping data for table `extra_ticket_log`
--

INSERT INTO `extra_ticket_log` (`id`, `student_id`, `added_tickets`, `ticket_price`, `total_added`, `added_by`, `added_on`) VALUES
(1, '333', 6, 3000.00, 18000.00, 2, '2025-10-31 11:09:31'),
(2, '333', 3, 3000.00, 9000.00, 2, '2025-10-31 11:36:02'),
(3, '333', 4, 3000.00, 12000.00, 3, '2025-10-31 14:09:54');

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
(1, 'payment_records', 2, '333', 'UPDATE', 'Payment record updated for student ID 333, receipt number: GC20255138', 'read', '2025-10-31 08:39:54'),
(2, 'extra_ticket_log', 3, '333', 'INSERT', 'Added 4 extra ticket(s) worth Rs. 12000.00', 'read', '2025-10-31 08:39:54');

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
(2, '333', 'mohamed', '2025-10-08', 'yournumplz@gmail.com', 'BSc (Hons) Global Business Management (Human Resources) - September 2024', 778899, 'paid', 'registered', 11);

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
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_records`
--

INSERT INTO `payment_records` (`id`, `student_id`, `program_name`, `graduation_fee`, `free_ticket_count`, `extra_ticket_count`, `extra_ticket_fee`, `total_amount`, `receipt_number`, `payment_date`) VALUES
(1, '123456', 'GDM', 20000.00, 2, 11, 33000.00, 53000, 'GC20256990', '2025-10-31 04:04:42'),
(2, '333', 'BSc (Hons) Global Business Management (Human Resources) - September 2024', 25000.00, 2, 15, 45000.00, 70000, 'GC20255138', '2025-10-31 05:06:28');

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
  `program_name` varchar(255) DEFAULT NULL,
  `given_email_add` varchar(50) DEFAULT NULL,
  `email_address` varchar(255) NOT NULL,
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

INSERT INTO `registered_students` (`id`, `student_id`, `in_no`, `dob`, `name_in_full`, `program_name`, `given_email_add`, `email_address`, `phone_no`, `attend`, `crsfee_payment_status`, `graduation_payment_status`, `invitation_collected`, `updated_at_invitation`, `created_at`) VALUES
(1, '123456', 10, '2025-10-23', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'GDM', 'mirshath.mmm@gmail.com', 'mirshath.mmm@gmail.com', '25490455', NULL, 'paid', 'paid', 'collected', '2025-10-28 15:48:09', '2025-10-28 11:00:38'),
(2, '333', NULL, '2025-10-08', 'mohamed', 'BSc (Hons) Global Business Management (Human Resources) - September 2024', 'yournumplz@gmail.com', 'yournumplz@gmail.com', '778899', NULL, 'paid', 'paid', NULL, NULL, '2025-10-31 10:35:56');

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
-- Indexes for table `data_tables`
--
ALTER TABLE `data_tables`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bulk_data_table`
--
ALTER TABLE `bulk_data_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `data_tables`
--
ALTER TABLE `data_tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `extra_ticket_log`
--
ALTER TABLE `extra_ticket_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `old_student_db`
--
ALTER TABLE `old_student_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
