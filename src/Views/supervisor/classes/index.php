<?php require_once __DIR__ . '/../../layout.php'; ?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Classes Management</h2>
    <a href="/supervisor/classes/add" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Add New Class
    </a>
  </div>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
      <?php
      echo $_SESSION['success'];
      unset($_SESSION['success']);
      ?>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
      <?php
      echo $_SESSION['error'];
      unset($_SESSION['error']);
      ?>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Class Name</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($classes as $class): ?>
              <tr>
                <td><?php echo htmlspecialchars($class['id']); ?></td>
                <td><?php echo htmlspecialchars($class['name']); ?></td>
                <td>
                  <a href="/supervisor/classes/delete/<?php echo $class['id']; ?>" class="btn btn-danger btn-sm"
                    onclick="return confirm('Are you sure you want to delete this class?')">
                    <i class="bi bi-trash"></i> Delete
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>