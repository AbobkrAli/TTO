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

  .time-slot {
    position: relative;
    min-height: 120px;
    padding: 1rem;
    transition: background-color 0.3s;
    min-width: 200px;
    padding: 10px;
    border: 1px solid #e9ecef;
  }

  .time-slot:hover {
    background-color: rgba(0, 0, 0, 0.02);
  }

  .subjects-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 8px;
  }

  .subject-info {
    background-color: #f8f9fa;
    border-radius: 6px;
    padding: 10px;
    border-left: 4px solid #0d6efd;
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .subject-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .subject-title {
    font-weight: 600;
    color: #212529;
    margin-bottom: 5px;
  }

  .subject-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 0.875rem;
  }

  .subject-meta-item {
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .subject-meta-item i {
    font-size: 1rem;
  }

  .add-subject-btn {
    width: 100%;
    margin-top: 8px;
  }

  .time-label {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #2c3e50;
    vertical-align: middle;
    text-align: center;
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
    background-color: #2575fc;
    color: white;
    margin-left: 0.5rem;
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
      <button class="nav-link" id="optional-subjects-tab" data-bs-toggle="tab" data-bs-target="#optional-subjects"
        type="button" role="tab" aria-controls="optional-subjects" aria-selected="false">
        <i class="bi bi-list-check me-1"></i> Optional Subjects
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

      <!-- Class Usage Section -->
      <?php if (!empty($classUsage)): ?>
        <div class="card mb-4">
          <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Most Used Classes</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <?php foreach ($classUsage as $class): ?>
                <div class="col-md-3 mb-3">
                  <div class="card h-100">
                    <div class="card-body">
                      <h6 class="card-title"><?php echo htmlspecialchars($class['name']); ?></h6>
                      <p class="card-text">
                        <span class="badge bg-primary">
                          <?php echo $class['usage_count']; ?> subjects
                        </span>
                      </p>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

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
        <div class="d-flex justify-content-between align-items-center">
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
          <button type="button" class="btn btn-success" onclick="printSchedule('<?= $selectedDay ?? 'Monday' ?>')">
            <i class="bi bi-printer"></i> Print Schedule
          </button>
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
                if (isset($subjects) && is_array($subjects)) {
                  foreach ($subjects as $day => $daySubjects) {
                    foreach ($daySubjects as $hour => $hourSubjects) {
                      if (!isset($scheduledSubjects[$day])) {
                        $scheduledSubjects[$day] = [];
                      }
                      if (!isset($scheduledSubjects[$day][$hour])) {
                        $scheduledSubjects[$day][$hour] = [];
                      }
                      $scheduledSubjects[$day][$hour] = $hourSubjects;
                    }
                  }
                }

                $currentDay = $selectedDay ?? 'Monday';
                foreach ($timeSlots as $hour => $displayTime) {
                  echo '<tr>';
                  echo '<td class="time-label">' . $displayTime . '</td>';
                  echo '<td class="time-slot" data-hour="' . $hour . '">';

                  // Add the flex container for subjects
                  echo '<div class="d-flex flex-column gap-2">';
                  echo '<div class="subjects-container">'; // Add this container for subjects
                
                  if (isset($scheduledSubjects[$currentDay][$hour]) && is_array($scheduledSubjects[$currentDay][$hour])) {
                    foreach ($scheduledSubjects[$currentDay][$hour] as $subject) {
                      $isOfficeHour = isset($subject['is_office_hour']) && $subject['is_office_hour'] == 1;

                      echo '<div class="subject-info mb-2">';
                      if (isset($subject['subject_name'])) {
                        echo '<div class="subject-title">' . htmlspecialchars($subject['subject_name']) . '</div>';
                      }
                      echo '<div class="subject-meta">';

                      // Subject Code
                      if (isset($subject['subject_code'])) {
                        echo '<div class="subject-meta-item">';
                        echo '<i class="bi bi-hash"></i> ';
                        echo htmlspecialchars($subject['subject_code']);
                        echo '</div>';
                      }

                      // Teacher Info
                      if (isset($subject['teacher_name']) && !empty($subject['teacher_name'])) {
                        echo '<div class="subject-meta-item">';
                        echo '<i class="bi bi-person-circle"></i> ';
                        echo htmlspecialchars($subject['teacher_name']);
                        echo '</div>';
                      }

                      // Class Info
                      if (isset($subject['class_name']) && !empty($subject['class_name'])) {
                        echo '<div class="subject-meta-item">';
                        echo '<i class="bi bi-building"></i> ';
                        echo htmlspecialchars($subject['class_name']);
                        echo '</div>';
                      }

                      echo '</div>'; // Close subject-meta
                      echo '<div class="d-flex justify-content-end mt-2">';
                      echo '<a href="/supervisor/subjects/delete/' . $subject['id'] . '" 
                                class="btn btn-sm btn-danger" 
                                onclick="return confirm(\'Are you sure you want to delete this subject?\');">';
                      echo '<i class="bi bi-trash"></i> Delete';
                      echo '</a>';
                      echo '</div>';
                      echo '</div>'; // Close subject-info
                    }
                  }

                  echo '</div>'; // Close subjects-container
                
                  // Add the "Add Subject" button
                  echo '<button type="button" class="btn btn-primary add-subject-btn" 
                            data-bs-toggle="modal" 
                            data-bs-target="#addSubjectModal" 
                            data-day="' . $currentDay . '" 
                            data-hour="' . $hour . '">';
                  echo '<i class="bi bi-plus-circle"></i> Add Subject';
                  echo '</button>';

                  echo '</div>'; // Close flex container
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
            <form id="addSubjectForm">
              <input type="hidden" name="day" id="day" value="<?= $selectedDay ?? 'Monday' ?>">
              <input type="hidden" name="hour" id="hour">
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
                <select class="form-select" id="teacher_id" name="teacher_id" required>
                  <option value="">-- Select Teacher --</option>
                  <?php foreach ($teachers as $teacher): ?>
                    <option value="<?= $teacher['id'] ?>"><?= htmlspecialchars($teacher['name']) ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="form-text">A teacher must be assigned to this subject</div>
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
              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Add Subject</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const addSubjectForm = document.getElementById('addSubjectForm');
        const addSubjectModal = document.getElementById('addSubjectModal');

        // Function to show validation error
        function showValidationError(field, message) {
          const input = document.getElementById(field);
          if (!input) {
            console.error(`Field with id "${field}" not found`);
            return;
          }
          const errorDiv = document.createElement('div');
          errorDiv.className = 'invalid-feedback';
          errorDiv.textContent = message;

          input.classList.add('is-invalid');
          input.parentNode.appendChild(errorDiv);
        }

        // Function to clear validation messages
        function clearValidationMessages() {
          // Remove all validation messages
          document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
          document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        }

        // Function to show message
        function showMessage(type, message) {
          const alertDiv = document.createElement('div');
          alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
          alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          `;

          // Add message to the top of the page
          const container = document.querySelector('.container-fluid');
          if (container) {
            container.insertBefore(alertDiv, container.firstChild);
          } else {
            // Fallback to tab content if container-fluid is not found
            const tabContent = document.querySelector('.tab-content');
            if (tabContent) {
              tabContent.insertBefore(alertDiv, tabContent.firstChild);
            }
          }

          // Auto dismiss after 5 seconds
          setTimeout(() => {
            alertDiv.remove();
          }, 5000);
        }

        // Handle day selection
        document.querySelectorAll('.day-btn').forEach(button => {
          button.addEventListener('click', function () {
            const day = this.dataset.day;
            // Update the URL and reload the page
            const url = new URL(window.location.href);
            url.searchParams.set('day', day);
            window.location.href = url.toString();
          });
        });

        // Handle modal show event
        addSubjectModal.addEventListener('show.bs.modal', function (event) {
          // Get the button that triggered the modal
          const button = event.relatedTarget;

          // If opened via a time slot button, use that day and hour
          if (button.classList.contains('add-subject-btn')) {
            const day = button.dataset.day;
            const hour = button.dataset.hour;
            document.getElementById('day').value = day;
            document.getElementById('hour').value = hour;
          }
        });

        // Add Subject Form Submission
        addSubjectForm.addEventListener('submit', function (e) {
          e.preventDefault();

          // Clear previous validation messages
          clearValidationMessages();

          // Get form data
          const formData = new FormData(this);

          // Basic validation
          let isValid = true;
          const requiredFields = {
            'code': 'Subject Code',
            'name': 'Subject Name',
            'day': 'Day',
            'hour': 'Hour',
            'class_id': 'Class'
          };

          // Check required fields
          for (const [field, label] of Object.entries(requiredFields)) {
            if (!formData.get(field)) {
              showValidationError(field, `${label} is required`);
              isValid = false;
            }
          }

          // Validate hour range
          const hour = parseInt(formData.get('hour'));
          if (hour < 9 || hour > 17) {
            showValidationError('hour', 'Hour must be between 9 and 17');
            isValid = false;
          }

          if (!isValid) {
            return;
          }

          // Submit form
          fetch('/supervisor/departments/<?= $department['id'] ?>/subjects/add', {
            method: 'POST',
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
            .then(response => {
              if (!response.ok) {
                throw new Error('Network response was not ok');
              }
              return response.json();
            })
            .then(data => {
              if (data.success) {
                // Show success message
                showMessage('success', data.message);

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addSubjectModal'));
                modal.hide();

                // Reload the page to ensure everything is in sync
                window.location.reload();
              } else {
                // Show error message
                showMessage('error', data.message);
              }
            })
            .catch(error => {
              console.error('Error:', error);
              showMessage('error', 'An error occurred while adding the subject');
            });
        });

        // Update the print function
        window.printSchedule = function (day) {
          // Create a new window for printing
          const printWindow = window.open('', '_blank');

          // Get the schedule content
          const scheduleContent = document.querySelector('.table-container').cloneNode(true);

          // Remove the "Add Subject" buttons and delete buttons from the print view
          scheduleContent.querySelectorAll('.add-subject-btn').forEach(btn => btn.remove());
          scheduleContent.querySelectorAll('.btn-danger').forEach(btn => btn.remove());
          scheduleContent.querySelectorAll('.btn-close').forEach(btn => btn.remove());
          scheduleContent.querySelectorAll('.request-badge').forEach(badge => badge.remove());
          scheduleContent.querySelectorAll('.btn-outline-danger').forEach(btn => btn.remove());

          // Create the print content
          const printContent = `
            <!DOCTYPE html>
            <html>
            <head>
              <title>${day} Schedule - <?= htmlspecialchars($department['name']) ?></title>
              <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
              <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
              <style>
                body { 
                  padding: 20px; 
                  font-family: Arial, sans-serif;
                }
                .print-header { 
                  text-align: center; 
                  margin-bottom: 20px; 
                  padding-bottom: 10px;
                  border-bottom: 2px solid #333;
                }
                .print-header h2 { 
                  margin: 0; 
                  color: #2c3e50;
                }
                .print-header p { 
                  margin: 5px 0; 
                  color: #666;
                }
                .table { 
                  width: 100%; 
                  margin-bottom: 1rem;
                  border-collapse: collapse;
                }
                .table th, .table td { 
                  padding: 0.75rem;
                  border: 1px solid #dee2e6;
                }
                .table th {
                  background-color: #f8f9fa;
                  font-weight: 600;
                }
                .time-label {
                  background-color: #f8f9fa;
                  font-weight: 600;
                  color: #2c3e50;
                  text-align: center;
                  width: 100px;
                }
                .subject-info { 
                  background-color: #f8f9fa;
                  border-radius: 6px;
                  padding: 10px;
                  margin-bottom: 10px;
                  border-left: 4px solid #0d6efd;
                }
                .subject-title { 
                  font-weight: 600; 
                  margin-bottom: 5px;
                  color: #2c3e50;
                }
                .subject-meta { 
                  font-size: 0.875rem;
                  display: flex;
                  flex-wrap: wrap;
                  gap: 10px;
                }
                .subject-meta-item { 
                  color: #6c757d;
                  display: flex;
                  align-items: center;
                  gap: 4px;
                }
                .subject-meta-item i {
                  font-size: 1rem;
                }
                @media print {
                  .no-print { 
                    display: none; 
                  }
                  body {
                    padding: 0;
                    margin: 0;
                  }
                  .print-header {
                    margin-top: 0;
                  }
                  .table th, .table td {
                    border: 1px solid #dee2e6;
                  }
                  .subject-info {
                    break-inside: avoid;
                  }
                }
              </style>
            </head>
            <body>
              <div class="print-header">
                <h2><?= htmlspecialchars($department['name']) ?></h2>
                <p>Schedule for ${day}</p>
                <p>Printed on ${new Date().toLocaleDateString()}</p>
              </div>
              <div class="table-responsive">
                ${scheduleContent.innerHTML}
              </div>
              <div class="text-center mt-4 no-print">
                <button onclick="window.print()" class="btn btn-primary">Print</button>
                <button onclick="window.close()" class="btn btn-secondary">Close</button>
              </div>
            </body>
            </html>
          `;

          // Write the content to the new window
          printWindow.document.write(printContent);
          printWindow.document.close();
        };
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
                  <th>Role</th>
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
                    <span class="badge <?php echo $teacher['role'] === 'manager' ? 'bg-primary' : 'bg-info'; ?>">
                      <?php echo ucfirst(htmlspecialchars($teacher['role'])); ?>
                    </span>
                  </td>
                  <td>
                    <button onclick="removeTeacher(<?php echo $department['id']; ?>, <?php echo $teacher['id']; ?>)"
                      class="btn btn-sm btn-danger">
                      <i class="bi bi-person-x"></i> Remove
                    </button>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center p-4">No teachers or managers assigned to this department.</td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Optional Subjects Tab -->
    <div class="tab-pane fade" id="optional-subjects" role="tabpanel" aria-labelledby="optional-subjects-tab">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Optional Subjects</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOptionalSubjectModal">
          <i class="bi bi-plus-circle me-1"></i> Add Optional Subject
        </button>
      </div>

      <div class="card department-card">
        <div class="card-body p-0">
          <div class="table-container">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Code</th>
                  <th>Name</th>
                  <th>Created At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (isset($optionalSubjects) && !empty($optionalSubjects)): ?>
                <?php foreach ($optionalSubjects as $subject): ?>
                <tr>
                  <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                  <td><?php echo htmlspecialchars($subject['name']); ?></td>
                  <td><?php echo date('M d, Y', strtotime($subject['created_at'])); ?></td>
                  <td>
                    <button onclick="deleteOptionalSubject(<?php echo $subject['id']; ?>)"
                      class="btn btn-sm btn-danger">
                      <i class="bi bi-trash"></i> Delete
                    </button>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center py-4">No optional subjects found.</td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Optional Subject Modal -->
    <div class="modal fade" id="addOptionalSubjectModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Optional Subject</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form action="/supervisor/departments/<?= $department['id'] ?>/optional-subjects" method="POST">
              <div class="mb-3">
                <label for="subject_code" class="form-label">Subject Code</label>
                <input type="text" class="form-control" id="subject_code" name="subject_code" required>
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

    <!-- Add this to your existing JavaScript section -->
    <script>
      function deleteOptionalSubject(subjectId) {
        if (confirm('Are you sure you want to delete this optional subject?')) {
          fetch(`/supervisor/departments/<?= $department['id'] ?>/optional-subjects/delete/${subjectId}`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = `
                ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              `;
                document.querySelector('.tab-content').insertBefore(alertDiv, document.querySelector('.tab-content').firstChild);

                // Remove the subject's row from the table
                const row = document.querySelector(`button[onclick="deleteOptionalSubject(${subjectId})"]`).closest('tr');
                row.remove();
              } else {
                // Show error message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                alertDiv.innerHTML = `
                ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              `;
                document.querySelector('.tab-content').insertBefore(alertDiv, document.querySelector('.tab-content').firstChild);
              }
            })
            .catch(error => {
              console.error('Error:', error);
              // Show error message
              const alertDiv = document.createElement('div');
              alertDiv.className = 'alert alert-danger alert-dismissible fade show';
              alertDiv.innerHTML = `
              An error occurred while deleting the subject.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
              document.querySelector('.tab-content').insertBefore(alertDiv, document.querySelector('.tab-content').firstChild);
            });
        }
      }
    </script>
  </div>
</div>

<script>
  function removeTeacher(departmentId, teacherId) {
    if (confirm('Are you sure you want to remove this teacher from the department?')) {
      fetch(`/supervisor/departments/${departmentId}/teachers/remove/${teacherId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
            ${data.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          `;
            document.querySelector('.tab-content').insertBefore(alertDiv, document.querySelector('.tab-content').firstChild);

            // Remove the teacher's row from the table
            const row = document.querySelector(`button[onclick="removeTeacher(${departmentId}, ${teacherId})"]`).closest('tr');
            row.remove();
          } else {
            // Show error message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
            ${data.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          `;
            document.querySelector('.tab-content').insertBefore(alertDiv, document.querySelector('.tab-content').firstChild);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          // Show error message
          const alertDiv = document.createElement('div');
          alertDiv.className = 'alert alert-danger alert-dismissible fade show';
          alertDiv.innerHTML = `
          An error occurred while removing the teacher.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
          document.querySelector('.tab-content').insertBefore(alertDiv, document.querySelector('.tab-content').firstChild);
        });
    }
  }
</script>

<?php
$content = ob_get_clean();
require dirname(dirname(dirname(__DIR__))) . '/Views/layout.php';
?>