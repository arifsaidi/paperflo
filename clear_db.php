<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
$tables = array_map('current', array_map('get_object_vars', DB::select('SHOW TABLES')));
foreach ($tables as $table) {
    echo "Dropping $table...\n";
    Schema::dropIfExists($table);
}
DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "Done.\n";
