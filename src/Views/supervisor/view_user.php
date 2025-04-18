<?php
$pageTitle = 'View User';
$activePage = 'users';

ob_start();
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5>User Information</h5>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-3 fw-bold">ID:</div>
            <div class="col-md-9"><?php echo $user['id']; ?></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-3 fw-bold">Full Name:</div>
            <div class="col-md-9"><?php echo htmlspecialchars($user['fullname']); ?></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-3 fw-bold">Email:</div>
            <div class="col-md-9"><?php echo htmlspecialchars($user['email']); ?></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-3 fw-bold">Role:</div>
            <div class="col-md-9">
              <span class="badge <?php echo $user['role'] === 'supervisor' ? 'bg-primary' : 'bg-success'; ?>">
                <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
              </span>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-3 fw-bold">Department:</div>
            <div class="col-md-9">
              <?php echo $user['department'] ? htmlspecialchars($user['department']) : '<em>Not assigned</em>'; ?>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12">
              <a href="/supervisor/users/edit/<?php echo $user['id']; ?>" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit User
              </a>
              <a href="/supervisor/dashboard" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>