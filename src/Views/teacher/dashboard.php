<?php
$pageTitle = 'Teacher Dashboard';
$activePage = 'dashboard';

ob_start();
?>

<!-- Custom styles for teacher dashboard -->
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
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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

  .profile-card {
    border-radius: 10px;
    overflow: hidden;
    padding: 0;
  }

  .profile-header {
    background: linear-gradient(to right, #e0eafc, #cfdef3);
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

  .nav-card {
    border-radius: 10px;
    overflow: hidden;
  }

  .nav-link-custom {
    display: flex;
    align-items: center;
    padding: 1rem 1.5rem;
    color: #495057;
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: all 0.3s;
  }

  .nav-link-custom:hover {
    background-color: #f8f9fa;
    border-left-color: #38ef7d;
    color: #212529;
  }

  .nav-link-custom i {
    margin-right: 10px;
    font-size: 1.2rem;
    color: #11998e;
  }

  .department-badge {
    display: inline-block;
    background-color: #e9ecef;
    color: #495057;
    border-radius: 50px;
    padding: 0.25rem 0.75rem;
    font-size: 0.85rem;
  }

  .stats-card {
    text-align: center;
    padding: 1.5rem;
  }

  .stats-card i {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #11998e;
  }

  .stats-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  .stats-label {
    font-size: 0.9rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 1px;
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
    color: #11998e;
  }
</style>

<div class="container-fluid">
  <!-- Welcome Banner -->
  <div class="welcome-banner">
    <h2><i class="bi bi-mortarboard"></i> Welcome, <?php echo htmlspecialchars($user['fullname']); ?>!</h2>
    <p>Access your teaching resources and manage your profile from this dashboard.</p>
  </div>

  <div class="row">
    <!-- Profile Card -->
    <div class="col-md-6">
      <div class="card dashboard-card profile-card">
        <div class="profile-header">
          <div class="profile-avatar">
            <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?>
          </div>
          <h4><?php echo htmlspecialchars($user['fullname']); ?></h4>
          <?php if ($user['department']): ?>
            <span class="department-badge"><?php echo htmlspecialchars($user['department']); ?></span>
          <?php endif; ?>
        </div>
        <div class="profile-info">
          <div class="profile-info-item">
            <i class="bi bi-envelope"></i>
            <div><?php echo htmlspecialchars($user['email']); ?></div>
          </div>
          <div class="profile-info-item">
            <i class="bi bi-person-badge"></i>
            <div>Teacher</div>
          </div>
          <div class="profile-info-item">
            <i class="bi bi-building"></i>
            <div>
              <?php echo $user['department'] ? htmlspecialchars($user['department']) : '<em>Department not assigned</em>'; ?>
            </div>
          </div>
        </div>
        <div class="profile-actions">
          <a href="/teacher/profile" class="btn btn-primary">
            <i class="bi bi-pencil-square"></i> Edit Profile
          </a>
        </div>
      </div>
    </div>

    <!-- Stats and Navigation Column -->
    <div class="col-md-6">
      <!-- Quick Stats Card -->
      <div class="card dashboard-card mb-4">
        <div class="card-header-custom">
          <h5><i class="bi bi-bar-chart"></i> Your Stats</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="stats-card">
                <i class="bi bi-calendar2-check"></i>
                <div class="stats-number">0</div>
                <div class="stats-label">Classes</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="stats-card">
                <i class="bi bi-journal-text"></i>
                <div class="stats-number">0</div>
                <div class="stats-label">Materials</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Navigation Card -->
      <div class="card dashboard-card nav-card">
        <div class="card-header-custom">
          <h5><i class="bi bi-grid"></i> Quick Navigation</h5>
        </div>
        <div class="card-body p-0">
          <a href="/teacher/profile" class="nav-link-custom">
            <i class="bi bi-person"></i> My Profile
          </a>
          <a href="#" class="nav-link-custom">
            <i class="bi bi-calendar3"></i> Class Schedule
          </a>
          <a href="#" class="nav-link-custom">
            <i class="bi bi-journal-richtext"></i> Teaching Materials
          </a>
          <a href="#" class="nav-link-custom">
            <i class="bi bi-people"></i> Students
          </a>
          <a href="/logout" class="nav-link-custom">
            <i class="bi bi-box-arrow-right"></i> Sign Out
          </a>
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
            Welcome to the new dashboard! We've updated the interface to provide a better user experience.
            Explore the new features and let us know if you have any feedback.
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