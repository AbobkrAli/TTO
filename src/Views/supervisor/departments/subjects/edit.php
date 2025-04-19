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
                <?php foreach ($departments as $dept): ?>
                  <option value="<?php echo $dept['id']; ?>" <?php echo ($dept['id'] == $subject['department_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($dept['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3">
              <label for="teacher_id" class="form-label">Assign Teacher</label>
              <select class="form-select" id="teacher_id" name="teacher_id">
                <option value="">-- Select Teacher (Optional) --</option>
                <?php foreach ($teachers as $teacher): ?>
                  <option value="<?php echo $teacher['id']; ?>" <?php echo ($teacher['id'] == ($subject['teacher_id'] ?? null)) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($teacher['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">Assign a teacher to this subject or leave unassigned</div>
            </div>

            <div class="row mb-3">
              <div class="col-md-4">
                <label for="day" class="form-label">Day</label>
                <select class="form-select" id="day" name="day" required>
                  <?php foreach ($days as $dayOption): ?>
                    <option value="<?php echo $dayOption; ?>" <?php echo ($dayOption == $subject['day']) ? 'selected' : ''; ?>>
                      <?php echo $dayOption; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-4">
                <label for="hour" class="form-label">Starting Hour</label>
                <select class="form-select" id="hour" name="hour" required>
                  <?php foreach ($hours as $hourValue => $hourDisplay): ?>
                    <option value="<?php echo $hourValue; ?>" <?php echo ($hourValue == $subject['hour']) ? 'selected' : ''; ?>>
                      <?php echo $hourDisplay; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-4">
                <label for="end_time" class="form-label">End Time</label>
                <input type="time" class="form-control" id="end_time" name="end_time" required
                  value="<?php echo htmlspecialchars($subject['end_time']); ?>">
              </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
              <a href="/supervisor/departments/view/<?php echo $subject['department_id']; ?>"
                class="btn btn-outline-secondary me-md-2">
                <i class="bi bi-x-circle me-1"></i> Cancel
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-1"></i> Save Changes
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