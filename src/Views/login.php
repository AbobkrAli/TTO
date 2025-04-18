<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - School Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Include error-handling script first -->
  <script src="/js/console-error-fix.js"></script>
  <style>
    body {
      background-color: #f8f9fa;
      height: 100vh;
      display: flex;
      align-items: center;
      padding-top: 40px;
      padding-bottom: 40px;
    }

    .form-signin {
      width: 100%;
      max-width: 330px;
      padding: 15px;
      margin: auto;
    }

    .form-signin .form-floating:focus-within {
      z-index: 2;
    }

    .form-signin input[type="email"] {
      margin-bottom: -1px;
      border-bottom-right-radius: 0;
      border-bottom-left-radius: 0;
    }

    .form-signin input[type="password"] {
      margin-bottom: 10px;
      border-top-left-radius: 0;
      border-top-right-radius: 0;
    }
  </style>
</head>

<body class="text-center">
  <main class="form-signin">
    <form action="/login" method="post">
      <h1 class="h3 mb-3 fw-normal">School Management System</h1>
      <h2 class="h5 mb-3 fw-normal">Please sign in</h2>

      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>

      <div class="form-floating">
        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
        <label for="email">Email address</label>
      </div>
      <div class="form-floating">
        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
        <label for="password">Password</label>
      </div>

      <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
      <p class="mt-3 text-muted">Don't have an account? Just enter your email and a password to auto-register.</p>
      <p class="mt-5 mb-3 text-muted">&copy; <?php echo date('Y'); ?></p>
    </form>

    <!-- Debug link - remove in production -->
    <p class="mt-3"><a href="/force_logout.php" class="text-danger">Force Logout</a> | <a href="/debug_login.php"
        class="text-primary">Debug Tool</a></p>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>