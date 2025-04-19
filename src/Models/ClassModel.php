<?php

namespace App\Models;

use App\Database;

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
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Get class by ID
   */
  public function getById($id)
  {
    $sql = "SELECT * FROM classes WHERE id = ?";
    $stmt = $this->db->query($sql, [$id]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
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
    $sql = "DELETE FROM classes WHERE id = ?";
    return $this->db->query($sql, [$id]);
  }
}