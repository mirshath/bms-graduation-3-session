-- Add title column to registered_students table
-- Run this migration if your database doesn't have the title column yet
-- Execute: mysql -u root bmsgraduation205 < database/add_title_column.sql
ALTER TABLE `registered_students` ADD COLUMN `title` varchar(10) DEFAULT NULL AFTER `name_in_full`;
