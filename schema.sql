-- Educational Institution Management System Database Schema

-- Drop database if exists (for clean installation)
DROP DATABASE IF EXISTS education_management;

-- Create database with proper character set
CREATE DATABASE education_management
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE education_management;

-- Departments table
CREATE TABLE departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL UNIQUE COMMENT 'Department name (e.g. Computer Science)',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('teacher', 'supervisor') NOT NULL,
  department_id INT NULL COMMENT 'Department the user belongs to (can be NULL for supervisors)',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (department_id) REFERENCES departments(id)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
    
  INDEX idx_user_role (role),
  INDEX idx_user_department (department_id)
) ENGINE=InnoDB;

-- Subjects table (for the schedule)
CREATE TABLE subjects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  subject_code VARCHAR(50) NOT NULL,
  name VARCHAR(255) NOT NULL COMMENT 'Subject name',
  department_id INT NOT NULL COMMENT 'Department this subject belongs to',
  day ENUM('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday') NOT NULL,
  hour INT NOT NULL CHECK (hour >= 9 AND hour <= 17) COMMENT 'Hour of the class (9-17)',
  class_id INT NULL COMMENT 'Class this subject is assigned to',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (department_id) REFERENCES departments(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    
  FOREIGN KEY (class_id) REFERENCES classes(id)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
    
  UNIQUE KEY uk_department_day_hour (department_id, day, hour) COMMENT 'Prevent scheduling conflicts in the same timeslot',
  INDEX idx_subject_department (department_id),
  INDEX idx_subject_day_hour (day, hour)
) ENGINE=InnoDB;

-- Requests table (for schedule change requests)
CREATE TABLE requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  teacher_id INT NOT NULL COMMENT 'Teacher who sent the request',
  department_id INT NOT NULL COMMENT 'Department the request is for',
  day ENUM('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday') NOT NULL,
  hour INT NOT NULL CHECK (hour >= 9 AND hour <= 17) COMMENT 'Hour requested (9-17)',
  subject_code VARCHAR(50) NULL COMMENT 'Optional subject code if requesting for a specific subject',
  subject_name VARCHAR(255) NULL COMMENT 'Optional subject name if requesting a new subject',
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (teacher_id) REFERENCES users(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    
  FOREIGN KEY (department_id) REFERENCES departments(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    
  INDEX idx_request_teacher (teacher_id),
  INDEX idx_request_department (department_id),
  INDEX idx_request_status (status)
) ENGINE=InnoDB;

-- Insert initial supervisor user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin Supervisor', 'admin@example.com', '$2y$10$Cq1RU8zaIvWg91rkD1XyJOKQxTEQ1vdBz/Wt5HP9HCE52lmH.xKHC', 'supervisor');

-- Insert sample departments
INSERT INTO departments (name) VALUES 
('Computer Science'),
('Engineering'),
('Business Administration'),
('Mathematics'),
('Physics');

-- Add comments explaining relationship structure
/*
RELATIONSHIPS:

1. One-to-Many: Department to Users
   - One department can have many users (teachers)
   - A user (teacher) belongs to exactly one department
   - Supervisors can have NULL department

2. One-to-Many: Department to Subjects (Schedule)
   - One department has a schedule consisting of many subjects
   - Each subject belongs to exactly one department
   - Subjects have a specific day and hour in the schedule

3. One-to-Many: Users to Requests
   - A teacher can submit multiple schedule change requests
   - Each request is submitted by exactly one teacher

4. One-to-Many: Department to Requests
   - Requests are associated with a specific department
   - A department can have multiple requests

CONSTRAINTS:
   - Hours must be between 9 and 17 (9 AM to 5 PM)
   - Days must be from Sunday to Thursday
   - A department cannot have two subjects at the same day and hour (schedule conflict prevention)
*/ 