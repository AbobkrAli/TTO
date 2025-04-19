<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/Database/connection.php';

// Get department ID from the request
$department_id = $_GET['department_id'] ?? null;

if (!$department_id) {
  die("Department ID is required");
}

// Fetch teachers for this department
$teacherQuery = "SELECT id, name FROM users WHERE department_id = ? AND role = 'teacher'";
$stmt = $conn->prepare($teacherQuery);
$stmt->execute([$department_id]);
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $subject_code = $_POST['subject_code'];
  $name = $_POST['name'];
  $day = $_POST['day'];
  $hour = $_POST['hour'];
  $teacher_id = $_POST['teacher_id'];

  try {
    $query = "INSERT INTO subjects (subject_code, name, department_id, day, hour, teacher_id) 
                  VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$subject_code, $name, $department_id, $day, $hour, $teacher_id]);

    header("Location: /supervisor/departments/view.php?id=" . $department_id);
    exit;
  } catch (PDOException $e) {
    $error = "Error adding subject: " . $e->getMessage();
  }
}

$pageTitle = 'Add Subject';
$activePage = 'departments';

ob_start();
?>

<style>
  .form-card {
    border-radius: 10px;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
  }

  .form-card:hover {
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
  }
</style>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-8 mx-auto">
      <div class="card form-card mb-4">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add New Subject</h5>
        </div>
        <div class="card-body">
          <?php if (isset($error)): ?>
            <div class="alert alert-danger">
              <?php echo $error; ?>
            </div>
          <?php endif; ?>

          <form method="POST">
            <div class="mb-3">
              <label for="subject_code" class="form-label">Subject Code</label>
              <input type="text" class="form-control" id="subject_code" name="subject_code" required>
              <div class="form-text">A unique identifier for the subject.</div>
            </div>

            <div class="mb-3">
              <label for="name" class="form-label">Subject Name</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <!-- Add teacher selection dropdown -->
            <div class="mb-3">
              <label for="teacher_id" class="form-label">Assign Teacher</label>
              <select class="form-select" id="teacher_id" name="teacher_id" required>
                <option value="">Select Teacher</option>
                <?php foreach ($teachers as $teacher): ?>
                  <option value="<?php echo $teacher['id']; ?>">
                    <?php echo htmlspecialchars($teacher['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3">
              <label for="day" class="form-label">Day</label>
              <select class="form-select" id="day" name="day" required>
                <option value="Sunday">Sunday</option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="hour" class="form-label">Hour</label>
              <input type="number" class="form-control" id="hour" name="hour" min="9" max="17" required>
              <div class="form-text">Class hour (9-17)</div>
            </div>

            <div class="text-end">
              <a href="/supervisor/departments/view.php?id=<?php echo $department_id; ?>"
                class="btn btn-secondary me-2">Cancel</a>
              <button type="submit" class="btn btn-primary">Add Subject</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require dirname(dirname(dirname(dirname(__DIR__)))) . '/Views/layout.php';
?>