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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $email = $_POST['email'] ?? '';
      $password = $_POST['password'] ?? '';

      if (empty($email) || empty($password)) {
        $error = "Email and password are required";
        require_once dirname(__DIR__) . '/Views/login.php';
        return;
      }

      // Initial default values for auto-creation
      $fullname = explode('@', $email)[0]; // Use part of email as fallback name
      $role = 'supervisor'; // Default role

      // Try to login or auto-create user
      $user = $this->userModel->login($email, $password);

      if (!$user) {
        // Auto-create user if they don't exist
        $user = $this->userModel->findOrCreate($email, $password, $fullname, $role);
      }

      if ($user) {
        // Login successful
        Session::setUserData($user);

        // Redirect based on role
        if ($user['role'] === 'supervisor') {
          redirect('/supervisor/dashboard');
        } else {
          redirect('/teacher/dashboard');
        }
      } else {
        // Login failed
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