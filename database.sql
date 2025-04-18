-- Create database
CREATE DATABASE IF NOT EXISTS php_project;
USE php_project;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('teacher', 'supervisor') NOT NULL DEFAULT 'teacher',
    department VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert initial supervisor user
INSERT INTO users (fullname, email, password, role) VALUES 
('Admin Supervisor', 'admin@example.com', '$2y$10$Cq1RU8zaIvWg91rkD1XyJOKQxTEQ1vdBz/Wt5HP9HCE52lmH.xKHC', 'supervisor');
-- Password is 'admin123' 

-- Departments table
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Update users table to reference departments by ID
ALTER TABLE users 
MODIFY COLUMN department INT NULL,
ADD CONSTRAINT fk_user_department 
FOREIGN KEY (department) REFERENCES departments(id) 
ON DELETE SET NULL;

-- Subjects table
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(50) NOT NULL UNIQUE,
    subject_name VARCHAR(255) NOT NULL,
    department_id INT NOT NULL,
    day ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    hour TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
);

-- Insert some sample departments
INSERT INTO departments (name) VALUES 
('Mathematics'),
('Science'),
('English'),
('Computer Science'); 