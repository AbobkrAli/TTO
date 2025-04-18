<?php

// Database configuration
$host = 'localhost'; // Using socket authentication
$username = 'root';
$password = '';
$database = 'php_project';

try {
  // Create connection without database first
  $conn = new PDO("mysql:host=$host", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Create database if it doesn't exist
  $conn->exec("CREATE DATABASE IF NOT EXISTS $database");
  echo "Database '$database' created or already exists.\n";

  // Switch to the database
  $conn->exec("USE $database");

  // Create users table
  $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('teacher', 'supervisor') NOT NULL DEFAULT 'teacher',
        department VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

  $conn->exec($sql);
  echo "Table 'users' created or already exists.\n";

  // Check if supervisor exists
  $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'supervisor'");
  $supervisorCount = $stmt->fetchColumn();

  if ($supervisorCount == 0) {
    // Insert default supervisor
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (fullname, email, password, role) VALUES 
                ('Admin Supervisor', 'admin@example.com', :password, 'supervisor')";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':password', $adminPassword);
    $stmt->execute();

    echo "Default supervisor account created.\n";
  } else {
    echo "Supervisor account already exists.\n";
  }

  echo "Database initialization complete!\n";

} catch (PDOException $e) {
  echo "Error: " . $e->getMessage() . "\n";
}