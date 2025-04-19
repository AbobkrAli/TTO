<?php
$pageTitle = 'Add Subject';
$activePage = 'departments';

// Get all places
$placeModel = new \App\Models\Place();
$places = $placeModel->getAll();

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
          <h5 class="mb-0"><i class="bi bi-book me-2"></i>Add New Subject to
            <?php echo htmlspecialchars($department['name']); ?>
          </h5>
        </div>
        <div class="card-body">
          <?php if (isset($error)): ?>
            <div class="alert alert-danger">
              <?php echo $error; ?>
            </div>
          <?php endif; ?>

          <form action="/supervisor/departments/<?php echo $department['id']; ?>/subjects/add" method="post">
            <div class="mb-3">
              <label for="subject_code" class="form-label">Subject Code</label>
              <input type="text" class="form-control" id="subject_code" name="subject_code" required
                placeholder="e.g., MATH101"
                value="<?php echo isset($_POST['subject_code']) ? htmlspecialchars($_POST['subject_code']) : ''; ?>">
              <div class="form-text">A unique identifier for the subject.</div>
            </div>

            <div class="mb-3">
              <label for="subject_name" class="form-label">Subject Name</label>
              <input type="text" class="form-control" id="subject_name" name="subject_name" required
                placeholder="e.g., Introduction to Mathematics"
                value="<?php echo isset($_POST['subject_name']) ? htmlspecialchars($_POST['subject_name']) : ''; ?>">
            </div>

            <div class="row mb-3">
              <div class="col-md-4">
                <label for="day" class="form-label">Day</label>
                <select class="form-select" id="day" name="day" required>
                  <option value="" disabled <?php echo !isset($_POST['day']) ? 'selected' : ''; ?>>Select a day</option>
                  <option value="Monday" <?php echo (isset($_POST['day']) && $_POST['day'] === 'Monday') ? 'selected' : ''; ?>>Monday</option>
                  <option value="Tuesday" <?php echo (isset($_POST['day']) && $_POST['day'] === 'Tuesday') ? 'selected' : ''; ?>>Tuesday</option>
                  <option value="Wednesday" <?php echo (isset($_POST['day']) && $_POST['day'] === 'Wednesday') ? 'selected' : ''; ?>>Wednesday</option>
                  <option value="Thursday" <?php echo (isset($_POST['day']) && $_POST['day'] === 'Thursday') ? 'selected' : ''; ?>>Thursday</option>
                  <option value="Friday" <?php echo (isset($_POST['day']) && $_POST['day'] === 'Friday') ? 'selected' : ''; ?>>Friday</option>
                  <option value="Saturday" <?php echo (isset($_POST['day']) && $_POST['day'] === 'Saturday') ? 'selected' : ''; ?>>Saturday</option>
                  <option value="Sunday" <?php echo (isset($_POST['day']) && $_POST['day'] === 'Sunday') ? 'selected' : ''; ?>>Sunday</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="hour" class="form-label">Start Time</label>
                <input type="time" class="form-control" id="hour" name="hour" required
                  value="<?php echo isset($_POST['hour']) ? htmlspecialchars($_POST['hour']) : ''; ?>">
              </div>
              <div class="col-md-4">
                <label for="end_time" class="form-label">End Time</label>
                <input type="time" class="form-control" id="end_time" name="end_time" required
                  value="<?php echo isset($_POST['end_time']) ? htmlspecialchars($_POST['end_time']) : ''; ?>">
              </div>
            </div>

            <div class="mb-3">
              <label for="place" class="form-label">Place/Room</label>
              <select class="form-select" id="place" name="place" required>
                <option value="" disabled <?php echo !isset($_POST['place']) ? 'selected' : ''; ?>>Select a place
                </option>
                <?php foreach ($places as $place): ?>
                  <option value="<?php echo htmlspecialchars($place['name']); ?>" <?php echo (isset($_POST['place']) && $_POST['place'] === $place['name']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($place['name']); ?>
                    (<?php echo ucfirst(str_replace('_', ' ', $place['type'])); ?>)
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">Select the location where the subject will be taught.</div>
            </div>

            <div class="d-flex justify-content-between">
              <a href="/supervisor/departments/view/<?php echo $department['id']; ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Subject
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