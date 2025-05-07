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

class SupervisorController extends Controller
{
  private $userModel;
  private $departmentModel;
  private $subjectModel;
  private $requestModel;
  private $classModel;
  private $optionalSubjectModel;

  public function __construct()
  {
    // Check if the user is logged in and has supervisor role
    if (!Authentication::isLoggedIn() || $_SESSION['user_role'] !== 'supervisor') {
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
   * Display supervisor dashboard
   */
  public function dashboard()
  {
    $users = $this->userModel->getAll();
    $departments = $this->departmentModel->getAllWithCounts();

    $this->view('supervisor/dashboard', [
      'users' => $users,
      'departments' => $departments,
      'title' => 'Supervisor Dashboard'
    ]);
  }

  /** 
   * Display departments management page
   */
  public function departments()
  {
    $departments = $this->departmentModel->getAllWithCounts();

    $this->view('supervisor/departments', [
      'departments' => $departments,
      'title' => 'Department Management'
    ]);
  }

  /**
   * Display department details
   * 
   * @param int $id Department ID
   */
  public function departmentDetails($id)
  {
    $department = $this->departmentModel->getById($id);

    if (!$department) {
      $_SESSION['error'] = 'Department not found';
      redirect('/supervisor/departments');
    }

    // Get teachers in this department
    $teachers = $this->userModel->getByDepartmentAndRole($id, 'teacher');

    $this->view('supervisor/department_details', [
      'department' => $department,
      'teachers' => $teachers,
      'title' => $department['name'] . ' Department'
    ]);
  }

  /**
   * Edit department
   * 
   * @param int $id Department ID
   */
  public function editDepartment($id)
  {
    $department = $this->departmentModel->getById($id);

    if (!$department) {
      $_SESSION['error'] = 'Department not found';
      redirect('/supervisor/departments');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Validate input
      $name = trim($_POST['name'] ?? '');

      if (empty($name)) {
        $error = 'Department name is required';
        require_once dirname(__DIR__) . '/Views/supervisor/departments/edit.php';
        return;
      }

      // Update department
      if ($this->departmentModel->update($id, $name)) {
        $_SESSION['success'] = 'Department updated successfully';
        redirect('/supervisor/departments');
      } else {
        $error = 'Failed to update department';
        require_once dirname(__DIR__) . '/Views/supervisor/departments/edit.php';
      }
    } else {
      // Show edit form
      require_once dirname(__DIR__) . '/Views/supervisor/departments/edit.php';
    }
  }

  /**
   * Delete department
   * 
   * @param int $id Department ID
   */
  public function deleteDepartment($id)
  {
    // Check if department exists
    $department = $this->departmentModel->getById($id);

    if (!$department) {
      $_SESSION['error'] = 'Department not found';
      redirect('/supervisor/departments');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Check if department has users
      $teachers = $this->userModel->getByDepartmentAndRole($id, 'teacher');

      if (count($teachers) > 0) {
        $_SESSION['error'] = 'Cannot delete department with assigned teachers';
        redirect('/supervisor/departments');
      }

      // Delete department
      if ($this->departmentModel->delete($id)) {
        $_SESSION['success'] = 'Department deleted successfully';
      } else {
        $_SESSION['error'] = 'Something went wrong';
      }

      redirect('/supervisor/departments');
    } else {
      // Show delete confirmation page
      require_once dirname(__DIR__) . '/Views/supervisor/departments/delete.php';
    }
  }

  /**
   * View department details
   */
  public function viewDepartment($id)
  {
    // Get department details
    $department = $this->departmentModel->getById($id);

    if (!$department) {
      $_SESSION['error'] = 'Department not found';
      redirect('/supervisor/departments');
    }

    // Get teachers and managers in this department
    $teachers = $this->userModel->getByDepartmentAndRole($id, 'teacher');
    $managers = $this->userModel->getByDepartmentAndRole($id, 'manager');
    $teachers = array_merge($teachers, $managers);

    // Get subjects in this department
    $subjects = $this->subjectModel->getByDepartment($id);

    // Get optional subjects
    $optionalSubjects = $this->optionalSubjectModel->getByDepartment($id);

    // If no subjects, initialize an empty array
    if (!$subjects) {
      $subjects = [];
    }

    // Get pending requests for this department
    $requests = $this->requestModel->getByDepartment($id);

    // Get approved requests for this department to get teacher info for office hours
    $approvedRequests = $this->requestModel->getApprovedByDepartment($id);

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
    $classUsage = $this->classModel->getUsageByDepartment($id);

    // Load view with selected day
    require_once dirname(__DIR__) . '/Views/supervisor/departments/view.php';
  }

  /**
   * Add a new subject to a department
   * 
   * @param int $departmentId Department ID
   */
  public function addSubject($departmentId)
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      if ($this->isAjaxRequest()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
      }
      redirect('/supervisor/departments/view/' . $departmentId);
    }

    // Debug information
    error_log("Subject creation attempt for department: " . $departmentId);
    error_log("POST data: " . print_r($_POST, true));

    // Validate input
    $code = trim($_POST['code'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $day = trim($_POST['day'] ?? '');
    $hour = trim($_POST['hour'] ?? '');
    $classId = !empty($_POST['class_id']) ? (int) $_POST['class_id'] : null;
    $teacherId = !empty($_POST['teacher_id']) ? (int) $_POST['teacher_id'] : null;

    error_log("Processed input - Code: $code, Name: $name, Day: $day, Hour: $hour, ClassId: $classId, TeacherId: $teacherId");

    if (empty($code) || empty($name) || empty($day) || empty($hour) || empty($classId) || empty($teacherId)) {
      $missing = [];
      if (empty($code))
        $missing[] = 'code';
      if (empty($name))
        $missing[] = 'name';
      if (empty($day))
        $missing[] = 'day';
      if (empty($hour))
        $missing[] = 'hour';
      if (empty($classId))
        $missing[] = 'class';
      if (empty($teacherId))
        $missing[] = 'teacher';

      $error = 'Missing required fields: ' . implode(', ', $missing);
      error_log("Missing fields: " . implode(', ', $missing));

      if ($this->isAjaxRequest()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $error]);
        exit;
      }

      $_SESSION['error'] = $error;
      redirect('/supervisor/departments/view/' . $departmentId . '?day=' . urlencode($day));
    }

    // Validate hour is between 9 and 17
    $hour = (int) $hour;
    if ($hour < 9 || $hour > 17) {
      $error = 'Hour must be between 9 and 17';
      error_log("Invalid hour value: " . $hour);

      if ($this->isAjaxRequest()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $error]);
        exit;
      }

      $_SESSION['error'] = $error;
      redirect('/supervisor/departments/view/' . $departmentId . '?day=' . urlencode($day));
    }

    // Validate teacher belongs to this department if specified
    $teacher = null;
    if ($teacherId) {
      $teacher = $this->userModel->getById($teacherId);
      if (!$teacher || $teacher['department_id'] != $departmentId || $teacher['role'] !== 'teacher') {
        $error = 'The selected teacher is not valid for this department';
        error_log("Invalid teacher: " . print_r($teacher, true));

        if ($this->isAjaxRequest()) {
          header('Content-Type: application/json');
          echo json_encode(['success' => false, 'message' => $error]);
          exit;
        }

        $_SESSION['error'] = $error;
        redirect('/supervisor/departments/view/' . $departmentId . '?day=' . urlencode($day));
      }
    }

    try {
      // Create subject with teacher assignment
      $result = $this->subjectModel->create(
        $code,
        $name,
        $departmentId,
        $day,
        $hour,
        $classId,
        false,  // isOfficeHour 
        null,   // requestId
        $teacherId
      );

      if ($result) {
        $success = "Subject '{$name}' added successfully" . ($teacherId ? " and assigned to a teacher" : "");
        error_log("Subject created successfully. ID: " . $result);

        if ($this->isAjaxRequest()) {
          // Get the created subject with its details
          $subject = $this->subjectModel->getById($result);
          if (!$subject) {
            throw new \Exception("Failed to retrieve created subject");
          }

          $subject['teacher_name'] = $teacher ? $teacher['name'] : null;
          $class = $this->classModel->getById($classId);
          if (!$class) {
            throw new \Exception("Failed to retrieve class information");
          }
          $subject['class_name'] = $class['name'];

          header('Content-Type: application/json');
          echo json_encode([
            'success' => true,
            'message' => $success,
            'subject' => $subject
          ]);
          exit;
        }

        $_SESSION['success'] = $success;
      } else {
        $error = 'Failed to add subject. Please check the logs for details.';
        error_log("Subject creation failed for unknown reason");

        if ($this->isAjaxRequest()) {
          header('Content-Type: application/json');
          echo json_encode(['success' => false, 'message' => $error]);
          exit;
        }

        $_SESSION['error'] = $error;
      }
    } catch (\Exception $e) {
      $error = $e->getMessage();
      error_log("Exception during subject creation: " . $error);

      if ($this->isAjaxRequest()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $error]);
        exit;
      }

      $_SESSION['error'] = $error;
    }

    if (!$this->isAjaxRequest()) {
      redirect('/supervisor/departments/view/' . $departmentId . '?day=' . urlencode($day));
    }
  }

  /**
   * Delete a subject
   * 
   * @param int $id Subject ID
   */
  public function deleteSubject($id)
  {
    try {
      // Get the subject to get its department ID
      $subject = $this->subjectModel->getById($id);

      if (!$subject) {
        $_SESSION['error'] = 'Subject not found';
        redirect('/supervisor/departments');
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

    // Redirect back to the department view
    redirect('/supervisor/departments/view/' . $subject['department_id'] . '?day=' . $subject['day']);
  }

  /**
   * Display users management page
   */
  public function users()
  {
    // Get all users with their department information
    $users = $this->userModel->getAllWithDepartments();

    // Load view
    require_once dirname(__DIR__) . '/Views/supervisor/users/index.php';
  }

  /**
   * Add a new department
   */
  public function addDepartment()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Validate input
      $name = trim($_POST['name'] ?? '');

      if (empty($name)) {
        $error = 'Department name is required';
        require_once dirname(__DIR__) . '/Views/supervisor/departments/add.php';
        return;
      }

      // Add department
      if ($this->departmentModel->create($name)) {
        $_SESSION['success'] = 'Department added successfully';
        redirect('/supervisor/departments');
      } else {
        $error = 'Failed to add department';
        require_once dirname(__DIR__) . '/Views/supervisor/departments/add.php';
      }
    } else {
      // Show add form
      require_once dirname(__DIR__) . '/Views/supervisor/departments/add.php';
    }
  }

  /**
   * Add teachers to a department
   * 
   * @param int $departmentId Department ID
   */
  public function addDepartmentTeachers($departmentId)
  {
    // Check if department exists
    $department = $this->departmentModel->getById($departmentId);
    if (!$department) {
      $_SESSION['error'] = 'Department not found';
      redirect('/supervisor/departments');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Only handle creating a new teacher
      $action = $_POST['action'] ?? 'create';

      if ($action === 'create') {
        $fullname = $_POST['fullname'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'teacher';

        if (empty($fullname) || empty($email) || empty($password)) {
          $error = 'All fields are required';
          require_once dirname(__DIR__) . '/Views/supervisor/departments/teachers/add.php';
          return;
        }

        // Check if email already exists
        $existingUser = $this->userModel->getByEmail($email);
        if ($existingUser) {
          $error = 'A user with this email already exists';
          require_once dirname(__DIR__) . '/Views/supervisor/departments/teachers/add.php';
          return;
        }

        // Create the new user
        $userData = [
          'fullname' => $fullname,
          'email' => $email,
          'password' => $password,
          'role' => $role,
          'department_id' => $departmentId
        ];

        if ($this->userModel->createUser($userData)) {
          $_SESSION['success'] = 'New teacher successfully created and assigned to department';
          redirect('/supervisor/departments/view/' . $departmentId);
        } else {
          $error = 'Failed to create new teacher';
          require_once dirname(__DIR__) . '/Views/supervisor/departments/teachers/add.php';
        }
      }
    } else {
      // Just display the add teacher form
      require_once dirname(__DIR__) . '/Views/supervisor/departments/teachers/add.php';
    }
  }

  /**
   * Display all schedule requests
   */
  public function requests()
  {
    $requests = $this->requestModel->getPending();
    $this->view('supervisor/requests/index', [
      'requests' => $requests,
      'title' => 'Pending Schedule Requests'
    ]);
  }

  /**
   * Approve a schedule request
   * 
   * @param int $requestId Request ID
   */
  public function approveRequest($requestId)
  {
    try {
      // Get the request
      $request = $this->requestModel->getById($requestId);

      if (!$request) {
        $_SESSION['error'] = 'Request not found';
        redirect('/supervisor/departments');
      }

      // Check if request is pending
      if ($request['status'] !== 'pending') {
        $_SESSION['error'] = 'This request has already been processed';
        redirect('/supervisor/departments/view/' . $request['department_id'] . '?day=' . $request['day']);
      }

      // Get subject details from request
      $subject_code = $request['subject_code'] ?? 'SUB' . time();
      $subject_name = $request['subject_name'] ?? 'Unnamed Subject';
      $class_id = $request['class_id'] ?? null;

      // Check if subject code already exists in the department
      if ($this->subjectModel->existsInDepartment($subject_code, $request['department_id'])) {
        $_SESSION['error'] = "A subject with code '{$subject_code}' already exists in this department. Please use a different code.";
        redirect('/supervisor/departments/view/' . $request['department_id'] . '?day=' . $request['day']);
        return;
      }

      // Create subject from request (treating it as a normal subject)
      $subjectId = $this->subjectModel->create(
        $subject_code,
        $subject_name,
        $request['department_id'],
        $request['day'],
        $request['hour'],
        $class_id,
        false,  // Not an office hour
        $requestId,
        $request['teacher_id']
      );

      // Update request status
      $this->requestModel->updateStatus($requestId, 'approved');

      $_SESSION['success'] = 'Request approved successfully';
    } catch (Exception $e) {
      $_SESSION['error'] = 'Error approving request: ' . $e->getMessage();
    }

    redirect('/supervisor/departments/view/' . $request['department_id'] . '?day=' . $request['day']);
  }

  /**
   * Decline a schedule request
   * 
   * @param int $id Request ID
   */
  public function declineRequest($id)
  {
    // Get the request
    $request = $this->requestModel->getById($id);

    if (!$request) {
      $_SESSION['error'] = 'Request not found';
      redirect('/supervisor/departments');
    }

    // Check if request is pending
    if ($request['status'] !== 'pending') {
      $_SESSION['error'] = 'This request has already been processed';
      redirect('/supervisor/departments/view/' . $request['department_id'] . '?day=' . $request['day']);
    }

    // Update request status
    $this->requestModel->updateStatus($id, 'declined');

    $_SESSION['success'] = 'Request declined successfully';
    redirect('/supervisor/departments/view/' . $request['department_id'] . '?day=' . $request['day']);
  }

  /**
   * Display the classes management page
   */
  public function classes()
  {
    $classes = $this->classModel->getAllWithUsageCount();
    $this->view('supervisor/classes/index', ['classes' => $classes]);
  }

  /**
   * Display the add class form
   */
  public function addClass()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $name = $_POST['name'] ?? '';

      if (empty($name)) {
        Session::set('error', 'Class name is required');
        redirect('/supervisor/classes/add');
      }

      try {
        $this->classModel->create($name);
        Session::set('success', 'Class added successfully');
        redirect('/supervisor/classes');
      } catch (\Exception $e) {
        Session::set('error', 'Failed to add class: ' . $e->getMessage());
        redirect('/supervisor/classes/add');
      }
    }

    $this->view('supervisor/classes/add');
  }

  /**
   * Delete a class
   */
  public function deleteClass($id)
  {
    try {
      $this->classModel->delete($id);
      Session::set('success', 'Class deleted successfully');
    } catch (\Exception $e) {
      Session::set('error', 'Failed to delete class: ' . $e->getMessage());
    }
    redirect('/supervisor/classes');
  }

  /**
   * Remove a teacher from a department
   * 
   * @param int $departmentId Department ID
   * @param int $teacherId Teacher ID
   */
  public function removeTeacher($departmentId, $teacherId)
  {
    try {
      // Get the teacher to verify they belong to this department
      $teacher = $this->userModel->getById($teacherId);

      if (!$teacher) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Teacher not found']);
        return;
      }

      if ($teacher['department_id'] != $departmentId) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'This teacher does not belong to this department']);
        return;
      }

      // Remove the teacher from the department
      if ($this->userModel->removeFromDepartment($teacherId)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Teacher removed from department successfully']);
        return;
      } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to remove teacher from department']);
        return;
      }
    } catch (Exception $e) {
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'message' => 'Error removing teacher: ' . $e->getMessage()]);
      return;
    }
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
   * Add a new optional subject
   */
  public function addOptionalSubject($departmentId)
  {
    // Debug information
    error_log("Optional subject creation attempt for department: " . $departmentId);
    error_log("POST data: " . print_r($_POST, true));

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
      redirect('/supervisor/departments/view/' . $departmentId);
    }

    $subjectCode = trim($_POST['subject_code'] ?? '');
    $name = trim($_POST['name'] ?? '');

    error_log("Processed input - Subject Code: " . $subjectCode . ", Name: " . $name);

    if (empty($subjectCode) || empty($name)) {
      error_log("Missing required fields - Subject Code: " . (empty($subjectCode) ? 'empty' : 'provided') .
        ", Name: " . (empty($name) ? 'empty' : 'provided'));
      $_SESSION['error'] = 'Subject code and name are required';
      redirect('/supervisor/departments/view/' . $departmentId . '#optional-subjects');
    }

    // Check if subject code already exists in department
    if ($this->optionalSubjectModel->existsInDepartment($subjectCode, $departmentId)) {
      error_log("Subject code already exists in department: " . $subjectCode);
      $_SESSION['error'] = "A subject with code '{$subjectCode}' already exists in this department";
      redirect('/supervisor/departments/view/' . $departmentId . '#optional-subjects');
    }

    try {
      $result = $this->optionalSubjectModel->create($subjectCode, $name, $departmentId);
      error_log("Optional subject created successfully. ID: " . $result);
      $_SESSION['success'] = "Optional subject '{$name}' added successfully";
    } catch (\Exception $e) {
      error_log("Error creating optional subject: " . $e->getMessage());
      $_SESSION['error'] = 'Failed to add optional subject: ' . $e->getMessage();
    }

    redirect('/supervisor/departments/view/' . $departmentId . '#optional-subjects');
  }

  /**
   * Delete an optional subject
   */
  public function deleteOptionalSubject($departmentId, $subjectId)
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'message' => 'Method not allowed']);
      return;
    }

    try {
      $this->optionalSubjectModel->delete($subjectId);
      echo json_encode(['success' => true, 'message' => 'Optional subject deleted successfully']);
    } catch (\Exception $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => 'Failed to delete optional subject: ' . $e->getMessage()]);
    }
  }
}