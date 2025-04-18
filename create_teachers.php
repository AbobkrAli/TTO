<?php

// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Creating Teacher Accounts\n";
echo "------------------------\n";

// Load database config
$config = require_once __DIR__ . '/config/database.php';

// Teacher accounts data
$teachers = [
  [
    'fullname' => 'John Smith',
    'email' => 'john.smith@example.com',
    'password' => 'teacher123',
    'department' => 'Mathematics'
  ],
  [
    'fullname' => 'Sarah Johnson',
    'email' => 'sarah.johnson@example.com',
    'password' => 'teacher123',
    'department' => 'Science'
  ],
  [
    'fullname' => 'Michael Brown',
    'email' => 'michael.brown@example.com',
    'password' => 'teacher123',
    'department' => 'English'
  ]
];

try {
  // Connect to the database
  if ($config['host'] === 'localhost') {
    $socketFile = '/var/run/mysqld/mysqld.sock';
    $dsn = "mysql:unix_socket={$socketFile};dbname={$config['database']};charset={$config['charset']}";
  } else {
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
  }

  $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);

  // Prepare the insert statement
  $stmt = $pdo->prepare("
        INSERT INTO users (fullname, email, password, role, department)
        VALUES (:fullname, :email, :password, 'teacher', :department)
    ");

  // Insert each teacher
  foreach ($teachers as $teacher) {
    // Check if the user already exists
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $checkStmt->execute(['email' => $teacher['email']]);

    if ($checkStmt->fetch()) {
      echo "Teacher {$teacher['fullname']} ({$teacher['email']}) already exists. Skipping.\n";
      continue;
    }

    // Hash the password
    $hashedPassword = password_hash($teacher['password'], PASSWORD_DEFAULT);

    // Insert the new teacher
    $stmt->execute([
      'fullname' => $teacher['fullname'],
      'email' => $teacher['email'],
      'password' => $hashedPassword,
      'department' => $teacher['department']
    ]);

    echo "Created teacher account: {$teacher['fullname']} ({$teacher['email']})\n";
    echo "Password: {$teacher['password']}\n";
    echo "Department: {$teacher['department']}\n";
    echo "----------------------\n";
  }

  echo "\nTeacher accounts created successfully!\n";
  echo "\nYou can now log in with any of these accounts:\n";

  foreach ($teachers as $teacher) {
    echo "- Email: {$teacher['email']}, Password: {$teacher['password']}\n";
  }

} catch (PDOException $e) {
  echo "Database Error: " . $e->getMessage() . "\n";
}