<?php

namespace App\Controllers;

use App\Models\User;
use App\Session;

class TeacherController extends Controller
{
  private $userModel;

  public function __construct()
  {
    $this->userModel = new User();

    // Check if user is logged in and is a teacher
    if (!Session::isLoggedIn()) {
      redirect('/login');
    }
  }

  public function dashboard()
  {
    $userId = Session::get('user_id');
    $user = $this->userModel->getById($userId);

    require_once dirname(__DIR__) . '/Views/teacher/dashboard.php';
  }

  public function profile()
  {
    $userId = Session::get('user_id');
    $user = $this->userModel->getById($userId);

    require_once dirname(__DIR__) . '/Views/teacher/profile.php';
  }

  public function updateProfile()
  {
    $userId = Session::get('user_id');
    $user = $this->userModel->getById($userId);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $fullname = $_POST['fullname'] ?? '';
      $email = $_POST['email'] ?? '';
      $department = $_POST['department'] ?? null;

      if (empty($fullname) || empty($email)) {
        $error = "Name and email are required";
        require_once dirname(__DIR__) . '/Views/teacher/edit_profile.php';
        return;
      }

      $this->userModel->updateUser($userId, $fullname, $email, 'teacher', $department);

      // Update session data
      $updatedUser = $this->userModel->getById($userId);
      Session::setUserData($updatedUser);

      redirect('/teacher/profile');
    }

    require_once dirname(__DIR__) . '/Views/teacher/edit_profile.php';
  }
}