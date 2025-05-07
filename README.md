# TTO

A simple PHP project for school management with different user roles (supervisor and teacher).

## Features

- User authentication (login/logout)
- Automatic user creation if not exists
- Role-based access control (supervisor vs teacher)
- Different dashboards based on user role
- User management (for supervisors)
- Profile management (for teachers)

## Requirements

- PHP 7.4+ (8.0+ recommended)
- MySQL 5.7+ or MariaDB 10.3+
- Composer

## Setup Instructions

1. Clone the repository
```bash
git clone <repository-url>
cd <repository-directory>
```

2. Install dependencies
```bash
composer install
```

3. Set up the database
   
   Option 1: Using the initialization script:
   ```bash
   php initialize_db.php
   ```
   
   Option 2: Using MySQL directly:
   ```bash
   # Create a database named 'php_project' in MySQL
   mysql -u root -p < database.sql
   ```

4. Configure database connection
Edit config/database.php to update your database credentials.

```php
<?php
return [
    'host' => 'localhost',     // Update if needed
    'database' => 'php_project',
    'username' => 'root',      // Update with your username
    'password' => '',          // Update with your password
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
```

5. Run the application using PHP's built-in server
```bash
cd public
php -S localhost:8000
```

6. Open the application in your browser
```
http://localhost:8000
```

## Default Login Credentials

### Supervisor Account
- Email: admin@example.com
- Password: admin123

### Teacher Account
No default teacher account is provided. You can create one by entering any email and password in the login form (auto-creation).

## Project Structure

- `config/` - Configuration files
- `public/` - Publicly accessible files, entry point
- `src/` - Application source code
  - `Controllers/` - Controller classes
  - `Models/` - Data models
  - `Views/` - Templates/view files
- `vendor/` - Composer dependencies

## Troubleshooting

If you encounter issues with autoloading classes:

1. Make sure the directory names match the namespaces (case-sensitive):
   - `src/Controllers` for `App\Controllers` namespace
   - `src/Models` for `App\Models` namespace
   - `src/Views` for views

2. Run composer dump-autoload to regenerate the autoloader:
```bash
composer dump-autoload
```

3. Check your database connection settings in `config/database.php`. 