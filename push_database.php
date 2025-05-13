<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load database configuration
$config = require __DIR__ . '/config/database.php';

try {
  // Create database connection
  $pdo = new PDO(
    "mysql:host={$config['host']};port={$config['port']}",
    $config['username'],
    $config['password']
  );
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Select the database
  $pdo->exec("USE {$config['database']}");

  // Read and execute the drop tables SQL
  $dropSql = file_get_contents(__DIR__ . '/drop_tables.sql');
  $pdo->exec($dropSql);
  echo "Successfully dropped specified tables.\n";

  // Read and execute the schema SQL
  $schemaSql = file_get_contents(__DIR__ . '/setup_schema.sql');
  $pdo->exec($schemaSql);
  echo "Successfully pushed database schema.\n";

  echo "Database push completed successfully!\n";
} catch (PDOException $e) {
  die("Database operation failed: " . $e->getMessage() . "\n");
}