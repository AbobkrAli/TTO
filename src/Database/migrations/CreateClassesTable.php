<?php

namespace App\Database\Migrations;

use App\Database;

class CreateClassesTable
{
  public function up()
  {
    $db = Database::getInstance();
    $sql = "CREATE TABLE IF NOT EXISTS classes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->query($sql);
  }

  public function down()
  {
    $db = Database::getInstance();
    $sql = "DROP TABLE IF EXISTS classes;";
    $db->query($sql);
  }
}