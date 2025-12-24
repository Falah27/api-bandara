<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AirportController;
use App\Http\Controllers\ReportUploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('airports')->group(function () {
    
    Route::get('/check-db', [AirportController::class, 'checkDatabase']);
    Route::get('/debug-distribution', [AirportController::class, 'debugDistribution']);
    Route::get('/move-reports', [AirportController::class, 'moveReports']);

    // 1. Get all airports
    Route::get('/', [AirportController::class, 'index']);
    
    // 2. Get hierarchy
    Route::get('{id}/hierarchy', [AirportController::class, 'hierarchy']);
    
    // 3. Get stats
    Route::get('{id}/stats', [AirportController::class, 'stats']);
    
    // 4. Get reports by month (Lama)
    Route::get('{id}/reports', [AirportController::class, 'getReportsByMonth']);

    // ✅ 5. GET REPORTS GENERAL (BARU - UNTUK DETAIL KATEGORI)
    Route::get('{id}/reports-general', [AirportController::class, 'getReports']);
});

<<<<<<< HEAD
// Debug & Utility Routes
Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Cache::flush();
    return response()->json(['message' => 'Cache cleared successfully']);
});

Route::get('/test-coordinates', [AirportController::class, 'testCoordinates']);

// Upload Routes with Rate Limiting
Route::post('/upload-reports', [ReportUploadController::class, 'upload'])
    ->middleware('throttle:10,1'); // Max 10 uploads per minute

Route::get('/upload-status/{uploadId}', [ReportUploadController::class, 'uploadStatus']);

Route::post('/delete-reports', [ReportUploadController::class, 'deleteRange'])
    ->middleware('throttle:20,1'); // Max 20 deletes per minute

Route::post('/restore-reports', [ReportUploadController::class, 'restoreRange'])
    ->middleware('throttle:20,1'); // Restore soft-deleted reports
=======
// Report Detail Route
Route::get('/reports/{id}', [AirportController::class, 'detailReport']);

// Upload Routes
Route::post('/upload-reports', [ReportUploadController::class, 'upload']);
Route::post('/delete-reports', [ReportUploadController::class, 'deleteRange']);
>>>>>>> 754487c (24/12)

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});