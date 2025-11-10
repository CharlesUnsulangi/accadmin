<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\ClosingService;
use Illuminate\Http\Request;

echo "=== TEST CLOSING CONTROLLER ===\n\n";

try {
    $closingService = app(ClosingService::class);
    
    echo "1. Test calculateMonthly():\n";
    $result = $closingService->calculateMonthly(2024, 10);
    
    echo "   Total COA: " . count($result) . "\n";
    echo "   Sample data: " . json_encode(array_slice($result, 0, 2), JSON_PRETTY_PRINT) . "\n\n";
    
    echo "✅ Service berjalan normal\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
