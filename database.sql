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