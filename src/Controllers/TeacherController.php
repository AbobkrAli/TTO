<?php

namespace App\Controllers;

use App\Models\User;
use App\Session;
use App\Models\Subject;

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

  /**
   * Get current logged in user
   */
  private function getCurrentUser()
  {
    $userId = Session::get('user_id');
    if (!$userId) {
      return null;
    }
    return $this->userModel->getById($userId);
  }

  /**
   * Get teacher's department schedule
   */
  public function getDepartmentSchedule()
  {
    $user = $this->getCurrentUser();
    if (!$user || !$user['department_id']) {
      return [];
    }

    $subjectModel = new Subject();
    return $subjectModel->getByDepartment($user['department_id']);
  }

  /**
   * Show teacher dashboard
   */
  public function dashboard()
  {
    $user = $this->getCurrentUser();
    if (!$user) {
      $this->redirect('/auth/login');
    }

    $schedule = $this->getDepartmentSchedule();

    // Group schedule by day for easier display
    $groupedSchedule = [];
    foreach ($schedule as $subject) {
      $day = $subject['day_of_week'];
      if (!isset($groupedSchedule[$day])) {
        $groupedSchedule[$day] = [];
      }
      $groupedSchedule[$day][] = $subject;
    }

    // Sort subjects by time within each day
    foreach ($groupedSchedule as &$daySubjects) {
      usort($daySubjects, function ($a, $b) {
        return strtotime($a['start_time']) - strtotime($b['start_time']);
      });
    }

    $this->view('teacher/dashboard', [
      'user' => $user,
      'schedule' => $groupedSchedule
    ]);
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