<?php require_once __DIR__ . '/../../layout.php'; ?>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Add New Class</h3>
        </div>
        <div class="card-body">
          <form action="/supervisor/classes/add" method="POST">
            <div class="mb-3">
              <label for="name" class="form-label">Class Name</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="d-flex justify-content-between">
              <a href="/supervisor/classes" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Class
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>