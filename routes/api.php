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

// Upload Routes
Route::post('/upload-reports', [ReportUploadController::class, 'upload']);
Route::post('/delete-reports', [ReportUploadController::class, 'deleteRange']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});