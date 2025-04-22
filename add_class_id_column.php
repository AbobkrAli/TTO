<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/Database.php';

try {
  $db = new App\Database();
  $pdo = $db->getConnection();

  // Add class_id column to requests table
  $sql = "ALTER TABLE requests
            ADD COLUMN class_id INT NULL,
            ADD CONSTRAINT fk_requests_class
            FOREIGN KEY (class_id) REFERENCES classes(id)
            ON DELETE SET NULL
            ON UPDATE CASCADE";

  $pdo->exec($sql);
  echo "Successfully added class_id column to requests table.\n";
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage() . "\n";
}