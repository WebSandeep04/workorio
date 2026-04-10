<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    $columns = Schema::getColumnListing('calling_campaign_calling');
    echo "Columns: " . implode(', ', $columns) . "\n";
    
    $sample = DB::table('calling_campaign_calling')->limit(1)->first();
    echo "Sample: " . json_encode($sample) . "\n";
    
    $count = DB::table('calling_campaign_calling')->count();
    echo "Total Count: " . $count . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
