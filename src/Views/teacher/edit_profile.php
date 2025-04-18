<?php
$pageTitle = 'Edit Profile';
$activePage = 'profile';

ob_start();
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5>Edit Profile Information</h5>
        </div>
        <div class="card-body">
          <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
          <?php endif; ?>

          <form action="/teacher/profile/edit" method="post">
            <div class="mb-3">
              <label for="fullname" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="fullname" name="fullname"
                value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email"
                value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="department" class="form-label">Department</label>
              <input type="text" class="form-control" id="department" name="department"
                value="<?php echo htmlspecialchars($user['department'] ?? ''); ?>">
              <div class="form-text">Leave blank if not applicable</div>
            </div>
            <div class="mb-3">
              <button type="submit" class="btn btn-primary">Update Profile</button>
              <a href="/teacher/profile" class="btn btn-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>