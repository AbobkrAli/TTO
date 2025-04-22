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
    height: 100%;
    position: relative;
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
    background-color: transparent;
    border: 1px dashed #dee2e6;
    transition: all 0.2s;
  }

  .add-request-btn:hover {
    background-color: #f8f9fa;
    color: #2575fc;
    border-color: #2575fc;
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

<div class="container-fluid py-4">
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
    <div class="day-nav">
      <div class="btn-group d-flex" role="group">
        <?php
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
        foreach ($days as $day) {
          $active = ($selectedDay === $day) ? 'active' : '';
          echo '<a href="/teacher/schedule?day=' . $day . '" class="btn btn-outline-primary ' . $active . '">' . $day . '</a>';
        }
        ?>
      </div>
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
            foreach ($subjects as $subject) {
              $day = $subject['day'];
              $hour = (int) $subject['hour'];
              $scheduledSubjects[$day][$hour] = $subject;
            }

            // Group requests by day and hour
            $pendingRequests = [];
            foreach ($requests as $request) {
              if ($request['status'] === 'pending') {
                $day = $request['day'];
                $hour = (int) $request['hour'];
                $pendingRequests[$day][$hour] = $request;
              }
            }

            foreach ($timeSlots as $hour => $displayTime) {
              echo '<tr>';
              echo '<td class="time-label">' . $displayTime . '</td>';
              echo '<td class="time-slot">';

              // Check if there's a subject at this time slot
              if (isset($scheduledSubjects[$selectedDay][$hour])) {
                $subject = $scheduledSubjects[$selectedDay][$hour];
                echo '<div class="subject-item">';
                echo '<div class="subject-title">' . htmlspecialchars($subject['subject_name']) . '</div>';
                echo '<div class="subject-code">' . htmlspecialchars($subject['subject_code']) . '</div>';

                // Add teacher information if available
                if (!empty($subject['teacher_name'])) {
                  echo '<div class="teacher-info mt-2">';
                  echo '<i class="bi bi-person-circle"></i> ';
                  echo '<span class="text-muted">Teacher: </span>';
                  echo htmlspecialchars($subject['teacher_name']);
                  echo '</div>';
                }

                // Add class information if available
                if (!empty($subject['class_name'])) {
                  echo '<div class="class-info mt-1">';
                  echo '<i class="bi bi-building"></i> ';
                  echo '<span class="text-muted">Class: </span>';
                  echo htmlspecialchars($subject['class_name']);
                  echo '</div>';
                }
                echo '</div>';
              }
              // Check if there's a pending request for this time slot
              elseif (isset($pendingRequests[$selectedDay][$hour])) {
                $request = $pendingRequests[$selectedDay][$hour];
                echo '<div class="subject-item" style="background-color: rgba(255, 193, 7, 0.1); border-left-color: #ffc107;">';
                echo '<div class="subject-title">' .
                  (empty($request['subject_name']) ? 'Time Slot Request' : htmlspecialchars($request['subject_name'])) .
                  '</div>';
                if (!empty($request['subject_code'])) {
                  echo '<div class="subject-code">' . htmlspecialchars($request['subject_code']) . '</div>';
                }
                echo '<span class="request-badge">Pending</span>';
                echo '<a href="/teacher/requests/cancel/' . $request['id'] . '" 
                         class="btn btn-sm btn-outline-danger position-absolute bottom-0 end-0 m-2"
                         onclick="return confirm(\'Are you sure you want to cancel this request?\');">
                         Cancel Request</a>';
                echo '</div>';
              }
              // Empty slot - show request button
              else {
                echo '<button type="button" class="add-request-btn w-100 h-100" 
                              data-bs-toggle="modal" 
                              data-bs-target="#requestModal" 
                              data-day="' . $selectedDay . '" 
                              data-hour="' . $hour . '"
                              data-time="' . $displayTime . '">';
                echo '<i class="bi bi-plus-circle me-2"></i> Request This Slot';
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
              <?php foreach ($requests as $request): ?>
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
        <h5 class="modal-title">Request Schedule Slot</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="/teacher/requests/create" method="POST">
          <input type="hidden" name="day" id="requestDay">
          <input type="hidden" name="hour" id="requestHour">

          <div class="mb-3">
            <label class="form-label">Selected Time:</label>
            <div class="form-control bg-light" id="displayTime" readonly></div>
          </div>

          <div class="mb-3">
            <label for="subject_code" class="form-label">Subject Code</label>
            <input type="text" class="form-control" id="subject_code" name="subject_code" required>
            <div class="form-text">Enter the subject code for your request.</div>
          </div>

          <div class="mb-3">
            <label for="subject_name" class="form-label">Subject Name</label>
            <input type="text" class="form-control" id="subject_name" name="subject_name" required>
            <div class="form-text">Enter the subject name for your request.</div>
          </div>

          <div class="mb-3">
            <label for="class_id" class="form-label">Class</label>
            <select class="form-select" id="class_id" name="class_id" required>
              <option value="">-- Select Class --</option>
              <?php foreach ($classes as $class): ?>
                <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <div class="form-text">Select the class for this subject.</div>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Submit Request</button>
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
  });
</script>

<?php
$content = ob_get_clean();
require_once dirname(dirname(__DIR__)) . '/Views/layout.php';
?>