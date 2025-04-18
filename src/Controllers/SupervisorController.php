<?php

namespace App\Controllers;

use App\Models\User;
use App\Session;

class SupervisorController
{
  private $userModel;

  public function __construct()
  {
    $this->userModel = new User();

    // Check if user is logged in and is a supervisor
    if (!Session::isLoggedIn() || Session::getUserRole() !== 'supervisor') {
      header('Location: /login');
      exit;
    }
  }

  public function dashboard()
  {
    $users = $this->userModel->getAll();
    require_once dirname(__DIR__) . '/Views/supervisor/dashboard.php';
  }

  public function viewUser($id)
  {
    $user = $this->userModel->getById($id);
    if (!$user) {
      header('Location: /supervisor/dashboard');
      exit;
    }

    require_once dirname(__DIR__) . '/Views/supervisor/view_user.php';
  }

  public function editUser($id)
  {
    $user = $this->userModel->getById($id);
    if (!$user) {
      header('Location: /supervisor/dashboard');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $fullname = $_POST['fullname'] ?? '';
      $email = $_POST['email'] ?? '';
      $role = $_POST['role'] ?? '';
      $department = $_POST['department'] ?? null;

      if (empty($fullname) || empty($email) || empty($role)) {
        $error = "Name, email and role are required";
        require_once dirname(__DIR__) . '/Views/supervisor/edit_user.php';
        return;
      }

      $this->userModel->updateUser($id, $fullname, $email, $role, $department);
      header('Location: /supervisor/dashboard');
      exit;
    }

    require_once dirname(__DIR__) . '/Views/supervisor/edit_user.php';
  }
}