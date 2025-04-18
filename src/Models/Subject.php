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
    $sql = "SELECT * FROM subjects WHERE department_id = ? ORDER BY name";
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
   * Create a new subject
   */
  public function create($subjectCode, $subjectName, $departmentId, $day, $hour, $endTime = null)
  {
    // If end_time is not provided, add 1 hour to the start time
    if ($endTime === null) {
      $startTime = strtotime($hour);
      $endTime = date('H:i:s', strtotime('+1 hour', $startTime));
    }

    $sql = "INSERT INTO subjects (code, name, department_id, day_of_week, start_time, end_time) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $this->db->query($sql, [$subjectCode, $subjectName, $departmentId, $day, $hour, $endTime]);
    return $this->db->getConnection()->lastInsertId();
  }

  /**
   * Update an existing subject
   */
  public function update($id, $subjectCode, $subjectName, $departmentId, $day, $hour, $endTime = null)
  {
    // If end_time is not provided, add 1 hour to the start time
    if ($endTime === null) {
      $startTime = strtotime($hour);
      $endTime = date('H:i:s', strtotime('+1 hour', $startTime));
    }

    $sql = "UPDATE subjects 
            SET code = ?, name = ?, department_id = ?, day_of_week = ?, start_time = ?, end_time = ? 
            WHERE id = ?";
    return $this->db->query($sql, [$subjectCode, $subjectName, $departmentId, $day, $hour, $endTime, $id]);
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