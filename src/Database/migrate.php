<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/migrations/CreateClassesTable.php';

use App\Database\Migrations\CreateClassesTable;

$migration = new CreateClassesTable();
$migration->up();

echo "Migration completed successfully.\n";