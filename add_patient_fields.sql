-- SQL Query to add new fields to imported_patients table
-- Run this query in your MySQL database to add the requested fields

ALTER TABLE `imported_patients` 
-- Add email field if it doesn't exist (some references suggest it might exist)
ADD COLUMN IF NOT EXISTS `email` VARCHAR(255) DEFAULT NULL AFTER `address`,

-- Add contact number field
ADD COLUMN IF NOT EXISTS `contact_number` VARCHAR(20) DEFAULT NULL AFTER `email`,

-- Add religion field
ADD COLUMN IF NOT EXISTS `religion` VARCHAR(100) DEFAULT NULL AFTER `contact_number`,

-- Add citizenship/nationality field
ADD COLUMN IF NOT EXISTS `citizenship` VARCHAR(100) DEFAULT NULL AFTER `religion`,

-- Add course/program field
ADD COLUMN IF NOT EXISTS `course_program` VARCHAR(255) DEFAULT NULL AFTER `citizenship`,

-- Add guardian/parent name field
ADD COLUMN IF NOT EXISTS `guardian_name` VARCHAR(255) DEFAULT NULL AFTER `year_level`,

-- Add guardian/parent contact field
ADD COLUMN IF NOT EXISTS `guardian_contact` VARCHAR(255) DEFAULT NULL AFTER `guardian_name`,

-- Add emergency contact person field
ADD COLUMN IF NOT EXISTS `emergency_contact_name` VARCHAR(255) DEFAULT NULL AFTER `guardian_contact`,

-- Add emergency contact number field
ADD COLUMN IF NOT EXISTS `emergency_contact_number` VARCHAR(20) DEFAULT NULL AFTER `emergency_contact_name`;

-- Alternative single-line version (if you prefer to run one column at a time):
-- ALTER TABLE `imported_patients` ADD COLUMN IF NOT EXISTS `email` VARCHAR(255) DEFAULT NULL AFTER `address`;
-- ALTER TABLE `imported_patients` ADD COLUMN IF NOT EXISTS `contact_number` VARCHAR(20) DEFAULT NULL AFTER `email`;
-- ALTER TABLE `imported_patients` ADD COLUMN IF NOT EXISTS `religion` VARCHAR(100) DEFAULT NULL AFTER `contact_number`;
-- ALTER TABLE `imported_patients` ADD COLUMN IF NOT EXISTS `citizenship` VARCHAR(100) DEFAULT NULL AFTER `religion`;
-- ALTER TABLE `imported_patients` ADD COLUMN IF NOT EXISTS `course_program` VARCHAR(255) DEFAULT NULL AFTER `citizenship`;
-- ALTER TABLE `imported_patients` ADD COLUMN IF NOT EXISTS `guardian_name` VARCHAR(255) DEFAULT NULL AFTER `year_level`;
-- ALTER TABLE `imported_patients` ADD COLUMN IF NOT EXISTS `guardian_contact` VARCHAR(255) DEFAULT NULL AFTER `guardian_name`;
-- ALTER TABLE `imported_patients` ADD COLUMN IF NOT EXISTS `emergency_contact_name` VARCHAR(255) DEFAULT NULL AFTER `emergency_contact_name`;
-- ALTER TABLE `imported_patients` ADD COLUMN IF NOT EXISTS `emergency_contact_number` VARCHAR(20) DEFAULT NULL AFTER `emergency_contact_name`;
