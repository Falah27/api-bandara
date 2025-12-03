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

// ✅ Route biasa - Max 60 request per menit
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/airports', [AirportController::class, 'index']);
    Route::get('/airports/{id}/stats', [AirportController::class, 'stats']);
    Route::get('/airports/{id}/reports', [AirportController::class, 'getReportsByMonth']);
});

// ✅ Route admin - Max 10 request per menit (anti spam)
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/upload-reports', [ReportUploadController::class, 'upload']);
    Route::post('/delete-reports', [ReportUploadController::class, 'deleteRange']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ✅ DEBUG ROUTE (Hapus setelah testing)
Route::get('/test', function() {
    return response()->json([
        'message' => 'API Laravel berfungsi!',
        'timestamp' => now()
    ]);
});