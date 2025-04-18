<?php
$pageTitle = 'Edit User';
$activePage = 'users';

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

  .user-header {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    color: white;
    padding: 1.5rem;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
  }

  .user-avatar-large {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background-color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2.5rem;
    font-weight: bold;
    color: #6c757d;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }
</style>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-8 col-lg-6 mx-auto">
      <div class="card form-card">
        <div class="user-header text-center">
          <div class="user-avatar-large">
            <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?>
          </div>
          <h4 class="mb-0">Edit User</h4>
        </div>
        <div class="card-body">
          <?php if (isset($error)): ?>
            <div class="alert alert-danger">
              <?php echo $error; ?>
            </div>
          <?php endif; ?>

          <form action="/supervisor/users/edit/<?php echo $user['id']; ?>" method="post">
            <div class="mb-3">
              <label for="fullname" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="fullname" name="fullname" required
                value="<?php echo htmlspecialchars($user['fullname']); ?>">
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" required
                value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>

            <div class="mb-3">
              <label for="role" class="form-label">Role</label>
              <select class="form-select" id="role" name="role" required>
                <option value="teacher" <?php echo $user['role'] === 'teacher' ? 'selected' : ''; ?>>Teacher</option>
                <option value="supervisor" <?php echo $user['role'] === 'supervisor' ? 'selected' : ''; ?>>Supervisor
                </option>
              </select>
            </div>

            <div class="mb-3">
              <label for="department_id" class="form-label">Department</label>
              <select class="form-select" id="department_id" name="department_id">
                <option value="">-- None --</option>
                <?php foreach ($departments as $department): ?>
                  <option value="<?php echo $department['id']; ?>" <?php echo isset($user['department_id']) && $user['department_id'] == $department['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($department['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">Assign this user to a department. Only required for teachers.</div>
            </div>

            <hr>

            <div class="d-flex justify-content-between">
              <a href="/supervisor/users" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Users
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Save Changes
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
require dirname(dirname(dirname(__DIR__))) . '/layout.php';
?>