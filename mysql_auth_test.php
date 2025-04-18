<?php

// Display all errors to help with debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "MySQL Authentication Test\n";
echo "-----------------------\n";

// Testing authentication methods
$methods = [
  [
    'name' => 'Standard authentication (empty password)',
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => ''
  ],
  [
    'name' => 'Standard authentication (with socket auth plugin)',
    'host' => 'localhost',
    'username' => 'root',
    'password' => ''
  ],
  [
    'name' => 'Using password "root"',
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => 'root'
  ],
  [
    'name' => 'Using password "password"',
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => 'password'
  ],
  [
    'name' => 'Different user (if available)',
    'host' => '127.0.0.1',
    'username' => 'phpmyadmin',
    'password' => ''
  ]
];

foreach ($methods as $method) {
  echo "\nTrying: {$method['name']}\n";
  echo "Connection string: mysql:host={$method['host']}\n";
  echo "Username: {$method['username']}\n";
  echo "Password: " . ($method['password'] === '' ? '[empty]' : '[set]') . "\n";

  try {
    $conn = new PDO("mysql:host={$method['host']}", $method['username'], $method['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ SUCCESS: Connected successfully\n";

    // If successful, try to create the database
    try {
      $conn->exec("CREATE DATABASE IF NOT EXISTS php_project");
      echo "✓ SUCCESS: Created/verified database php_project\n";

      // Try to create a table
      $conn->exec("USE php_project");
      $conn->exec("CREATE TABLE IF NOT EXISTS test_connection (id INT)");
      echo "✓ SUCCESS: Created test table\n";

      // Remember the successful connection info
      echo "\n*** THIS CONNECTION METHOD WORKS! ***\n";
      echo "Use these settings in your config/database.php:\n";
      echo "host: {$method['host']}\n";
      echo "username: {$method['username']}\n";
      echo "password: " . ($method['password'] === '' ? '[empty string]' : $method['password']) . "\n";
    } catch (PDOException $e) {
      echo "✗ ERROR with database operations: " . $e->getMessage() . "\n";
    }

    $conn = null;
  } catch (PDOException $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
  }
}

echo "\n\nRecommendations:\n";
echo "----------------\n";
echo "1. Update config/database.php with the successful connection settings\n";
echo "2. If all attempts failed, try resetting MySQL root password:\n";
echo "   - sudo mysql\n";
echo "   - ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_new_password';\n";
echo "   - FLUSH PRIVILEGES;\n";
echo "   - exit;\n";
echo "3. Then update your config/database.php with the new password\n";