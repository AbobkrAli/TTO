<?php require_once dirname(dirname(dirname(__DIR__))) . '/Views/includes/header.php'; ?>

<div class="container mt-4">
  <div class="card shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <h4 class="mb-0">Edit Department</h4>
      <a href="/supervisor/departments" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Departments
      </a>
    </div>
    <div class="card-body">
      <?php if (isset($error)): ?>
        <div class="alert alert-danger">
          <?php echo $error; ?>
        </div>
      <?php endif; ?>

      <form action="/supervisor/departments/edit/<?php echo $department['id']; ?>" method="POST">
        <div class="mb-3">
          <label for="name" class="form-label">Department Name</label>
          <input type="text" class="form-control" id="name" name="name" value="<?php echo $department['name']; ?>"
            required>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Update Department
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require dirname(dirname(dirname(__DIR__))) . '/Views/layout.php';
?>