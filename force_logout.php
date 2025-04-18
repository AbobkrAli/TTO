<?php
// Force logout script
// This will clear the session and any cookies

// Initialize session
session_start();

// Clear all session variables
$_SESSION = array();

// If session has a cookie, destroy that too
if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(
    session_name(),
    '',
    time() - 42000,
    $params["path"],
    $params["domain"],
    $params["secure"],
    $params["httponly"]
  );
}

// Destroy the session
session_destroy();

echo "Session has been completely destroyed. You've been forcefully logged out.<br>";
echo "Please <a href='/login'>go to the login page</a> again.";
?>