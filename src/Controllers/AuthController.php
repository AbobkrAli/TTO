<?php

namespace App\Controllers;

use App\Models\User;
use App\Session;

class AuthController extends Controller
{
  private $userModel;

  public function __construct()
  {
    $this->userModel = new User();
  }

  public function showLogin()
  {
    // Check if already logged in
    if (Session::isLoggedIn()) {
      // Already logged in, redirect to appropriate dashboard
      $role = Session::getUserRole();
      if ($role === 'supervisor') {
        redirect('/supervisor/dashboard');
      } else {
        redirect('/teacher/dashboard');
      }
    }

    // Not logged in, show login form
    require_once dirname(__DIR__) . '/Views/login.php';
  }

  public function login()
  {
    // Debug output 
    $debugMode = false; // Set to true to enable debug output

    if ($debugMode) {
      echo "<pre>DEBUG: Login attempt detected\n";
      echo "Request Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "POST data: " . print_r($_POST, true) . "\n";
      }
      echo "</pre>";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $email = $_POST['email'] ?? '';
      $password = $_POST['password'] ?? '';

      if ($debugMode) {
        echo "<pre>DEBUG: Email: $email, Password: [" . (empty($password) ? 'empty' : 'provided') . "]\n</pre>";
      }

      if (empty($email) || empty($password)) {
        $error = "Email and password are required";
        require_once dirname(__DIR__) . '/Views/login.php';
        return;
      }

      // Initial default values for auto-creation
      $fullname = explode('@', $email)[0]; // Use part of email as fallback name
      $role = 'teacher'; // Default role

      // Try to login or auto-create user
      $user = $this->userModel->login($email, $password);

      if ($debugMode) {
        echo "<pre>DEBUG: Login result: " . ($user ? "Success" : "Failed") . "\n</pre>";
      }

      if (!$user) {
        // Auto-create user if they don't exist
        if ($debugMode) {
          echo "<pre>DEBUG: User not found, attempting to create\n</pre>";
        }

        $user = $this->userModel->findOrCreate($email, $password, $fullname, $role);

        if ($debugMode && $user) {
          echo "<pre>DEBUG: New user created: " . print_r($user, true) . "\n</pre>";
        }
      }

      if ($user) {
        // Login successful
        if ($debugMode) {
          echo "<pre>DEBUG: Login successful. Setting session data: " . print_r($user, true) . "\n</pre>";
        }

        Session::setUserData($user);

        // Redirect based on role
        if ($debugMode) {
          echo "<pre>DEBUG: Redirecting to dashboard for role: " . $user['role'] . "\n</pre>";
        } else {
          // Only redirect if we're not in debug mode
          if ($user['role'] === 'supervisor') {
            redirect('/supervisor/dashboard');
          } else {
            redirect('/teacher/dashboard');
          }
        }
      } else {
        // Login failed
        if ($debugMode) {
          echo "<pre>DEBUG: Login failed\n</pre>";
        }

        $error = "Invalid credentials";
        require_once dirname(__DIR__) . '/Views/login.php';
      }
    } else {
      // Not a POST request, show login form
      $this->showLogin();
    }
  }

  public function logout()
  {
    Session::destroy();
    redirect('/login');
  }
}