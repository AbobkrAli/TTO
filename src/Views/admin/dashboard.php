<?php
$pageTitle = 'Admin Dashboard';
$activePage = 'dashboard';

// Calculate some stats for the dashboard
$totalUsers = count($users);
$totalTeachers = 0;
$totalSupervisors = 0;
$departmentCounts = [];

foreach ($users as $user) {
  if ($user['role'] === 'teacher') {
    $totalTeachers++;
  } else if ($user['role'] === 'supervisor') {
    $totalSupervisors++;
  }

  if (!empty($user['department'])) {
    $dept = $user['department'];
    if (!isset($departmentCounts[$dept])) {
      $departmentCounts[$dept] = 0;
    }
    $departmentCounts[$dept]++;
  }
}

ob_start();
?>

<!-- Custom styles for admin dashboard -->
<style>
  .dashboard-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border-radius: 10px;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 24px;
    overflow: hidden;
  }

  .dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
  }

  .welcome-banner {
    background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
    color: white;
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  }

  .welcome-banner h2 {
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  .welcome-banner p {
    opacity: 0.9;
    font-size: 1.1rem;
    margin-bottom: 0;
  }

  .stats-card {
    padding: 1.5rem;
    text-align: center;
    height: 100%;
  }

  .stats-card i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
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

  .profile-card {
    border-radius: 10px;
    overflow: hidden;
    padding: 0;
  }

  .profile-header {
    background: linear-gradient(to right, #4b6cb7, #182848);
    padding: 1.5rem;
    text-align: center;
  }

  .profile-avatar {
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

  .profile-info {
    padding: 1.5rem;
  }

  .profile-info-item {
    display: flex;
    margin-bottom: 1rem;
    align-items: center;
  }

  .profile-info-item i {
    width: 24px;
    margin-right: 10px;
    color: #6c757d;
  }

  .profile-actions {
    text-align: center;
    padding: 0 1.5rem 1.5rem;
  }

  .search-container {
    position: relative;
    width: 300px;
  }

  .search-container input {
    padding-left: 40px;
    border-radius: 50px;
    border: 1px solid #ced4da;
    padding: 0.5rem 1rem 0.5rem 2.5rem;
  }

  .search-container i {
    position: absolute;
    left: 12px;
    top: 11px;
    color: #6c757d;
  }

  .table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }

  .badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.7rem;
  }

  .user-table-container {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .user-table {
    margin-bottom: 0;
  }

  .user-table thead th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fa;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
  }

  .user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    font-weight: bold;
    color: #6c757d;
  }

  .user-info {
    display: flex;
    align-items: center;
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

  .departments-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }

  .department-pill {
    display: inline-block;
    background-color: #e9ecef;
    color: #495057;
    border-radius: 50px;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
  }

  .quick-action-card {
    height: 100%;
  }

  .quick-action {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 10px;
    transition: all 0.3s;
    color: #495057;
    text-decoration: none;
    margin-bottom: 1rem;
    background-color: #f8f9fa;
  }

  .quick-action:hover {
    background-color: #e9ecef;
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }

  .quick-action i {
    font-size: 1.5rem;
    margin-right: 1rem;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    color: white;
  }

  .quick-action:nth-child(1) i {
    background-color: #3498db;
  }

  .quick-action:nth-child(2) i {
    background-color: #2ecc71;
  }

  .quick-action:nth-child(3) i {
    background-color: #9b59b6;
  }

  .quick-action:nth-child(4) i {
    background-color: #e74c3c;
  }

  .action-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
  }

  .action-desc {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0;
  }

  .card-header-custom {
    background-color: white;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    padding: 1rem 1.5rem;
  }

  .card-header-custom h5 {
    margin: 0;
    font-weight: 600;
    display: flex;
    align-items: center;
  }

  .card-header-custom h5 i {
    margin-right: 10px;
    color: #4b6cb7;
  }
</style>

<div class="container-fluid">
  <!-- Welcome Banner -->
  <div class="welcome-banner">
    <h2><i class="bi bi-gear"></i> Welcome to the Admin Dashboard</h2>
    <p>Manage users, departments, and system settings from this central control panel.</p>
  </div>

  <div class="row">
    <!-- Left Column -->
    <div class="col-md-4">
      <!-- Profile Card -->
      <div class="card dashboard-card profile-card">
        <div class="profile-header">
          <div class="profile-avatar">
            <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?>
          </div>
          <h4><?php echo htmlspecialchars($user['fullname']); ?></h4>
          <span class="badge bg-light text-dark">Administrator</span>
        </div>
        <div class="profile-info">
          <div class="profile-info-item">
            <i class="bi bi-envelope"></i>
            <div><?php echo htmlspecialchars($user['email']); ?></div>
          </div>
          <div class="profile-info-item">
            <i class="bi bi-shield-check"></i>
            <div>Administrator</div>
          </div>
          <?php if (!empty($user['department'])): ?>
            <div class="profile-info-item">
              <i class="bi bi-building"></i>
              <div><?php echo htmlspecialchars($user['department']); ?></div>
            </div>
          <?php endif; ?>
        </div>
        <div class="profile-actions">
          <a href="#" class="btn btn-primary">
            <i class="bi bi-pencil-square"></i> Edit Profile
          </a>
        </div>
      </div>
    </div>

    <!-- Middle Column - Stats -->
    <div class="col-md-8">
      <div class="row">
        <!-- Total Users Card -->
        <div class="col-md-4">
          <div class="card dashboard-card bg-white">
            <div class="stats-card">
              <i class="bi bi-people text-primary"></i>
              <div class="stats-number"><?php echo $totalUsers; ?></div>
              <div class="stats-label">Total Users</div>
            </div>
          </div>
        </div>

        <!-- Teachers Card -->
        <div class="col-md-4">
          <div class="card dashboard-card bg-white">
            <div class="stats-card">
              <i class="bi bi-person-video3 text-success"></i>
              <div class="stats-number"><?php echo $totalTeachers; ?></div>
              <div class="stats-label">Teachers</div>
            </div>
          </div>
        </div>

        <!-- Supervisors Card -->
        <div class="col-md-4">
          <div class="card dashboard-card bg-white">
            <div class="stats-card">
              <i class="bi bi-person-workspace text-info"></i>
              <div class="stats-number"><?php echo $totalSupervisors; ?></div>
              <div class="stats-label">Supervisors</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card dashboard-card quick-action-card mt-4">
        <div class="card-header-custom">
          <h5><i class="bi bi-lightning-charge"></i> Quick Actions</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <a href="#" class="quick-action">
                <i class="bi bi-person-plus"></i>
                <div>
                  <div class="action-title">Add New User</div>
                  <div class="action-desc">Create teacher or supervisor accounts</div>
                </div>
              </a>
            </div>
            <div class="col-md-6">
              <a href="#" class="quick-action">
                <i class="bi bi-building-add"></i>
                <div>
                  <div class="action-title">Create Department</div>
                  <div class="action-desc">Set up a new department</div>
                </div>
              </a>
            </div>
            <div class="col-md-6">
              <a href="#" class="quick-action">
                <i class="bi bi-gear"></i>
                <div>
                  <div class="action-title">System Settings</div>
                  <div class="action-desc">Configure application parameters</div>
                </div>
              </a>
            </div>
            <div class="col-md-6">
              <a href="#" class="quick-action">
                <i class="bi bi-archive"></i>
                <div>
                  <div class="action-title">View Reports</div>
                  <div class="action-desc">Access system reports and logs</div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- User Management Table -->
  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card dashboard-card">
        <div class="card-header bg-white">
          <div class="table-header">
            <h5><i class="bi bi-people-fill"></i> User Management</h5>
            <div class="search-container">
              <i class="bi bi-search"></i>
              <input type="text" class="form-control" id="userSearch" placeholder="Search users...">
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="user-table-container">
            <table class="table table-hover user-table" id="userTable">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Department</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (isset($users) && !empty($users)): ?>
                  <?php foreach ($users as $user): ?>
                    <tr>
                      <td>
                        <div class="user-info">
                          <div class="user-avatar">
                            <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?>
                          </div>
                          <span><?php echo htmlspecialchars($user['fullname']); ?></span>
                        </div>
                      </td>
                      <td><?php echo htmlspecialchars($user['email']); ?></td>
                      <td>
                        <span class="badge <?php
                        if ($user['role'] === 'supervisor')
                          echo 'bg-primary';
                        elseif ($user['role'] === 'teacher')
                          echo 'bg-success';
                        else
                          echo 'bg-dark';
                        ?>">
                          <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                        </span>
                      </td>
                      <td>
                        <?php if (!empty($user['department'])): ?>
                          <span class="department-pill"><?php echo htmlspecialchars($user['department']); ?></span>
                        <?php else: ?>
                          <span class="text-muted"><em>Not assigned</em></span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <a href="#" class="btn btn-sm btn-view btn-action">
                          <i class="bi bi-eye"></i> View
                        </a>
                        <a href="#" class="btn btn-sm btn-edit btn-action">
                          <i class="bi bi-pencil"></i> Edit
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center p-4">No users found</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- System Notifications Row -->
  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card dashboard-card">
        <div class="card-header-custom">
          <h5><i class="bi bi-bell"></i> System Notifications</h5>
        </div>
        <div class="card-body">
          <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle me-2"></i>
            Welcome to the new admin dashboard! This interface has been updated to provide a consistent experience
            across all user roles.
            Explore the new features and let us know if you have any feedback.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- User Search JavaScript -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('userSearch');
    const table = document.getElementById('userTable');
    const rows = table.getElementsByTagName('tr');

    searchInput.addEventListener('keyup', function () {
      const query = searchInput.value.toLowerCase();

      // Start from 1 to skip the header row
      for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;

        // Search through cells
        for (let j = 0; j < cells.length; j++) {
          const cellText = cells[j].textContent.toLowerCase();
          if (cellText.indexOf(query) > -1) {
            found = true;
            break;
          }
        }

        // Show/hide row
        row.style.display = found ? '' : 'none';
      }
    });
  });
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>