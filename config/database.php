<?php

return [
  'host' => 'interchange.proxy.rlwy.net',
  'port' => 23554,
  'database' => 'railway',
  'username' => 'root',
  'password' => 'auqVyHkslaLKIohEAyqHpQWgRAknUVno',
  'charset' => 'utf8mb4',
  'options' => [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_PERSISTENT => true,
  ]
];