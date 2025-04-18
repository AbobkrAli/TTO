<?php
// Debug script to test login and session functionality

// Include autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Start session
\App\Session::start();

// Show current session state
echo "<h2>Current Session State</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . session_status() . "\n";
echo "Is Logged In: " . (\App\Session::isLoggedIn() ? 'Yes' : 'No') . "\n";

if (\App\Session::isLoggedIn()) {
  echo "Logged in as: " . \App\Session::get('user_fullname') . " (" . \App\Session::get('user_email') . ")\n";
  echo "User ID: " . \App\Session::get('user_id') . "\n";
  echo "Role: " . \App\Session::get('user_role') . "\n";
  echo "Department: " . \App\Session::get('user_department') . "\n";
}

echo "All Session Variables:\n";
print_r($_SESSION);
echo "</pre>";

// Test login functionality if requested
if (isset($_POST['login'])) {
  echo "<h2>Login Test</h2>";
  echo "<pre>";

  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  echo "Attempting to login with:\n";
  echo "Email: $email\n";
  echo "Password: " . (empty($password) ? '[empty]' : '[provided]') . "\n\n";

  // Manually perform login
  $userModel = new \App\Models\User();
  $user = $userModel->login($email, $password);

  if ($user) {
    echo "Login successful!\n";
    echo "User details:\n";
    print_r($user);

    // Set user data in session
    \App\Session::setUserData($user);
    echo "\nSession has been updated with user data.\n";
  } else {
    echo "Login failed - invalid credentials.\n";

    // Try find/create
    echo "\nAttempting to find or create user...\n";
    $fullname = explode('@', $email)[0];
    $user = $userModel->findOrCreate($email, $password, $fullname, 'teacher');

    if ($user) {
      echo "User found or created:\n";
      print_r($user);

      // Set user data in session
      \App\Session::setUserData($user);
      echo "\nSession has been updated with user data.\n";
    } else {
      echo "Failed to find or create user.\n";
    }
  }

  echo "</pre>";
}

// Offer option to destroy session
if (isset($_GET['logout'])) {
  \App\Session::destroy();
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

// HTML Form for testing login
?>

<!DOCTYPE html>
<html>

<head>
  <title>Login Debug Tool</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }

    form {
      margin: 20px 0;
      padding: 20px;
      border: 1px solid #ccc;
    }

    label {
      display: block;
      margin-bottom: 5px;
    }

    input[type="text"],
    input[type="password"] {
      width: 300px;
      padding: 5px;
      margin-bottom: 10px;
    }

    button {
      padding: 10px;
      background: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
    }

    a {
      display: inline-block;
      margin: 10px 0;
    }
  </style>
</head>

<body>
  <h1>Login Debug Tool</h1>

  <form method="post" action="">
    <h3>Test Login</h3>
    <label for="email">Email:</label>
    <input type="text" id="email" name="email" value="john.smith@example.com">

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" value="teacher123">

    <button type="submit" name="login">Test Login</button>
  </form>

  <a href="<?php echo $_SERVER['PHP_SELF']; ?>?logout=1">Destroy Session</a>
  <p>After testing login, try the <a href="/login" target="_blank">actual login page</a>.</p>

  <hr>
  <h3>User Accounts for Testing</h3>
  <pre>
1. Admin (Supervisor):
   Email: admin@example.com
   Password: admin123

2. Mathematics Teacher:
   Email: john.smith@example.com
   Password: teacher123

3. Science Teacher:
   Email: sarah.johnson@example.com
   Password: teacher123

4. English Teacher:
   Email: michael.brown@example.com
   Password: teacher123
    </pre>

  <p><a href="/">Go to Homepage</a> | <a href="/force_logout.php">Force Logout</a></p>
</body>

</html>