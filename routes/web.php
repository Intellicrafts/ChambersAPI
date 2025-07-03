<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommandController;

Route::get('/', function () {
    return view('welcome');
});

// Health check route for debugging
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'environment' => app()->environment(),
        'database' => try_connect_db(),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
    ]);
});

// Helper function to check database connection
// function try_connect_db() {
//     try {
//         \DB::connection()->getPdo();
//         return [
//             'connected' => true,
//             'name' => \DB::connection()->getDatabaseName(),
//             'driver' => \DB::connection()->getDriverName(),
//         ];
//     } catch (\Exception $e) {
//         return [
//             'connected' => false,
//             'error' => $e->getMessage(),
//         ];
//     }
// }

// Sanctum CSRF cookie route - this is crucial for your frontend
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
})->middleware('web');

// Command execution routes - protected by auth middleware
Route::middleware('auth:sanctum')->group(function () {
    // JSON API endpoint for command execution
    Route::get('/api/run/{command?}', [CommandController::class, 'executeCommand'])->where('command', '.*');
    
    // Web view for command execution
    Route::get('/run/{command?}', [CommandController::class, 'executeCommandView'])->where('command', '.*');
});