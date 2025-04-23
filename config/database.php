<?php

return [
  'host' => getenv('RAILWAY_MYSQL_HOST') ?: 'localhost',
  'database' => getenv('RAILWAY_MYSQL_DATABASE') ?: 'railway',
  'username' => getenv('RAILWAY_MYSQL_USERNAME') ?: 'root',
  'password' => getenv('RAILWAY_MYSQL_PASSWORD') ?: '',
  'charset' => 'utf8mb4',
  'options' => [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_PERSISTENT => true,
  ]
];