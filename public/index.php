<?php

define('BASE_PATH', dirname(__DIR__));

// Autoload classes
require_once BASE_PATH . '/vendor/autoload.php';

// Start session
\App\Session::start();

// Get the request URI
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');

// Simple router
if (empty($path) || $path === 'index.php') {
  // Redirect to login page if not logged in
  if (!\App\Session::isLoggedIn()) {
    header('Location: /login');
    exit;
  } else {
    // Redirect based on role
    $role = \App\Session::getUserRole();
    if ($role === 'supervisor') {
      header('Location: /supervisor/dashboard');
    } else {
      header('Location: /teacher/dashboard');
    }
    exit;
  }
}

// Routes
try {
  switch ($path) {
    // Auth routes
    case 'login':
      $controller = new \App\Controllers\AuthController();
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->login();
      } else {
        $controller->showLogin();
      }
      break;

    case 'logout':
      $controller = new \App\Controllers\AuthController();
      $controller->logout();
      break;

    case 'force_logout.php':
      // Just let PHP execute this file directly
      require_once BASE_PATH . '/force_logout.php';
      break;

    case 'debug_login.php':
      // Just let PHP execute this file directly
      require_once BASE_PATH . '/debug_login.php';
      break;

    // Supervisor routes
    case 'supervisor/dashboard':
      $controller = new \App\Controllers\SupervisorController();
      $controller->dashboard();
      break;

    case (preg_match('/^supervisor\/users\/view\/(\d+)$/', $path, $matches) ? true : false):
      $controller = new \App\Controllers\SupervisorController();
      $controller->viewUser($matches[1]);
      break;

    case (preg_match('/^supervisor\/users\/edit\/(\d+)$/', $path, $matches) ? true : false):
      $controller = new \App\Controllers\SupervisorController();
      $controller->editUser($matches[1]);
      break;

    // Teacher routes
    case 'teacher/dashboard':
      $controller = new \App\Controllers\TeacherController();
      $controller->dashboard();
      break;

    case 'teacher/profile':
      $controller = new \App\Controllers\TeacherController();
      $controller->profile();
      break;

    case 'teacher/profile/edit':
      $controller = new \App\Controllers\TeacherController();
      $controller->updateProfile();
      break;

    default:
      // 404 Page Not Found
      header("HTTP/1.0 404 Not Found");
      echo "<h1>404 - Page Not Found</h1>";
      echo "<p>The page you requested could not be found.</p>";
      echo "<p><a href='/'>Go to Home</a></p>";
      break;
  }
} catch (Exception $e) {
  // Error handler
  header("HTTP/1.0 500 Internal Server Error");
  echo "<h1>500 - Internal Server Error</h1>";
  echo "<p>An error occurred while processing your request.</p>";

  // Only show detailed error in development
  if (true) { // Change this for production
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
  }
}
