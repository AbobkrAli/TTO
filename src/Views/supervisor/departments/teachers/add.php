<?php
$pageTitle = 'Add Teachers to Department';
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

  .teacher-card {
    transition: all 0.2s;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 1rem;
  }

  .teacher-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }

  .teacher-card .form-check-input {
    width: 20px;
    height: 20px;
  }

  .card-header-custom {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    color: white;
    padding: 1.5rem;
  }
</style>

<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header-custom">
          <h4 class="mb-0"><i class="bi bi-person-plus me-2"></i>Add Teachers to
            <?php echo htmlspecialchars($department['name']); ?> Department
          </h4>
        </div>
      </div>
    </div>
  </div>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger">
      <?php echo $error; ?>
    </div>
  <?php endif; ?>

  <?php if (isset($success)): ?>
    <div class="alert alert-success">
      <?php echo $success; ?>
    </div>
  <?php endif; ?>

  <!-- Tab navigation -->
  <ul class="nav nav-tabs mb-4" id="userTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="existing-tab" data-bs-toggle="tab" data-bs-target="#existing" type="button"
        role="tab" aria-controls="existing" aria-selected="true">
        <i class="bi bi-people me-1"></i> Assign Existing Teachers
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="new-tab" data-bs-toggle="tab" data-bs-target="#new" type="button" role="tab"
        aria-controls="new" aria-selected="false">
        <i class="bi bi-person-plus me-1"></i> Create New Teacher
      </button>
    </li>
  </ul>

  <!-- Tab content -->
  <div class="tab-content" id="userTabsContent">
    <!-- Existing Users Tab -->
    <div class="tab-pane fade show active" id="existing" role="tabpanel" aria-labelledby="existing-tab">
      <?php if (empty($availableTeachers)): ?>
        <div class="alert alert-info">
          <i class="bi bi-info-circle me-2"></i>No teachers available to add to this department. All teachers may already
          be assigned.
        </div>
        <div class="text-center mt-4">
          <a href="/supervisor/departments/view/<?php echo $department['id']; ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Department
          </a>
        </div>
      <?php else: ?>
        <form action="/supervisor/departments/<?php echo $department['id']; ?>/teachers/add" method="post">
          <input type="hidden" name="action" value="assign">
          <div class="row">
            <?php foreach ($availableTeachers as $teacher): ?>
              <div class="col-md-4">
                <div class="card teacher-card">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="teacher_ids[]"
                          value="<?php echo $teacher['id']; ?>" id="teacher-<?php echo $teacher['id']; ?>">
                      </div>
                      <div class="ms-3">
                        <h5 class="mb-1"><?php echo htmlspecialchars($teacher['fullname']); ?></h5>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($teacher['email']); ?></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="d-flex justify-content-between mt-4">
            <a href="/supervisor/departments/view/<?php echo $department['id']; ?>" class="btn btn-secondary">
              <i class="bi bi-x-circle me-2"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-2"></i>Assign Selected Teachers
            </button>
          </div>
        </form>
      <?php endif; ?>
    </div>

    <!-- New User Tab -->
    <div class="tab-pane fade" id="new" role="tabpanel" aria-labelledby="new-tab">
      <div class="card">
        <div class="card-body">
          <form action="/supervisor/departments/<?php echo $department['id']; ?>/teachers/add" method="post">
            <input type="hidden" name="action" value="create">
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="fullname" name="fullname" required
                  placeholder="Enter full name">
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required
                  placeholder="Enter email address">
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required
                  placeholder="Enter password">
              </div>
              <div class="col-md-6">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role">
                  <option value="teacher" selected>Teacher</option>
                </select>
                <input type="hidden" name="department_id" value="<?php echo $department['id']; ?>">
              </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
              <a href="/supervisor/departments/view/<?php echo $department['id']; ?>" class="btn btn-secondary">
                <i class="bi bi-x-circle me-2"></i>Cancel
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i>Create New Teacher
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