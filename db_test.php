<?php
// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "PHP MySQL Connection Test\n";
echo "-----------------------\n";

// Load database config
$config = require_once __DIR__ . '/config/database.php';

echo "Connection details:\n";
echo "Host: " . $config['host'] . "\n";
echo "Database: " . $config['database'] . "\n";
echo "Username: " . $config['username'] . "\n";
echo "Password: " . (!empty($config['password']) ? '[set]' : '[empty]') . "\n";

echo "\nTesting PDO_MYSQL extension:\n";
echo "Is PDO available: " . (extension_loaded('pdo') ? 'Yes' : 'No') . "\n";
echo "Is PDO_MYSQL available: " . (extension_loaded('pdo_mysql') ? 'Yes' : 'No') . "\n";

echo "\nAttempting connection:\n";

// Get the MySQL socket path if we're using localhost
if ($config['host'] === 'localhost') {
  // Check if we can find the MySQL socket file
  $possibleSocketPaths = [
    '/var/run/mysqld/mysqld.sock',
    '/tmp/mysql.sock',
    '/var/lib/mysql/mysql.sock'
  ];

  echo "Checking MySQL socket paths:\n";
  foreach ($possibleSocketPaths as $path) {
    echo "- $path: " . (file_exists($path) ? 'exists' : 'not found') . "\n";
  }
}

try {
  // Normal connection attempt
  echo "\nConnection attempt 1: Standard method\n";
  $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
  $conn = new PDO($dsn, $config['username'], $config['password'], $config['options']);
  echo "✓ Connection successful!\n";
  $conn = null;
} catch (PDOException $e) {
  echo "✗ Connection failed: " . $e->getMessage() . "\n";

  // Try alternative connection methods if the standard one failed
  try {
    echo "\nConnection attempt 2: Using unix_socket parameter explicitly\n";

    // Search for the MySQL socket file
    $socketFile = null;
    foreach ($possibleSocketPaths as $path) {
      if (file_exists($path)) {
        $socketFile = $path;
        break;
      }
    }

    if ($socketFile) {
      echo "Using socket: $socketFile\n";
      $dsn = "mysql:unix_socket=$socketFile;dbname={$config['database']};charset={$config['charset']}";
      $conn = new PDO($dsn, $config['username'], $config['password'], $config['options']);
      echo "✓ Connection successful using unix_socket!\n";
      $conn = null;

      echo "\nSolution: Update your connection string to use socket explicitly.\n";
    } else {
      echo "✗ No socket file found to try this method.\n";
    }
  } catch (PDOException $e2) {
    echo "✗ Socket connection failed: " . $e2->getMessage() . "\n";

    try {
      echo "\nConnection attempt 3: Using TCP/IP (127.0.0.1) instead of socket\n";
      $dsn = "mysql:host=127.0.0.1;dbname={$config['database']};charset={$config['charset']}";
      $conn = new PDO($dsn, $config['username'], $config['password'], $config['options']);
      echo "✓ Connection successful using TCP/IP!\n";
      $conn = null;

      echo "\nSolution: Change 'host' => 'localhost' to 'host' => '127.0.0.1' in your config/database.php file.\n";
    } catch (PDOException $e3) {
      echo "✗ TCP/IP connection failed: " . $e3->getMessage() . "\n";
    }
  }
}