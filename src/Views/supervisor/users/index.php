<?php
$pageTitle = 'User Management';
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
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
  }

  .table-container {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

  .department-pill {
    display: inline-block;
    background-color: #e9ecef;
    color: #495057;
    border-radius: 50px;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
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
</style>

<div class="container-fluid">
  <!-- Header with Add Button -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-people me-2"></i>User Management</h3>
    <a href="/supervisor/users/add" class="btn btn-primary">
      <i class="bi bi-person-plus me-1"></i> Add User
    </a>
  </div>

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

  <!-- Users Table -->
  <div class="card user-card">
    <div class="card-header bg-white">
      <div class="table-header">
        <h5 class="mb-0">All Users</h5>
        <div class="search-container">
          <i class="bi bi-search"></i>
          <input type="text" class="form-control" id="userSearch" placeholder="Search users...">
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-container">
        <table class="table table-hover user-table" id="userTable">
          <thead class="table-light">
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
                    <?php if (!empty($user['department_name'])): ?>
                      <span class="department-pill"><?php echo htmlspecialchars($user['department_name']); ?></span>
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

  <!-- Quick Help Card -->
  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card user-card bg-light">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-info-circle me-2"></i>Managing Users</h5>
          <p class="card-text">
            From this page, you can manage all users in the system. You can view user details, edit their information,
            and assign them to departments.
          </p>
          <ul>
            <li><strong>View:</strong> See user details and assigned department</li>
            <li><strong>Edit:</strong> Update user information and assign to departments</li>
            <li><strong>Department Assignment:</strong> When editing a user, you can assign them to any department</li>
          </ul>
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
require dirname(dirname(dirname(__DIR__))) . '/layout.php';
?>