<?php

class DepartmentModel
{
  private $db;

  public function __construct()
  {
    $this->db = Database::getInstance()->getConnection();
  }

  /**
   * Get all departments
   *
   * @return array
   */
  public function getAll()
  {
    $query = "SELECT * FROM departments ORDER BY name";
    $stmt = $this->db->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get department by ID
   *
   * @param int $id
   * @return array|false
   */
  public function getById($id)
  {
    $query = "SELECT * FROM departments WHERE id = :id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Create a new department
   *
   * @param array $data
   * @return int The ID of the new department
   */
  public function create($data)
  {
    $query = "INSERT INTO departments (name, description) VALUES (:name, :description)";
    $stmt = $this->db->prepare($query);

    // Bind values
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':description', $data['description']);

    // Execute
    if ($stmt->execute()) {
      return $this->db->lastInsertId();
    } else {
      return false;
    }
  }

  /**
   * Update a department
   *
   * @param int $id
   * @param array $data
   * @return bool
   */
  public function update($id, $data)
  {
    $query = "UPDATE departments SET name = :name, description = :description WHERE id = :id";
    $stmt = $this->db->prepare($query);

    // Bind values
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':description', $data['description']);
    $stmt->bindParam(':id', $id);

    // Execute
    return $stmt->execute();
  }

  /**
   * Delete a department
   *
   * @param int $id
   * @return bool
   */
  public function delete($id)
  {
    $query = "DELETE FROM departments WHERE id = :id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':id', $id);

    // Execute
    return $stmt->execute();
  }

  /**
   * Get all departments with teacher counts
   *
   * @return array
   */
  public function getAllWithCounts()
  {
    $query = "SELECT d.*, COUNT(u.id) as teacher_count 
              FROM departments d
              LEFT JOIN users u ON d.id = u.department_id AND u.role = 'teacher'
              GROUP BY d.id
              ORDER BY d.name";
    $stmt = $this->db->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Check if department has teachers
   *
   * @param int $id
   * @return bool
   */
  public function hasTeachers($id)
  {
    $query = "SELECT COUNT(*) as count FROM users WHERE department_id = :id AND role = 'teacher'";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
  }
}