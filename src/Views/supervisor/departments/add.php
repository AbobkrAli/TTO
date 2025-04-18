<?php
$pageTitle = 'Add Department';
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
          <h5 class="mb-0"><i class="bi bi-building me-2"></i>Add New Department</h5>
        </div>
        <div class="card-body">
          <?php if (isset($error)): ?>
            <div class="alert alert-danger">
              <?php echo $error; ?>
            </div>
          <?php endif; ?>

          <form action="/supervisor/departments/add" method="post">
            <div class="mb-3">
              <label for="name" class="form-label">Department Name</label>
              <input type="text" class="form-control" id="name" name="name" required placeholder="Enter department name"
                value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
              <div class="form-text">Department name should be unique and descriptive.</div>
            </div>

            <div class="d-flex justify-content-between">
              <a href="/supervisor/departments" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Departments
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create Department
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
require dirname(dirname(dirname(__DIR__))) . '/Views/layout.php';
?>