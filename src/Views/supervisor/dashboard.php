<?php
$pageTitle = 'Supervisor Dashboard';
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

<!-- Custom styles for this dashboard -->
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

  .table th {
    font-weight: 600;
    background-color: #f8f9fa;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
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

  .badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.7rem;
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

  .welcome-banner {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    color: white;
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  }

  .table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
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

  .department-pill {
    display: inline-block;
    background-color: #e9ecef;
    color: #495057;
    border-radius: 50px;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
  }

  .department-card {
    height: 100%;
  }

  .department-card-body {
    display: flex;
    flex-direction: column;
    height: 100%;
  }

  .department-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
  }

  .department-stats {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 1rem;
  }

  .departments-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }
</style>

<div class="container-fluid">
  <!-- Welcome Banner -->
  <div class="welcome-banner">
    <h2><i class="bi bi-graph-up"></i> Welcome to Your Supervisor Dashboard</h2>
    <p>Manage your teachers, view statistics, and oversee your school's operations.</p>
  </div>

  <!-- Stats Cards Row -->
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

    <!-- Departments Card -->
    <div class="col-md-4">
      <div class="card dashboard-card bg-white">
        <div class="stats-card">
          <a href="/supervisor/departments" class="text-decoration-none">
            <i class="bi bi-building text-info"></i>
            <div class="stats-number"><?php echo count($departments); ?></div>
            <div class="stats-label">Departments</div>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Departments Row -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="departments-header">
        <h4><i class="bi bi-building me-2"></i>Departments</h4>
        <a href="/supervisor/departments" class="btn btn-primary">
          <i class="bi bi-grid-3x3-gap me-1"></i> Manage Departments
        </a>
      </div>
    </div>
    
    <?php if (!empty($departments)): ?>
      <?php $counter = 0; ?>
      <?php foreach ($departments as $dept): ?>
        <?php if ($counter < 3): ?>
          <div class="col-md-4">
            <div class="card dashboard-card">
              <div class="card-body department-card-body">
                <h5 class="department-title">
                  <i class="bi bi-folder me-2 text-primary"></i>
                  <?php echo htmlspecialchars($dept['name']); ?>
                </h5>
                <div class="department-stats">
                  <div><i class="bi bi-person me-1"></i> <?php echo $dept['teacher_count']; ?> Teachers</div>
                </div>
                <div class="mt-auto">
                  <a href="/supervisor/departments/view/<?php echo $dept['id']; ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye me-1"></i> View Details
                  </a>
                </div>
              </div>
            </div>
          </div>
          <?php $counter++; ?>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info">
          No departments found. <a href="/supervisor/departments/add" class="alert-link">Add your first department</a>.
        </div>
      </div>
    <?php endif; ?>

    <?php if (count($departments) > 3): ?>
      <div class="col-12 text-center mt-3">
        <a href="/supervisor/departments" class="btn btn-outline-secondary">
          View All <?php echo count($departments); ?> Departments
        </a>
      </div>
    <?php endif; ?>
  </div>

  <!-- User Management Table -->
  <div class="row">
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
                        <span class="badge <?php echo $user['role'] === 'supervisor' ? 'bg-primary' : 'bg-success'; ?>">
                          <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                        </span>
                      </td>
                      <td>
                        <?php if ($user['department']): ?>
                          <span class="department-pill"><?php echo htmlspecialchars($user['department']); ?></span>
                        <?php else: ?>
                          <span class="text-muted"><em>Not assigned</em></span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <a href="/supervisor/users/view/<?php echo $user['id']; ?>" class="btn btn-sm btn-view btn-action">
                          <i class="bi bi-eye"></i> View
                        </a>
                        <a href="/supervisor/users/edit/<?php echo $user['id']; ?>" class="btn btn-sm btn-edit btn-action">
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