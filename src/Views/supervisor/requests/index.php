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
            <?php foreach ($requests as $request): ?>
              <tr>
                <td><?php echo htmlspecialchars($request['teacher_name']); ?></td>
                <td>
                  <?php echo htmlspecialchars($request['subject_name']); ?>
                  <small class="text-muted d-block"><?php echo htmlspecialchars($request['subject_code']); ?></small>
                </td>
                <td><?php echo htmlspecialchars($request['day']); ?></td>
                <td><?php echo htmlspecialchars($request['hour']); ?></td>
                <td><?php echo htmlspecialchars($request['class_name'] ?? 'Not assigned'); ?></td>
                <td>
                  <span class="badge bg-<?php
                  echo $request['status'] === 'pending' ? 'warning' :
                    ($request['status'] === 'approved' ? 'success' : 'danger');
                  ?>">
                    <?php echo ucfirst($request['status']); ?>
                  </span>
                </td>
                <td>
                  <?php if ($request['status'] === 'pending'): ?>
                    <div class="btn-group">
                      <a href="/supervisor/requests/approve/<?php echo $request['id']; ?>" class="btn btn-sm btn-success">
                        Approve
                      </a>
                      <a href="/supervisor/requests/decline/<?php echo $request['id']; ?>" class="btn btn-sm btn-danger">
                        Decline
                      </a>
                    </div>
                  <?php else: ?>
                    <span class="text-muted">Processed</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>