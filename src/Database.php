<?php

namespace App;

use PDO;
use PDOException;

class Database
{
  private static $instance = null;
  private $connection;

  private function __construct()
  {
    $config = require_once dirname(__DIR__) . '/config/database.php';

    try {
      // Use socket connection for 'localhost'
      if ($config['host'] === 'localhost') {
        $socketFile = '/var/run/mysqld/mysqld.sock'; // This is the socket path we found
        $dsn = "mysql:unix_socket={$socketFile};dbname={$config['database']};charset={$config['charset']}";
      } else {
        $port = isset($config['port']) ? ";port={$config['port']}" : "";
        $dsn = "mysql:host={$config['host']}{$port};dbname={$config['database']};charset={$config['charset']}";
      }

      $this->connection = new PDO($dsn, $config['username'], $config['password'], $config['options']);
    } catch (PDOException $e) {
      die("Connection failed: " . $e->getMessage());
    }
  }

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function getConnection()
  {
    return $this->connection;
  }

  public function query($sql, $params = [])
  {
    $stmt = $this->connection->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }
}