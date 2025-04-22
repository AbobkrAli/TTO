<?php

namespace App\Controllers;

use App\Models\User;
use App\Session;
use App\Models\Subject;

class TeacherController extends Controller
{
  private $userModel;
  private $subjectModel;
  private $requestModel;
  private $classModel;

  public function __construct()
  {
    $this->userModel = new User();
    $this->subjectModel = new Subject();
    $this->requestModel = new \App\Models\Request();
    $this->classModel = new \App\Models\ClassModel();

    // Check if user is logged in and is a teacher
    if (!Session::isLoggedIn()) {
      redirect('/login');
    }

    // Check if user is a teacher
    $user = $this->getCurrentUser();
    if ($user && $user['role'] !== 'teacher') {
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
      $day = $subject['day'] ?? 'Unknown';
      if (!isset($groupedSchedule[$day])) {
        $groupedSchedule[$day] = [];
      }
      $groupedSchedule[$day][] = $subject;
    }

    // Sort subjects by hour within each day
    foreach ($groupedSchedule as &$daySubjects) {
      usort($daySubjects, function ($a, $b) {
        $hourA = isset($a['hour']) ? (int) $a['hour'] : 0;
        $hourB = isset($b['hour']) ? (int) $b['hour'] : 0;
        return $hourA - $hourB;
      });
    }

    $this->view('teacher/dashboard', [
      'user' => $user,
      'schedule' => $groupedSchedule
    ]);
  }

  /**
   * Get the department schedule with a selected day
   */
  public function departmentSchedule($selectedDay = null)
  {
    $user = $this->getCurrentUser();
    if (!$user || !$user['department_id']) {
      $_SESSION['error'] = "You are not assigned to any department.";
      redirect('/teacher/dashboard');
    }

    // Get selected day from query parameter or use default
    $selectedDay = $selectedDay ?? ($_GET['day'] ?? 'Monday');

    // Get all subjects in the department
    $subjects = $this->subjectModel->getByDepartment($user['department_id']);

    // Get pending requests made by this teacher
    $requests = $this->requestModel->getByTeacher($user['id']);

    // Get all classes
    $classes = $this->classModel->getAll();

    // Load view
    $this->view('teacher/schedule', [
      'user' => $user,
      'subjects' => $subjects,
      'requests' => $requests,
      'selectedDay' => $selectedDay,
      'classes' => $classes
    ]);
  }

  /**
   * Create a schedule change request
   */
  public function createRequest()
  {
    $user = $this->getCurrentUser();
    if (!$user || !$user['department_id']) {
      $_SESSION['error'] = "You are not assigned to any department.";
      redirect('/teacher/dashboard');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      redirect('/teacher/schedule');
    }

    // Get and validate input data
    $day = $_POST['day'] ?? '';
    $hour = (int) ($_POST['hour'] ?? 0);
    $subjectCode = $_POST['subject_code'] ?? '';
    $subjectName = $_POST['subject_name'] ?? '';
    $classId = !empty($_POST['class_id']) ? (int) $_POST['class_id'] : null;

    if (empty($day) || $hour < 9 || $hour > 17) {
      $_SESSION['error'] = "Invalid day or hour selected.";
      redirect('/teacher/schedule?day=' . $day);
    }

    // Check if slot is already requested
    if ($this->requestModel->isSlotRequested($user['id'], $user['department_id'], $day, $hour)) {
      $_SESSION['error'] = "You already have a pending request for this time slot.";
      redirect('/teacher/schedule?day=' . $day);
    }

    // Create the request
    $this->requestModel->create(
      $user['id'],
      $user['department_id'],
      $day,
      $hour,
      $subjectCode,
      $subjectName,
      $classId
    );

    $_SESSION['success'] = "Schedule request submitted successfully.";
    redirect('/teacher/schedule?day=' . $day);
  }

  /**
   * Cancel a schedule request
   */
  public function cancelRequest($id)
  {
    $user = $this->getCurrentUser();

    // Get the request
    $request = $this->requestModel->getById($id);

    // Check if request exists and belongs to this teacher
    if (!$request || $request['teacher_id'] != $user['id']) {
      $_SESSION['error'] = "Request not found or you don't have permission to cancel it.";
      redirect('/teacher/schedule');
    }

    // Only allow cancelling pending requests
    if ($request['status'] !== 'pending') {
      $_SESSION['error'] = "Only pending requests can be cancelled.";
      redirect('/teacher/schedule');
    }

    // Delete the request
    $this->requestModel->delete($id);

    $_SESSION['success'] = "Request cancelled successfully.";
    redirect('/teacher/schedule?day=' . $request['day']);
  }
}