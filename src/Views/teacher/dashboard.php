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

  /* Schedule Table Styles */
  .schedule-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 1rem;
  }

  .schedule-table th {
    background-color: #f8f9fa;
    padding: 0.75rem;
    text-align: center;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
  }

  .schedule-table td {
    padding: 0.75rem;
    text-align: center;
    border-bottom: 1px solid #dee2e6;
    vertical-align: middle;
  }

  .schedule-table tr:hover {
    background-color: #f8f9fa;
  }

  .schedule-time {
    font-weight: 600;
    color: #11998e;
  }

  .schedule-course {
    font-weight: 500;
  }

  .schedule-room {
    color: #6c757d;
    font-size: 0.9rem;
  }

  .schedule-empty {
    color: #6c757d;
    font-style: italic;
  }
</style>

<div class="container-fluid">
  <!-- Welcome Banner -->
  <div class="welcome-banner">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h2><i class="bi bi-mortarboard"></i> Welcome,
          <?php echo htmlspecialchars($user['fullname'] ?? $user['name'] ?? 'Teacher'); ?>!
        </h2>
        <p>Access your teaching resources and manage your schedule from this dashboard.</p>
      </div>
      <a href="/teacher/schedule" class="btn btn-light px-4 py-2">
        <i class="bi bi-calendar3"></i> View Department Schedule
      </a>
    </div>
  </div>

  <!-- Department Subjects Section -->
  <div class="card dashboard-card mt-4">
    <div class="card-header-custom">
      <h5><i class="bi bi-book"></i> Department Subjects</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Code</th>
              <th>Subject Name</th>
              <th>Day</th>
              <th>Time</th>
              <th>Room</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $allSubjects = [];
            foreach ($schedule as $day => $subjects) {
              foreach ($subjects as $subject) {
                $allSubjects[] = $subject;
              }
            }

            // Remove duplicates based on subject code
            $uniqueSubjects = [];
            foreach ($allSubjects as $subject) {
              $uniqueSubjects[$subject['subject_code']] = $subject;
            }

            foreach ($uniqueSubjects as $subject) {
              echo '<tr>';
              echo '<td>' . htmlspecialchars($subject['subject_code']) . '</td>';
              echo '<td>' . htmlspecialchars($subject['subject_name']) . '</td>';
              echo '<td>' . htmlspecialchars($subject['day']) . '</td>';

              // Format hour as a time display (e.g., "14:00")
              $hour = isset($subject['hour']) ? (int) $subject['hour'] : 0;
              $hourDisplay = sprintf('%02d:00', $hour);
              $nextHourDisplay = sprintf('%02d:00', $hour + 1);

              echo '<td>' . $hourDisplay . ' - ' . $nextHourDisplay . '</td>';

              // Room information may not be present in the current data model
              echo '<td>' . (!empty($subject['room']) ? 'Room ' . htmlspecialchars($subject['room']) : '-') . '</td>';
              echo '</tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>