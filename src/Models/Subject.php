<?php

namespace App\Models;

use App\Database;
use PDO;

class Subject
{
  private $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  /**
   * Get all subjects
   */
  public function getAll()
  {
    $sql = "SELECT s.*, d.name as department_name 
            FROM subjects s
            JOIN departments d ON s.department_id = d.id
            ORDER BY s.name";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get subjects by department ID
   */
  public function getByDepartment($departmentId)
  {
    $sql = "SELECT 
              id, 
              department_id,
              subject_code, 
              name as subject_name, 
              day, 
              hour
            FROM subjects 
            WHERE department_id = ? 
            ORDER BY name";
    $stmt = $this->db->query($sql, [$departmentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get subject by ID
   */
  public function getById($id)
  {
    $sql = "SELECT s.*, d.name as department_name 
            FROM subjects s
            JOIN departments d ON s.department_id = d.id
            WHERE s.id = ?";
    $stmt = $this->db->query($sql, [$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Check if a subject with the same code exists in the department
   */
  public function existsInDepartment($code, $departmentId)
  {
    $sql = "SELECT COUNT(*) as count FROM subjects WHERE subject_code = ? AND department_id = ?";
    $stmt = $this->db->query($sql, [$code, $departmentId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
  }

  /**
   * Create a new subject
   */
  public function create($subjectCode, $subjectName, $departmentId, $day, $hour)
  {
    // Check if subject with same code exists in department
    if ($this->existsInDepartment($subjectCode, $departmentId)) {
      throw new \Exception("A subject with code '{$subjectCode}' already exists in this department");
    }

    $sql = "INSERT INTO subjects (subject_code, name, department_id, day, hour) 
            VALUES (?, ?, ?, ?, ?)";
    $this->db->query($sql, [$subjectCode, $subjectName, $departmentId, $day, $hour]);
    return $this->db->getConnection()->lastInsertId();
  }

  /**
   * Update an existing subject
   */
  public function update($id, $subjectCode, $subjectName, $departmentId, $day, $hour)
  {
    $sql = "UPDATE subjects 
            SET subject_code = ?, name = ?, department_id = ?, day = ?, hour = ? 
            WHERE id = ?";
    return $this->db->query($sql, [$subjectCode, $subjectName, $departmentId, $day, $hour, $id]);
  }

  /**
   * Delete a subject
   */
  public function delete($id)
  {
    $sql = "DELETE FROM subjects WHERE id = ?";
    return $this->db->query($sql, [$id]);
  }

  /**
   * Count subjects in department
   */
  public function countByDepartment($departmentId)
  {
    $sql = "SELECT COUNT(*) as count FROM subjects WHERE department_id = ?";
    $stmt = $this->db->query($sql, [$departmentId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
  }

  /**
   * Format time to 12-hour format
   */
  public static function formatTime($time)
  {
    return date("g:i A", strtotime($time));
  }
}