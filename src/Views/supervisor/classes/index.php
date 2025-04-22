<?php require_once __DIR__ . '/../../layout.php'; ?>

<div class="container" style="width: 80%; margin-right: 100px;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-building"></i> Classes Management</h2>
    <a href="/supervisor/classes/add" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Add New Class
    </a>
  </div>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php
      echo $_SESSION['success'];
      unset($_SESSION['success']);
      ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?php
      echo $_SESSION['error'];
      unset($_SESSION['error']);
      ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Class Name</th>
              <th>Usage Count</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($classes)): ?>
              <tr>
                <td colspan="3" class="text-center py-4">
                  <div class="text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2 mb-0">No classes found</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($classes as $class): ?>
                <tr>
                  <td class="fw-bold"><?php echo htmlspecialchars($class['id']); ?></td>
                  <td><?php echo htmlspecialchars($class['name']); ?></td>
                  <td>
                    <span class="badge bg-info">
                      <?php echo $class['usage_count']; ?> subjects
                    </span>
                  </td>
                  <td class="text-end">
                    <form action="/supervisor/classes/delete/<?php echo $class['id']; ?>" method="POST" class="d-inline">
                      <input type="hidden" name="csrf_token" value="<?php echo \App\Session::get('csrf_token'); ?>">
                      <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure you want to delete this class? This will remove it from any subjects it is assigned to.')">
                        <i class="bi bi-trash"></i> Delete
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<style>
  .table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
  }

  .table td {
    vertical-align: middle;
  }

  .btn-danger {
    transition: all 0.2s ease;
  }

  .btn-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
  }

  .card {
    border: none;
    border-radius: 0.5rem;
  }

  .table-responsive {
    border-radius: 0.5rem;
    overflow: hidden;
  }
</style>