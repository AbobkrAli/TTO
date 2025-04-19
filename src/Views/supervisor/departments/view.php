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

  /* Office Hour Styles */
  .office-hour-item {
    background-color: rgba(156, 39, 176, 0.1) !important;
    border-left: 3px solid #9c27b0 !important;
  }

  .office-hour-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 500;
    background-color: #9c27b0;
    color: white;
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
    <li class="nav-item" role="presentation">
      <button class="nav-link position-relative" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests"
        type="button" role="tab" aria-controls="requests" aria-selected="false">
        <i class="bi bi-bell me-1"></i> Requests
        <?php
        $pendingCount = 0;
        $totalRequests = 0;
        if (isset($requests) && is_array($requests)) {
          $totalRequests = count($requests);
          foreach ($requests as $request) {
            if ($request['status'] === 'pending') {
              $pendingCount++;
            }
          }
          if ($pendingCount > 0) {
            echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">';
            echo $pendingCount;
            echo '<span class="visually-hidden">pending requests</span>';
            echo '</span>';
          } elseif ($totalRequests > 0) {
            echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">';
            echo $totalRequests;
            echo '<span class="visually-hidden">total requests</span>';
            echo '</span>';
          }
        }
        ?>
      </button>
    </li>
  </ul>

  <!-- Tab Content -->
  <div class="tab-content" id="departmentTabContent">
    <!-- Subjects Tab -->
    <div class="tab-pane fade show active" id="subjects" role="tabpanel" aria-labelledby="subjects-tab">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Department Schedule</h4>

      </div>

      <!-- Display messages -->
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo htmlspecialchars($_SESSION['error']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?php echo htmlspecialchars($_SESSION['success']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>

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

                // Group requests by day and hour
                $pendingRequests = [];
                if (isset($requests) && is_array($requests)) {
                  foreach ($requests as $request) {
                    if ($request['status'] === 'pending') {
                      $day = $request['day'];
                      $hour = (int) $request['hour'];
                      $pendingRequests[$day][$hour][] = $request;
                    }
                  }
                }

                $currentDay = $selectedDay ?? 'Monday';
                foreach ($timeSlots as $hour => $displayTime) {
                  echo '<tr>';
                  echo '<td class="table-light fw-bold" style="height: 120px;">' . $displayTime . '</td>';
                  echo '<td style="height: 120px; position: relative;">';

                  if (isset($scheduledSubjects[$currentDay][$hour])) {
                    $subject = $scheduledSubjects[$currentDay][$hour];
                    $isOfficeHour = isset($subject['is_office_hour']) && $subject['is_office_hour'] == 1;

                    echo '<div class="position-absolute top-0 start-0 end-0 bottom-0 p-2 ' . ($isOfficeHour ? 'office-hour-item' : '') . '" 
                               style="background-color: ' . ($isOfficeHour ? 'rgba(156, 39, 176, 0.1)' : 'rgba(37, 117, 252, 0.1)') . '; 
                               border-left: 3px solid ' . ($isOfficeHour ? '#9c27b0' : '#2575fc') . ';">';

                    echo '<div class="fw-bold">' . htmlspecialchars($subject['subject_name']) . '</div>';
                    echo '<div class="small text-muted">' . htmlspecialchars($subject['subject_code']) . '</div>';

                    // Display teacher information
                    if (isset($subject['teacher_name']) && !empty($subject['teacher_name'])) {
                      echo '<div class="small mt-1">';
                      echo '<i class="bi bi-person-circle"></i> ' . htmlspecialchars($subject['teacher_name']);
                      echo '</div>';
                    }

                    // Display class information
                    if (isset($subject['class_name']) && !empty($subject['class_name'])) {
                      echo '<div class="small mt-1">';
                      echo '<i class="bi bi-building"></i> ' . htmlspecialchars($subject['class_name']);
                      echo '</div>';
                    }

                    if ($isOfficeHour) {
                      echo '<span class="office-hour-badge">Office Hour</span>';
                    }

                    echo '<div class="position-absolute top-0 end-0 p-1">';
                    echo '<button class="btn btn-sm btn-link text-danger delete-subject" 
                                  data-subject-id="' . $subject['id'] . '" 
                                  data-subject-name="' . htmlspecialchars($subject['subject_name']) . '"
                                  title="Delete ' . ($isOfficeHour ? 'Office Hour' : 'Subject') . '">';
                    echo '<i class="bi bi-trash"></i>';
                    echo '</button>';

                    echo '</a>';
                    echo '</div>';
                    echo '</div>';
                  }
                  // Check if there are pending requests for this time slot
                  elseif (isset($pendingRequests[$currentDay][$hour]) && !empty($pendingRequests[$currentDay][$hour])) {
                    foreach ($pendingRequests[$currentDay][$hour] as $request) {
                      echo '<div class="position-absolute top-0 start-0 end-0 bottom-0 p-2" 
                                 style="background-color: rgba(255, 193, 7, 0.1); border-left: 3px solid #ffc107;">';

                      echo '<div class="d-flex justify-content-between align-items-start">';
                      echo '<div>';
                      echo '<div class="fw-bold">' .
                        (!empty($request['subject_name']) ? htmlspecialchars($request['subject_name']) : 'Unnamed Subject') .
                        '</div>';
                      echo '<div class="small text-muted">' .
                        (!empty($request['subject_code']) ? htmlspecialchars($request['subject_code']) : 'No code') .
                        '</div>';
                      echo '<div class="small mt-1">';
                      echo '<span class="badge bg-warning">Request by: ' . htmlspecialchars($request['teacher_name']) . '</span>';
                      echo '</div>';
                      echo '</div>';

                      echo '<div class="d-flex">';
                      echo '<a href="/supervisor/requests/approve/' . $request['id'] . '" 
                                class="btn btn-sm btn-success me-1" 
                                title="Approve Request"
                                onclick="return confirm(\'Are you sure you want to approve this request?\');">';
                      echo '<i class="bi bi-check-lg"></i> Approve';
                      echo '</a>';

                      echo '<a href="/supervisor/requests/decline/' . $request['id'] . '" 
                                class="btn btn-sm btn-danger" 
                                title="Decline Request"
                                onclick="return confirm(\'Are you sure you want to decline this request?\');">';
                      echo '<i class="bi bi-x-lg"></i> Decline';
                      echo '</a>';
                      echo '</div>';
                      echo '</div>';

                      echo '</div>';
                    }
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
              <input type="hidden" name="day" id="addSubjectDay" value="<?= $selectedDay ?? 'Monday' ?>">
              <input type="hidden" name="hour" id="addSubjectHour">
              <div class="mb-3">
                <label for="code" class="form-label">Subject Code</label>
                <input type="text" class="form-control" id="code" name="code" required>
              </div>
              <div class="mb-3">
                <label for="name" class="form-label">Subject Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
              </div>
              <div class="mb-3">
                <label for="teacher_id" class="form-label">Assign Teacher</label>
                <select class="form-select" id="teacher_id" name="teacher_id">
                  <option value="">-- Select Teacher (Optional) --</option>
                  <?php foreach ($teachers as $teacher): ?>
                    <option value="<?= $teacher['id'] ?>"><?= htmlspecialchars($teacher['name']) ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="form-text">Assign a teacher to this subject or leave unassigned</div>
              </div>
              <div class="mb-3">
                <label for="class_id" class="form-label">Class</label>
                <select class="form-select" id="class_id" name="class_id" required>
                  <option value="">-- Select Class --</option>
                  <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['name']) ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="form-text">Select the class for this subject</div>
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

        // Pre-select the current day when modal is opened
        addSubjectModal.addEventListener('show.bs.modal', function (event) {
          // Get the button that triggered the modal
          const button = event.relatedTarget;

          // If opened via a time slot button, use that day and hour
          if (button.classList.contains('add-subject-btn')) {
            const day = button.dataset.day;
            const hour = button.dataset.hour;
            addSubjectDayInput.value = day;
            addSubjectHourInput.value = hour;
          } else {
            // Otherwise use the currently selected day
            const activeDay = document.querySelector('.day-btn.active').dataset.day;
            addSubjectDayInput.value = activeDay;
            // Default to first hour if not specified
            addSubjectHourInput.value = '9';
          }
        });

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
            const subjectId = this.dataset.subjectId;
            const subjectName = this.dataset.subjectName;

            if (confirm('Are you sure you want to delete "' + subjectName + '"? This action cannot be undone.')) {
              // Create and submit a form to delete the subject
              const form = document.createElement('form');
              form.method = 'POST';
              form.action = '/supervisor/subjects/delete/' + subjectId;
              document.body.appendChild(form);
              form.submit();
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

    <!-- Requests Tab -->
    <div class="tab-pane fade" id="requests" role="tabpanel" aria-labelledby="requests-tab">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Schedule Requests</h4>
      </div>

      <div class="card department-card">
        <div class="card-body p-0">
          <div class="table-container">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Teacher</th>
                  <th>Subject</th>
                  <th>Day & Time</th>
                  <th>Requested On</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (isset($requests) && is_array($requests) && !empty($requests)): ?>
                  <?php
                  $hasRequests = false;
                  foreach ($requests as $request):
                    $hasRequests = true;
                    $statusClass = '';
                    $statusLabel = '';

                    if ($request['status'] === 'pending') {
                      $statusClass = 'bg-warning text-dark';
                      $statusLabel = 'Pending';
                    } elseif ($request['status'] === 'approved') {
                      $statusClass = 'bg-success';
                      $statusLabel = 'Approved';
                    } elseif ($request['status'] === 'declined') {
                      $statusClass = 'bg-danger';
                      $statusLabel = 'Declined';
                    }
                    ?>
                    <tr>
                      <td><?php echo htmlspecialchars($request['teacher_name']); ?></td>
                      <td>
                        <?php if (!empty($request['subject_name'])): ?>
                          <div><?php echo htmlspecialchars($request['subject_name']); ?></div>
                          <div class="small text-muted"><?php echo htmlspecialchars($request['subject_code'] ?? 'No code'); ?>
                          </div>
                        <?php else: ?>
                          <span class="text-muted">Not specified</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php echo htmlspecialchars($request['day']); ?>
                        <div class="small text-muted">
                          <?php echo sprintf('%02d:00', (int) $request['hour']); ?> -
                          <?php echo sprintf('%02d:00', (int) $request['hour'] + 1); ?>
                        </div>
                      </td>
                      <td><?php echo date('M d, Y', strtotime($request['created_at'])); ?></td>
                      <td>
                        <span class="badge <?php echo $statusClass; ?>">
                          <?php echo $statusLabel; ?>
                        </span>
                      </td>
                      <td>
                        <?php if ($request['status'] === 'pending'): ?>
                          <div class="btn-group" role="group">
                            <a href="/supervisor/requests/approve/<?php echo $request['id']; ?>"
                              class="btn btn-sm btn-success"
                              onclick="return confirm('Are you sure you want to approve this request?');">
                              <i class="bi bi-check-lg"></i> Approve
                            </a>
                            <a href="/supervisor/requests/decline/<?php echo $request['id']; ?>" class="btn btn-sm btn-danger"
                              onclick="return confirm('Are you sure you want to decline this request?');">
                              <i class="bi bi-x-lg"></i> Decline
                            </a>
                          </div>
                        <?php else: ?>
                          <span class="text-muted">No actions available</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach;

                  if (!$hasRequests): ?>
                    <tr>
                      <td colspan="6" class="text-center py-4">No requests for this department.</td>
                    </tr>
                  <?php endif; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center py-4">No requests for this department.</td>
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