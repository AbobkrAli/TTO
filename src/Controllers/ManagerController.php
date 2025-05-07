<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Session;
use App\Authentication;
use App\Models\Request;
use App\Models\OptionalSubject;

class ManagerController extends Controller
{
  private $userModel;
  private $departmentModel;
  private $subjectModel;
  private $requestModel;
  private $classModel;
  private $optionalSubjectModel;

  public function __construct()
  {
    // Check if the user is logged in and has manager role
    if (!Authentication::isLoggedIn() || $_SESSION['user_role'] !== 'manager') {
      redirect('/login');
    }

    $this->userModel = new User();
    $this->departmentModel = new Department();
    $this->subjectModel = new Subject();
    $this->requestModel = new Request();
    $this->classModel = new ClassModel();
    $this->optionalSubjectModel = new OptionalSubject();
  }

  /**
   * Display manager dashboard
   */
  public function dashboard()
  {
    // Get the current manager's department
    $user = $this->userModel->getById($_SESSION['user_id']);
    if (!$user || !$user['department_id']) {
      $_SESSION['error'] = 'You are not assigned to any department';
      redirect('/login');
    }

    // Get department details
    $department = $this->departmentModel->getById($user['department_id']);
    if (!$department) {
      $_SESSION['error'] = 'Department not found';
      redirect('/login');
    }

    // Get teachers in this department
    $teachers = $this->userModel->getByDepartmentAndRole($user['department_id'], 'teacher');
    $managers = $this->userModel->getByDepartmentAndRole($user['department_id'], 'manager');
    $teachers = array_merge($teachers, $managers);

    // Get subjects in this department
    $subjects = $this->subjectModel->getByDepartment($user['department_id']);

    // Get optional subjects
    $optionalSubjects = $this->optionalSubjectModel->getByDepartment($user['department_id']);

    // If no subjects, initialize an empty array
    if (!$subjects) {
      $subjects = [];
    }

    // Get pending requests for this department
    $requests = $this->requestModel->getByDepartment($user['department_id']);

    // Get approved requests for this department to get teacher info for office hours
    $approvedRequests = $this->requestModel->getApprovedByDepartment($user['department_id']);

    // Create a lookup map for approved requests
    $requestInfo = [];
    foreach ($approvedRequests as $req) {
      $requestInfo[$req['id']] = $req;
    }

    // Attach teacher info to office hour subjects
    foreach ($subjects as &$subject) {
      if (!empty($subject['request_id']) && isset($requestInfo[$subject['request_id']])) {
        $subject['teacher_name'] = $requestInfo[$subject['request_id']]['teacher_name'];
        $subject['teacher_id'] = $requestInfo[$subject['request_id']]['teacher_id'];
      }
    }

    // Get selected day from query parameter
    $selectedDay = $_GET['day'] ?? 'Monday';

    // Get all classes
    $classes = $this->classModel->getAll();

    // Get class usage by department
    $classUsage = $this->classModel->getUsageByDepartment($user['department_id']);

    // Load view with selected day
    require_once dirname(__DIR__) . '/Views/manager/dashboard.php';
  }
}