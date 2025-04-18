<?php

/**
 * Helper functions
 */

/**
 * Redirect to a specific page
 *
 * @param string $location The location to redirect to
 * @return void
 */
function redirect($location)
{
  header('Location: ' . $location);
  exit;
}

/**
 * Format date to a more readable format
 *
 * @param string $date The date to format
 * @param string $format The format to use
 * @return string The formatted date
 */
function formatDate($date, $format = 'd M Y, H:i')
{
  $dateTime = new DateTime($date);
  return $dateTime->format($format);
}

/**
 * Sanitize input data
 *
 * @param string $data The data to sanitize
 * @return string The sanitized data
 */
function sanitize($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

/**
 * Flash message helper
 * 
 * @param string $name Message name
 * @param string $message Message content
 * @param string $class CSS class for the message
 * @return string The message HTML
 */
function flash($name = '', $message = '', $class = 'alert alert-success')
{
  if (!empty($name)) {
    if (!empty($message) && empty($_SESSION[$name])) {
      $_SESSION[$name] = $message;
      $_SESSION[$name . '_class'] = $class;
    } else if (empty($message) && !empty($_SESSION[$name])) {
      $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : $class;
      echo '<div class="' . $class . '" role="alert">' . $_SESSION[$name] . '</div>';
      unset($_SESSION[$name]);
      unset($_SESSION[$name . '_class']);
    }
  }
}