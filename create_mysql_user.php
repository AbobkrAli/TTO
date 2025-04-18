<?php

// To be run with sudo if needed: sudo php create_mysql_user.php

// Display all errors to help with debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "PHP MySQL User Creation Script\n";
echo "-----------------------------\n";

// Try different methods to connect to MySQL
$methods = [
  [
    'name' => 'Using socket authentication (localhost)',
    'host' => 'localhost',
    'username' => 'root',
    'password' => ''
  ],
  [
    'name' => 'Using TCP/IP (127.0.0.1)',
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => ''
  ],
  [
    'name' => 'Using sudo authentication',
    'method' => 'exec',
    'command' => 'sudo mysql -e "CREATE USER IF NOT EXISTS \'phpuser\'@\'localhost\' IDENTIFIED BY \'password123\'; GRANT ALL PRIVILEGES ON *.* TO \'phpuser\'@\'localhost\'; FLUSH PRIVILEGES;"'
  ]
];

// First try connecting directly with PDO
$connected = false;
foreach ($methods as $method) {
  if (isset($method['method']) && $method['method'] === 'exec') {
    continue; // Skip exec methods for now
  }

  echo "\nAttempting connection: {$method['name']}\n";
  try {
    $conn = new PDO("mysql:host={$method['host']}", $method['username'], $method['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✓ Connected to MySQL successfully!\n";
    echo "Creating new user 'phpuser'...\n";

    try {
      // Create a new user with privileges
      $conn->exec("CREATE USER IF NOT EXISTS 'phpuser'@'localhost' IDENTIFIED BY 'password123'");
      $conn->exec("GRANT ALL PRIVILEGES ON *.* TO 'phpuser'@'localhost'");
      $conn->exec("FLUSH PRIVILEGES");

      echo "✓ User 'phpuser' created successfully!\n";
      echo "\nUpdate your database configuration in config/database.php with:\n";
      echo "host: localhost\n";
      echo "username: phpuser\n";
      echo "password: password123\n";

      $connected = true;
      break;
    } catch (PDOException $e) {
      echo "✗ Error creating user: " . $e->getMessage() . "\n";
    }

    $conn = null;
  } catch (PDOException $e) {
    echo "✗ Connection failed: " . $e->getMessage() . "\n";
  }
}

// If PDO connections failed, try using system commands
if (!$connected) {
  echo "\nAttempting to create user using system commands...\n";

  foreach ($methods as $method) {
    if (!isset($method['method']) || $method['method'] !== 'exec') {
      continue;
    }

    echo "Executing: {$method['name']}\n";
    echo "This might prompt for your sudo password...\n";

    $output = [];
    $return_var = 0;
    exec($method['command'] . " 2>&1", $output, $return_var);

    if ($return_var === 0) {
      echo "✓ Command executed successfully!\n";
      echo "\nUpdate your database configuration in config/database.php with:\n";
      echo "host: localhost\n";
      echo "username: phpuser\n";
      echo "password: password123\n";
      $connected = true;
      break;
    } else {
      echo "✗ Command failed with error code $return_var:\n";
      echo implode("\n", $output) . "\n";
    }
  }
}

if (!$connected) {
  echo "\n\nAll methods failed. Please try the following:\n\n";
  echo "1. Manually run in a terminal:\n";
  echo "   sudo mysql\n\n";
  echo "2. Then in the MySQL prompt, execute:\n";
  echo "   CREATE USER 'phpuser'@'localhost' IDENTIFIED BY 'password123';\n";
  echo "   GRANT ALL PRIVILEGES ON *.* TO 'phpuser'@'localhost';\n";
  echo "   FLUSH PRIVILEGES;\n";
  echo "   exit;\n\n";
  echo "3. Update config/database.php with phpuser credentials\n";
} else {
  echo "\nNow let's create the database and tables...\n";

  try {
    $conn = new PDO("mysql:host=localhost", 'phpuser', 'password123');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database
    $conn->exec("CREATE DATABASE IF NOT EXISTS php_project");
    echo "✓ Database 'php_project' created successfully!\n";

    // Use the database
    $conn->exec("USE php_project");

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
    echo "✓ Table 'users' created successfully!\n";

    // Insert default supervisor user
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'supervisor'");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
      $password = password_hash('admin123', PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
      $stmt->execute(['Admin Supervisor', 'admin@example.com', $password, 'supervisor']);
      echo "✓ Default supervisor user created successfully!\n";
    } else {
      echo "✓ Supervisor user already exists.\n";
    }

    echo "\nSetup complete! You can now run your PHP application.\n";

  } catch (PDOException $e) {
    echo "✗ Error setting up database: " . $e->getMessage() . "\n";
  }
}