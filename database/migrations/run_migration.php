<?php

require_once __DIR__ . '/../../src/Database.php';

try {
  $db = \App\Database::getInstance();
  $sql = "ALTER TABLE subjects DROP INDEX uk_department_day_hour";
  $db->query($sql);
  echo "Migration completed successfully!\n";
} catch (\Exception $e) {
  echo "Error running migration: " . $e->getMessage() . "\n";
}