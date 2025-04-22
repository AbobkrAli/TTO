<?php

namespace App\Models;

use App\Database;
use PDO;

class Department
{
  private $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  /**
   * Get all departments
   */
  public function getAll()
  {
    $sql = "SELECT * FROM departments ORDER BY name";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get department by ID
   */
  public function getById($id)
  {
    $sql = "SELECT * FROM departments WHERE id = ?";
    $stmt = $this->db->query($sql, [$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Create a new department
   */
  public function create($name)
  {
    $sql = "INSERT INTO departments (name) VALUES (?)";
    $this->db->query($sql, [$name]);
    return $this->db->getConnection()->lastInsertId();
  }

  /**
   * Update an existing department
   */
  public function update($id, $name)
  {
    $sql = "UPDATE departments SET name = ? WHERE id = ?";
    return $this->db->query($sql, [$name, $id]);
  }

  /**
   * Delete a department
   */
  public function delete($id)
  {
    $sql = "DELETE FROM departments WHERE id = ?";
    return $this->db->query($sql, [$id]);
  }

  /**
   * Get teachers in department
   */
  public function getTeachers($departmentId)
  {
    $sql = "SELECT * FROM users WHERE department_id = ? AND role = 'teacher' ORDER BY fullname";
    $stmt = $this->db->query($sql, [$departmentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Count teachers in department
   */
  // public function countTeachers($departmentId)
  // {
  //   $sql = "SELECT COUNT(*) as count FROM users WHERE department_id = ? AND role = 'teacher'";
  //   $stmt = $this->db->query($sql, [$departmentId]);
  //   $result = $stmt->fetch(PDO::FETCH_ASSOC);
  //   return $result['count'];
  // }

  /**
   * Get departments with teacher counts
   */
  // public function getAllWithTeacherCount()
  // {
  //   $sql = "SELECT d.*, COUNT(u.id) as teacher_count 
  //           FROM departments d
  //           LEFT JOIN users u ON d.id = u.department_id AND u.role = 'teacher'
  //           GROUP BY d.id
  //           ORDER BY d.name";
  //   $stmt = $this->db->query($sql);
  //   return $stmt->fetchAll(PDO::FETCH_ASSOC);
  // }

  /**
   * Get all departments with counts for teachers and subjects
   * This is an alias for backward compatibility
   */
  // public function getAllWithCounts()
  // {
  //   return $this->getAllWithTeacherCount();
  // }
}