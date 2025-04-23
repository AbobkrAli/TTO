<?php

// Debug environment variables
error_log("Database Configuration:");
error_log("MYSQLHOST: " . getenv('MYSQLHOST'));
error_log("MYSQLDATABASE: " . getenv('MYSQLDATABASE'));
error_log("MYSQLUSER: " . getenv('MYSQLUSER'));
error_log("MYSQLPASSWORD: " . getenv('MYSQLPASSWORD'));

return [
  'host' => getenv('MYSQLHOST') ?: 'mysql.railway.internal',
  'database' => getenv('MYSQLDATABASE') ?: 'railway',
  'username' => getenv('MYSQLUSER') ?: 'root',
  'password' => getenv('MYSQLPASSWORD') ?: 'yuHPIfVykOUfHMEUWQTIHToShgwmMSyn',
  'charset' => 'utf8mb4',
  'options' => [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_PERSISTENT => true,
  ]
];