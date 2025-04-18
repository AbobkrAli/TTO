<?php
$pageTitle = 'My Profile';
$activePage = 'profile';

ob_start();
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5>My Profile Information</h5>
        </div>
        <div class="card-body">
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
              <span class="badge bg-success">
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
          <div class="row">
            <div class="col-md-12">
              <a href="/teacher/profile/edit" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit Profile
              </a>
              <a href="/teacher/dashboard" class="btn btn-secondary">
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