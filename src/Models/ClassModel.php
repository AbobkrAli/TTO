<?php

namespace App\Models;

use App\Database;
use PDO;

class ClassModel
{
  private $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  /**
   * Get all classes
   */
  public function getAll()
  {
    $sql = "SELECT * FROM classes ORDER BY name";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get class by ID
   */
  public function getById($id)
  {
    $sql = "SELECT * FROM classes WHERE id = ?";
    $stmt = $this->db->query($sql, [$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Create a new class
   */
  public function create($name)
  {
    try {
      $sql = "INSERT INTO classes (name) VALUES (?)";
      $result = $this->db->query($sql, [$name]);

      if ($result !== false) {
        return $this->db->getConnection()->lastInsertId();
      }
      return false;
    } catch (\PDOException $e) {
      error_log("Database error in ClassModel::create: " . $e->getMessage());
      throw new \Exception("Database error occurred while creating class: " . $e->getMessage());
    }
  }

  /**
   * Delete a class
   */
  public function delete($id)
  {
    try {
      // First, update any subjects that reference this class to set class_id to NULL
      $updateSql = "UPDATE subjects SET class_id = NULL WHERE class_id = ?";
      $this->db->query($updateSql, [$id]);

      // Then delete the class
      $deleteSql = "DELETE FROM classes WHERE id = ?";
      return $this->db->query($deleteSql, [$id]);
    } catch (\PDOException $e) {
      error_log("Database error in ClassModel::delete: " . $e->getMessage());
      throw new \Exception("Database error occurred while deleting class: " . $e->getMessage());
    }
  }
}