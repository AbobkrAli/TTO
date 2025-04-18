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
      $this->log("Password verified for user: {$user['fullname']} (ID: {$user['id']})");
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

      $sql = "INSERT INTO users (fullname, email, password, role, department) VALUES (?, ?, ?, ?, ?)";
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $this->db->query($sql, [$fullname, $email, $hashed_password, $role, $department]);

      // Get the created user
      $sql = "SELECT * FROM users WHERE email = ?";
      $stmt = $this->db->query($sql, [$email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      $this->log("Created new user with ID: {$user['id']}");
    } else {
      $this->log("User already exists with ID: {$user['id']}, name: {$user['fullname']}");
    }

    return $user;
  }

  public function getAll()
  {
    $sql = "SELECT * FROM users";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getById($id)
  {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $this->db->query($sql, [$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function updateUser($id, $fullname, $email, $role, $department = null)
  {
    $sql = "UPDATE users SET fullname = ?, email = ?, role = ?, department = ? WHERE id = ?";
    $this->db->query($sql, [$fullname, $email, $role, $department, $id]);
    return true;
  }
}