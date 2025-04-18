<?php
$pageTitle = 'Department: ' . htmlspecialchars($department['name']);
$activePage = 'departments';

ob_start();
?>

<style>
  .department-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border-radius: 10px;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 24px;
    overflow: hidden;
  }

  .department-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
  }

  .table-container {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .dept-header {
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

  .btn-delete {
    background-color: #e74c3c;
    border-color: #e74c3c;
    color: white;
  }

  .subject-day {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 500;
    background-color: #e0f7fa;
    color: #0288d1;
  }

  .subject-time {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 500;
    background-color: #f3e5f5;
    color: #7b1fa2;
  }

  .subject-code {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: bold;
    background-color: #e8f5e9;
    color: #388e3c;
  }
</style>

<div class="container-fluid">
  <!-- Department Header -->
  <div class="dept-header">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h2><i class="bi bi-building me-2"></i><?php echo htmlspecialchars($department['name']); ?></h2>
        <p class="mb-0">Created on <?php echo date('F d, Y', strtotime($department['created_at'])); ?></p>
      </div>
      <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="/supervisor/departments/edit/<?php echo $department['id']; ?>" class="btn btn-light me-2">
          <i class="bi bi-pencil"></i> Edit Department
        </a>
        <a href="/supervisor/departments" class="btn btn-outline-light">
          <i class="bi bi-arrow-left"></i> Back
        </a>
      </div>
    </div>
  </div>

  <!-- Department Tabs -->
  <ul class="nav nav-tabs mb-4" id="departmentTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button"
        role="tab" aria-controls="subjects" aria-selected="true">
        <i class="bi bi-book me-1"></i> Subjects
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="teachers-tab" data-bs-toggle="tab" data-bs-target="#teachers" type="button"
        role="tab" aria-controls="teachers" aria-selected="false">
        <i class="bi bi-person-video3 me-1"></i> Teachers
      </button>
    </li>
  </ul>

  <!-- Tab Content -->
  <div class="tab-content" id="departmentTabContent">
    <!-- Subjects Tab -->
    <div class="tab-pane fade show active" id="subjects" role="tabpanel" aria-labelledby="subjects-tab">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Department Schedule</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
          <i class="bi bi-plus-circle me-1"></i> Add Subject
        </button>
      </div>

      <!-- Day Selection -->
      <div class="mb-4">
        <div class="btn-group" role="group">
          <?php
          $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
          foreach ($days as $day) {
            $active = ($selectedDay ?? 'Monday') === $day ? 'active' : '';
            echo '<button type="button" class="btn btn-outline-primary day-btn ' . $active . '" 
                      data-day="' . $day . '">' . $day . '</button>';
          }
          ?>
        </div>
      </div>

      <div class="card department-card">
        <div class="card-body p-0">
          <div class="table-container">
            <table class="table table-bordered mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width: 100px;">Time</th>
                  <th>Subject Details</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $timeSlots = [
                  9 => '9:00 AM',
                  10 => '10:00 AM',
                  11 => '11:00 AM',
                  12 => '12:00 PM',
                  13 => '1:00 PM',
                  14 => '2:00 PM',
                  15 => '3:00 PM',
                  16 => '4:00 PM',
                  17 => '5:00 PM'
                ];

                // Group subjects by day and hour
                $scheduledSubjects = [];
                foreach ($subjects as $subject) {
                  $day = $subject['day'];
                  $hour = (int) $subject['hour'];
                  $scheduledSubjects[$day][$hour] = $subject;
                }

                $currentDay = $selectedDay ?? 'Monday';
                foreach ($timeSlots as $hour => $displayTime) {
                  echo '<tr>';
                  echo '<td class="table-light fw-bold">' . $displayTime . '</td>';
                  echo '<td style="height: 80px; position: relative;">';

                  if (isset($scheduledSubjects[$currentDay][$hour])) {
                    $subject = $scheduledSubjects[$currentDay][$hour];
                    echo '<div class="position-absolute top-0 start-0 end-0 bottom-0 p-2" 
                               style="background-color: rgba(37, 117, 252, 0.1); border-left: 3px solid #2575fc;">';
                    echo '<div class="fw-bold">' . htmlspecialchars($subject['subject_name']) . '</div>';
                    echo '<div class="small text-muted">' . htmlspecialchars($subject['subject_code']) . '</div>';
                    echo '<div class="position-absolute top-0 end-0 p-1">';
                    echo '<button class="btn btn-sm btn-link text-danger delete-subject" 
                                  data-subject-id="' . $subject['id'] . '" 
                                  title="Delete Subject">';
                    echo '<i class="bi bi-trash"></i>';
                    echo '</button>';
                    echo '</div>';
                    echo '</div>';
                  } else {
                    echo '<button class="btn btn-light btn-sm position-absolute top-50 start-50 translate-middle add-subject-btn" 
                                  data-bs-toggle="modal" 
                                  data-bs-target="#addSubjectModal" 
                                  data-day="' . $currentDay . '" 
                                  data-hour="' . $hour . '">';
                    echo '<i class="bi bi-plus"></i> Add Subject';
                    echo '</button>';
                  }
                  echo '</td>';
                  echo '</tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Subject Modal -->
    <div class="modal fade" id="addSubjectModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Subject</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form action="/supervisor/departments/<?= $department['id'] ?>/subjects/add" method="POST">
              <input type="hidden" name="day" id="addSubjectDay">
              <input type="hidden" name="hour" id="addSubjectHour">
              <div class="mb-3">
                <label for="code" class="form-label">Subject Code</label>
                <input type="text" class="form-control" id="code" name="code" required>
              </div>
              <div class="mb-3">
                <label for="name" class="form-label">Subject Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
              </div>
              <button type="submit" class="btn btn-primary">Add Subject</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Add JavaScript for handling the modal and day selection -->
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const addSubjectModal = document.getElementById('addSubjectModal');
        const addSubjectDayInput = document.getElementById('addSubjectDay');
        const addSubjectHourInput = document.getElementById('addSubjectHour');

        // Handle day selection
        document.querySelectorAll('.day-btn').forEach(button => {
          button.addEventListener('click', function () {
            // Remove active class from all buttons
            document.querySelectorAll('.day-btn').forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            // Update the selected day
            const day = this.dataset.day;
            // Reload the page with the selected day
            window.location.href = window.location.pathname + '?day=' + day;
          });
        });

        // Handle add subject button clicks
        document.querySelectorAll('.add-subject-btn').forEach(button => {
          button.addEventListener('click', function () {
            const day = this.dataset.day;
            const hour = this.dataset.hour;
            addSubjectDayInput.value = day;
            addSubjectHourInput.value = hour;
          });
        });

        // Handle subject deletion
        document.querySelectorAll('.delete-subject').forEach(button => {
          button.addEventListener('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this subject?')) {
              window.location.href = '/supervisor/subjects/delete/' + this.dataset.subjectId;
            }
          });
        });
      });
    </script>

    <!-- Teachers Tab -->
    <div class="tab-pane fade" id="teachers" role="tabpanel" aria-labelledby="teachers-tab">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Teacher List</h4>
        <a href="/supervisor/departments/<?php echo $department['id']; ?>/teachers/add" class="btn btn-primary">
          <i class="bi bi-person-plus me-1"></i> Add Teacher
        </a>
      </div>

      <div class="card department-card">
        <div class="card-body p-0">
          <div class="table-container">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($teachers)): ?>
                  <?php foreach ($teachers as $teacher): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($teacher['name']); ?></td>
                      <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                      <td>
                        <a href="/supervisor/users/view/<?php echo $teacher['id']; ?>"
                          class="btn btn-sm btn-view btn-action">
                          <i class="bi bi-eye"></i> View
                        </a>
                        <a href="/supervisor/users/edit/<?php echo $teacher['id']; ?>"
                          class="btn btn-sm btn-edit btn-action">
                          <i class="bi bi-pencil"></i> Edit
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" class="text-center p-4">No teachers assigned to this department.</td>
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

<?php
$content = ob_get_clean();
require dirname(dirname(dirname(__DIR__))) . '/Views/layout.php';
?>