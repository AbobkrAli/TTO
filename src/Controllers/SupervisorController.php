<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Session;
use App\Authentication;
use App\Models\Request;

class SupervisorController extends Controller
{
  private $userModel;
  private $departmentModel;
  private $subjectModel;
  private $requestModel;
  private $classModel;

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

  // /**
  //  * View user details
  //  * 
  //  * @param int $id User ID
  //  */
  // public function viewUser($id)
  // {
  //   $user = $this->userModel->getById($id);
  //   if (!$user) {
  //     $_SESSION['error'] = 'User not found';
  //     redirect('/supervisor/users');
  //   }

  //   // Get department information if user is assigned to one
  //   $department = null;
  //   if (!empty($user['department_id'])) {
  //     $department = $this->departmentModel->getById($user['department_id']);
  //   }

  //   require_once dirname(__DIR__) . '/Views/supervisor/users/view.php';
  // }

  // /**
  //  * Edit user
  //  * 
  //  * @param int $id User ID
  //  */
  // public function editUser($id)
  // {
  //   $user = $this->userModel->getById($id);
  //   if (!$user) {
  //     $_SESSION['error'] = 'User not found';
  //     redirect('/supervisor/users');
  //   }

  //   $departments = $this->departmentModel->getAll();

  //   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //     $fullname = $_POST['fullname'] ?? '';
  //     $email = $_POST['email'] ?? '';
  //     $role = $_POST['role'] ?? '';
  //     $department_id = !empty($_POST['department_id']) ? $_POST['department_id'] : null;

  //     if (empty($fullname) || empty($email) || empty($role)) {
  //       $error = "Name, email and role are required";
  //       require_once dirname(__DIR__) . '/Views/supervisor/users/edit.php';
  //       return;
  //     }

  //     if ($this->userModel->updateUser($id, $fullname, $email, $role, $department_id)) {
  //       $_SESSION['success'] = 'User updated successfully';
  //       redirect('/supervisor/users');
  //     } else {
  //       $error = "Failed to update user";
  //       require_once dirname(__DIR__) . '/Views/supervisor/users/edit.php';
  //     }
  //   } else {
  //     require_once dirname(__DIR__) . '/Views/supervisor/users/edit.php';
  //   }
  // }

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
   * Create a new department
   */
  // public function createDepartment()
  // {
  //   // Redirect to addDepartment method for consistency
  //   $this->addDepartment();
  // }

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
   * 
   * @param int $id Department ID
   */
  public function viewDepartment($id)
  {
    // Get department details
    $department = $this->departmentModel->getById($id);

    if (!$department) {
      $_SESSION['error'] = 'Department not found';
      redirect('/supervisor/departments');
    }

    // Get teachers in this department
    $teachers = $this->userModel->getByDepartmentAndRole($id, 'teacher');

    // Get subjects in this department
    $subjects = $this->subjectModel->getByDepartment($id);

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

    if (empty($code) || empty($name) || empty($day) || empty($hour) || empty($classId)) {
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

      $_SESSION['error'] = 'Missing required fields: ' . implode(', ', $missing);
      error_log("Missing fields: " . implode(', ', $missing));
      redirect('/supervisor/departments/view/' . $departmentId . '?day=' . urlencode($day));
    }

    // Validate hour is between 9 and 17
    $hour = (int) $hour;
    if ($hour < 9 || $hour > 17) {
      $_SESSION['error'] = 'Hour must be between 9 and 17';
      error_log("Invalid hour value: " . $hour);
      redirect('/supervisor/departments/view/' . $departmentId . '?day=' . urlencode($day));
    }

    // Validate teacher belongs to this department if specified
    if ($teacherId) {
      $teacher = $this->userModel->getById($teacherId);
      if (!$teacher || $teacher['department_id'] != $departmentId || $teacher['role'] !== 'teacher') {
        $_SESSION['error'] = 'The selected teacher is not valid for this department';
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
        $_SESSION['success'] = "Subject '{$name}' added successfully" . ($teacherId ? " and assigned to a teacher" : "");
        error_log("Subject created successfully. ID: " . $result);
      } else {
        $_SESSION['error'] = 'Failed to add subject. Please check the logs for details.';
        error_log("Subject creation failed for unknown reason");
      }
    } catch (\Exception $e) {
      $_SESSION['error'] = $e->getMessage();
      error_log("Exception during subject creation: " . $e->getMessage());
    }

    redirect('/supervisor/departments/view/' . $departmentId . '?day=' . urlencode($day));
  }

  // /**
  //  * Edit subject
  //  * 
  //  * @param int $id Subject ID
  //  */
  // public function editSubject($id)
  // {
  //   $subject = $this->subjectModel->getById($id);
  //   if (!$subject) {
  //     $_SESSION['error'] = 'Subject not found';
  //     redirect('/supervisor/departments');
  //   }

  //   $departments = $this->departmentModel->getAll();

  //   // Get teachers for the current department
  //   $teachers = $this->userModel->getByDepartmentAndRole($subject['department_id'], 'teacher');

  //   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //     $subjectCode = trim($_POST['subject_code'] ?? '');
  //     $subjectName = trim($_POST['subject_name'] ?? '');
  //     $departmentId = (int) ($_POST['department_id'] ?? $subject['department_id']);
  //     $day = trim($_POST['day'] ?? '');
  //     $hour = (int) ($_POST['hour'] ?? 0);
  //     $teacherId = !empty($_POST['teacher_id']) ? (int) $_POST['teacher_id'] : null;

  //     if (empty($subjectCode) || empty($subjectName) || empty($departmentId) || empty($day) || $hour < 9 || $hour > 17) {
  //       $_SESSION['error'] = "All fields are required and hour must be between 9 and 17";
  //       redirect('/supervisor/subjects/edit/' . $id);
  //     }

  //     // Validate teacher belongs to this department if specified
  //     if ($teacherId) {
  //       $teacher = $this->userModel->getById($teacherId);
  //       if (!$teacher || $teacher['department_id'] != $departmentId || $teacher['role'] !== 'teacher') {
  //         $_SESSION['error'] = 'The selected teacher is not valid for this department';
  //         redirect('/supervisor/subjects/edit/' . $id);
  //       }
  //     }

  //     if ($this->subjectModel->update($id, $subjectCode, $subjectName, $departmentId, $day, $hour, $teacherId)) {
  //       $_SESSION['success'] = 'Subject updated successfully';
  //       redirect('/supervisor/departments/view/' . $departmentId . '?day=' . $day);
  //     } else {
  //       $_SESSION['error'] = 'Failed to update subject';
  //       redirect('/supervisor/subjects/edit/' . $id);
  //     }
  //   }

  //   // Get the day options
  //   $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];

  //   // Get the hour options
  //   $hours = [];
  //   for ($i = 9; $i <= 17; $i++) {
  //     $hours[$i] = sprintf('%02d:00', $i);
  //   }

  //   require_once dirname(__DIR__) . '/Views/supervisor/departments/subjects/edit.php';
  // }

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
}