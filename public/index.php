<?php

define('BASE_PATH', dirname(__DIR__));

// Autoload classes
require_once BASE_PATH . '/vendor/autoload.php';

// Load helpers
require_once BASE_PATH . '/src/helpers.php';

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
    redirect('/login');
  } else {
    // Redirect based on role
    $role = \App\Session::getUserRole();
    if ($role === 'supervisor') {
      redirect('/supervisor/departments');
    } else {
      redirect('/teacher/dashboard');
    }
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
      redirect('/supervisor/departments');
      break;

    // Users management routes
    case 'supervisor/users':
      $controller = new \App\Controllers\SupervisorController();
      $controller->users();
      break;

    case (preg_match('/^supervisor\/users\/view\/(\d+)$/', $path, $matches) ? true : false):
      $controller = new \App\Controllers\SupervisorController();
      $controller->viewUser($matches[1]);
      break;

    case (preg_match('/^supervisor\/users\/edit\/(\d+)$/', $path, $matches) ? true : false):
      $controller = new \App\Controllers\SupervisorController();
      $controller->editUser($matches[1]);
      break;

    case (preg_match('/^supervisor\/users\/delete\/(\d+)$/', $path, $matches) ? true : false):
      $controller = new \App\Controllers\SupervisorController();
      $controller->deleteUser($matches[1]);
      break;

    // Department routes
    case 'supervisor/departments':
      $controller = new \App\Controllers\SupervisorController();
      $controller->departments();
      break;

    case 'supervisor/departments/add':
      $controller = new \App\Controllers\SupervisorController();
      $controller->addDepartment();
      break;

    case (preg_match('/^supervisor\/departments\/view\/(\d+)$/', $path, $matches) ? true : false):
      $controller = new \App\Controllers\SupervisorController();
      $controller->viewDepartment($matches[1]);
      break;

    case (preg_match('/^supervisor\/departments\/edit\/(\d+)$/', $path, $matches) ? true : false):
      $controller = new \App\Controllers\SupervisorController();
      $controller->editDepartment($matches[1]);
      break;

    case (preg_match('/^supervisor\/departments\/delete\/(\d+)$/', $path, $matches) ? true : false):
      $controller = new \App\Controllers\SupervisorController();
      $controller->deleteDepartment($matches[1]);
      break;

    case (preg_match('/^supervisor\/departments\/(\d+)\/teachers\/add$/', $path, $matches) ? true : false):
      $controller = new \App\Controllers\SupervisorController();
      $controller->addDepartmentTeachers($matches[1]);
      break;

    // Subject routes
    case (preg_match('/^supervisor\/departments\/(\d+)\/subjects\/add$/', $path, $matches) ? true : false):
      $controller = new \App\Controllers\SupervisorController();
      $controller->addSubject($matches[1]);
      break;

    case (preg_match('/^supervisor\/subjects\/edit\/(\d+)$/', $path, $matches) ? true : false):
      $controller = new \App\Controllers\SupervisorController();
      $controller->editSubject($matches[1]);
      break;

    case (preg_match('/^supervisor\/subjects\/delete\/(\d+)$/', $path, $matches) ? true : false):
      $controller = new \App\Controllers\SupervisorController();
      $controller->deleteSubject($matches[1]);
      break;

    // Request routes
    case (preg_match('/^supervisor\/requests\/approve\/(\d+)$/', $path, $matches) ? true : false):
      $controller = new \App\Controllers\SupervisorController();
      $controller->approveRequest($matches[1]);
      break;

    case (preg_match('/^supervisor\/requests\/decline\/(\d+)$/', $path, $matches) ? true : false):
      $controller = new \App\Controllers\SupervisorController();
      $controller->declineRequest($matches[1]);
      break;

    // Teacher routes
    case 'teacher/dashboard':
      $controller = new \App\Controllers\TeacherController();
      $controller->dashboard();
      break;

    case 'teacher/schedule':
      $controller = new \App\Controllers\TeacherController();
      $controller->departmentSchedule();
      break;

    case 'teacher/requests/create':
      $controller = new \App\Controllers\TeacherController();
      $controller->createRequest();
      break;

    case (preg_match('/^teacher\/requests\/cancel\/(\d+)$/', $path, $matches) ? true : false):
      $controller = new \App\Controllers\TeacherController();
      $controller->cancelRequest($matches[1]);
      break;

    case 'teacher/profile':
      $controller = new \App\Controllers\TeacherController();
      $controller->profile();
      break;

    case 'teacher/profile/edit':
      $controller = new \App\Controllers\TeacherController();
      $controller->updateProfile();
      break;

    // Supervisor routes
    case 'supervisor/users/add':
      $controller = new \App\Controllers\SupervisorController();
      $controller->addUser();
      break;

    case 'supervisor/users/edit/{id}':
      $controller = new \App\Controllers\SupervisorController();
      $controller->editUser($id);
      break;

    case 'supervisor/users/delete/{id}':
      $controller = new \App\Controllers\SupervisorController();
      $controller->deleteUser($id);
      break;

    case 'supervisor/departments/add':
      $controller = new \App\Controllers\SupervisorController();
      $controller->addDepartment();
      break;

    case 'supervisor/departments/edit/{id}':
      $controller = new \App\Controllers\SupervisorController();
      $controller->editDepartment($id);
      break;

    case 'supervisor/departments/delete/{id}':
      $controller = new \App\Controllers\SupervisorController();
      $controller->deleteDepartment($id);
      break;

    case 'supervisor/departments/view/{id}':
      $controller = new \App\Controllers\SupervisorController();
      $controller->viewDepartment($id);
      break;

    case 'supervisor/departments/{id}/subjects/add':
      $controller = new \App\Controllers\SupervisorController();
      $controller->addSubject($id);
      break;

    case 'supervisor/departments/{id}/subjects/edit/{subjectId}':
      $controller = new \App\Controllers\SupervisorController();
      $controller->editSubject($subjectId);
      break;

    case 'supervisor/departments/{id}/subjects/delete/{subjectId}':
      $controller = new \App\Controllers\SupervisorController();
      $controller->deleteSubject($subjectId);
      break;

    case 'supervisor/requests':
      $controller = new \App\Controllers\SupervisorController();
      $controller->requests();
      break;

    case 'supervisor/requests/view/{id}':
      $controller = new \App\Controllers\SupervisorController();
      $controller->viewRequest($id);
      break;

    case 'supervisor/requests/respond/{id}':
      $controller = new \App\Controllers\SupervisorController();
      $controller->respondToRequest($id);
      break;

    case 'supervisor/classes':
      $controller = new \App\Controllers\SupervisorController();
      $controller->classes();
      break;

    case 'supervisor/classes/add':
      $controller = new \App\Controllers\SupervisorController();
      $controller->addClass();
      break;

    case 'supervisor/classes/delete/{id}':
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new \App\Controllers\SupervisorController();
        $controller->deleteClass($id);
        redirect('/supervisor/classes');
      } else {
        redirect('/supervisor/classes');
      }
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
