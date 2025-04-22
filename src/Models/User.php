<?php

namespace App\Models;

use App\Database;
use PDO;

class User
{
  private $db;
  private $debug = false;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  private function log($message)
  {
    if ($this->debug) {
      error_log("[USER_MODEL] " . $message);
    }
  }

  public function login($email, $password)
  {
    $this->log("Login attempt for email: $email");

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $this->db->query($sql, [$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      $this->log("No user found with email: $email");
      return false;
    }

    // Check password
    if (password_verify($password, $user['password'])) {
      $this->log("Password verified for user: {$user['name']} (ID: {$user['id']})");
      return $user;
    } else {
      $this->log("Password verification failed for email: $email");
      return false;
    }
  }

  public function findOrCreate($email, $password, $fullname, $role, $department = null)
  {
    $this->log("FindOrCreate attempt for email: $email");

    // Check if user exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $this->db->query($sql, [$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      // Create new user
      $this->log("Creating new user with email: $email, name: $fullname, role: $role");

      $sql = "INSERT INTO users (name, email, password, role, department_id) VALUES (?, ?, ?, ?, ?)";
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $this->db->query($sql, [$fullname, $email, $hashed_password, $role, $department]);

      // Get the created user
      $sql = "SELECT * FROM users WHERE email = ?";
      $stmt = $this->db->query($sql, [$email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      $this->log("Created new user with ID: {$user['id']}");
    } else {
      $this->log("User already exists with ID: {$user['id']}, name: {$user['name']}");
    }

    return $user;
  }

  /**
   * Get all users with their department information
   */
  public function getAllWithDepartments()
  {
    $sql = "SELECT u.*, d.name as department_name 
            FROM users u
            LEFT JOIN departments d ON u.department_id = d.id
            ORDER BY u.name";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get users by department and role
   * 
   * @param int $departmentId Department ID
   * @param string $role User role (teacher, supervisor, admin)
   * @return array Array of users
   */
  public function getByDepartmentAndRole($departmentId, $role)
  {
    $query = "SELECT * FROM users WHERE department_id = :department_id AND role = :role ORDER BY name";
    $stmt = $this->db->getConnection()->prepare($query);
    $stmt->bindParam(':department_id', $departmentId);
    $stmt->bindParam(':role', $role);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getById($id)
  {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $this->db->query($sql, [$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Get user by email
   *
   * @param string $email User email
   * @return array|bool User data or false if not found
   */
  public function getByEmail($email)
  {
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $this->db->query($sql, [$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Create a new user with provided data
   *
   * @param array $userData User data
   * @return bool Success or failure
   */
  public function createUser($userData)
  {
    $sql = "INSERT INTO users (name, email, password, role, department_id) 
            VALUES (?, ?, ?, ?, ?)";

    // Hash the password
    $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);

    try {
      $this->db->query($sql, [
        $userData['fullname'],
        $userData['email'],
        $hashedPassword,
        $userData['role'],
        $userData['department_id']
      ]);
      return true;
    } catch (\Exception $e) {
      $this->log("Error creating user: " . $e->getMessage());
      return false;
    }
  }
}