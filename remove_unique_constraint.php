<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Database.php';

try {
  $db = App\Database::getInstance();
  $pdo = $db->getConnection();

  // Remove the unique constraint from subjects table
  $sql = "ALTER TABLE subjects DROP INDEX uk_department_day_hour";

  $pdo->exec($sql);
  echo "Successfully removed unique constraint from subjects table.\n";
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage() . "\n";
}