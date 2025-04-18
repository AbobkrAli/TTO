<?php

// Display all errors to help with debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "PHP MySQL Connection Test Script\n";
echo "--------------------------------\n";
echo "PHP Version: " . phpversion() . "\n";
echo "PDO Extensions enabled: " . (extension_loaded('pdo') ? 'Yes' : 'No') . "\n";
echo "PDO MySQL Extension enabled: " . (extension_loaded('pdo_mysql') ? 'Yes' : 'No') . "\n\n";

// Test methods to connect to MySQL
echo "Testing connection methods...\n\n";

// Method 1: TCP/IP (127.0.0.1)
echo "Method 1: TCP/IP Connection (127.0.0.1)\n";
try {
  $host = '127.0.0.1';
  $username = 'root';
  $password = '';
  $database = 'php_project';

  echo "Connecting to: mysql:host=$host\n";
  $conn = new PDO("mysql:host=$host", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "✅ Connection successful using TCP/IP\n";

  // Try to select/create the database
  $conn->exec("CREATE DATABASE IF NOT EXISTS $database");
  echo "✅ Database '$database' exists or was created\n";

  // Try a query
  $conn->exec("USE $database");
  echo "✅ Selected database '$database'\n";

  // Close connection
  $conn = null;
} catch (PDOException $e) {
  echo "❌ TCP/IP Connection failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Method 2: Socket connection (localhost)
echo "Method 2: Socket Connection (localhost)\n";
try {
  $host = 'localhost';
  $username = 'root';
  $password = '';

  echo "Connecting to: mysql:host=$host\n";
  $conn = new PDO("mysql:host=$host", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "✅ Connection successful using socket\n";

  // Close connection
  $conn = null;
} catch (PDOException $e) {
  echo "❌ Socket Connection failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Method 3: Specify port explicitly
echo "Method 3: Explicit Port Connection (127.0.0.1:3306)\n";
try {
  $host = '127.0.0.1';
  $port = 3306;
  $username = 'root';
  $password = '';

  echo "Connecting to: mysql:host=$host;port=$port\n";
  $conn = new PDO("mysql:host=$host;port=$port", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "✅ Connection successful with explicit port\n";

  // Close connection
  $conn = null;
} catch (PDOException $e) {
  echo "❌ Explicit Port Connection failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Method 4: Try with different credentials
echo "Method 4: Try with username 'root' and empty password\n";
try {
  $host = '127.0.0.1';
  $username = 'root';
  $password = '';

  echo "Connecting to: mysql:host=$host with username=$username\n";
  $conn = new PDO("mysql:host=$host", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "✅ Connection successful with username '$username'\n";

  // Close connection
  $conn = null;
} catch (PDOException $e) {
  echo "❌ Connection with username '$username' failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Provide recommendations based on results
echo "Recommendations:\n";
echo "----------------\n";
echo "1. If all methods failed, make sure the MySQL server is running with: sudo service mysql status\n";
echo "2. If not running, start it with: sudo service mysql start\n";
echo "3. If still failing, check if MySQL is installed with: which mysql\n";
echo "4. Verify MySQL root password if connection is refused\n";
echo "5. Update config/database.php with the method that worked\n";