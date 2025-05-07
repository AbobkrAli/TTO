<?php

namespace App\Models;

use App\Database;
use PDO;

class OptionalSubject
{
  private $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  /**
   * Get all optional subjects for a department
   */
  public function getByDepartment($departmentId)
  {
    $sql = "SELECT * FROM optional_subjects WHERE department_id = ? ORDER BY created_at DESC";
    $stmt = $this->db->query($sql, [$departmentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Create a new optional subject
   */
  public function create($subjectCode, $name, $departmentId)
  {
    $sql = "INSERT INTO optional_subjects (subject_code, name, department_id) VALUES (?, ?, ?)";
    $this->db->query($sql, [$subjectCode, $name, $departmentId]);
    return $this->db->getConnection()->lastInsertId();
  }

  /**
   * Delete an optional subject
   */
  public function delete($id)
  {
    $sql = "DELETE FROM optional_subjects WHERE id = ?";
    return $this->db->query($sql, [$id]);
  }

  /**
   * Check if subject code already exists in department
   */
  public function existsInDepartment($subjectCode, $departmentId)
  {
    $sql = "SELECT COUNT(*) as count FROM optional_subjects 
                WHERE subject_code = ? AND department_id = ?";
    $stmt = $this->db->query($sql, [$subjectCode, $departmentId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
  }

  /**
   * Get optional subject by ID
   */
  public function getById($id)
  {
    $sql = "SELECT * FROM optional_subjects WHERE id = ?";
    $stmt = $this->db->query($sql, [$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}