<?php require_once dirname(__DIR__, 2) . '/layout.php'; ?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Schedule Requests</h1>
  </div>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
      <?php
      echo $_SESSION['success'];
      unset($_SESSION['success']);
      ?>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
      <?php
      echo $_SESSION['error'];
      unset($_SESSION['error']);
      ?>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Teacher</th>
              <th>Subject</th>
              <th>Day</th>
              <th>Hour</th>
              <th>Class</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($requests)): ?>
              <tr>
                <td colspan="7" class="text-center py-4">
                  <div class="text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2 mb-0">No requests found</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($requests as $request): ?>
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
                  <td>
                    <?php if (!empty($request['class_name'])): ?>
                      <span class="badge bg-info"><?php echo htmlspecialchars($request['class_name']); ?></span>
                    <?php else: ?>
                      <span class="text-muted">Not specified</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php
                    $statusClass = '';
                    $statusLabel = '';
                    switch ($request['status']) {
                      case 'pending':
                        $statusClass = 'bg-warning text-dark';
                        $statusLabel = 'Pending';
                        break;
                      case 'approved':
                        $statusClass = 'bg-success';
                        $statusLabel = 'Approved';
                        break;
                      case 'declined':
                        $statusClass = 'bg-danger';
                        $statusLabel = 'Declined';
                        break;
                    }
                    ?>
                    <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                  </td>
                  <td>
                    <?php if ($request['status'] === 'pending'): ?>
                      <div class="btn-group">
                        <a href="/supervisor/requests/approve/<?php echo $request['id']; ?>" class="btn btn-sm btn-success">
                          <i class="bi bi-check-circle"></i> Approve
                        </a>
                        <a href="/supervisor/requests/decline/<?php echo $request['id']; ?>" class="btn btn-sm btn-danger">
                          <i class="bi bi-x-circle"></i> Decline
                        </a>
                      </div>
                    <?php else: ?>
                      <span class="text-muted">No actions available</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require dirname(dirname(dirname(__DIR__))) . '/Views/layout.php';
?>