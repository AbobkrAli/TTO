<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/migrations/CreatePlacesTable.php';

use App\Database\Migrations\CreatePlacesTable;

$migration = new CreatePlacesTable();
$migration->up();

echo "Migration completed successfully.\n";