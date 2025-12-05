<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AirportController;
use App\Http\Controllers\ReportUploadController;

Route::middleware('throttle:60,1')->group(function () {
    
    Route::prefix('airports')->group(function () {
        
        // 1. Get all airports (HANYA 28 Cabang Utama)
        Route::get('/', [AirportController::class, 'index']);
        
        // 2. Get hierarchy untuk sidebar dropdown
        Route::get('{id}/hierarchy', [AirportController::class, 'hierarchy']);
        
        // 3. Get stats
        Route::get('{id}/stats', [AirportController::class, 'stats']);
        
        // 4. Get reports by month
        Route::get('{id}/reports', [AirportController::class, 'getReportsByMonth']);
    });
});

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/upload-reports', [ReportUploadController::class, 'upload']);
    Route::post('/delete-reports', [ReportUploadController::class, 'deleteRange']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Testing routes
Route::get('/test', function() {
    return response()->json([
        'message' => 'API v2.0 with Hierarchy!',
        'features' => [
            'hierarchy_system' => true,
            'level_filter' => true,
            '28_cabang_utama' => true,
            'total_airports' => \App\Models\Airport::count()
        ]
    ]);
});