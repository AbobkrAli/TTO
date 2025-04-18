<?php

namespace App\Controllers;

/**
 * Base Controller class
 * This class will be extended by all other controllers
 */
class Controller
{
  /**
   * Load view
   *
   * @param string $view The view file to load
   * @param array $data The data to pass to the view
   * @return void
   */
  protected function view($view, $data = [])
  {
    // Extract data to make it available in the view
    extract($data);

    // Check if view exists
    if (file_exists(dirname(__DIR__) . '/Views/' . $view . '.php')) {
      require_once dirname(__DIR__) . '/Views/' . $view . '.php';
    } else {
      // If view doesn't exist, show error
      die('View does not exist');
    }
  }

  /**
   * Load model
   *
   * @param string $model The model to load
   * @return object The model instance
   */
  protected function model($model)
  {
    // Check if model exists
    $modelClass = 'App\\Models\\' . $model;
    if (class_exists($modelClass)) {
      return new $modelClass();
    } else {
      die('Model does not exist');
    }
  }
}