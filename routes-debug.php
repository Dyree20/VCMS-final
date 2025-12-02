<?php
// Temporary debug route - add this to routes/web.php temporarily

Route::get('/debug/env', function () {
    return response()->json([
        'DB_HOST' => env('DB_HOST'),
        'DB_PORT' => env('DB_PORT'),
        'DB_DATABASE' => env('DB_DATABASE'),
        'DB_USERNAME' => env('DB_USERNAME'),
        'DB_PASSWORD' => env('DB_PASSWORD') ? 'SET (****)' : 'NOT SET',
        'APP_ENV' => env('APP_ENV'),
        'APP_KEY' => env('APP_KEY') ? 'SET' : 'NOT SET',
        'Config DB Host' => config('database.connections.mysql.host'),
        'Config DB Port' => config('database.connections.mysql.port'),
        'Config DB Database' => config('database.connections.mysql.database'),
    ]);
});

Route::get('/debug/test-db', function () {
    try {
        DB::connection('mysql')->getPdo();
        return response()->json(['status' => 'MySQL connection OK']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'MySQL connection FAILED', 'error' => $e->getMessage()], 500);
    }
});
?>
