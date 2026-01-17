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

    // Attendance History/Summary
    Route::get('/attendance/history', [AttendanceController::class, 'getHistory']);

    // Active Employees Birthdays
    Route::get('/employees/birthdays', [\App\Http\Controllers\Api\EmployeeController::class, 'getActiveEmployeesBirthdayList']);

    // Upcoming Holidays
    Route::get('/holidays/upcoming', [\App\Http\Controllers\Api\HolidayController::class, 'getUpcomingHolidays']);

    // Leave Routes
    Route::get('/leave', [\App\Http\Controllers\Api\LeaveController::class, 'index']);
    Route::post('/leave', [\App\Http\Controllers\Api\LeaveController::class, 'store']);
    Route::put('/leave/{id}', [\App\Http\Controllers\Api\LeaveController::class, 'update']);
    Route::delete('/leave/{id}', [\App\Http\Controllers\Api\LeaveController::class, 'destroy']);
    Route::get('/leave/types', [\App\Http\Controllers\Api\LeaveController::class, 'getLeaveTypes']);
});


