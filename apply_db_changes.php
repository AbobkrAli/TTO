<?php

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/vendor/autoload.php';

use App\Database;

echo "Starting database update for departments and subjects...\n";

$db = Database::getInstance();

// Create the departments table
$createDepartmentsTable = "
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

try {
  $db->query($createDepartmentsTable);
  echo "✓ Departments table created or already exists\n";
} catch (Exception $e) {
  echo "✗ Error creating departments table: " . $e->getMessage() . "\n";
  exit(1);
}

// Check if users.department is already migrated to INT
$stmt = $db->query("SHOW COLUMNS FROM users WHERE Field = 'department'");
$column = $stmt->fetch(PDO::FETCH_ASSOC);
$dataType = strtoupper($column['Type']);

if (strpos($dataType, 'INT') === false) {
  // Back up existing department names
  echo "Backing up existing department data...\n";
  $users = $db->query("SELECT id, department FROM users WHERE department IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);

  $departmentMap = [];
  foreach ($users as $user) {
    if (!empty($user['department']) && !isset($departmentMap[$user['department']])) {
      // Insert the department
      $db->query("INSERT IGNORE INTO departments (name) VALUES (?)", [$user['department']]);
      // Get its ID
      $deptId = $db->query("SELECT id FROM departments WHERE name = ?", [$user['department']])->fetch(PDO::FETCH_COLUMN);
      $departmentMap[$user['department']] = $deptId;
    }
  }

  echo "Converting users.department column to INT...\n";
  try {
    // Temporarily modify column to allow NULL (to avoid constraint errors)
    $db->query("ALTER TABLE users MODIFY COLUMN department VARCHAR(255) NULL");

    // Update users with department IDs
    foreach ($users as $user) {
      if (!empty($user['department']) && isset($departmentMap[$user['department']])) {
        $db->query(
          "UPDATE users SET department = ? WHERE id = ?",
          [$departmentMap[$user['department']], $user['id']]
        );
      }
    }

    // Now alter the column to INT and add foreign key
    $db->query("ALTER TABLE users MODIFY COLUMN department INT NULL");
    $db->query("ALTER TABLE users ADD CONSTRAINT fk_user_department 
                    FOREIGN KEY (department) REFERENCES departments(id) 
                    ON DELETE SET NULL");

    echo "✓ Users table successfully migrated to use department IDs\n";
  } catch (Exception $e) {
    echo "✗ Error migrating users.department column: " . $e->getMessage() . "\n";
    exit(1);
  }
} else {
  echo "✓ Users table is already using department IDs\n";
}

// Create the subjects table
$createSubjectsTable = "
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
)";

try {
  $db->query($createSubjectsTable);
  echo "✓ Subjects table created or already exists\n";
} catch (Exception $e) {
  echo "✗ Error creating subjects table: " . $e->getMessage() . "\n";
  exit(1);
}

// Add sample data if tables are empty
if ($db->query("SELECT COUNT(*) FROM departments")->fetchColumn() == 0) {
  echo "Adding sample departments...\n";
  $sampleDepartments = [
    'Mathematics',
    'Science',
    'English',
    'Computer Science'
  ];

  foreach ($sampleDepartments as $dept) {
    $db->query("INSERT INTO departments (name) VALUES (?)", [$dept]);
  }

  echo "✓ Sample departments added\n";
}

if ($db->query("SELECT COUNT(*) FROM subjects")->fetchColumn() == 0) {
  echo "Adding sample subjects...\n";
  $sampleSubjects = [
    ['MATH101', 'Introduction to Calculus', 1, 'Monday', '09:00:00'],
    ['CS101', 'Computer Programming', 4, 'Wednesday', '14:00:00'],
    ['ENG201', 'Advanced English Grammar', 3, 'Tuesday', '11:30:00'],
    ['SCI301', 'Physics Fundamentals', 2, 'Friday', '10:15:00']
  ];

  foreach ($sampleSubjects as $subject) {
    $db->query(
      "INSERT INTO subjects (subject_code, subject_name, department_id, day, hour) VALUES (?, ?, ?, ?, ?)",
      $subject
    );
  }

  echo "✓ Sample subjects added\n";
}

echo "\nDatabase update completed successfully!\n";
?>