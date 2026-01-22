<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AirportController;
use App\Http\Controllers\ReportUploadController;

/*
|--------------------------------------------------------------------------
| API Routes - Optimized & Clean
|--------------------------------------------------------------------------
| Semua endpoint dikelompokkan secara logis dengan RESTful pattern
*/

// ============================================================================
// AIRPORT ENDPOINTS
// ============================================================================
Route::prefix('airports')->group(function () {
    
    // 📍 Get all airports (Data untuk Peta)
    Route::get('/', [AirportController::class, 'index']);
    
    // � Debug & Utility Endpoints (HARUS DI ATAS {id} routes!)
    Route::get('/check-db', [AirportController::class, 'checkDatabase']);
    Route::get('/test-coordinates', [AirportController::class, 'testCoordinates']);
    
    // 📊 Get airport statistics (Grafik & Summary)
    Route::get('/{id}/stats', [AirportController::class, 'stats']);
    
    // 🌳 Get airport hierarchy (Cabang Pembantu & Unit)
    Route::get('/{id}/hierarchy', [AirportController::class, 'hierarchy']);
    
    // 📋 Get reports list (Per Kategori/Bulan)
    Route::get('/{id}/reports', [AirportController::class, 'getReports']);
});

// ============================================================================
// REPORT ENDPOINTS
// ============================================================================

// 📄 Get single report detail
Route::get('/reports/{id}', [AirportController::class, 'detailReport']);


// ============================================================================
// FILE UPLOAD & MANAGEMENT
// ============================================================================

// 📤 Upload Excel/CSV reports
Route::post('/reports/upload', [ReportUploadController::class, 'upload'])
    ->middleware('throttle:10,1');

// ⏳ Check upload progress
Route::get('/reports/upload-status/{uploadId}', [ReportUploadController::class, 'uploadStatus']);

// 🗑️ Delete reports by date range
Route::delete('/reports/range', [ReportUploadController::class, 'deleteRange'])
    ->middleware('throttle:20,1');

// ♻️ Restore deleted reports
Route::post('/reports/restore', [ReportUploadController::class, 'restoreRange'])
    ->middleware('throttle:20,1');

// ============================================================================
// UTILITY ENDPOINTS
// ============================================================================

// 🧹 Clear application cache
Route::post('/cache/clear', function () {
    \Illuminate\Support\Facades\Cache::flush();
    return response()->json([
        'success' => true,
        'message' => 'Cache berhasil dihapus',
        'timestamp' => now()->toISOString()
    ]);
});

// 👤 Get authenticated user (untuk future authentication)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});