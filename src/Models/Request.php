<?php

namespace App\Models;

use App\Database;
use PDO;

class Request
{
  private $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  /**
   * Get all requests
   */
  public function getAll()
  {
    $sql = "SELECT r.*, 
                  u.name as teacher_name, 
                  d.name as department_name 
            FROM requests r
            JOIN users u ON r.teacher_id = u.id
            JOIN departments d ON r.department_id = d.id
            ORDER BY r.created_at DESC";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get requests by department ID
   */
  public function getByDepartment($departmentId)
  {
    $sql = "SELECT r.*, 
                  u.name as teacher_name
            FROM requests r
            JOIN users u ON r.teacher_id = u.id
            WHERE r.department_id = ?
            ORDER BY r.created_at DESC";
    $stmt = $this->db->query($sql, [$departmentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get requests by teacher ID
   */
  public function getByTeacher($teacherId)
  {
    $sql = "SELECT r.*, 
                  d.name as department_name
            FROM requests r
            JOIN departments d ON r.department_id = d.id
            WHERE r.teacher_id = ?
            ORDER BY r.created_at DESC";
    $stmt = $this->db->query($sql, [$teacherId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get request by ID
   */
  public function getById($id)
  {
    $sql = "SELECT r.*, 
                  u.name as teacher_name, 
                  d.name as department_name
            FROM requests r
            JOIN users u ON r.teacher_id = u.id
            JOIN departments d ON r.department_id = d.id
            WHERE r.id = ?";
    $stmt = $this->db->query($sql, [$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Create a new request
   */
  public function create($teacherId, $departmentId, $day, $hour, $subjectCode = null, $subjectName = null)
  {
    $sql = "INSERT INTO requests (teacher_id, department_id, day, hour, subject_code, subject_name, status)
            VALUES (?, ?, ?, ?, ?, ?, 'pending')";
    $this->db->query($sql, [$teacherId, $departmentId, $day, $hour, $subjectCode, $subjectName]);
    return $this->db->getConnection()->lastInsertId();
  }

  /**
   * Update request status
   */
  public function updateStatus($id, $status)
  {
    $sql = "UPDATE requests SET status = ? WHERE id = ?";
    return $this->db->query($sql, [$status, $id]);
  }

  /**
   * Check if a slot is already requested by this teacher
   */
  public function isSlotRequested($teacherId, $departmentId, $day, $hour)
  {
    $sql = "SELECT COUNT(*) as count FROM requests 
            WHERE teacher_id = ? AND department_id = ? AND day = ? AND hour = ? AND status = 'pending'";
    $stmt = $this->db->query($sql, [$teacherId, $departmentId, $day, $hour]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
  }

  /**
   * Delete a request
   */
  public function delete($id)
  {
    $sql = "DELETE FROM requests WHERE id = ?";
    return $this->db->query($sql, [$id]);
  }

  /**
   * Count pending requests for a department
   */
  public function countPendingByDepartment($departmentId)
  {
    $sql = "SELECT COUNT(*) as count FROM requests WHERE department_id = ? AND status = 'pending'";
    $stmt = $this->db->query($sql, [$departmentId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
  }

  /**
   * Get approved requests by department ID
   */
  public function getApprovedByDepartment($departmentId)
  {
    $sql = "SELECT r.*, 
                  u.name as teacher_name,
                  CONCAT('REQ-', r.id) as request_reference
            FROM requests r
            JOIN users u ON r.teacher_id = u.id
            WHERE r.department_id = ? AND r.status = 'approved'
            ORDER BY r.day, r.hour";
    $stmt = $this->db->query($sql, [$departmentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}