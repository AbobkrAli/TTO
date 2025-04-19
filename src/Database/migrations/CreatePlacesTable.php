<?php

namespace App\Database\Migrations;

use App\Database;

class CreatePlacesTable
{
  public function up()
  {
    $db = Database::getInstance();
    $sql = "CREATE TABLE IF NOT EXISTS places (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            type ENUM('classroom', 'lab', 'lecture_hall', 'other') NOT NULL DEFAULT 'classroom',
            capacity INT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->query($sql);

    // Insert some default places
    $sql = "INSERT INTO places (name, type) VALUES 
            ('Room 101', 'classroom'),
            ('Room 102', 'classroom'),
            ('Room 103', 'classroom'),
            ('Lab A', 'lab'),
            ('Lab B', 'lab'),
            ('Lecture Hall 1', 'lecture_hall'),
            ('Lecture Hall 2', 'lecture_hall');";
    $db->query($sql);
  }

  public function down()
  {
    $db = Database::getInstance();
    $sql = "DROP TABLE IF EXISTS places;";
    $db->query($sql);
  }
}