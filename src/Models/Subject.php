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
              s.id, 
              s.department_id,
              s.subject_code, 
              s.name as subject_name, 
              s.day, 
              s.hour,
              s.is_office_hour,
              s.request_id,
              s.teacher_id,
              s.class_id,
              u.name as teacher_name,
              c.name as class_name
            FROM subjects s
            LEFT JOIN users u ON s.teacher_id = u.id
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE s.department_id = ? 
            ORDER BY s.day, s.hour";
    $stmt = $this->db->query($sql, [$departmentId]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group subjects by day and hour
    $groupedSubjects = [];
    foreach ($subjects as $subject) {
      $day = $subject['day'];
      $hour = $subject['hour'];
      if (!isset($groupedSubjects[$day])) {
        $groupedSubjects[$day] = [];
      }
      if (!isset($groupedSubjects[$day][$hour])) {
        $groupedSubjects[$day][$hour] = [];
      }
      $groupedSubjects[$day][$hour][] = $subject;
    }

    return $groupedSubjects;
  }

  /**
   * Get subject by ID
   */
  public function getById($id)
  {
    $sql = "SELECT s.*, 
                  d.name as department_name,
                  u.name as teacher_name
            FROM subjects s
            JOIN departments d ON s.department_id = d.id
            LEFT JOIN users u ON s.teacher_id = u.id
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
   * Check if a subject exists at the same time slot in the department
   */
  public function existsAtTimeSlot($departmentId, $day, $hour)
  {
    $sql = "SELECT COUNT(*) as count FROM subjects WHERE department_id = ? AND day = ? AND hour = ?";
    $stmt = $this->db->query($sql, [$departmentId, $day, $hour]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
  }

  /**
   * Create a new subject
   */
  public function create($subjectCode, $subjectName, $departmentId, $day, $hour, $classId, $isOfficeHour = false, $requestId = null, $teacherId = null)
  {
    try {
      // Check if subject with same code exists in department (skip for office hours)
      if (!$isOfficeHour && $this->existsInDepartment($subjectCode, $departmentId)) {
        throw new \Exception("A subject with code '{$subjectCode}' already exists in this department");
      }

      $sql = "INSERT INTO subjects (subject_code, name, department_id, day, hour, class_id, is_office_hour, request_id, teacher_id) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $result = $this->db->query($sql, [
        $subjectCode,
        $subjectName,
        $departmentId,
        $day,
        $hour,
        $classId,
        $isOfficeHour ? 1 : 0,
        $requestId,
        $teacherId
      ]);

      if ($result !== false) {
        return $this->db->getConnection()->lastInsertId();
      }
      return false;
    } catch (\PDOException $e) {
      error_log("Database error in Subject::create: " . $e->getMessage());
      throw new \Exception("Database error occurred while creating subject: " . $e->getMessage());
    }
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
   * Format time to 12-hour format
   */
  public static function formatTime($time)
  {
    return date("g:i A", strtotime($time));
  }
}