<?php
require_once 'vendor/autoload.php';
require_once 'config/database.php';

try {
  // Create database connection
  $pdo = new PDO(
    "mysql:host=" . $config['host'],
    $config['username'],
    $config['password']
  );
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Create database if not exists
  $pdo->exec("CREATE DATABASE IF NOT EXISTS " . $config['database']);
  $pdo->exec("USE " . $config['database']);

  // Read and execute schema.sql
  $sql = file_get_contents('schema.sql');
  $pdo->exec($sql);

  echo "Database setup completed successfully!\n";
} catch (PDOException $e) {
  die("Database setup failed: " . $e->getMessage() . "\n");
}