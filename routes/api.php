<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'app' => config('app.name'),
        'timezone' => config('app.timezone'),
        'timestamp' => now()->toIso8601String(),
    ]);
});
