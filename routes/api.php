<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

// Attendance Routes - Requires Tenant Database Connection & Authentication
Route::middleware(['tenant.db', 'auth:sanctum'])->group(function () {
    Route::get('/attendance/today-status', [AttendanceController::class, 'getTodayStatus']);
    Route::post('/attendance/punch-in', [AttendanceController::class, 'punchIn']);
    Route::post('/attendance/punch-out', [AttendanceController::class, 'punchOut']);
    Route::post('/attendance/break/start', [AttendanceController::class, 'startBreak']);
    Route::post('/attendance/break/end', [AttendanceController::class, 'endBreak']);
    
    // Additional Helper for validation checks
    Route::get('/attendance/check-validation', [AttendanceController::class, 'checkWorklogValidation']);
});


