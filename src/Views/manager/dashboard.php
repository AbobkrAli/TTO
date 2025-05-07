<?php
$pageTitle = 'Manager Dashboard';
$activePage = 'dashboard';

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

  .table-container {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .welcome-banner {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    color: white;
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
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

  .badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.7rem;
  }

  .subject-item {
    background-color: #f8f9fa;
    border-radius: 6px;
    padding: 10px;
    border-left: 4px solid #0d6efd;
    transition: transform 0.2s, box-shadow 0.2s;
    margin-bottom: 8px;
  }

  .subject-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .day-btn {
    border-radius: 20px;
    padding: 0.5rem 1.5rem;
    margin: 0 0.25rem;
    transition: all 0.3s;
  }

  .day-btn:hover {
    transform: translateY(-2px);
  }

  .day-btn.active {
    background-color: #2575fc;
    color: white;
    box-shadow: 0 4px 6px rgba(37, 117, 252, 0.2);
  }
</style>

<div class="container-fluid">
  <!-- Welcome Banner -->
  <div class="welcome-banner">
    <h2><i class="bi bi-building"></i> Welcome to Your Department Dashboard</h2>
    <p>View your department's schedule, teachers, and optional subjects.</p>
  </div>

  <!-- Stats Cards Row -->
  <div class="row">
    <!-- Teachers Card -->
    <div class="col-md-4">
      <div class="card dashboard-card bg-white">
        <div class="stats-card">
          <i class="bi bi-person-video3 text-primary"></i>
          <div class="stats-number"><?php echo count($teachers); ?></div>
          <div class="stats-label">Teachers & Managers</div>
        </div>
      </div>
    </div>

    <!-- Subjects Card -->
    <div class="col-md-4">
      <div class="card dashboard-card bg-white">
        <div class="stats-card">
          <i class="bi bi-book text-success"></i>
          <div class="stats-number"><?php echo count($subjects); ?></div>
          <div class="stats-label">Subjects</div>
        </div>
      </div>
    </div>

    <!-- Optional Subjects Card -->
    <div class="col-md-4">
      <div class="card dashboard-card bg-white">
        <div class="stats-card">
          <i class="bi bi-bookmark text-info"></i>
          <div class="stats-number"><?php echo count($optionalSubjects); ?></div>
          <div class="stats-label">Optional Subjects</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Schedule Section -->
  <section id="schedule" class="mb-5">
    <div class="card dashboard-card">
      <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Department Schedule</h5>
          <button type="button" class="btn btn-primary" onclick="printSchedule()">
            <i class="bi bi-printer"></i> Print Schedule
          </button>
        </div>
      </div>
      <div class="card-body">
        <!-- Day selector -->
        <div class="mb-4">
          <div class="btn-group" role="group">
            <?php
            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
            foreach ($days as $day) {
              $active = $selectedDay === $day ? 'active' : '';
              echo "<a href='?day=" . urlencode($day) . "' class='btn btn-outline-primary day-btn $active'>$day</a>";
            }
            ?>
          </div>
        </div>

        <!-- Schedule table -->
        <div class="table-container">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Hour</th>
                <th>Subject</th>
                <th>Class</th>
                <th>Teacher</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $hours = range(9, 17);
              foreach ($hours as $hour) {
                echo "<tr>";
                echo "<td class='align-middle'>" . sprintf('%02d:00', $hour) . "</td>";
                echo "<td>";
                $subjectFound = false;

                // Check if we have subjects for the selected day and hour
                if (isset($subjects[$selectedDay][$hour])) {
                  foreach ($subjects[$selectedDay][$hour] as $subject) {
                    $subjectFound = true;
                    echo "<div class='subject-item'>";
                    echo "<strong>" . htmlspecialchars($subject['subject_code']) . "</strong><br>";
                    echo htmlspecialchars($subject['subject_name']);
                    if ($subject['is_office_hour']) {
                      echo " <span class='badge bg-info'>Office Hour</span>";
                    }
                    echo "</div>";
                  }
                }

                if (!$subjectFound) {
                  echo "<span class='text-muted'>No subject scheduled</span>";
                }
                echo "</td>";
                echo "<td class='align-middle'>";

                if (isset($subjects[$selectedDay][$hour])) {
                  foreach ($subjects[$selectedDay][$hour] as $subject) {
                    echo htmlspecialchars($subject['class_name'] ?? 'Not assigned');
                    break;
                  }
                } else {
                  echo "<span class='text-muted'>Not assigned</span>";
                }

                echo "</td>";
                echo "<td class='align-middle'>";

                if (isset($subjects[$selectedDay][$hour])) {
                  foreach ($subjects[$selectedDay][$hour] as $subject) {
                    if (!empty($subject['teacher_name'])) {
                      echo htmlspecialchars($subject['teacher_name']);
                    } else {
                      echo "<span class='text-muted'>Not assigned</span>";
                    }
                    break;
                  }
                } else {
                  echo "<span class='text-muted'>Not assigned</span>";
                }

                echo "</td>";
                echo "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- Teachers Section -->
  <section id="teachers" class="mb-5">
    <div class="card dashboard-card">
      <div class="card-header bg-white">
        <h5 class="card-title mb-0">Department Teachers</h5>
      </div>
      <div class="card-body">
        <div class="table-container">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($teachers)): ?>
                <tr>
                  <td colspan="3" class="text-center">No teachers or managers assigned to this department</td>
                </tr>
              <?php else: ?>
                <?php foreach ($teachers as $teacher): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($teacher['name']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                    <td>
                      <span class="badge <?php echo $teacher['role'] === 'manager' ? 'bg-primary' : 'bg-info'; ?>">
                        <?php echo ucfirst($teacher['role']); ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- Optional Subjects Section -->
  <section id="optional-subjects" class="mb-5">
    <div class="card dashboard-card">
      <div class="card-header bg-white">
        <h5 class="card-title mb-0">Optional Subjects</h5>
      </div>
      <div class="card-body">
        <div class="table-container">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Code</th>
                <th>Name</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($optionalSubjects)): ?>
                <tr>
                  <td colspan="2" class="text-center">No optional subjects available</td>
                </tr>
              <?php else: ?>
                <?php foreach ($optionalSubjects as $subject): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                    <td><?php echo htmlspecialchars($subject['name']); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  function printSchedule() {
    const scheduleContent = document.querySelector('.table-container').cloneNode(true);
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
      <html>
        <head>
          <title>Department Schedule - Print View</title>
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
          <style>
            body { padding: 20px; }
            .print-header { margin-bottom: 20px; }
            .subject-item { margin-bottom: 10px; }
            @media print {
              .no-print { display: none; }
              .table { width: 100%; }
              .table td, .table th { padding: 8px; }
            }
          </style>
        </head>
        <body>
          <div class="print-header">
            <h2><?php echo htmlspecialchars($department['name']); ?> Department</h2>
            <p>Schedule for <?php echo htmlspecialchars($selectedDay); ?></p>
            <p>Printed on: ${new Date().toLocaleDateString()}</p>
          </div>
          ${scheduleContent.innerHTML}
          <div class="no-print mt-3">
            <button onclick="window.print()" class="btn btn-primary">Print</button>
            <button onclick="window.close()" class="btn btn-secondary">Close</button>
          </div>
        </body>
      </html>
    `);
    printWindow.document.close();
  }
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>