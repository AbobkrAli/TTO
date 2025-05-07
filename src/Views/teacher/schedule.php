<?php
$pageTitle = 'Department Schedule';
$activePage = 'schedule';

ob_start();
?>

<style>
  .schedule-card {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 24px;
  }

  .schedule-header {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 10px 10px 0 0;
  }

  .schedule-header h2 {
    margin-bottom: 0.5rem;
    font-weight: 600;
  }

  .schedule-header p {
    opacity: 0.9;
    margin-bottom: 0;
  }

  .day-nav {
    background-color: #f8f9fa;
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
  }

  .time-slot {
    height: 80px;
    position: relative;
    border-bottom: 1px solid #f0f0f0;
    padding: 0.5rem;
  }

  .time-slot-content {
    display: flex;
    gap: 0.5rem;
    height: 100%;
  }

  .time-label {
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
    padding: 0.75rem;
    text-align: center;
    border-right: 1px solid #dee2e6;
  }

  .subject-item {
    background-color: rgba(37, 117, 252, 0.1);
    border-left: 3px solid #2575fc;
    padding: 0.75rem;
    border-radius: 6px;
    height: 100%;
    position: relative;
    flex: 1;
  }

  .subject-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
  }

  .subject-code {
    font-size: 0.85rem;
    color: #6c757d;
  }

  .request-badge {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background-color: #ffc107;
    color: #212529;
    font-size: 0.7rem;
    padding: 0.15rem 0.5rem;
    border-radius: 20px;
  }

  .add-request-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #6c757d;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 0.5rem 1rem;
    transition: all 0.2s;
    text-decoration: none;
    font-weight: 500;
  }

  .add-request-btn:hover {
    background-color: #e9ecef;
    color: #2575fc;
    border-color: #2575fc;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .add-request-btn i {
    margin-right: 0.5rem;
    font-size: 1.1rem;
  }

  .my-requests-card {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .my-requests-header {
    background-color: #f8f9fa;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #dee2e6;
  }

  .request-item {
    padding: 1rem;
    border-bottom: 1px solid #f0f0f0;
  }

  .request-status {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
  }

  .request-status-pending {
    background-color: #ffc107;
    color: #212529;
  }

  .request-status-approved {
    background-color: #28a745;
    color: white;
  }

  .request-status-rejected {
    background-color: #dc3545;
    color: white;
  }
</style>

<script>
  function printSchedule() {
    // Create a new window
    const printWindow = window.open('', '_blank');

    // Get the schedule content
    const scheduleContent = document.querySelector('.table-responsive').cloneNode(true);

    // Remove interactive elements
    scheduleContent.querySelectorAll('.add-request-btn').forEach(btn => btn.remove());
    scheduleContent.querySelectorAll('.btn-close').forEach(btn => btn.remove());
    scheduleContent.querySelectorAll('.request-badge').forEach(badge => badge.remove());
    scheduleContent.querySelectorAll('.btn-outline-danger').forEach(btn => btn.remove());
    scheduleContent.querySelectorAll('.btn-danger').forEach(btn => btn.remove()); // Remove delete buttons

    // Write the content to the new window
    printWindow.document.write(`
      <!DOCTYPE html>
      <html>
      <head>
        <title>Schedule - <?= $selectedDay ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
          body { padding: 20px; }
          .header { text-align: center; margin-bottom: 20px; }
          .header h1 { font-size: 24px; margin-bottom: 5px; }
          .header p { font-size: 14px; color: #666; }
          .table { width: 100%; margin-bottom: 0; }
          .table th { background-color: #f8f9fa; }
          .time-slot { min-height: 60px; }
          .subject-item { 
            background-color: rgba(37, 117, 252, 0.1);
            border-left: 3px solid #2575fc;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 8px;
          }
          .subject-title { font-weight: bold; }
          .subject-meta { font-size: 12px; color: #666; }
          @media print {
            .no-print { display: none; }
            body { padding: 0; }
          }
        </style>
      </head>
      <body>
        <div class="header">
          <h1><?= isset($user['department_name']) ? htmlspecialchars($user['department_name']) : 'Department Schedule' ?></h1>
          <p><?= $selectedDay ?> Schedule</p>
          <p>Printed on: ${new Date().toLocaleDateString()}</p>
        </div>
        ${scheduleContent.outerHTML}
        <div class="text-center mt-4 no-print">
          <button onclick="window.print()" class="btn btn-primary me-2">Print</button>
          <button onclick="window.close()" class="btn btn-secondary">Close</button>
        </div>
      </body>
      </html>
    `);

    printWindow.document.close();
  }
</script>

<div class="container-fluid py-4">
  <?php if (isset($_SESSION['success'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $_SESSION['success'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $_SESSION['error'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <!-- Department Schedule Card -->
  <div class="schedule-card">
    <div class="schedule-header">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h2><i class="bi bi-calendar3"></i> Department Schedule</h2>
          <p>View and request changes to the department schedule</p>
        </div>
        <a href="/teacher/dashboard" class="btn btn-outline-light">
          <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
      </div>
    </div>

    <!-- Day Navigation -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div class="btn-group" role="group">
        <?php
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
        foreach ($days as $day) {
          $active = ($selectedDay === $day) ? 'btn-primary' : 'btn-outline-primary';
          echo '<a href="/teacher/schedule?day=' . $day . '" class="btn ' . $active . '">' . $day . '</a>';
        }
        ?>
      </div>
      <button onclick="printSchedule()" class="btn btn-warning">
        <i class="bi bi-printer"></i> Print Schedule
      </button>
    </div>

    <!-- Schedule Content -->
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered m-0">
          <thead class="bg-light">
            <tr>
              <th style="width: 100px;">Time</th>
              <th>Subject / Request</th>
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
                  // Filter subjects to only include those assigned to the current teacher
                  foreach ($hourSubjects as $subject) {
                    if (isset($subject['teacher_id']) && $subject['teacher_id'] == $_SESSION['user_id']) {
                      $scheduledSubjects[$day][$hour][] = $subject;
                    }
                  }
                }
              }
            }

            // Group requests by day and hour
            $pendingRequests = [];
            if (isset($requests) && is_array($requests)) {
              foreach ($requests as $request) {
                if ($request['status'] === 'pending' && $request['teacher_id'] == $_SESSION['user_id']) {
                  // Check if required keys exist
                  if (!isset($request['day']) || !isset($request['hour'])) {
                    continue; // Skip this request if required keys are missing
                  }

                  $day = $request['day'];
                  $hour = (int) $request['hour'];
                  if (!isset($pendingRequests[$day])) {
                    $pendingRequests[$day] = [];
                  }
                  if (!isset($pendingRequests[$day][$hour])) {
                    $pendingRequests[$day][$hour] = [];
                  }
                  $pendingRequests[$day][$hour][] = $request;
                }
              }
            }

            foreach ($timeSlots as $hour => $displayTime) {
              echo '<tr>';
              echo '<td class="time-label">' . $displayTime . '</td>';
              echo '<td class="time-slot">';

              // Check if there's a subject at this time slot
              if (isset($scheduledSubjects[$selectedDay][$hour]) && is_array($scheduledSubjects[$selectedDay][$hour])) {
                echo '<div class="time-slot-content">'; // Add flex container for side-by-side display
            
                // First show existing subjects
                foreach ($scheduledSubjects[$selectedDay][$hour] as $subject) {
                  echo '<div class="subject-item">'; // Make each subject item grow to fill space
                  echo '<div class="subject-title">' . htmlspecialchars($subject['subject_name']) . '</div>';
                  echo '<div class="subject-meta">';

                  // Subject Code
                  echo '<div class="subject-meta-item">';
                  echo '<i class="bi bi-hash"></i> ';
                  echo htmlspecialchars($subject['subject_code']);
                  echo '</div>';

                  // Class Info
                  if (!empty($subject['class_name'])) {
                    echo '<div class="subject-meta-item">';
                    echo '<i class="bi bi-building"></i> ';
                    echo htmlspecialchars($subject['class_name']);
                    echo '</div>';
                  }

                  echo '</div>'; // Close subject-meta
            
                  // Add delete button
                  echo '<div class="d-flex justify-content-end mt-2">';
                  echo '<a href="/teacher/subjects/delete/' . $subject['id'] . '" 
                            class="btn btn-sm btn-danger" 
                            onclick="return confirm(\'Are you sure you want to delete this subject?\');">';
                  echo '<i class="bi bi-trash"></i> Delete';
                  echo '</a>';
                  echo '</div>';

                  echo '</div>'; // Close subject-item
                }

                // Then show pending request if exists
                if (isset($pendingRequests[$selectedDay][$hour])) {
                  $request = $pendingRequests[$selectedDay][$hour][0];
                  echo '<div class="subject-item" style="background-color: rgba(255, 193, 7, 0.1); border-left-color: #ffc107;">';
                  echo '<div class="subject-title">' .
                    (empty($request['subject_name']) ? 'Time Slot Request' : htmlspecialchars($request['subject_name'])) .
                    '</div>';
                  echo '<div class="subject-meta">';

                  // Subject Code
                  if (!empty($request['subject_code'])) {
                    echo '<div class="subject-meta-item">';
                    echo '<i class="bi bi-hash"></i> ';
                    echo htmlspecialchars($request['subject_code']);
                    echo '</div>';
                  }

                  // Class Info
                  if (!empty($request['class_name'])) {
                    echo '<div class="subject-meta-item">';
                    echo '<i class="bi bi-building"></i> ';
                    echo htmlspecialchars($request['class_name']);
                    echo '</div>';
                  }

                  echo '</div>'; // Close subject-meta
                  echo '<span class="request-badge">Pending</span>';
                  if (isset($request['id'])) {
                    echo '<a href="/teacher/requests/cancel/' . $request['id'] . '" 
                             class="btn btn-sm btn-outline-danger position-absolute bottom-0 end-0 m-2"
                             onclick="return confirm(\'Are you sure you want to cancel this request?\');">
                             Cancel Request</a>';
                  }
                  echo '</div>';
                }

                // Add request button if no pending request
                if (!isset($pendingRequests[$selectedDay][$hour])) {
                  echo '<button type="button" class="add-request-btn" 
                                data-bs-toggle="modal" 
                                data-bs-target="#requestModal" 
                                data-day="' . $selectedDay . '" 
                                data-hour="' . $hour . '"
                                data-time="' . $displayTime . '">';
                  echo '<i class="bi bi-plus-circle"></i> Request Slot';
                  echo '</button>';
                }
                echo '</div>'; // Close time-slot-content
              }
              // Empty slot - show request button or pending request
              else {
                echo '<div class="time-slot-content">';
                if (isset($pendingRequests[$selectedDay][$hour])) {
                  $request = $pendingRequests[$selectedDay][$hour][0];
                  echo '<div class="subject-item" style="background-color: rgba(255, 193, 7, 0.1); border-left-color: #ffc107;">';
                  echo '<div class="subject-title">' .
                    (empty($request['subject_name']) ? 'Time Slot Request' : htmlspecialchars($request['subject_name'])) .
                    '</div>';
                  echo '<div class="subject-meta">';

                  // Subject Code
                  if (!empty($request['subject_code'])) {
                    echo '<div class="subject-meta-item">';
                    echo '<i class="bi bi-hash"></i> ';
                    echo htmlspecialchars($request['subject_code']);
                    echo '</div>';
                  }

                  // Class Info
                  if (!empty($request['class_name'])) {
                    echo '<div class="subject-meta-item">';
                    echo '<i class="bi bi-building"></i> ';
                    echo htmlspecialchars($request['class_name']);
                    echo '</div>';
                  }

                  echo '</div>'; // Close subject-meta
                  echo '<span class="request-badge">Pending</span>';
                  if (isset($request['id'])) {
                    echo '<a href="/teacher/requests/cancel/' . $request['id'] . '" 
                             class="btn btn-sm btn-outline-danger position-absolute bottom-0 end-0 m-2"
                             onclick="return confirm(\'Are you sure you want to cancel this request?\');">
                             Cancel Request</a>';
                  }
                  echo '</div>';
                } else {
                  echo '<button type="button" class="add-request-btn w-100" 
                                data-bs-toggle="modal" 
                                data-bs-target="#requestModal" 
                                data-day="' . $selectedDay . '" 
                                data-hour="' . $hour . '"
                                data-time="' . $displayTime . '">';
                  echo '<i class="bi bi-plus-circle"></i> Request This Slot';
                  echo '</button>';
                }
                echo '</div>';
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

  <!-- My Requests Card -->
  <div class="my-requests-card">
    <div class="my-requests-header">
      <h5 class="mb-0"><i class="bi bi-clock-history"></i> My Schedule Requests</h5>
    </div>
    <div class="card-body">
      <?php if (empty($requests)): ?>
      <p class="text-muted text-center py-3">You haven't made any schedule requests yet.</p>
      <?php else: ?>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Day</th>
              <th>Time</th>
              <th>Subject</th>
              <th>Status</th>
              <th>Requested On</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Get only the last 10 requests
            $recentRequests = array_slice($requests, 0, 10);
            foreach ($recentRequests as $request):
              ?>
            <tr>
              <td><?= htmlspecialchars($request['day']) ?></td>
              <td><?= $timeSlots[$request['hour']] ?></td>
              <td>
                <?php if (!empty($request['subject_name'])): ?>
                <?= htmlspecialchars($request['subject_name']) ?>
                <?php if (!empty($request['subject_code'])): ?>
                <small class="text-muted d-block"><?= htmlspecialchars($request['subject_code']) ?></small>
                <?php endif; ?>
                <?php else: ?>
                <span class="text-muted">Time slot request</span>
                <?php endif; ?>
              </td>
              <td>
                <?php
                $statusClass = 'request-status-pending';
                if ($request['status'] === 'approved') {
                  $statusClass = 'request-status-approved';
                } elseif ($request['status'] === 'rejected') {
                  $statusClass = 'request-status-rejected';
                }
                ?>
                <span class="request-status <?= $statusClass ?>"><?= ucfirst($request['status']) ?></span>
              </td>
              <td><?= date('M d, Y', strtotime($request['created_at'])) ?></td>
              <td>
                <?php if ($request['status'] === 'pending'): ?>
                <a href="/teacher/requests/cancel/<?= $request['id'] ?>" class="btn btn-sm btn-outline-danger"
                  onclick="return confirm('Are you sure you want to cancel this request?');">
                  Cancel
                </a>
                <?php else: ?>
                -
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Request Modal -->
<div class="modal fade" id="requestModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Subject</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="requestForm" action="/teacher/requests/create" method="POST">
          <input type="hidden" name="day" id="requestDay">
          <input type="hidden" name="hour" id="requestHour">

          <div class="mb-3">
            <label for="subject_id" class="form-label">Select Subject</label>
            <select class="form-select" id="subject_id" name="subject_id" required>
              <option value="">-- Select Subject --</option>
              <?php foreach ($optionalSubjects as $subject): ?>
              <option value="<?= $subject['id'] ?>" data-code="<?= htmlspecialchars($subject['subject_code']) ?>"
                data-name="<?= htmlspecialchars($subject['name']) ?>">
                <?= htmlspecialchars($subject['subject_code']) ?> - <?= htmlspecialchars($subject['name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="class_id" class="form-label">Select Class</label>
            <select class="form-select" id="class_id" name="class_id" required>
              <option value="">-- Select Class --</option>
              <?php foreach ($classes as $class): ?>
              <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Subject</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Handle request modal
    const requestModal = document.getElementById('requestModal');
    const requestDayInput = document.getElementById('requestDay');
    const requestHourInput = document.getElementById('requestHour');
    const displayTimeElement = document.getElementById('displayTime');

    // Set up event listeners for request buttons
    document.querySelectorAll('.add-request-btn').forEach(button => {
      button.addEventListener('click', function () {
        const day = this.dataset.day;
        const hour = this.dataset.hour;
        const time = this.dataset.time;

        requestDayInput.value = day;
        requestHourInput.value = hour;
        displayTimeElement.textContent = `${day} at ${time}`;
      });
    });

    // Handle request form submission
    document.getElementById('requestForm').addEventListener('submit', function (e) {
      e.preventDefault();

      const form = this;
      const formData = new FormData(form);

      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
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
            document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.container-fluid').firstChild);

            // Close modal and reload page
            const modal = bootstrap.Modal.getInstance(document.getElementById('requestModal'));
            modal.hide();
            window.location.reload();
          } else {
            // Show error message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
              ${data.message}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.container-fluid').firstChild);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          // Show error message
          const alertDiv = document.createElement('div');
          alertDiv.className = 'alert alert-danger alert-dismissible fade show';
          alertDiv.innerHTML = `
            An error occurred while submitting the request. Please try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          `;
          document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.container-fluid').firstChild);
        });
    });
  });
</script>

<?php
$content = ob_get_clean();
require_once dirname(dirname(__DIR__)) . '/Views/layout.php';
?>