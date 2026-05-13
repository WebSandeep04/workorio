<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\TaskApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

// Attendance Routes - Requires Tenant Database Connection & Authentication
Route::middleware(['tenant.db', 'auth:sanctum'])->group(function () {
    Route::get('/menus', [AuthController::class, 'getMenus']);
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

    // Worklog Routes
    Route::get('/worklog/form-data', [\App\Http\Controllers\Api\WorklogApiController::class, 'getFormData']);
    Route::get('/worklog/projects/{customerId}', [\App\Http\Controllers\Api\WorklogApiController::class, 'getProjects']);
    Route::get('/worklog/services/{customerId}', [\App\Http\Controllers\Api\WorklogApiController::class, 'getServices']);
    Route::get('/worklog/modules/{serviceId}', [\App\Http\Controllers\Api\WorklogApiController::class, 'getModules']);
    Route::post('/worklog/validate-date', [\App\Http\Controllers\Api\WorklogApiController::class, 'validateDate']);
    Route::post('/worklog/submit', [\App\Http\Controllers\Api\WorklogApiController::class, 'submit']);
    Route::get('/worklog/history', [\App\Http\Controllers\Api\WorklogApiController::class, 'history']);
    Route::delete('/worklog/{id}', [\App\Http\Controllers\Api\WorklogApiController::class, 'destroy']);

    // Task Routes
    Route::get('/tasks/form-data', [TaskApiController::class, 'getFormData']);
    Route::get('/tasks/created', [TaskApiController::class, 'createdTasks']);
    Route::get('/tasks/assigned', [TaskApiController::class, 'myTasks']);
    Route::post('/tasks', [TaskApiController::class, 'store']);
    Route::get('/tasks/{id}', [TaskApiController::class, 'show']);
    Route::post('/tasks/{id}', [TaskApiController::class, 'update']); // Use POST for multipart updates
    Route::delete('/tasks/{id}', [TaskApiController::class, 'destroy']);
    
    // Task Actions
    Route::post('/tasks/{id}/status', [TaskApiController::class, 'updateStatus']);
    Route::post('/tasks/{id}/toggle-done', [TaskApiController::class, 'toggleDone']);
    Route::post('/tasks/{id}/remarks', [TaskApiController::class, 'addRemark']);
    Route::delete('/tasks/{id}/images/{imageId}', [TaskApiController::class, 'deleteImage']);

    // Business Card Routes
    Route::get('/business-cards', [\App\Http\Controllers\Api\BusinessCardController::class, 'index']);
    Route::post('/business-cards', [\App\Http\Controllers\Api\BusinessCardController::class, 'store']);
    Route::get('/business-cards/{id}', [\App\Http\Controllers\Api\BusinessCardController::class, 'show']);
    Route::put('/business-cards/{id}', [\App\Http\Controllers\Api\BusinessCardController::class, 'update']);
    Route::delete('/business-cards/{id}', [\App\Http\Controllers\Api\BusinessCardController::class, 'destroy']);
    // Leads Management Routes
    Route::get('/leads/stats', [\App\Http\Controllers\Api\LeadApiController::class, 'getSummaryStats']);
    Route::get('/leads/status-counts', [\App\Http\Controllers\Api\LeadApiController::class, 'getStatusCounts']);
    Route::get('/leads/all-stats', [\App\Http\Controllers\Api\LeadApiController::class, 'getAllSummaryStats']);
    Route::get('/leads/all-status-counts', [\App\Http\Controllers\Api\LeadApiController::class, 'getAllStatusCounts']);
    Route::get('/leads/assigned-stats', [\App\Http\Controllers\Api\LeadApiController::class, 'getAssignedSummaryStats']);
    Route::get('/leads/assigned-status-counts', [\App\Http\Controllers\Api\LeadApiController::class, 'getAssignedStatusCounts']);
    Route::get('/leads/team-stats', [\App\Http\Controllers\Api\LeadApiController::class, 'getTeamSummaryStats']);
    Route::get('/leads/team-status-counts', [\App\Http\Controllers\Api\LeadApiController::class, 'getTeamStatusCounts']);
    Route::get('/leads/my-leads', [\App\Http\Controllers\Api\LeadApiController::class, 'index']);
    Route::get('/leads/all-leads', [\App\Http\Controllers\Api\LeadApiController::class, 'allLeads']);
    Route::get('/leads/assigned-leads', [\App\Http\Controllers\Api\LeadApiController::class, 'assignedLeads']);
    Route::get('/leads/team-leads', [\App\Http\Controllers\Api\LeadApiController::class, 'teamLeads']);
    Route::get('/leads/followup-leads', [\App\Http\Controllers\Api\LeadApiController::class, 'followupLeads']);
    Route::post('/leads/add', [\App\Http\Controllers\Api\LeadApiController::class, 'store']);
    Route::post('/leads/assign', [\App\Http\Controllers\Api\LeadApiController::class, 'assign']);
    Route::get('/leads/filter-options', [\App\Http\Controllers\Api\LeadApiController::class, 'getFilterOptions']);
    Route::get('/leads/cities/{stateId}', [\App\Http\Controllers\Api\LeadApiController::class, 'getCitiesByState']);
    Route::get('/leads/team-members', [\App\Http\Controllers\Api\LeadApiController::class, 'getTeamMembers']);

    // Prospect Management Routes
    Route::get('/prospects', [\App\Http\Controllers\Api\ProspectusApiController::class, 'index']);
    Route::post('/prospects', [\App\Http\Controllers\Api\ProspectusApiController::class, 'store']);
    Route::get('/prospects/{id}', [\App\Http\Controllers\Api\ProspectusApiController::class, 'show']);
    Route::put('/prospects/{id}', [\App\Http\Controllers\Api\ProspectusApiController::class, 'update']);

    // Remarks Management
    Route::get('/remarks', [\App\Http\Controllers\Api\RemarkApiController::class, 'index']);
    Route::post('/remarks', [\App\Http\Controllers\Api\RemarkApiController::class, 'store']);

    // Lead Details
    Route::get('/leads/{id}', [\App\Http\Controllers\Api\LeadApiController::class, 'show']);

    // User Management Routes
    Route::get('/users', [\App\Http\Controllers\Api\UserApiController::class, 'index']);

    // Employee Location Tracking
    Route::post('/employee/location', [\App\Http\Controllers\Api\EmployeeLocationController::class, 'store']);
});


    // Gemini Business Card Scanner
    use App\Http\Controllers\Api\GeminiController;
    Route::post('/gemini/parse-card', [GeminiController::class, 'parseCard']);
