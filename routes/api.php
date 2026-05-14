<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\TaskApiController;
use App\Http\Controllers\Api\SubscriptionApiController;
use App\Http\Controllers\Api\AttendanceReportApiController;
use App\Http\Controllers\Api\WorklogReportApiController;

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

    // Unlock Attendance (Admin)
    Route::get('/attendance/unlock-logs', [AttendanceController::class, 'fetchUnlockLogs']);
    Route::post('/attendance/unlock-by-date', [AttendanceController::class, 'unlockByDate']);
    Route::post('/attendance/unlock-individual/{id}', [AttendanceController::class, 'unlockIndividual']);

    // Attendance Reports (Admin)
    Route::get('/attendance/report/users', [AttendanceReportApiController::class, 'fetchReportUsers']);
    Route::get('/attendance/report/user-wise', [AttendanceReportApiController::class, 'getUserWiseReport']);
    Route::get('/attendance/report/monthly', [AttendanceReportApiController::class, 'getMonthlySummaryReport']);
    Route::get('/attendance/report/date-wise', [AttendanceReportApiController::class, 'getDateWiseReport']);

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
    Route::get('/leave/approvals', [\App\Http\Controllers\Api\LeaveController::class, 'fetchApprovals']);
    Route::post('/leave/{id}/approve', [\App\Http\Controllers\Api\LeaveController::class, 'approve']);
    Route::post('/leave/{id}/reject', [\App\Http\Controllers\Api\LeaveController::class, 'reject']);
    Route::get('/leave/user-history/{userId}', [\App\Http\Controllers\Api\LeaveController::class, 'userHistory']);

    // Worklog Routes
    Route::get('/worklog/form-data', [\App\Http\Controllers\Api\WorklogApiController::class, 'getFormData']);
    Route::get('/worklog/projects/{customerId}', [\App\Http\Controllers\Api\WorklogApiController::class, 'getProjects']);
    Route::get('/worklog/services/{customerId}', [\App\Http\Controllers\Api\WorklogApiController::class, 'getServices']);
    Route::get('/worklog/modules/{serviceId}', [\App\Http\Controllers\Api\WorklogApiController::class, 'getModules']);
    Route::post('/worklog/validate-date', [\App\Http\Controllers\Api\WorklogApiController::class, 'validateDate']);
    Route::post('/worklog/submit', [\App\Http\Controllers\Api\WorklogApiController::class, 'submit']);
    Route::get('/worklog/history', [\App\Http\Controllers\Api\WorklogApiController::class, 'history']);
    Route::delete('/worklog/{id}', [\App\Http\Controllers\Api\WorklogApiController::class, 'destroy']);
    
    // Worklog Approval Routes
    Route::get('/worklog/pending-approvals', [\App\Http\Controllers\Api\WorklogApiController::class, 'getPendingApprovals']);
    Route::post('/worklog/{id}/approve', [\App\Http\Controllers\Api\WorklogApiController::class, 'approveWorklog']);
    Route::post('/worklog/{id}/reject', [\App\Http\Controllers\Api\WorklogApiController::class, 'rejectWorklog']);
    Route::post('/worklog/approve-group', [\App\Http\Controllers\Api\WorklogApiController::class, 'approveGroup']);
    Route::post('/worklog/reject-group', [\App\Http\Controllers\Api\WorklogApiController::class, 'rejectGroup']);
    Route::post('/worklog/approve-bulk', [\App\Http\Controllers\Api\WorklogApiController::class, 'approveBulk']);
    Route::post('/worklog/reject-bulk', [\App\Http\Controllers\Api\WorklogApiController::class, 'rejectBulk']);

    // Worklog Reports API Group
    Route::get('/worklog/report/filters', [WorklogReportApiController::class, 'fetchFilters']);
    Route::get('/worklog/report/projects', [WorklogReportApiController::class, 'fetchCustomerProjects']);
    Route::get('/worklog/report/general', [WorklogReportApiController::class, 'fetchGeneralReport']);
    Route::get('/worklog/report/user-wise', [WorklogReportApiController::class, 'fetchUserWiseReport']);

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

    // IndiaMART / External Leads API Group
    Route::get('/indiamart/leads', [\App\Http\Controllers\IndiaMartLeadsController::class, 'fetch']);
    Route::get('/indiamart/junk-leads', [\App\Http\Controllers\IndiaMartLeadsController::class, 'junkFetch']);
    Route::get('/indiamart/stats', [\App\Http\Controllers\IndiaMartLeadsController::class, 'summaryStats']);
    Route::get('/indiamart/status-counts', [\App\Http\Controllers\IndiaMartLeadsController::class, 'statusCounts']);
    Route::get('/indiamart/filter-options', [\App\Http\Controllers\IndiaMartLeadsController::class, 'filterOptions']);
    Route::post('/indiamart/assign', [\App\Http\Controllers\IndiaMartLeadsController::class, 'assign']);
    Route::post('/indiamart/junk', [\App\Http\Controllers\IndiaMartLeadsController::class, 'junk']);
    Route::post('/indiamart/junk/restore', [\App\Http\Controllers\IndiaMartLeadsController::class, 'junkRestore']);
    Route::post('/indiamart/junk/delete', [\App\Http\Controllers\IndiaMartLeadsController::class, 'junkDelete']);
    Route::get('/indiamart/leads/{id}/followups', [\App\Http\Controllers\IndiaMartLeadsController::class, 'getFollowups']);
    Route::post('/indiamart/leads/followup', [\App\Http\Controllers\IndiaMartLeadsController::class, 'storeFollowup']);

    // Tele Calling API Group
    Route::get('/calling/all', [\App\Http\Controllers\Api\CallingApiController::class, 'getAllCallings']);
    Route::get('/calling/filter-options', [\App\Http\Controllers\Api\CallingApiController::class, 'getFilterOptions']);
    Route::get('/calling/remarks/meta', [\App\Http\Controllers\Api\CallingApiController::class, 'getRemarksMeta']);
    Route::get('/calling/{id}/remarks', [\App\Http\Controllers\Api\CallingApiController::class, 'getRemarks']);
    Route::post('/calling/{id}/remarks', [\App\Http\Controllers\Api\CallingApiController::class, 'storeRemarkMobile']);
    Route::get('/calling/lists', [\App\Http\Controllers\Api\CallingApiController::class, 'getCallingLists']);
    Route::post('/calling/lists', [\App\Http\Controllers\Api\CallingApiController::class, 'storeCallingList']);
    Route::delete('/calling/lists/{id}', [\App\Http\Controllers\Api\CallingApiController::class, 'deleteCallingList']);
    Route::get('/calling/campaign-filters', [\App\Http\Controllers\Api\CallingApiController::class, 'getCampaignFilterOptions']);
    Route::get('/calling/master', [\App\Http\Controllers\Api\CallingApiController::class, 'getCallingMaster']);
    Route::post('/calling/campaigns', [\App\Http\Controllers\Api\CallingApiController::class, 'createCampaignMobile']);
    Route::post('/calling/lock-leads', [\App\Http\Controllers\Api\CallingApiController::class, 'lockLeadsMobile']);
    Route::get('/calling/my-calls', [\App\Http\Controllers\Api\CallingApiController::class, 'getMyCalls']);
    Route::get('/calling/my-filters', [\App\Http\Controllers\Api\CallingApiController::class, 'getMyCallsFilterOptions']);
    Route::get('/calling/todays-calls', [\App\Http\Controllers\Api\CallingApiController::class, 'getTodaysCalls']);
    Route::get('/calling/todays-filters', [\App\Http\Controllers\Api\CallingApiController::class, 'getTodaysCallsFilterOptions']);
    Route::get('/calling/junk-calls', [\App\Http\Controllers\Api\CallingApiController::class, 'getJunkCalls']);
    Route::get('/calling/junk-filters', [\App\Http\Controllers\Api\CallingApiController::class, 'getJunkCallsFilterOptions']);
    Route::post('/calling/junk/{pivotId}/restore', [\App\Http\Controllers\Api\CallingApiController::class, 'restoreJunkLeadMobile']);
    Route::delete('/calling/junk/{pivotId}', [\App\Http\Controllers\Api\CallingApiController::class, 'deleteJunkLeadMobile']);
    Route::get('/calling/team-calls', [\App\Http\Controllers\Api\CallingApiController::class, 'getTeamCalls']);
    Route::get('/calling/team-filters', [\App\Http\Controllers\Api\CallingApiController::class, 'getTeamCallsFilterOptions']);
    Route::post('/calling/team/reassign', [\App\Http\Controllers\Api\CallingApiController::class, 'reassignTeamCallMobile']);
    Route::get('/calling/assigned-calls', [\App\Http\Controllers\Api\CallingApiController::class, 'getAssignedCalls']);
    Route::get('/calling/assigned-filters', [\App\Http\Controllers\Api\CallingApiController::class, 'getAssignedCallsFilterOptions']);
    Route::post('/calling/assigned/reassign', [\App\Http\Controllers\Api\CallingApiController::class, 'reassignAssignedCallMobile']);
    Route::get('/calling/converted-calls', [\App\Http\Controllers\Api\CallingApiController::class, 'getConvertedCalls']);
    Route::get('/calling/converted-filters', [\App\Http\Controllers\Api\CallingApiController::class, 'getConvertedCallsFilterOptions']);

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

    // Mobile Lead Generation APIs
    Route::get('/leadgen/my-leads', [\App\Http\Controllers\Api\LeadGenApiController::class, 'getMyLeads']);
    Route::get('/leadgen/my-filters', [\App\Http\Controllers\Api\LeadGenApiController::class, 'getFilterOptions']);
    Route::get('/leadgen/my-stats', [\App\Http\Controllers\Api\LeadGenApiController::class, 'getLeadGenStats']);
    Route::post('/leadgen/my/reassign', [\App\Http\Controllers\Api\LeadGenApiController::class, 'reassignLeadGenLead']);
    Route::post('/leadgen/my/store', [\App\Http\Controllers\Api\LeadGenApiController::class, 'storeLeadGenLead']);

    // Mobile Subscription & Renewal APIs
    Route::get('/subscriptions', [SubscriptionApiController::class, 'getSubscriptions']);
    Route::get('/subscriptions/stats', [SubscriptionApiController::class, 'getStats']);
    Route::get('/subscriptions/form-options', [SubscriptionApiController::class, 'getFormOptions']);
    Route::get('/subscriptions/{id}', [SubscriptionApiController::class, 'show']);
    Route::post('/subscriptions', [SubscriptionApiController::class, 'store']);
    Route::post('/subscriptions/{id}/status', [SubscriptionApiController::class, 'updateStatus']);
    Route::get('/subscriptions/{id}/history', [SubscriptionApiController::class, 'getHistory']);

    // Mobile Petty Cash APIs
    Route::get('/petty-cash', [\App\Http\Controllers\Api\PettyCashApiController::class, 'index']);
    Route::get('/petty-cash/stats', [\App\Http\Controllers\Api\PettyCashApiController::class, 'getStats']);
    Route::get('/petty-cash/form-options', [\App\Http\Controllers\Api\PettyCashApiController::class, 'getFormOptions']);
    Route::post('/petty-cash', [\App\Http\Controllers\Api\PettyCashApiController::class, 'store']);
    Route::post('/petty-cash/{id}', [\App\Http\Controllers\Api\PettyCashApiController::class, 'update']); // Use POST for multipart updates
    Route::post('/petty-cash/{id}/toggle-approval', [\App\Http\Controllers\Api\PettyCashApiController::class, 'toggleApproval']);
    Route::post('/petty-cash/approve-bulk', [\App\Http\Controllers\Api\PettyCashApiController::class, 'approveBulk']);
    Route::delete('/petty-cash/{id}', [\App\Http\Controllers\Api\PettyCashApiController::class, 'destroy']);
});


    // Gemini Business Card Scanner
    use App\Http\Controllers\Api\GeminiController;
    Route::post('/gemini/parse-card', [GeminiController::class, 'parseCard']);
