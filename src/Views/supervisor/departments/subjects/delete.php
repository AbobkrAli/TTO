<?php
$pageTitle = 'Delete Subject';
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
          <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Delete Subject</h5>
        </div>
        <div class="card-body text-center py-5">
          <div class="mb-4">
            <i class="bi bi-exclamation-triangle-fill warning-icon"></i>
          </div>
          <h4>Are you sure you want to delete this subject?</h4>
          <p class="text-muted mb-2">You are about to delete the following subject:</p>
          <div class="mb-4">
            <strong class="d-block mb-1"><?php echo htmlspecialchars($subject['code']); ?> -
              <?php echo htmlspecialchars($subject['name']); ?></strong>
            <span class="text-muted">Department: <?php echo htmlspecialchars($subject['department_name']); ?></span>
          </div>
          <p class="text-danger mb-4">This action cannot be undone.</p>

          <form action="/supervisor/subjects/delete/<?php echo $subject['id']; ?>" method="post">
            <div class="d-flex justify-content-center gap-3">
              <a href="/supervisor/departments/view/<?php echo $subject['department_id']; ?>" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancel
              </a>
              <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash"></i> Yes, Delete Subject
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