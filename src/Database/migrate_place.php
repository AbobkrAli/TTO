<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/migrations/AddPlaceToSubjects.php';

use App\Database\Migrations\AddPlaceToSubjects;

$migration = new AddPlaceToSubjects();
$migration->up();

echo "Migration completed successfully.\n";