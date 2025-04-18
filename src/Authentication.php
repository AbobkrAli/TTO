<?php

namespace App;

/**
 * Authentication Class
 * Handles user authentication logic
 */
class Authentication
{
  /**
   * Check if user is logged in
   *
   * @return bool
   */
  public static function isLoggedIn()
  {
    return Session::isLoggedIn();
  }

  /**
   * Check if the logged in user has the specified role
   *
   * @param string $role Role to check
   * @return bool
   */
  public static function hasRole($role)
  {
    if (!self::isLoggedIn()) {
      return false;
    }

    return Session::getUserRole() === $role;
  }

  /**
   * Authenticate user with email and password
   *
   * @param string $email User email
   * @param string $password User password
   * @param \App\Models\User $userModel User model instance
   * @return array|false User data or false on failure
   */
  public static function authenticate($email, $password, $userModel)
  {
    // Validate inputs
    if (empty($email) || empty($password)) {
      return false;
    }

    // Attempt to login
    $user = $userModel->login($email, $password);

    if ($user) {
      // Set session data
      Session::setUserData($user);
      return $user;
    }

    return false;
  }

  /**
   * Log user out
   *
   * @return void
   */
  public static function logout()
  {
    Session::destroy();
  }
}