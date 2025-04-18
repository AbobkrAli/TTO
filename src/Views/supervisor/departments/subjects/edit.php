<?php
$pageTitle = 'Edit Subject';
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
    <div class="col-md-8 col-lg-6 mx-auto">
      <div class="card form-card">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Subject</h5>
        </div>
        <div class="card-body">
          <?php if (isset($error)): ?>
            <div class="alert alert-danger">
              <?php echo $error; ?>
            </div>
          <?php endif; ?>

          <form action="/supervisor/subjects/edit/<?php echo $subject['id']; ?>" method="post">
            <div class="mb-3">
              <label for="subject_code" class="form-label">Subject Code</label>
              <input type="text" class="form-control" id="subject_code" name="subject_code" required
                value="<?php echo htmlspecialchars($subject['subject_code']); ?>">
              <div class="form-text">A unique identifier for the subject.</div>
            </div>

            <div class="mb-3">
              <label for="subject_name" class="form-label">Subject Name</label>
              <input type="text" class="form-control" id="subject_name" name="subject_name" required
                value="<?php echo htmlspecialchars($subject['subject_name']); ?>">
            </div>

            <div class="mb-3">
              <label for="department_id" class="form-label">Department</label>
              <select class="form-select" id="department_id" name="department_id" required>
                <?php foreach ($departments as $department): ?>
                  <option value="<?php echo $department['id']; ?>" <?php echo $subject['department_id'] == $department['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($department['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="row mb-3">
              <div class="col-md-4">
                <label for="day" class="form-label">Day</label>
                <select class="form-select" id="day" name="day" required>
                  <option value="Monday" <?php echo $subject['day_of_week'] === 'Monday' ? 'selected' : ''; ?>>Monday
                  </option>
                  <option value="Tuesday" <?php echo $subject['day_of_week'] === 'Tuesday' ? 'selected' : ''; ?>>Tuesday
                  </option>
                  <option value="Wednesday" <?php echo $subject['day_of_week'] === 'Wednesday' ? 'selected' : ''; ?>>
                    Wednesday
                  </option>
                  <option value="Thursday" <?php echo $subject['day_of_week'] === 'Thursday' ? 'selected' : ''; ?>>
                    Thursday
                  </option>
                  <option value="Friday" <?php echo $subject['day_of_week'] === 'Friday' ? 'selected' : ''; ?>>Friday
                  </option>
                  <option value="Saturday" <?php echo $subject['day_of_week'] === 'Saturday' ? 'selected' : ''; ?>>
                    Saturday
                  </option>
                  <option value="Sunday" <?php echo $subject['day_of_week'] === 'Sunday' ? 'selected' : ''; ?>>Sunday
                  </option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="hour" class="form-label">Start Time</label>
                <input type="time" class="form-control" id="hour" name="hour" required
                  value="<?php echo htmlspecialchars($subject['start_time']); ?>">
              </div>
              <div class="col-md-4">
                <label for="end_time" class="form-label">End Time</label>
                <input type="time" class="form-control" id="end_time" name="end_time" required
                  value="<?php echo htmlspecialchars($subject['end_time']); ?>">
              </div>
            </div>

            <div class="d-flex justify-content-between">
              <a href="/supervisor/departments/view/<?php echo $subject['department_id']; ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Update Subject
              </button>
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