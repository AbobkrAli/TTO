<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle ?? 'School Management System'; ?></title>
  <!-- Include error-handling script first -->
  <script src="/js/console-error-fix.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
  <style>
    :root {
      --sidebar-width: 250px;
      --primary-gradient: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      --sidebar-bg: #f8f9fa;
      --sidebar-hover: #e9ecef;
      --sidebar-active: rgba(37, 117, 252, 0.1);
      --sidebar-text: #495057;
      --sidebar-icon: #6a11cb;
      --sidebar-active-border: #2575fc;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
      background-color: #f8f9fa;
    }

    .sidebar {
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 100;
      padding: 48px 0 0;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      background-color: white;
      width: var(--sidebar-width);
      transition: all 0.3s;
    }

    .sidebar-sticky {
      position: relative;
      top: 0;
      height: calc(100vh - 48px);
      padding-top: 0.5rem;
      overflow-x: hidden;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
    }

    .navbar-brand {
      padding: 1rem;
      font-size: 1.1rem;
      font-weight: 600;
      color: white;
      background: var(--primary-gradient);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      width: 100%;
      display: flex;
      align-items: center;
    }

    .navbar-brand i {
      margin-right: 10px;
      font-size: 1.2rem;
    }

    .content {
      margin-left: var(--sidebar-width);
      padding: 20px;
    }

    .nav-item {
      margin: 5px 10px;
    }

    .nav-link {
      display: flex;
      align-items: center;
      padding: 10px 15px;
      color: var(--sidebar-text);
      border-radius: 8px;
      transition: all 0.2s;
      font-weight: 500;
      position: relative;
      border-left: 3px solid transparent;
    }

    .nav-link i {
      margin-right: 10px;
      font-size: 1.1rem;
      color: var(--sidebar-icon);
      transition: all 0.2s;
    }

    .nav-link:hover {
      background-color: var(--sidebar-hover);
      color: #212529;
    }

    .nav-link.active {
      background-color: var(--sidebar-active);
      color: #2575fc;
      border-left-color: var(--sidebar-active-border);
    }

    .nav-link.active i {
      color: #2575fc;
    }

    .sidebar-divider {
      border-top: 1px solid rgba(0, 0, 0, 0.05);
      margin: 1rem 0;
    }

    .nav-flex-wrapper {
      flex-grow: 1;
    }

    .nav-logout {
      margin-top: auto;
      padding-top: 1rem;
      border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .nav-logout .nav-link {
      color: #dc3545;
    }

    .nav-logout .nav-link i {
      color: #dc3545;
    }

    .nav-logout .nav-link:hover {
      background-color: rgba(220, 53, 69, 0.1);
    }

    /* Mobile adjustments */
    @media (max-width: 767.98px) {
      .sidebar {
        width: 100%;
        height: auto;
        padding-top: 0;
      }

      .content {
        margin-left: 0;
        margin-top: 60px;
      }
    }
  </style>
</head>

<body>
  <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#"><i class="bi bi-book"></i> School Management</a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse"
      data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  </header>

  <div class="container-fluid">
    <div class="row">
      <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
        <div class="position-sticky pt-3 sidebar-sticky">
          <div class="nav-flex-wrapper">
            <ul class="nav flex-column">
              <?php if (\App\Session::getUserRole() === 'admin'): ?>
                <li class="nav-item">
                  <a class="nav-link <?php echo $activePage === 'dashboard' ? 'active' : ''; ?>"
                    href="/admin/dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?php echo $activePage === 'users' ? 'active' : ''; ?>" href="/admin/users">
                    <i class="bi bi-people"></i> Users
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?php echo $activePage === 'departments' ? 'active' : ''; ?>" href="/admin/departments">
                    <i class="bi bi-building"></i> Departments
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?php echo $activePage === 'settings' ? 'active' : ''; ?>" href="/admin/settings">
                    <i class="bi bi-gear"></i> Settings
                  </a>
                </li>
                <div class="sidebar-divider"></div>
                <li class="nav-item">
                  <a class="nav-link" href="/admin/profile">
                    <i class="bi bi-person-badge"></i> My Profile
                  </a>
                </li>
              <?php elseif (\App\Session::getUserRole() === 'supervisor'): ?>
                <li class="nav-item">
                  <a class="nav-link <?php echo $activePage === 'dashboard' ? 'active' : ''; ?>"
                    href="/supervisor/dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?php echo $activePage === 'users' ? 'active' : ''; ?>" href="/supervisor/users">
                    <i class="bi bi-people"></i> Manage Users
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?php echo $activePage === 'departments' ? 'active' : ''; ?>" href="/supervisor/departments">
                    <i class="bi bi-building"></i> Departments
                  </a>
                </li>
                <div class="sidebar-divider"></div>
                <li class="nav-item">
                  <a class="nav-link" href="/supervisor/profile">
                    <i class="bi bi-person-badge"></i> My Profile
                  </a>
                </li>
              <?php else: ?>
                <li class="nav-item">
                  <a class="nav-link <?php echo $activePage === 'dashboard' ? 'active' : ''; ?>"
                    href="/teacher/dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?php echo $activePage === 'profile' ? 'active' : ''; ?>" href="/teacher/profile">
                    <i class="bi bi-person"></i> My Profile
                  </a>
                </li>
                <div class="sidebar-divider"></div>
                <li class="nav-item">
                  <a class="nav-link" href="#">
                    <i class="bi bi-calendar3"></i> Schedule
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">
                    <i class="bi bi-journal-richtext"></i> Materials
                  </a>
                </li>
              <?php endif; ?>
            </ul>
          </div>
          <!-- Logout Button -->
          <div class="nav-logout">
            <ul class="nav flex-column">
              <li class="nav-item">
                <a class="nav-link" href="/logout">
                  <i class="bi bi-box-arrow-right"></i> Sign Out
                </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>

      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div
          class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
        </div>

        <?php echo $content ?? ''; ?>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>