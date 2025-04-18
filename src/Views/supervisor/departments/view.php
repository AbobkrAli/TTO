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
        <h4>Subject List</h4>
        <a href="/supervisor/departments/<?php echo $department['id']; ?>/subjects/add" class="btn btn-primary">
          <i class="bi bi-plus-circle me-1"></i> Add Subject
        </a>
      </div>

      <div class="card department-card">
        <div class="card-body p-0">
          <div class="table-container">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Code</th>
                  <th>Name</th>
                  <th>Schedule</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($subjects)): ?>
                  <?php foreach ($subjects as $subject): ?>
                    <tr>
                      <td>
                        <span class="subject-code"><?php echo htmlspecialchars($subject['code']); ?></span>
                      </td>
                      <td><?php echo htmlspecialchars($subject['name']); ?></td>
                      <td>
                        <span class="subject-day"><?php echo htmlspecialchars($subject['day_of_week']); ?></span>
                        <span class="subject-time">
                          <?php echo \App\Models\Subject::formatTime($subject['start_time']); ?> -
                          <?php echo \App\Models\Subject::formatTime($subject['end_time']); ?>
                        </span>
                      </td>
                      <td>
                        <a href="/supervisor/subjects/delete/<?php echo $subject['id']; ?>"
                          class="btn btn-sm btn-delete btn-action">
                          <i class="bi bi-trash"></i> Delete
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" class="text-center p-4">
                      No subjects found for this department.
                      <a href="/supervisor/departments/<?php echo $department['id']; ?>/subjects/add">Add a new
                        subject</a>.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

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
                      <td><?php echo htmlspecialchars($teacher['fullname']); ?></td>
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