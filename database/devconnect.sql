-- =============================================================================
-- ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
-- Task 3: Backend Integration & CRUD
-- Database Schema & Initial Seed Data
-- Database Name: devconnect
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `devconnect` 
  DEFAULT CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `devconnect`;

-- -----------------------------------------------------------------------------
-- Table structure for table `users`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `date_of_birth` DATE NULL,
  `gender` ENUM('male', 'female', 'non-binary', 'prefer-not-to-say') DEFAULT 'prefer-not-to-say',
  `country` VARCHAR(60) NULL,
  `profile_image` VARCHAR(255) DEFAULT 'default-avatar.svg',
  `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_role` (`role`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Seed Data for Evaluation & Testing
-- 
-- Default Credentials for Testing:
-- 1. Administrator Account:
--    Email:    admin@devconnect.io
--    Password: Admin@12345
--    Role:     admin
-- 
-- 2. Standard User Account:
--    Email:    user@devconnect.io
--    Password: User@12345
--    Role:     user
-- 
-- Passwords are encrypted using PHP's standard password_hash(PASSWORD_DEFAULT).
-- -----------------------------------------------------------------------------
INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `date_of_birth`, `gender`, `country`, `profile_image`, `role`, `status`) 
VALUES
  (1, 'System Administrator', 'admin@devconnect.io', '$2y$10$cHCJqdYzlFE7TKmwPZbMFeRCCAhjifSvzvT/ZOCmEQY76CTMVfXli', '1995-01-15', 'prefer-not-to-say', 'India', 'default-avatar.svg', 'admin', 'active'),
  (2, 'Pitla Yadagiri', 'user@devconnect.io', '$2y$10$anESw4dcKkYg5auVMi9vFe1voHDkmHGRjVelMR.58.dlsw5dS50Ze', '2004-06-20', 'male', 'India', 'default-avatar.svg', 'user', 'active')
ON DUPLICATE KEY UPDATE `id`=`id`;
