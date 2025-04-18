<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Subject;
use App\Session;
use App\Authentication;

class SupervisorController extends Controller
{
  private $userModel;
  private $departmentModel;
  private $subjectModel;

  public function __construct()
  {
    // Check if the user is logged in and has supervisor role
    if (!Authentication::isLoggedIn() || $_SESSION['user_role'] !== 'supervisor') {
      redirect('/login');
    }

    $this->userModel = new User();
    $this->departmentModel = new Department();
    $this->subjectModel = new Subject();
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
   * View user details
   * 
   * @param int $id User ID
   */
  public function viewUser($id)
  {
    $user = $this->userModel->getById($id);
    if (!$user) {
      $_SESSION['error'] = 'User not found';
      redirect('/supervisor/users');
    }

    // Get department information if user is assigned to one
    $department = null;
    if (!empty($user['department_id'])) {
      $department = $this->departmentModel->getById($user['department_id']);
    }

    require_once dirname(__DIR__) . '/Views/supervisor/users/view.php';
  }

  /**
   * Edit user
   * 
   * @param int $id User ID
   */
  public function editUser($id)
  {
    $user = $this->userModel->getById($id);
    if (!$user) {
      $_SESSION['error'] = 'User not found';
      redirect('/supervisor/users');
    }

    $departments = $this->departmentModel->getAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $fullname = $_POST['fullname'] ?? '';
      $email = $_POST['email'] ?? '';
      $role = $_POST['role'] ?? '';
      $department_id = !empty($_POST['department_id']) ? $_POST['department_id'] : null;

      if (empty($fullname) || empty($email) || empty($role)) {
        $error = "Name, email and role are required";
        require_once dirname(__DIR__) . '/Views/supervisor/users/edit.php';
        return;
      }

      if ($this->userModel->updateUser($id, $fullname, $email, $role, $department_id)) {
        $_SESSION['success'] = 'User updated successfully';
        redirect('/supervisor/users');
      } else {
        $error = "Failed to update user";
        require_once dirname(__DIR__) . '/Views/supervisor/users/edit.php';
      }
    } else {
      require_once dirname(__DIR__) . '/Views/supervisor/users/edit.php';
    }
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
   * Create a new department
   */
  public function createDepartment()
  {
    // Redirect to addDepartment method for consistency
    $this->addDepartment();
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
    
    // Load view
    require_once dirname(__DIR__) . '/Views/supervisor/departments/view.php';
  }

  /* Subject Management Methods */
  
  // Add subject to department
  public function addSubject($departmentId)
  {
    $department = $this->departmentModel->getById($departmentId);
    if (!$department) {
      redirect('/supervisor/departments');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $subjectCode = $_POST['subject_code'] ?? '';
      $subjectName = $_POST['subject_name'] ?? '';
      $day = $_POST['day'] ?? '';
      $hour = $_POST['hour'] ?? '';

      if (empty($subjectCode) || empty($subjectName) || empty($day) || empty($hour)) {
        $error = "All fields are required";
        require_once dirname(__DIR__) . '/Views/supervisor/departments/subjects/add.php';
        return;
      }

      $this->subjectModel->create($subjectCode, $subjectName, $departmentId, $day, $hour);
      redirect('/supervisor/departments/view/' . $departmentId);
    }

    require_once dirname(__DIR__) . '/Views/supervisor/departments/subjects/add.php';
  }

  // Edit a subject
  public function editSubject($id)
  {
    $subject = $this->subjectModel->getById($id);
    if (!$subject) {
      redirect('/supervisor/departments');
    }

    $departments = $this->departmentModel->getAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $subjectCode = $_POST['subject_code'] ?? '';
      $subjectName = $_POST['subject_name'] ?? '';
      $departmentId = $_POST['department_id'] ?? '';
      $day = $_POST['day'] ?? '';
      $hour = $_POST['hour'] ?? '';

      if (empty($subjectCode) || empty($subjectName) || empty($departmentId) || empty($day) || empty($hour)) {
        $error = "All fields are required";
        require_once dirname(__DIR__) . '/Views/supervisor/departments/subjects/edit.php';
        return;
      }

      $this->subjectModel->update($id, $subjectCode, $subjectName, $departmentId, $day, $hour);
      redirect('/supervisor/departments/view/' . $departmentId);
    }

    require_once dirname(__DIR__) . '/Views/supervisor/departments/subjects/edit.php';
  }

  // Delete a subject
  public function deleteSubject($id)
  {
    $subject = $this->subjectModel->getById($id);
    if (!$subject) {
      redirect('/supervisor/departments');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $departmentId = $subject['department_id'];
      $this->subjectModel->delete($id);
      redirect('/supervisor/departments/view/' . $departmentId);
    }

    require_once dirname(__DIR__) . '/Views/supervisor/departments/subjects/delete.php';
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
}