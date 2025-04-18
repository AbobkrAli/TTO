<?php
$pageTitle = 'Departments';
$activePage = 'departments';

ob_start();
?>

<style>
  .department-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border-radius: 10px;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 24px;
    overflow: hidden;
  }

  .department-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
  }

  .table-container {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .stats-card {
    padding: 1.5rem;
    text-align: center;
    height: 100%;
  }

  .stats-card i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: #6a11cb;
  }

  .stats-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  .stats-label {
    font-size: 1rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .btn-action {
    border-radius: 50px;
    padding: 0.375rem 1rem;
    margin: 0 2px;
  }

  .btn-view {
    background-color: #3498db;
    border-color: #3498db;
    color: white;
  }

  .btn-edit {
    background-color: #f39c12;
    border-color: #f39c12;
    color: white;
  }

  .btn-delete {
    background-color: #e74c3c;
    border-color: #e74c3c;
    color: white;
  }
</style>

<div class="container-fluid">
  <!-- Header with Add Button -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-building me-2"></i>Departments Management</h3>
    <a href="/supervisor/departments/add" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Add Department
    </a>
  </div>

  <!-- Departments Table -->
  <div class="card department-card">
    <div class="card-header bg-white py-3">
      <h5 class="mb-0">All Departments</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-container">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Department Name</th>
              <th>Teachers</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (isset($departments) && !empty($departments)): ?>
              <?php foreach ($departments as $department): ?>
                <tr>
                  <td><?php echo $department['id']; ?></td>
                  <td><strong><?php echo htmlspecialchars($department['name']); ?></strong></td>
                  <td><?php echo $department['teacher_count']; ?></td>
                  <td><?php echo date('M d, Y', strtotime($department['created_at'])); ?></td>
                  <td>
                    <a href="/supervisor/departments/view/<?php echo $department['id']; ?>"
                      class="btn btn-sm btn-view btn-action">
                      <i class="bi bi-eye"></i> View
                    </a>
                    <a href="/supervisor/departments/edit/<?php echo $department['id']; ?>"
                      class="btn btn-sm btn-edit btn-action">
                      <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="/supervisor/departments/delete/<?php echo $department['id']; ?>"
                      class="btn btn-sm btn-delete btn-action">
                      <i class="bi bi-trash"></i> Delete
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center p-4">No departments found. <a href="/supervisor/departments/add">Add a
                    new department</a>.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Quick Help Card -->
  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card department-card bg-light">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-info-circle me-2"></i>Managing Departments</h5>
          <p class="card-text">
            Departments allow you to organize teachers and subjects. Each department can have multiple teachers and
            subjects assigned to it.
            Use the actions above to view, edit, or delete departments.
          </p>
          <ul>
            <li><strong>View:</strong> See teachers and subjects associated with a department</li>
            <li><strong>Edit:</strong> Change the department name</li>
            <li><strong>Delete:</strong> Remove a department and all its subjects</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require dirname(dirname(dirname(__DIR__))) . '/Views/layout.php';
?>