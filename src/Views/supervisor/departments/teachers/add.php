<?php
$pageTitle = 'Add New Teacher to Department';
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
          <h4 class="mb-0"><i class="bi bi-person-plus me-2"></i>Add New Teacher to
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

  <!-- New User Form -->
  <div class="row">
    <div class="col-md-8 mx-auto">
      <div class="card form-card">
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
                  <option value="teacher">Teacher</option>
                  <option value="manager">Manager</option>
                </select>
                <div class="form-text">Select the role for this user. Managers have additional privileges in the
                  department.</div>
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