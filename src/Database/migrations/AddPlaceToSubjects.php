<?php

namespace App\Database\Migrations;

use App\Database;

class AddPlaceToSubjects
{
  public function up()
  {
    $db = Database::getInstance();
    $sql = "ALTER TABLE subjects ADD COLUMN place VARCHAR(255) NOT NULL;";
    $db->query($sql);
  }

  public function down()
  {
    $db = Database::getInstance();
    $sql = "ALTER TABLE subjects DROP COLUMN place;";
    $db->query($sql);
  }
}