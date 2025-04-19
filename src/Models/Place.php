<?php

namespace App\Models;

use App\Database;
use PDO;

class Place
{
  private $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  /**
   * Get all places
   */
  public function getAll()
  {
    $sql = "SELECT * FROM places ORDER BY type, name";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get place by ID
   */
  public function getById($id)
  {
    $sql = "SELECT * FROM places WHERE id = ?";
    $stmt = $this->db->query($sql, [$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Create a new place
   */
  public function create($name, $type = 'classroom', $capacity = null)
  {
    $sql = "INSERT INTO places (name, type, capacity) VALUES (?, ?, ?)";
    $result = $this->db->query($sql, [$name, $type, $capacity]);

    if ($result !== false) {
      return $this->db->getConnection()->lastInsertId();
    }
    return false;
  }

  /**
   * Update a place
   */
  public function update($id, $name, $type, $capacity)
  {
    $sql = "UPDATE places SET name = ?, type = ?, capacity = ? WHERE id = ?";
    return $this->db->query($sql, [$name, $type, $capacity, $id]);
  }

  /**
   * Delete a place
   */
  public function delete($id)
  {
    $sql = "DELETE FROM places WHERE id = ?";
    return $this->db->query($sql, [$id]);
  }
}