<?php
$pageTitle = 'Delete Department';
$activePage = 'departments';

ob_start();
?>

<style>
  .delete-card {
    border-radius: 10px;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .warning-icon {
    font-size: 3rem;
    color: #e74c3c;
  }
</style>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-8 col-lg-6 mx-auto">
      <div class="card delete-card">
        <div class="card-header bg-danger text-white py-3">
          <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Delete Department</h5>
        </div>
        <div class="card-body text-center py-5">
          <div class="mb-4">
            <i class="bi bi-exclamation-triangle-fill warning-icon"></i>
          </div>
          <h4>Are you sure you want to delete this department?</h4>
          <p class="text-muted mb-4">This will permanently delete the department
            <strong><?php echo htmlspecialchars($department['name']); ?></strong> and all associated subjects. This
            action cannot be undone.
          </p>

          <form action="/supervisor/departments/delete/<?php echo $department['id']; ?>" method="post">
            <div class="d-flex justify-content-center gap-3">
              <a href="/supervisor/departments" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancel
              </a>
              <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash"></i> Yes, Delete Department
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