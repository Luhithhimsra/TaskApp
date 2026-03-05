-- TaskFlow Pro — Database Setup
-- Passwords: admin=admin123, john=password123, trump=password123, luhith-luhith123


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Create & select database
-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `task_management_db`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_general_ci;

USE `task_management_db`;

-- --------------------------------------------------------
-- Drop tables if re-running (order matters for FK)
-- --------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `tasks`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- Users Table
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `full_name`  VARCHAR(50)  NOT NULL,
  `username`   VARCHAR(50)  NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('admin','employee') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Tasks Table (priority + soft delete)
-- --------------------------------------------------------
CREATE TABLE `tasks` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `title`       VARCHAR(100) NOT NULL,
  `description` TEXT         DEFAULT NULL,
  `assigned_to` INT          DEFAULT NULL,
  `due_date`    DATE         DEFAULT NULL,
  `status`      ENUM('pending','in_progress','completed') DEFAULT 'pending',
  `priority`    ENUM('low','medium','high')               DEFAULT 'medium',
  `deleted_at`  TIMESTAMP    NULL DEFAULT NULL,
  `created_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Notifications Table
-- --------------------------------------------------------
CREATE TABLE `notifications` (
  `id`        INT AUTO_INCREMENT PRIMARY KEY,
  `message`   TEXT        NOT NULL,
  `recipient` INT         NOT NULL,
  `type`      VARCHAR(50) NOT NULL,
  `date`      DATE        NOT NULL DEFAULT (CURRENT_DATE),
  `is_read`   TINYINT(1)  DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Sample Users
-- admin    - admin123
-- john     - password123
-- trump    - password123
-- luhith   - luhith123
-- --------------------------------------------------------
INSERT INTO `users` (`full_name`, `username`, `password`, `role`) VALUES
('Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('John Smith',    'john',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee'),
('Elias A.',      'elias', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee');

-- --------------------------------------------------------
-- Sample Tasks
-- --------------------------------------------------------
INSERT INTO `tasks` (`title`, `description`, `assigned_to`, `due_date`, `status`, `priority`) VALUES
('Monthly Financial Report',  'Prepare and review the monthly financial report.',          2, DATE_ADD(CURDATE(), INTERVAL 5 DAY),  'in_progress', 'high'),
('Website Maintenance',       'Update content and apply security patches.',                3, DATE_ADD(CURDATE(), INTERVAL 3 DAY),  'pending',     'medium'),
('Customer Survey Analysis',  'Analyze feedback from the latest customer survey.',         2, DATE_ADD(CURDATE(), INTERVAL 7 DAY),  'pending',     'high'),
('Employee Training Program', 'Develop a new training program for the team.',              3, DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'pending',     'low'),
('Q4 Budget Preparation',     'Create and review the budget for Q4.',                      2, DATE_ADD(CURDATE(), INTERVAL 2 DAY),  'completed',   'high'),
('Design New Company Website','Create wireframes and mockups for the new website.',        3, DATE_ADD(CURDATE(), INTERVAL 8 DAY),  'in_progress', 'medium'),
('Server Downtime Fix',       'Investigate and resolve the server downtime issue.',        2, DATE_ADD(CURDATE(), INTERVAL 1 DAY),  'pending',     'high'),
('Write Industry Blog Post',  'Draft and publish a post on current industry trends.',      3, DATE_ADD(CURDATE(), INTERVAL 6 DAY),  'pending',     'low');

-- --------------------------------------------------------
-- Sample Notifications
-- --------------------------------------------------------
INSERT INTO `notifications` (`message`, `recipient`, `type`, `date`, `is_read`) VALUES
('Monthly Financial Report has been assigned to you. Please review and start working on it.', 2, 'New Task Assigned', CURDATE(), 0),
('Customer Survey Analysis has been assigned to you. Please review and start working on it.', 2, 'New Task Assigned', CURDATE(), 0),
('Website Maintenance has been assigned to you. Please review and start working on it.',       3, 'New Task Assigned', CURDATE(), 0),
('Employee Training Program has been assigned to you. Please review and start working on it.',3, 'New Task Assigned', CURDATE(), 0);

COMMIT;