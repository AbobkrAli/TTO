<?php

namespace App;

class Session
{
  public static function start()
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  public static function set($key, $value)
  {
    self::start();
    $_SESSION[$key] = $value;
  }

  public static function get($key)
  {
    self::start();
    return $_SESSION[$key] ?? null;
  }

  public static function destroy()
  {
    self::start();
    session_unset();
    session_destroy();
  }

  public static function isLoggedIn()
  {
    return self::get('user_id') !== null;
  }

  public static function getUserRole()
  {
    return self::get('user_role');
  }

  public static function setUserData($user)
  {
    self::set('user_id', $user['id']);
    self::set('user_email', $user['email']);
    self::set('user_fullname', $user['fullname']);
    self::set('user_role', $user['role']);
    self::set('user_department', $user['department']);
  }
}