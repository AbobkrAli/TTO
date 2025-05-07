<?php

// Get database URL from environment variable
$databaseUrl = getenv('DATABASE_URL') ?: 'mysql://root:yuHPIfVykOUfHMEUWQTIHToShgwmMSyn@caboose.proxy.rlwy.net:12570/railway';

// Parse the URL
$url = parse_url($databaseUrl);

// Debug the parsed URL
error_log("Database URL: " . $databaseUrl);
error_log("Parsed URL: " . print_r($url, true));

return [
  'host' => $url['host'],
  'port' => $url['port'] ?? 3306,
  'database' => ltrim($url['path'], '/'),
  'username' => $url['user'],
  'password' => $url['pass'],
  'charset' => 'utf8mb4',
  'options' => [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_PERSISTENT => true,
  ]
];