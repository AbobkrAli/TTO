<?php
$pageTitle = 'View User';
$activePage = 'users';

ob_start();
?>

<style>
  .user-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border-radius: 10px;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 24px;
    overflow: hidden;
  }

  .user-card:hover {
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
  }

  .user-header {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    color: white;
    padding: 2rem;
    text-align: center;
  }

  .user-avatar-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background-color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 3rem;
    font-weight: bold;
    color: #6c757d;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  }

  .user-detail {
    padding: 0.75rem;
    display: flex;
    border-bottom: 1px solid #f0f0f0;
  }

  .user-detail:last-child {
    border-bottom: none;
  }

  .user-detail-label {
    width: 120px;
    color: #6c757d;
    font-weight: 500;
  }

  .user-detail-value {
    flex: 1;
  }

  .badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.7rem;
  }

  .department-pill {
    display: inline-block;
    background-color: #e9ecef;
    color: #495057;
    border-radius: 50px;
    padding: 0.25rem 0.75rem;
    font-size: 0.85rem;
  }
</style>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-8 col-lg-6 mx-auto">
      <!-- Flash Messages -->
      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?php echo $_SESSION['success'];
          unset($_SESSION['success']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo $_SESSION['error'];
          unset($_SESSION['error']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="card user-card">
        <div class="user-header">
          <div class="user-avatar-large">
            <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?>
          </div>
          <h3 class="mb-1"><?php echo htmlspecialchars($user['fullname']); ?></h3>
          <span class="badge <?php echo $user['role'] === 'supervisor' ? 'bg-primary' : 'bg-success'; ?>">
            <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
          </span>
        </div>
        <div class="card-body p-0">
          <div class="user-detail">
            <div class="user-detail-label">Email</div>
            <div class="user-detail-value"><?php echo htmlspecialchars($user['email']); ?></div>
          </div>
          <div class="user-detail">
            <div class="user-detail-label">Role</div>
            <div class="user-detail-value"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></div>
          </div>
          <div class="user-detail">
            <div class="user-detail-label">Department</div>
            <div class="user-detail-value">
              <?php if (!empty($department)): ?>
                <span class="department-pill"><?php echo htmlspecialchars($department['name']); ?></span>
              <?php else: ?>
                <span class="text-muted"><em>Not assigned</em></span>
              <?php endif; ?>
            </div>
          </div>
          <div class="user-detail">
            <div class="user-detail-label">Created</div>
            <div class="user-detail-value">
              <?php echo isset($user['created_at']) ? date('F d, Y', strtotime($user['created_at'])) : 'N/A'; ?>
            </div>
          </div>
        </div>
        <div class="card-footer bg-white">
          <div class="d-flex justify-content-between">
            <a href="/supervisor/users" class="btn btn-secondary">
              <i class="bi bi-arrow-left"></i> Back to Users
            </a>
            <a href="/supervisor/users/edit/<?php echo $user['id']; ?>" class="btn btn-primary">
              <i class="bi bi-pencil"></i> Edit User
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require dirname(dirname(dirname(__DIR__))) . '/layout.php';
?>