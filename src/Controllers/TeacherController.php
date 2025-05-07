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
    $subjects = $subjectModel->getByDepartment($user['department_id']);

    // Flatten the nested array structure and filter for teacher's subjects
    $flattenedSubjects = [];
    foreach ($subjects as $day => $hours) {
      foreach ($hours as $hour => $daySubjects) {
        foreach ($daySubjects as $subject) {
          // Only include subjects assigned to this teacher
          if (isset($subject['teacher_id']) && $subject['teacher_id'] == $user['id']) {
            $flattenedSubjects[] = $subject;
          }
        }
      }
    }

    return $flattenedSubjects;
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

    // Get department information
    $departmentModel = new \App\Models\Department();
    $department = $departmentModel->getById($user['department_id']);

    $this->view('teacher/dashboard', [
      'user' => $user,
      'schedule' => $groupedSchedule,
      'department' => $department
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
    $allSubjects = $this->subjectModel->getByDepartment($user['department_id']);

    // Filter subjects to only include those assigned to this teacher
    $subjects = [];
    foreach ($allSubjects as $day => $daySubjects) {
      foreach ($daySubjects as $hour => $hourSubjects) {
        foreach ($hourSubjects as $subject) {
          if (isset($subject['teacher_id']) && $subject['teacher_id'] == $user['id']) {
            if (!isset($subjects[$day])) {
              $subjects[$day] = [];
            }
            if (!isset($subjects[$day][$hour])) {
              $subjects[$day][$hour] = [];
            }
            $subjects[$day][$hour][] = $subject;
          }
        }
      }
    }

    // Get pending requests made by this teacher
    $requests = $this->requestModel->getByTeacher($user['id']);

    // Get all classes
    $classes = $this->classModel->getAll();

    // Get optional subjects for the department
    $optionalSubjectModel = new \App\Models\OptionalSubject();
    $optionalSubjects = $optionalSubjectModel->getByDepartment($user['department_id']);

    // Load view
    $this->view('teacher/schedule', [
      'user' => $user,
      'subjects' => $subjects,
      'requests' => $requests,
      'selectedDay' => $selectedDay,
      'classes' => $classes,
      'optionalSubjects' => $optionalSubjects
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
    $subjectId = (int) ($_POST['subject_id'] ?? 0);
    $classId = !empty($_POST['class_id']) ? (int) $_POST['class_id'] : null;

    if (empty($day) || $hour < 9 || $hour > 17 || $subjectId <= 0) {
      $_SESSION['error'] = "Invalid day, hour, or subject selected.";
      redirect('/teacher/schedule?day=' . $day);
    }

    // Get the optional subject details
    $optionalSubjectModel = new \App\Models\OptionalSubject();
    $optionalSubject = $optionalSubjectModel->getById($subjectId);

    if (!$optionalSubject || $optionalSubject['department_id'] != $user['department_id']) {
      $_SESSION['error'] = "Invalid subject selected.";
      redirect('/teacher/schedule?day=' . $day);
    }

    try {
      // Create the subject directly
      $result = $this->subjectModel->create(
        $optionalSubject['subject_code'],
        $optionalSubject['name'],
        $user['department_id'],
        $day,
        $hour,
        $classId,
        false,  // isOfficeHour
        null,   // requestId
        $user['id']  // teacherId
      );

      if ($result) {
        if ($this->isAjaxRequest()) {
          header('Content-Type: application/json');
          echo json_encode([
            'success' => true,
            'message' => "Subject added successfully."
          ]);
          exit;
        }

        $_SESSION['success'] = "Subject added successfully.";
      } else {
        throw new \Exception("Failed to add subject.");
      }
    } catch (\Exception $e) {
      if ($this->isAjaxRequest()) {
        header('Content-Type: application/json');
        echo json_encode([
          'success' => false,
          'message' => $e->getMessage()
        ]);
        exit;
      }

      $_SESSION['error'] = $e->getMessage();
    }

    redirect('/teacher/schedule?day=' . $day);
  }

  /**
   * Check if the request is an AJAX request
   */
  private function isAjaxRequest()
  {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
  }

  /**
   * Cancel a schedule request
   */
  public function cancelRequest($id)
  {
    // Get the request
    $request = $this->requestModel->getById($id);

    if (!$request) {
      $_SESSION['error'] = 'Request not found';
      redirect('/teacher/schedule');
    }

    // Check if the request belongs to the current user
    if ($request['teacher_id'] != $_SESSION['user_id']) {
      $_SESSION['error'] = 'You can only cancel your own requests';
      redirect('/teacher/schedule');
    }

    // Check if the request is still pending
    if ($request['status'] !== 'pending') {
      $_SESSION['error'] = 'You can only cancel pending requests';
      redirect('/teacher/schedule');
    }

    // Delete the request
    if ($this->requestModel->delete($id)) {
      $_SESSION['success'] = 'Request cancelled successfully';
    } else {
      $_SESSION['error'] = 'Failed to cancel request';
    }

    redirect('/teacher/schedule');
  }

  /**
   * Delete a subject
   * 
   * @param int $id Subject ID
   */
  public function deleteSubject($id)
  {
    try {
      $user = $this->getCurrentUser();
      if (!$user) {
        $_SESSION['error'] = "You must be logged in to perform this action.";
        redirect('/login');
      }

      // Get the subject to verify ownership
      $subject = $this->subjectModel->getById($id);
      if (!$subject) {
        $_SESSION['error'] = 'Subject not found';
        redirect('/teacher/schedule');
      }

      // Verify that the subject belongs to this teacher
      if ($subject['teacher_id'] != $user['id']) {
        $_SESSION['error'] = 'You can only delete your own subjects';
        redirect('/teacher/schedule');
      }

      // Delete the subject
      if ($this->subjectModel->delete($id)) {
        $_SESSION['success'] = 'Subject deleted successfully';
      } else {
        $_SESSION['error'] = 'Failed to delete subject';
      }
    } catch (Exception $e) {
      $_SESSION['error'] = 'Error deleting subject: ' . $e->getMessage();
    }

    // Redirect back to the schedule view
    redirect('/teacher/schedule?day=' . $subject['day']);
  }
}