<?php

class UserModel
{
  private $db;

  public function __construct()
  {
    $this->db = Database::getInstance()->getConnection();
  }

  /**
   * Get all users
   */
  public function getAll()
  {
    $query = "SELECT * FROM users ORDER BY last_name, first_name";
    $stmt = $this->db->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get user by ID
   */
  public function getById($id)
  {
    $query = "SELECT * FROM users WHERE id = :id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Get user by email
   */
  public function getByEmail($email)
  {
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Get users by department ID and role
   * 
   * @param int $departmentId Department ID
   * @param string $role User role (teacher, supervisor, admin)
   * @return array Array of users
   */
  public function getByDepartmentAndRole($departmentId, $role)
  {
    $query = "SELECT * FROM users WHERE department_id = :department_id AND role = :role ORDER BY last_name, first_name";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':department_id', $departmentId);
    $stmt->bindParam(':role', $role);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Create new user
   */
  public function create($data)
  {
    $query = "INSERT INTO users (first_name, last_name, email, password, role, department_id) 
                  VALUES (:first_name, :last_name, :email, :password, :role, :department_id)";

    $stmt = $this->db->prepare($query);

    // Hash password
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

    // Bind values
    $stmt->bindParam(':first_name', $data['first_name']);
    $stmt->bindParam(':last_name', $data['last_name']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':password', $data['password']);
    $stmt->bindParam(':role', $data['role']);
    $stmt->bindParam(':department_id', $data['department_id']);

    // Execute
    if ($stmt->execute()) {
      return $this->db->lastInsertId();
    } else {
      return false;
    }
  }

  /**
   * Update user
   */
  public function update($id, $data)
  {
    $query = "UPDATE users SET 
                  first_name = :first_name, 
                  last_name = :last_name, 
                  email = :email";

    // Only update password if it's provided
    if (!empty($data['password'])) {
      $query .= ", password = :password";
    }

    $query .= ", role = :role, 
                   department_id = :department_id 
                   WHERE id = :id";

    $stmt = $this->db->prepare($query);

    // Bind values
    $stmt->bindParam(':first_name', $data['first_name']);
    $stmt->bindParam(':last_name', $data['last_name']);
    $stmt->bindParam(':email', $data['email']);

    // Only bind password if it's provided
    if (!empty($data['password'])) {
      $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
      $stmt->bindParam(':password', $data['password']);
    }

    $stmt->bindParam(':role', $data['role']);
    $stmt->bindParam(':department_id', $data['department_id']);
    $stmt->bindParam(':id', $id);

    // Execute
    return $stmt->execute();
  }

  /**
   * Delete user
   */
  public function delete($id)
  {
    $query = "DELETE FROM users WHERE id = :id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':id', $id);

    // Execute
    return $stmt->execute();
  }

  /**
   * Get teachers not assigned to a specific department
   * 
   * @param int $departmentId Department ID
   * @return array Array of teachers not in the department
   */
  public function getTeachersNotInDepartment($departmentId)
  {
    $query = "SELECT * FROM users 
              WHERE role = 'teacher' 
              AND (department_id IS NULL OR department_id != :department_id)
              ORDER BY last_name, first_name";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':department_id', $departmentId);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Update user's department
   * 
   * @param int $userId User ID
   * @param int $departmentId Department ID
   * @return bool Success or failure
   */
  public function updateDepartment($userId, $departmentId)
  {
    $query = "UPDATE users SET department_id = :department_id WHERE id = :id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':department_id', $departmentId);
    $stmt->bindParam(':id', $userId);

    return $stmt->execute();
  }
}