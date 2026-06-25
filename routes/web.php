<?php

use App\Http\Controllers\AllDataController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetManagementController;
use App\Http\Controllers\AssetStatusController;
use App\Http\Controllers\AssetTypeController;
use App\Http\Controllers\AssignedCallingController;
use App\Http\Controllers\AssignedLeadsController;
use App\Http\Controllers\AttendanceApprovalController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CalendarClientController;
use App\Http\Controllers\CalendarClientSocialController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CalendarEventClientController;
use App\Http\Controllers\CalendarEventsSetupController;
use App\Http\Controllers\CalendarMissedReasonController;
use App\Http\Controllers\CalendarSocialHandleController;
use App\Http\Controllers\CalendarStatusChecklistController;
use App\Http\Controllers\CalendarStatusController;
use App\Http\Controllers\CallingCampaignController;
use App\Http\Controllers\CallingController;
use App\Http\Controllers\CallingDashboardController;
use App\Http\Controllers\CallingListController;
use App\Http\Controllers\CallingTypeController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ClientEventLinkController;
use App\Http\Controllers\CommonEventController;
use App\Http\Controllers\ContactManagementController;
use App\Http\Controllers\ConvertedCallingController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CriticalPathController;
use App\Http\Controllers\CustomerAnalyticsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerProjectController;
use App\Http\Controllers\CustomerProjectRemarkController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmploymentTypeController;
use App\Http\Controllers\EmailMarketingController;
use App\Http\Controllers\EntryTypeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\FormBuilderController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\IndiaMartLeadsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceFollowupController;
use App\Http\Controllers\JunkCallingController;
use App\Http\Controllers\LateReasonController;
use App\Http\Controllers\LeadGenerationController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\MyCallingController;
use App\Http\Controllers\MyLeadsController;
use App\Http\Controllers\PaymentFollowupController;
use App\Http\Controllers\PettyCashController;
use App\Http\Controllers\PettyOpeningBalanceController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ProspectusController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\QuotationSetupController;
use App\Http\Controllers\RemarkController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoleMasterController;
use App\Http\Controllers\SalesAnalyticsController;
use App\Http\Controllers\SalesBusinessTypeController;
use App\Http\Controllers\SalesCityController;
use App\Http\Controllers\SalesDashboardController;
use App\Http\Controllers\SalesLeadController;
use App\Http\Controllers\SalesLeadSourceController;
use App\Http\Controllers\SalesProductController;
use App\Http\Controllers\SalesStateController;
use App\Http\Controllers\SalesStatusController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionStatusController;
use App\Http\Controllers\SuperAdminAuthController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskStatusController;
use App\Http\Controllers\TeamAnalyticsController;
use App\Http\Controllers\TeamCallingController;
use App\Http\Controllers\TeamLeadsController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TodaysCallingController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UnlockAttendanceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsappTemplateController;
use App\Http\Controllers\WorkflowDependencyController;
use App\Http\Controllers\WorkflowTaskDependencyController;
use App\Http\Controllers\WorkflowTemplateController;
use App\Http\Controllers\WorklogController;
use App\Http\Controllers\WorklogHistoryController;
use App\Http\Controllers\WorklogReportController;
use App\Services\TenantDatabaseService;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// --------------------------------------------------------------------------
// Public and Auth Routes
// --------------------------------------------------------------------------
Route::view('/download', 'download')->name('download');

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

Route::get('/superadmin/login', [SuperAdminAuthController::class, 'showLoginForm'])->name('superadmin.login');
Route::post('/superadmin/login', [SuperAdminAuthController::class, 'login']);
Route::post('/superadmin/logout', [SuperAdminAuthController::class, 'logout'])->name('superadmin.logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendOtp'])->name('password.email');
Route::get('/verify-otp', [AuthController::class, 'showVerifyOtpForm'])->name('password.otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('password.otp.verify');
Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

Route::get('/', function () {
    if (Auth::check() || session()->has('user_id')) {
        return redirect('/dashboard');
    }
    return redirect()->route('login');
});

Route::get('/prospect', function(){
    return View('/prospect');
})->name('prospect');

route::get('/sandeep', function() {
    return view('sandeep');
});

// --------------------------------------------------------------------------
// Super Admin Protected Routes
// --------------------------------------------------------------------------
Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/superadmin/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/superadmin/stats', [SuperAdminController::class, 'dashboardStats'])->name('superadmin.stats');
    Route::get('/superadmin/analytics', [SuperAdminController::class, 'systemAnalytics'])->name('superadmin.analytics');
    Route::get('/superadmin/tenant/{id}/activity', [SuperAdminController::class, 'tenantActivity'])->name('superadmin.tenant.activity');
    Route::get('/superadmin/tenant/{id}/export', [SuperAdminController::class, 'exportTenantData'])->name('superadmin.tenant.export');
    Route::get('/totaltenant',[SuperAdminController::class, 'totaltenant'])->name('totaltenant');
    Route::get('/viewtenant',[SuperAdminController::class, 'viewtenant'])->name('viewtenant');

    Route::get('/tenant', [TenantController::class, 'index'])->name('tenant');
    Route::get('/tenant/fetch', [TenantController::class, 'fetchTenants'])->name('tenant.fetch');
    Route::post('/tenant/store', [TenantController::class, 'store'])->name('tenant.store');
    Route::put('/tenant/{id}', [TenantController::class, 'update'])->name('tenant.update');
    Route::delete('/tenant/{id}', [TenantController::class, 'destroy'])->name('tenant.destroy');
    Route::post('/tenant/{id}/regenerate-code', [TenantController::class, 'regenerateCode'])->name('tenant.regenerate-code');
});

Route::get('/superadmindashboard', function () {
    return view('superadmindashboard');
})->middleware('auth');

// --------------------------------------------------------------------------
// Public Form Builder Routes (No Auth Required)
// --------------------------------------------------------------------------
Route::group(['middleware' => ['web']], function () {
    Route::get('/public/form/{tenant}/{form}', [FormBuilderController::class, 'publicView'])->name('public.form.view');
    Route::post('/public/form/{tenant}/{form}/submit', [FormBuilderController::class, 'publicSubmit'])->name('public.form.submit');
});

// --------------------------------------------------------------------------
// Multi-Tenant Authenticated / Session Protected Routes
// --------------------------------------------------------------------------
Route::middleware(['auth.or.session'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    // Sales Status routes
    Route::get('/status/fetch', [SalesStatusController::class, 'fetchSaleStatus'])->name('status.fetch');
    Route::get('/status', [SalesStatusController::class, 'index'])->name('status');
    Route::put('/status/{id}', [SalesStatusController::class, 'update']);
    Route::delete('/status/{id}', [SalesStatusController::class, 'destroy']);
    Route::post('/status/store', [SalesStatusController::class, 'store'])->name('status.store');
    Route::get('/getStatuses', [SalesStatusController::class, 'getStatuses'])->name('getStatuses');

    // Subscription status routes
    Route::get('/subscription-status/fetch', [SubscriptionStatusController::class, 'fetch'])->name('subscription-status.fetch');
    Route::get('/subscription-status', [SubscriptionStatusController::class, 'index'])->name('subscription-status.index');
    Route::put('/subscription-status/{id}', [SubscriptionStatusController::class, 'update']);
    Route::delete('/subscription-status/{id}', [SubscriptionStatusController::class, 'destroy']);
    Route::post('/subscription-status/store', [SubscriptionStatusController::class, 'store'])->name('subscription-status.store');
    Route::get('/get-subscription-statuses', [SubscriptionStatusController::class, 'getStatuses'])->name('subscription-status.list');

    // Employee & Organization Management
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/list', [EmployeeController::class, 'list'])->name('employees.list');
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::get('/employees/{employee}/documents', [EmployeeController::class, 'documents'])->name('employees.documents');
    Route::post('/employees/{employee}/documents', [EmployeeController::class, 'storeDocument'])->name('employees.documents.store');
    Route::delete('/employees/{employee}/documents/{document}', [EmployeeController::class, 'destroyDocument'])->name('employees.documents.destroy');

    Route::get('/designations', [DesignationController::class, 'index'])->name('designations.index');
    Route::get('/designations/list', [DesignationController::class, 'list'])->name('designations.list');
    Route::post('/designations', [DesignationController::class, 'store'])->name('designations.store');
    Route::put('/designations/{designation}', [DesignationController::class, 'update'])->name('designations.update');
    Route::delete('/designations/{designation}', [DesignationController::class, 'destroy'])->name('designations.destroy');

    Route::get('/employment-types', [EmploymentTypeController::class, 'index'])->name('employment-types.index');
    Route::get('/employment-types/list', [EmploymentTypeController::class, 'list'])->name('employment-types.list');
    Route::post('/employment-types', [EmploymentTypeController::class, 'store'])->name('employment-types.store');
    Route::put('/employment-types/{employment_type}', [EmploymentTypeController::class, 'update'])->name('employment-types.update');
    Route::delete('/employment-types/{employment_type}', [EmploymentTypeController::class, 'destroy'])->name('employment-types.destroy');
    Route::get('/employment-types/{id}/leave-rules', [EmploymentTypeController::class, 'leaveRules'])->name('employment-types.leave-rules');
    Route::post('/employment-types/{id}/leave-rules', [EmploymentTypeController::class, 'saveLeaveRules'])->name('employment-types.save-leave-rules');

    Route::get('/leave-type', [LeaveTypeController::class, 'index'])->name('leave-type.index');
    Route::get('/leave-type/fetch', [LeaveTypeController::class, 'fetch'])->name('leave-type.fetch');
    Route::post('/leave-type', [LeaveTypeController::class, 'store'])->name('leave-type.store');
    Route::put('/leave-type/{id}', [LeaveTypeController::class, 'update'])->name('leave-type.update');
    Route::delete('/leave-type/{id}', [LeaveTypeController::class, 'destroy'])->name('leave-type.destroy');

    Route::get('/countries', [CountryController::class, 'index'])->name('countries.index');
    Route::get('/countries/list', [CountryController::class, 'list'])->name('countries.list');
    Route::post('/countries', [CountryController::class, 'store'])->name('countries.store');
    Route::put('/countries/{country}', [CountryController::class, 'update'])->name('countries.update');
    Route::delete('/countries/{country}', [CountryController::class, 'destroy'])->name('countries.destroy');

    Route::get('/cities/options', [EmployeeController::class, 'cityOptions'])->name('cities.options');

    Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
    Route::get('/branches/list', [BranchController::class, 'list'])->name('branches.list');
    Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
    Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
    Route::delete('/branches/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');

    Route::get('/late-reasons', [LateReasonController::class, 'index'])->name('late-reasons.index');
    Route::get('/late-reasons/list', [LateReasonController::class, 'list'])->name('late-reasons.list');
    Route::post('/late-reasons', [LateReasonController::class, 'store'])->name('late-reasons.store');
    Route::put('/late-reasons/{lateReason}', [LateReasonController::class, 'update'])->name('late-reasons.update');
    Route::delete('/late-reasons/{lateReason}', [LateReasonController::class, 'destroy'])->name('late-reasons.destroy');

    Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::get('/shifts/list', [ShiftController::class, 'list'])->name('shifts.list');
    Route::post('/shifts', [ShiftController::class, 'store'])->name('shifts.store');
    Route::put('/shifts/{shift}', [ShiftController::class, 'update'])->name('shifts.update');
    Route::delete('/shifts/{shift}', [ShiftController::class, 'destroy'])->name('shifts.destroy');

    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::get('/departments/list', [DepartmentController::class, 'list'])->name('departments.list');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    Route::get('/departments/options/all', [DepartmentController::class, 'options'])->name('departments.options');

    Route::get('/places', [PlaceController::class, 'index'])->name('places.index');
    Route::get('/places/list', [PlaceController::class, 'list'])->name('places.list');
    Route::post('/places', [PlaceController::class, 'store'])->name('places.store');
    Route::put('/places/{place}', [PlaceController::class, 'update'])->name('places.update');
    Route::delete('/places/{place}', [PlaceController::class, 'destroy'])->name('places.destroy');

    // Workflow Management
    Route::get('/workflow/critical-path', [CriticalPathController::class, 'index'])->name('critical-path.index');
    Route::get('/workflow/templates', [WorkflowTemplateController::class, 'index'])->name('workflow-templates.index');
    Route::get('/workflow/templates/list', [WorkflowTemplateController::class, 'fetch'])->name('workflow-templates.fetch');
    Route::get('/workflow/templates/users', [WorkflowTemplateController::class, 'users'])->name('workflow-templates.users');
    Route::post('/workflow/templates', [WorkflowTemplateController::class, 'store'])->name('workflow-templates.store');
    Route::post('/workflow/templates/{workflowTemplate}/duplicate', [WorkflowTemplateController::class, 'duplicate'])->name('workflow-templates.duplicate');
    Route::get('/workflow/templates/{workflowTemplate}/tasks', [WorkflowTemplateController::class, 'show'])->name('workflow-templates.tasks');
    Route::put('/workflow/templates/{workflowTemplate}', [WorkflowTemplateController::class, 'update'])->name('workflow-templates.update');
    Route::delete('/workflow/templates/{workflowTemplate}', [WorkflowTemplateController::class, 'destroy'])->name('workflow-templates.destroy');
    Route::get('/workflow/dependencies', [WorkflowDependencyController::class, 'index'])->name('workflow-dependencies.index');
    Route::get('/workflow/dependencies/list', [WorkflowDependencyController::class, 'fetch'])->name('workflow-dependencies.fetch');
    Route::post('/workflow/dependencies', [WorkflowDependencyController::class, 'store'])->name('workflow-dependencies.store');
    Route::put('/workflow/dependencies/{dependency}', [WorkflowDependencyController::class, 'update'])->name('workflow-dependencies.update');
    Route::delete('/workflow/dependencies/{dependency}', [WorkflowDependencyController::class, 'destroy'])->name('workflow-dependencies.destroy');
    Route::get('/workflow/dependency-types', [WorkflowTaskDependencyController::class, 'types'])->name('workflow-dependency-types.index');
    Route::get('/workflow/templates/{workflowTemplate}/task-dependencies', [WorkflowTaskDependencyController::class, 'index'])->name('workflow-task-dependencies.index');
    Route::post('/workflow/templates/{workflowTemplate}/task-dependencies', [WorkflowTaskDependencyController::class, 'store'])->name('workflow-task-dependencies.store');
    Route::put('/workflow/templates/{workflowTemplate}/task-dependencies/{dependency}', [WorkflowTaskDependencyController::class, 'update'])->name('workflow-task-dependencies.update');
    Route::delete('/workflow/templates/{workflowTemplate}/task-dependencies/{dependency}', [WorkflowTaskDependencyController::class, 'destroy'])->name('workflow-task-dependencies.destroy');

    // Calendar & Checklist
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
    Route::get('/calendar/event/{id}/details', [CalendarController::class, 'eventDetails'])->name('calendar.event.details');
    Route::get('/calendar/grid', [CalendarController::class, 'grid'])->name('calendar.grid');
    Route::get('/calendar/date/{date}/handles', [CalendarController::class, 'dateHandles'])->name('calendar.date.handles');
    Route::post('/calendar/date/handle/toggle', [CalendarController::class, 'toggleDateHandle'])->name('calendar.date.handle.toggle');
    Route::post('/calendar/date/client/status', [CalendarController::class, 'saveDateClientStatus'])->name('calendar.date.client.status');
    Route::get('/calendar/status/{id}/checklists', [CalendarController::class, 'statusChecklists'])->name('calendar.status.checklists');

    Route::get('/checklist', function(){ return view('checklist.index'); })->name('checklist.index');
    Route::get('/checklist/fetch', [ChecklistController::class, 'fetch'])->name('checklist.fetch');
    Route::post('/checklist/store', [ChecklistController::class, 'store'])->name('checklist.store');
    Route::put('/checklist/{id}', [ChecklistController::class, 'update'])->name('checklist.update');
    Route::delete('/checklist/{id}', [ChecklistController::class, 'destroy'])->name('checklist.destroy');

    Route::get('/checklist/options/fetch', [ChecklistController::class, 'fetchOptions'])->name('checklist.options.fetch');
    Route::post('/checklist/options/store', [ChecklistController::class, 'storeOption'])->name('checklist.options.store');
    Route::put('/checklist/options/{id}', [ChecklistController::class, 'updateOption'])->name('checklist.options.update');
    Route::delete('/checklist/options/{id}', [ChecklistController::class, 'destroyOption'])->name('checklist.options.destroy');

    Route::get('/calendar/status-checklist', [CalendarStatusChecklistController::class, 'index'])->name('calendar-status-checklist.index');
    Route::get('/calendar/status-checklist/fetch', [CalendarStatusChecklistController::class, 'fetch'])->name('calendar-status-checklist.fetch');
    Route::post('/calendar/status-checklist/update', [CalendarStatusChecklistController::class, 'updateRelationships'])->name('calendar-status-checklist.update');

    Route::get('/calendar/client-event-links', [ClientEventLinkController::class, 'clientsView'])->name('calendar-client-event.links');
    Route::get('/calendar/client-event-links/clients', [ClientEventLinkController::class, 'fetchClients'])->name('calendar-client-event.clients');
    Route::get('/calendar/client-event-links/{clientId}', [ClientEventLinkController::class, 'eventsView'])->name('calendar-client-event.events.view');
    Route::get('/calendar/client-event-links/{clientId}/events', [ClientEventLinkController::class, 'fetchEvents'])->name('calendar-client-event.events');
    Route::post('/calendar/client-event-links/{clientId}/save', [ClientEventLinkController::class, 'saveLinks'])->name('calendar-client-event.save');
    Route::get('/calendar/client-event-links/{clientId}/common-events', [ClientEventLinkController::class, 'fetchCommonEvents'])->name('calendar-client-event.common.fetch');
    Route::post('/calendar/client-event-links/{clientId}/common-events/save', [ClientEventLinkController::class, 'saveCommonEvents'])->name('calendar-client-event.common.save');

    Route::get('/calendar/common-events', [CommonEventController::class, 'index'])->name('common-events.index');
    Route::get('/calendar/common-events/fetch', [CommonEventController::class, 'fetch'])->name('common-events.fetch');
    Route::post('/calendar/common-events/store', [CommonEventController::class, 'store'])->name('common-events.store');
    Route::put('/calendar/common-events/{id}', [CommonEventController::class, 'update'])->name('common-events.update');
    Route::delete('/calendar/common-events/{id}', [CommonEventController::class, 'destroy'])->name('common-events.destroy');

    Route::get('/calendar/clients', [CalendarClientController::class, 'index'])->name('calendar-clients.index');
    Route::get('/calendar/clients/fetch', [CalendarClientController::class, 'fetch'])->name('calendar-clients.fetch');
    Route::post('/calendar/clients/store', [CalendarClientController::class, 'store'])->name('calendar-clients.store');
    Route::put('/calendar/clients/{id}', [CalendarClientController::class, 'update'])->name('calendar-clients.update');
    Route::delete('/calendar/clients/{id}', [CalendarClientController::class, 'destroy'])->name('calendar-clients.destroy');

    Route::get('/calendar/status', [CalendarStatusController::class, 'index'])->name('calendar-status.index');
    Route::get('/calendar/status/fetch', [CalendarStatusController::class, 'fetch'])->name('calendar-status.fetch');
    Route::post('/calendar/status/store', [CalendarStatusController::class, 'store'])->name('calendar-status.store');
    Route::put('/calendar/status/{id}', [CalendarStatusController::class, 'update'])->name('calendar-status.update');
    Route::delete('/calendar/status/{id}', [CalendarStatusController::class, 'destroy'])->name('calendar-status.destroy');

    Route::get('/calendar/social', [CalendarSocialHandleController::class, 'index'])->name('calendar-social.index');
    Route::get('/calendar/social/fetch', [CalendarSocialHandleController::class, 'fetch'])->name('calendar-social.fetch');
    Route::post('/calendar/social/store', [CalendarSocialHandleController::class, 'store'])->name('calendar-social.store');
    Route::put('/calendar/social/{id}', [CalendarSocialHandleController::class, 'update'])->name('calendar-social.update');
    Route::delete('/calendar/social/{id}', [CalendarSocialHandleController::class, 'destroy'])->name('calendar-social.destroy');

    Route::get('/calendar/client-social', [CalendarClientSocialController::class, 'index'])->name('calendar-client-social.index');
    Route::get('/calendar/client-social/fetch', [CalendarClientSocialController::class, 'fetch'])->name('calendar-client-social.fetch');
    Route::post('/calendar/client-social/update', [CalendarClientSocialController::class, 'updateRelationships'])->name('calendar-client-social.update');

    Route::get('/calendar/events-setup', [CalendarEventsSetupController::class, 'index'])->name('calendar-events.index');
    Route::get('/calendar/events-setup/fetch', [CalendarEventsSetupController::class, 'fetch'])->name('calendar-events.fetch');
    Route::post('/calendar/events-setup/store', [CalendarEventsSetupController::class, 'store'])->name('calendar-events.store');
    Route::put('/calendar/events-setup/{id}', [CalendarEventsSetupController::class, 'update'])->name('calendar-events.update');
    Route::delete('/calendar/events-setup/{id}', [CalendarEventsSetupController::class, 'destroy'])->name('calendar-events.destroy');

    Route::get('/calendar/missed-reasons', [CalendarMissedReasonController::class, 'index'])->name('calendar-missed-reasons.index');
    Route::get('/calendar/missed-reasons/fetch', [CalendarMissedReasonController::class, 'fetch'])->name('calendar-missed-reasons.fetch');
    Route::post('/calendar/missed-reasons/store', [CalendarMissedReasonController::class, 'store'])->name('calendar-missed-reasons.store');
    Route::put('/calendar/missed-reasons/{id}', [CalendarMissedReasonController::class, 'update'])->name('calendar-missed-reasons.update');
    Route::delete('/calendar/missed-reasons/{id}', [CalendarMissedReasonController::class, 'destroy'])->name('calendar-missed-reasons.destroy');

    // Sales Lead Sources
    Route::get('/source/fetch', [SalesLeadSourceController::class, 'fetchSalesources'])->name('source.fetch');
    Route::get('/source', [SalesLeadSourceController::class, 'index'])->name('source');
    Route::put('/source/{id}', [SalesLeadSourceController::class, 'update']);
    Route::delete('/source/{id}', [SalesLeadSourceController::class, 'destroy']);
    Route::post('/source/store', [SalesLeadSourceController::class, 'store'])->name('source.store');
    Route::get('/getsource', [SalesLeadSourceController::class, 'getsource'])->name('getsource');

    // Sales Products
    Route::get('/product/fetch', [SalesProductController::class, 'fetchSalesProducts'])->name('product.fetch');
    Route::get('/product', [SalesProductController::class, 'index'])->name('product');
    Route::put('/product/{id}', [SalesProductController::class, 'update']);
    Route::delete('/product/{id}', [SalesProductController::class, 'destroy']);
    Route::post('/product/store', [SalesProductController::class, 'store'])->name('product.store');
    Route::get('/getproduct', [SalesProductController::class, 'getproduct'])->name('getproduct');

    // Sales Business Type
    Route::get('/business/fetch', [SalesBusinessTypeController::class, 'fetchSalesBusiness'])->name('business.fetch');
    Route::get('/business', [SalesBusinessTypeController::class, 'index'])->name('business');
    Route::put('/business/{id}', [SalesBusinessTypeController::class, 'update']);
    Route::delete('/business/{id}', [SalesBusinessTypeController::class, 'destroy']);
    Route::post('/business/store', [SalesBusinessTypeController::class, 'store'])->name('business.store');
    Route::get('/getbusiness', [SalesBusinessTypeController::class, 'getbusiness'])->name('getbusiness');

    // Sales Geography
    Route::get('/state/fetch', [SalesStateController::class, 'fetchSalesStates'])->name('state.fetch');
    Route::get('/state', [SalesStateController::class, 'index'])->name('state');
    Route::put('/state/{id}', [SalesStateController::class, 'update']);
    Route::delete('/state/{id}', [SalesStateController::class, 'destroy']);
    Route::post('/state/store', [SalesStateController::class, 'store'])->name('state.store');

    Route::get('/city/fetch', [SalesCityController::class, 'fetchSalesCities'])->name('city.fetch');
    Route::get('/city', [SalesCityController::class, 'index'])->name('city');
    Route::put('/city/{id}', [SalesCityController::class, 'update']);
    Route::delete('/city/{id}', [SalesCityController::class, 'destroy']);
    Route::post('/city/store', [SalesCityController::class, 'store'])->name('city.store');
    Route::get('/city/{state_id}', [SalesCityController::class, 'getCities'])->name('get.city');
    Route::get('/allcity', [SalesCityController::class, 'allcity'])->name('allcity');

    // Prospectus
    Route::post('/prospectus', [ProspectusController::class, 'store']);
    Route::get('/getProspectus', [ProspectusController::class, 'getProspectus'])->name('getProspectus');
    Route::get('/fillprospectus/{id}', [ProspectusController::class, 'fillprospectus'])->name('fillprospectus');
    Route::post('/updateprospectus/{id}', [ProspectusController::class, 'update'])->name('updateprospectus');

    // Sales Lead
    Route::get('/lead', [SalesLeadController::class, 'index'])->name('lead');
    Route::Post('/savelead', [SalesLeadController::class, 'store'])->name('savelead');

    // Sales Followup & Quotation
    Route::get('/followup', [FollowupController::class, 'index'])->name('followup');
    Route::get('/followup/summary-stats', [FollowupController::class, 'getSummaryStats'])->name('followup.summary-stats');
    Route::get('/followup/status-counts', [FollowupController::class, 'getStatusCounts'])->name('followup.status-counts');
    Route::post('/filter', [FollowupController::class, 'filter'])->name('filter');
    Route::post('/filterdate', [FollowupController::class, 'filterdate'])->name('filterdate');
    Route::get('/sales-records', [FollowupController::class, 'getSalesRecords'])->name('sales.records');
    Route::get('/followup/search', [FollowupController::class, 'search'])->name('search');

    Route::get('/quotation', [QuotationController::class, 'index'])->name('quotation');
    Route::get('/quotation/create', [QuotationController::class, 'create'])->name('quotation.create');
    Route::get('/quotation/customers', [QuotationController::class, 'getCustomers'])->name('quotation.customers');
    Route::get('/quotation/prospects', [QuotationController::class, 'getProspects'])->name('quotation.prospects');
    Route::get('/quotation/products', [QuotationController::class, 'getSalesProducts'])->name('quotation.products');
    Route::get('/quotation/users', [QuotationController::class, 'getUsers'])->name('quotation.users');
    Route::get('/quotation/generate-number', [QuotationController::class, 'generateQuotationNumber'])->name('quotation.generate-number');
    Route::get('/quotation/current-date', [QuotationController::class, 'getCurrentDate'])->name('quotation.current-date');
    Route::post('/quotation/store', [QuotationController::class, 'store'])->name('quotation.store');
    Route::get('/quotation/list', [QuotationController::class, 'list'])->name('quotation.list');
    Route::get('/quotation/show/{number}', [QuotationController::class, 'showByNumber'])->name('quotation.show');
    Route::get('/quotation/latest', [QuotationController::class, 'latestForEntity'])->name('quotation.latest');
    Route::get('/quotation/{id}/revisions', [QuotationController::class, 'revisions'])->name('quotation.revisions');
    Route::get('/quotation/payment-terms', [QuotationController::class, 'getPaymentTerms'])->name('quotation.payment-terms');
    Route::get('/quotation/{id}/download', [QuotationController::class, 'download'])->name('quotation.download');
    Route::get('/quotation/revision/{id}/preview', [QuotationController::class, 'previewRevision'])->name('quotation.revision.preview');

    Route::get('/quotation/setup', [QuotationSetupController::class, 'index'])->name('quotation.setup');
    Route::get('/quotation/setup/fetch', [QuotationSetupController::class, 'fetch'])->name('quotation.setup.fetch');
    Route::post('/quotation/setup/store', [QuotationSetupController::class, 'store'])->name('quotation.setup.store');
    Route::get('/quotation/setup/get', [QuotationSetupController::class, 'getSettings'])->name('quotation.setup.get');

    Route::get('/reports/worklog', [WorklogReportController::class, 'index'])->name('reports.worklog');
    Route::get('/reports/worklog/fetch', [WorklogReportController::class, 'fetchWorklogs'])->name('reports.worklog.fetch');
    Route::get('/reports/user-worklog', [WorklogReportController::class, 'userReport'])->name('reports.user-worklog');
    Route::get('/reports/user-worklog/fetch', [WorklogReportController::class, 'fetchUserWorklogs']);
    Route::get('/reports/user-worklog/customers', [WorklogReportController::class, 'fetchCustomersForUser']);

    Route::get('/remark', [RemarkController::class, 'index'])->name('remark');
    Route::post('/saveremark', [RemarkController::class, 'store'])->name('saveremark');

    // Sales Dashboard (Table Data)
    Route::get('/todayfollowups', [SalesDashboardController::class, 'todayfollowups'])->name('todayfollowups');
    Route::get('/allleads', [SalesDashboardController::class, 'allleads'])->name('allleads');
    Route::get('/underprocess', [SalesDashboardController::class, 'underprocess'])->name('underprocess');
    Route::get('/todaycompleted', [SalesDashboardController::class, 'todaycompleted'])->name('todaycompleted');
    Route::get('/todaypending', [SalesDashboardController::class, 'todaypending'])->name('todaypending');
    Route::get('/todaynew', [SalesDashboardController::class, 'todaynew'])->name('todaynew');
    Route::get('/estimateticket', [SalesDashboardController::class, 'estimateticket'])->name('estimateticket');
    Route::get('/orders/summary', [SalesDashboardController::class, 'ordersSummary'])->name('orders.summary');
    Route::get('/calling/summary', [SalesDashboardController::class, 'callingSummary'])->name('calling.summary');
    Route::get('/worklog/summary', [SalesDashboardController::class, 'worklogSummary'])->name('worklog.summary');
    Route::get('/calendar/summary', [SalesDashboardController::class, 'calendarSummary'])->name('calendar.summary');
    Route::get('/attendance/summary', [SalesDashboardController::class, 'attendanceSummary'])->name('attendance.summary');
    Route::get('/lead-source/data', [SalesDashboardController::class, 'leadSourceData'])->name('lead-source.data');
    Route::get('/petty-cash/summary', [SalesDashboardController::class, 'pettyCashSummary'])->name('petty-cash.summary');
    Route::get('/due-payments/summary', [SalesDashboardController::class, 'duePaymentsSummary'])->name('due-payments.summary');
    Route::get('/due-subscriptions/summary', [SalesDashboardController::class, 'dueSubscriptionsSummary'])->name('due-subscriptions.summary');
    Route::get('/pending-approvals/summary', [SalesDashboardController::class, 'pendingApprovalsSummary'])->name('pending-approvals.summary');
    Route::get('/holidays/summary', [SalesDashboardController::class, 'holidaysSummary'])->name('holidays.summary');
    Route::get('/celebrations/summary', [SalesDashboardController::class, 'celebrationsSummary'])->name('celebrations.summary');
    Route::get('/upcoming-leaves/summary', [SalesDashboardController::class, 'upcomingLeavesSummary'])->name('upcoming-leaves.summary');

    Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
    Route::get('/holidays/fetch', [HolidayController::class, 'fetchHolidays'])->name('holidays.fetch');
    Route::post('/holidays', [HolidayController::class, 'store'])->name('holidays.store');
    Route::put('/holidays/{id}', [HolidayController::class, 'update'])->name('holidays.update');
    Route::delete('/holidays/{id}', [HolidayController::class, 'destroy'])->name('holidays.destroy');

    Route::get('/piedata', [SalesDashboardController::class, 'piedata'])->name('piedata');
    Route::get('/bardata', [SalesDashboardController::class, 'bardata'])->name('bardata');
    Route::get('/user-tasks', [SalesDashboardController::class, 'userTasks'])->name('user-tasks');
    Route::get('/todayfollowupstable', [SalesDashboardController::class, 'todayfollowupstable'])->name('todayfollowupstable');
    Route::get('/underprocesstable', [SalesDashboardController::class, 'underprocesstable'])->name('underprocesstable');
    Route::get('/todaycompletedtable', [SalesDashboardController::class, 'todaycompletedtable'])->name('todaycompletedtable');
    Route::get('/todaypendingtable', [SalesDashboardController::class, 'todaypendingtable'])->name('todaypendingtable');
    Route::get('/todaynewtable', [SalesDashboardController::class, 'todaynewtable'])->name('todaynewtable');

    Route::get('/todayfollowupstabledata', [SalesDashboardController::class, 'todayfollowupstabledata'])->name('todayfollowupstabledata');
    Route::get('/todayunderprocessfollowupstabledata', [SalesDashboardController::class, 'todayunderprocessfollowupstabledata'])->name('todayunderprocessfollowupstabledata');
    Route::get('/todaycompletedfollowupstabledata', [SalesDashboardController::class, 'todaycompletedfollowupstabledata'])->name('todaycompletedfollowupstabledata');
    Route::get('/todaypendingfollowupstabledata', [SalesDashboardController::class, 'todaypendingfollowupstabledata'])->name('todaypendingfollowupstabledata');
    Route::get('/todaynewfollowupstabledata', [SalesDashboardController::class, 'todaynewfollowupstabledata'])->name('todaynewfollowupstabledata');

    Route::get('/searchfollowups', [SalesDashboardController::class, 'searchFollowups'])->name('searchFollowups');
    Route::get('/searchunderprocessFollowups', [SalesDashboardController::class, 'searchunderprocessFollowups'])->name('searchunderprocessFollowups');
    Route::get('/searchcompletedFollowups', [SalesDashboardController::class, 'searchcompletedFollowups'])->name('searchcompletedFollowups');
    Route::get('/searchpendingFollowups', [SalesDashboardController::class, 'searchpendingFollowups'])->name('searchpendingFollowups');
    Route::get('/searchnewFollowups', [SalesDashboardController::class, 'searchnewFollowups'])->name('searchnewFollowups');

    // Users Management
    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::get('/fetchuser', [UserController::class, 'fetchuser'])->name('fetchuser');
    Route::get('/user/fetch-for-manager', [UserController::class, 'fetchUsersForManager'])->name('fetchUsersForManager');
    Route::get('/user/sales-users', [UserController::class, 'fetchSalesUsers'])->name('user.sales-users');
    Route::get('/user/fetch-employees', [UserController::class, 'fetchEmployees'])->name('user.fetch-employees');
    Route::get('/fetchrole', [RoleController::class, 'fetchrole'])->name('fetchrole');
    Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::put('/user/change-password/{id}', [UserController::class, 'changePassword'])->name('user.change-password');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password.post');
    Route::post('/profile/documents', [ProfileController::class, 'storeDocument'])->name('profile.documents.store');
    Route::delete('/profile/documents/{document}', [ProfileController::class, 'destroyDocument'])->name('profile.documents.destroy');
    Route::get('/profile/picture/{id}', [ProfileController::class, 'getProfilePicture'])->name('profile.picture');

    // Role Master
    Route::get('/role-master', [RoleMasterController::class, 'index'])->name('role-master');
    Route::get('/role-master/fetch', [RoleMasterController::class, 'fetch'])->name('role-master.fetch');
    Route::get('/role-master/permissions', [RoleMasterController::class, 'getPermissions'])->name('role-master.permissions');
    Route::post('/role-master', [RoleMasterController::class, 'store'])->name('role-master.store');
    Route::get('/role-master/{role}/edit', [RoleMasterController::class, 'edit'])->name('role-master.edit');
    Route::put('/role-master/{role}', [RoleMasterController::class, 'update'])->name('role-master.update');
    Route::delete('/role-master/{role}', [RoleMasterController::class, 'destroy'])->name('role-master.destroy');

    // All Data (Admin)
    Route::get('/alldata', [AllDataController::class, 'index'])->name('alldata');
    Route::get('/fetchalldata', [AllDataController::class, 'fetchalldata'])->name('fetchalldata');
    Route::get('/alldata/summary-stats', [AllDataController::class, 'getSummaryStats'])->name('alldata.summary-stats');
    Route::get('/alldata/status-counts', [AllDataController::class, 'getStatusCounts'])->name('alldata.status-counts');
    Route::post('/alldatafilter', [AllDataController::class, 'alldatafilter'])->name('alldatafilter');
    Route::get('/alldatasearch', [AllDataController::class, 'alldatasearch'])->name('alldatasearch');
    Route::post('/alldatafilterdate', [AllDataController::class, 'alldatafilterdate'])->name('alldatafilterdate');
    Route::post('/alldata/reassign', [AllDataController::class, 'reassignLead'])->name('alldata.reassign');
    Route::get('/alldata/team-members', [AllDataController::class, 'getTeamMembers'])->name('alldata.team-members');

    Route::get('/alldata/today-followups', [AllDataController::class, 'todayFollowupsTable'])->name('alldata.today-followups');
    Route::get('/alldata/today-followups-data', [AllDataController::class, 'todayFollowupsData'])->name('alldata.today-followups.data');

    Route::get('/alldata/under-process', [AllDataController::class, 'underProcessTable'])->name('alldata.under-process');
    Route::get('/alldata/under-process-data', [AllDataController::class, 'underProcessData'])->name('alldata.under-process.data');

    Route::get('/alldata/today-completed', [AllDataController::class, 'todayCompletedTable'])->name('alldata.today-completed');
    Route::get('/alldata/today-completed-data', [AllDataController::class, 'todayCompletedData'])->name('alldata.today-completed.data');

    Route::get('/alldata/today-pending', [AllDataController::class, 'todayPendingTable'])->name('alldata.today-pending');
    Route::get('/alldata/today-pending-data', [AllDataController::class, 'todayPendingData'])->name('alldata.today-pending.data');

    Route::get('/alldata/today-new', [AllDataController::class, 'todayNewTable'])->name('alldata.today-new');
    Route::get('/alldata/today-new-data', [AllDataController::class, 'todayNewData'])->name('alldata.today-new.data');

    // Sales Analytics
    Route::get('/sales-analytics', [SalesAnalyticsController::class, 'index'])->name('sales-analytics');
    Route::get('/sales-analytics/data', [SalesAnalyticsController::class, 'getAnalytics'])->name('sales-analytics.data');
    Route::get('/sales-analytics/user', [SalesAnalyticsController::class, 'getUserAnalytics'])->name('sales-analytics.user');
    Route::get('/sales-analytics/users', [SalesAnalyticsController::class, 'getUsers'])->name('sales-analytics.users');

    // My Leads
    Route::get('/myleads', [MyLeadsController::class, 'index'])->name('myleads');
    Route::get('/myleads/data', [MyLeadsController::class, 'getMyLeads'])->name('myleads.data');
    Route::get('/myleads/summary-stats', [MyLeadsController::class, 'getSummaryStats'])->name('myleads.summary-stats');
    Route::get('/myleads/status-counts', [MyLeadsController::class, 'getStatusCounts'])->name('myleads.status-counts');
    Route::post('/myleads/filter', [MyLeadsController::class, 'filterLeads'])->name('myleads.filter');
    Route::get('/myleads/filter-options', [MyLeadsController::class, 'getFilterOptions'])->name('myleads.filter-options');
    Route::get('/myleads/cities/{stateId}', [MyLeadsController::class, 'getCitiesByState'])->name('myleads.cities');
    Route::get('/myleads/stats', [MyLeadsController::class, 'getLeadStats'])->name('myleads.stats');
    Route::post('/myleads/export', [MyLeadsController::class, 'exportLeads'])->name('myleads.export');
    Route::post('/myleads/reassign', [MyLeadsController::class, 'reassignLead'])->name('myleads.reassign');
    Route::get('/myleads/team-members', [MyLeadsController::class, 'getTeamMembers'])->name('myleads.team-members');

    // Lead Generation
    Route::get('/leadgen/my', [LeadGenerationController::class, 'myLeads'])->name('leadgen.my');
    Route::get('/leadgen/my/data', [LeadGenerationController::class, 'getMyLeads'])->name('leadgen.my.data');
    Route::get('/leadgen/my/summary-stats', [LeadGenerationController::class, 'getSummaryStats'])->name('leadgen.my.summary-stats');
    Route::get('/leadgen/my/status-counts', [LeadGenerationController::class, 'getStatusCounts'])->name('leadgen.my.status-counts');
    Route::post('/leadgen/my/filter', [LeadGenerationController::class, 'filterLeads'])->name('leadgen.my.filter');
    Route::get('/leadgen/my/filter-options', [LeadGenerationController::class, 'getFilterOptions'])->name('leadgen.my.filter-options');
    Route::get('/leadgen/my/cities/{stateId}', [LeadGenerationController::class, 'getCitiesByState'])->name('leadgen.my.cities');
    Route::get('/leadgen/my/stats', [LeadGenerationController::class, 'getLeadStats'])->name('leadgen.my.stats');
    Route::post('/leadgen/my/export', [LeadGenerationController::class, 'exportLeads'])->name('leadgen.my.export');
    Route::post('/leadgen/my/reassign', [LeadGenerationController::class, 'reassignLead'])->name('leadgen.my.reassign');
    Route::get('/leadgen/my/team-members', [LeadGenerationController::class, 'getTeamMembers'])->name('leadgen.my.team-members');

    // Payment Followup
    Route::get('/payment-followup', [PaymentFollowupController::class, 'index'])->name('payment-followup');
    Route::get('/payment-followup/data', [PaymentFollowupController::class, 'getPaymentFollowupLeads'])->name('payment-followup.data');
    Route::get('/payment-followup/stats', [PaymentFollowupController::class, 'getStats'])->name('payment-followup.stats');
    Route::post('/payment-followup/filter', [PaymentFollowupController::class, 'filterLeads'])->name('payment-followup.filter');
    Route::get('/payment-followup/filter-options', [PaymentFollowupController::class, 'getFilterOptions'])->name('payment-followup.filter-options');
    Route::get('/payment-followup/cities/{stateId}', [PaymentFollowupController::class, 'getCitiesByState'])->name('payment-followup.cities');
    Route::get('/payment-followup/status-counts', [PaymentFollowupController::class, 'getStatusCounts'])->name('payment-followup.status-counts');
    Route::get('/payment-followup/lead/{id}', [PaymentFollowupController::class, 'getLeadData'])->name('payment-followup.lead');
    Route::post('/payment-followup/pay-lumpsum', [PaymentFollowupController::class, 'payLumpsum'])->name('payment-followup.pay-lumpsum');
    Route::get('/payment-followup/customers', [PaymentFollowupController::class, 'getAllCustomers'])->name('payment-followup.customers');
    Route::get('/payment-followup/products', [PaymentFollowupController::class, 'getAllProducts'])->name('payment-followup.products');
    Route::get('/payment-followup/customer/{id}/leads', [PaymentFollowupController::class, 'getCloseWonLeads'])->name('payment-followup.customer-leads');
    Route::get('/payment-followup/details/{type}', [PaymentFollowupController::class, 'details'])->name('payment-followup.details');
    Route::get('/payment-followup/details/{type}/data', [PaymentFollowupController::class, 'getDetailsData'])->name('payment-followup.details.data');

    // Invoice Management
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{id}/get', [InvoiceController::class, 'getInvoice'])->name('invoices.get');
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::put('/invoices/{id}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

    // Invoice Followups
    Route::get('/invoice-followup/{invoiceId}', [InvoiceFollowupController::class, 'index'])->name('invoice-followup.index');
    Route::get('/invoice-followup/{invoiceId}/data', [InvoiceFollowupController::class, 'getFollowups'])->name('invoice-followup.data');
    Route::get('/invoice-followup/{invoiceId}/followup/{id}', [InvoiceFollowupController::class, 'getFollowup'])->name('invoice-followup.get');
    Route::post('/invoice-followup/{invoiceId}', [InvoiceFollowupController::class, 'store'])->name('invoice-followup.store');
    Route::put('/invoice-followup/{invoiceId}/followup/{id}', [InvoiceFollowupController::class, 'update'])->name('invoice-followup.update');

    // Projects
    Route::get('/project-tracking', [ProjectsController::class, 'index'])->name('projects.index');
    Route::get('/project-tracking/{id}', [ProjectsController::class, 'show'])->name('projects.show');
    Route::get('/projects/fetch', [ProjectsController::class, 'fetch'])->name('projects.fetch');
    Route::get('/projects/fetch-customers', [ProjectsController::class, 'fetchCustomers'])->name('projects.fetch_customers');
    Route::get('/projects/fetch/{customerId}', [ProjectsController::class, 'fetchByCustomer'])->name('projects.fetch_by_customer');
    Route::post('/projects', [ProjectsController::class, 'store'])->name('projects.store');
    Route::get('/projects/options', [ProjectsController::class, 'getOptions'])->name('projects.options');
    Route::put('/projects/{id}', [ProjectsController::class, 'update'])->name('projects.update');
    Route::post('/projects/{id}/toggle-favourite', [ProjectsController::class, 'toggleFavourite'])->name('projects.toggle-favourite');
    Route::delete('/projects/{id}', [ProjectsController::class, 'destroy'])->name('projects.destroy');
    Route::put('/projects/{id}/update-status', [ProjectsController::class, 'updateStatus'])->name('projects.updateStatus');
    Route::patch('/projects/{id}/progress', [ProjectsController::class, 'updateProgress'])->name('projects.updateProgress');
    Route::get('/projects/{id}/worklogs', [ProjectsController::class, 'fetchWorklogs'])->name('projects.worklogs');
    Route::post('/projects/remarks', [CustomerProjectRemarkController::class, 'store'])->name('projects.remarks.store');
    Route::get('/projects/{id}/remarks/latest', [CustomerProjectRemarkController::class, 'latest'])->name('projects.remarks.latest');

    // Subscriptions
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/email-view-data', [SubscriptionController::class, 'getEmailViewData'])->name('subscriptions.email-view-data');
    Route::get('/subscriptions/customer/{customerId}', [SubscriptionController::class, 'customerSubscriptions'])->name('subscriptions.customer');
    Route::get('/subscriptions/fetch', [SubscriptionController::class, 'getSubscriptions'])->name('subscriptions.fetch');
    Route::get('/subscriptions/fetch-all', [SubscriptionController::class, 'fetchAllSubscriptions'])->name('subscriptions.fetch-all');
    Route::get('/subscriptions/filter-options', [SubscriptionController::class, 'getFilterOptions'])->name('subscriptions.filter-options');
    Route::get('/subscriptions/products', [SubscriptionController::class, 'getProducts'])->name('subscriptions.products');
    Route::get('/subscriptions/customers', [SubscriptionController::class, 'getCustomers'])->name('subscriptions.customers');
    Route::get('/subscriptions/cities/{stateId}', [SubscriptionController::class, 'getCitiesByState'])->name('subscriptions.cities-by-state');
    Route::match(['get', 'post'], '/subscriptions/filter', [SubscriptionController::class, 'filterSubscriptions'])->name('subscriptions.filter');
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::patch('/subscriptions/{id}/status', [SubscriptionController::class, 'updateStatus'])->name('subscriptions.update-status');
    Route::get('/subscriptions/{id}/history', [SubscriptionController::class, 'history'])->name('subscriptions.history');
    Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::put('/subscriptions/{id}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
    Route::delete('/subscriptions/{id}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');

    // Location Tracking
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::get('/tracking/fetch-locations', [TrackingController::class, 'fetchLocations'])->name('tracking.fetch-locations');
    Route::get('/tracking/report', [TrackingController::class, 'reportView'])->name('tracking.report');
    Route::post('/tracking/report-data', [TrackingController::class, 'getReportData'])->name('tracking.report-data');
    Route::post('/tracking/monthly-report-data', [TrackingController::class, 'getMonthlyReportData'])->name('tracking.monthly-report-data');
    Route::post('/tracking/date-report-data', [TrackingController::class, 'getDateReportData'])->name('tracking.date-report-data');
    Route::get('/tracking/export-user-report', [TrackingController::class, 'exportUserReport'])->name('tracking.export-user-report');
    Route::get('/tracking/export-monthly-report', [TrackingController::class, 'exportMonthlyReport'])->name('tracking.export-monthly-report');
    Route::get('/tracking/export-date-report', [TrackingController::class, 'exportDateReport'])->name('tracking.export-date-report');

    // Team Leads (Managers Only)
    Route::get('/teamleads', [TeamLeadsController::class, 'index'])->name('teamleads');
    Route::get('/teamleads/data', [TeamLeadsController::class, 'getTeamLeads'])->name('teamleads.data');
    Route::post('/teamleads/filter', [TeamLeadsController::class, 'filterTeamLeads'])->name('teamleads.filter');
    Route::get('/teamleads/cities/{stateId}', [TeamLeadsController::class, 'getCitiesByState'])->name('teamleads.cities');
    Route::get('/teamleads/stats', [TeamLeadsController::class, 'getTeamLeadStats'])->name('teamleads.stats');
    Route::post('/teamleads/export', [TeamLeadsController::class, 'exportTeamLeads'])->name('teamleads.export');
    Route::post('/teamleads/reassign', [TeamLeadsController::class, 'reassignLead'])->name('teamleads.reassign');
    Route::get('/teamleads/team-members', [TeamLeadsController::class, 'getTeamMembers'])->name('teamleads.team-members');

    // Team Analytics
    Route::get('/team-analytics', [TeamAnalyticsController::class, 'index'])->name('team-analytics');
    Route::get('/team-analytics/members', [TeamAnalyticsController::class, 'getTeamMembers'])->name('team-analytics.members');
    Route::post('/team-analytics/member', [TeamAnalyticsController::class, 'getMemberAnalytics'])->name('team-analytics.member');
    Route::get('/team-analytics/overview', [TeamAnalyticsController::class, 'getTeamOverview'])->name('team-analytics.overview');
    Route::get('/team-analytics/remarks', [TeamAnalyticsController::class, 'getRemarks'])->name('team-analytics.remarks');

    // Customer
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
    Route::get('/customer/fetch', [CustomerController::class, 'fetchCustomers'])->name('customer.fetch');
    Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');
    Route::put('/customer/{id}', [CustomerController::class, 'update'])->name('customer.update');
    Route::delete('/customer/{id}', [CustomerController::class, 'destroy'])->name('customer.destroy');

    // Form Builder (Lead Form)
    Route::get('/form-builder', [FormBuilderController::class, 'index'])->name('formbuilder.index');
    Route::get('/form-builder/create', [FormBuilderController::class, 'create'])->name('formbuilder.create');
    Route::get('/form-builder/fields', [FormBuilderController::class, 'fields'])->name('formbuilder.fields');
    Route::get('/form-builder/list', [FormBuilderController::class, 'list'])->name('formbuilder.list');
    Route::get('/form-builder/{form}/edit', [FormBuilderController::class, 'edit'])->name('formbuilder.edit');
    Route::get('/form-builder/{form}/config', [FormBuilderController::class, 'config'])->name('formbuilder.config');
    Route::post('/form-builder/{form}/config', [FormBuilderController::class, 'saveConfig'])->name('formbuilder.config.save');
    Route::post('/form-builder/{form}/config/test', [FormBuilderController::class, 'testConnection'])->name('formbuilder.config.test');
    Route::get('/form-builder/{form}/view', [FormBuilderController::class, 'viewPage'])->name('formbuilder.view');
    Route::post('/form-builder/{form}/submit', [FormBuilderController::class, 'submit'])->name('formbuilder.submit');
    Route::get('/form-builder/{form}', [FormBuilderController::class, 'show'])->name('formbuilder.show');
    Route::post('/form-builder/store', [FormBuilderController::class, 'store'])->name('formbuilder.store');
    Route::put('/form-builder/{form}', [FormBuilderController::class, 'update'])->name('formbuilder.update');
    Route::delete('/form-builder/{form}', [FormBuilderController::class, 'destroy'])->name('formbuilder.destroy');

    // Services (Renamed from Project)
    Route::get('/service', [ProjectController::class, 'index'])->name('service');
    Route::get('/service/fetch', [ProjectController::class, 'fetchProjects'])->name('service.fetch');
    Route::post('/service/store', [ProjectController::class, 'store'])->name('service.store');
    Route::put('/service/{id}', [ProjectController::class, 'update'])->name('service.update');
    Route::delete('/service/{id}', [ProjectController::class, 'destroy'])->name('service.destroy');

    // Modules
    Route::get('/module', [ModuleController::class, 'index'])->name('module');
    Route::get('/module/fetch', [ModuleController::class, 'fetchModules'])->name('module.fetch');
    Route::get('/module/service/{serviceId}', [ModuleController::class, 'getModulesByService'])->name('module.by-service');
    Route::post('/module/store', [ModuleController::class, 'store'])->name('module.store');
    Route::put('/module/{id}', [ModuleController::class, 'update'])->name('module.update');
    Route::delete('/module/{id}', [ModuleController::class, 'destroy'])->name('module.destroy');

    // Customer Services (Renamed from Customer Project)
    Route::get('/customer-project', [CustomerProjectController::class, 'index'])->name('customer-project');
    Route::get('/customer-project/fetch', [CustomerProjectController::class, 'fetchCustomerProjects'])->name('customer-project.fetch');
    Route::post('/customer-project/store', [CustomerProjectController::class, 'store'])->name('customer-project.store');
    Route::put('/customer-project/{id}', [CustomerProjectController::class, 'update'])->name('customer-project.update');
    Route::delete('/customer-project/{id}', [CustomerProjectController::class, 'destroy'])->name('customer-project.destroy');
    Route::get('/customer-project/customers', [CustomerProjectController::class, 'getCustomers'])->name('customer-project.customers');
    Route::get('/customer-project/services', [CustomerProjectController::class, 'getServices'])->name('customer-project.services');
    Route::put('/customer-project/{customerProjectId}/module/{moduleId}/status', [CustomerProjectController::class, 'updateModuleStatus'])->name('customer-project.module-status');

    // Customer Analytics
    Route::get('/customer-analytics', [CustomerAnalyticsController::class, 'index'])->name('customer-analytics.index');
    Route::get('/customer-analytics/customers', [CustomerAnalyticsController::class, 'getCustomers'])->name('customer-analytics.get-customers');
    Route::get('/customer-analytics/{customerId}', [CustomerAnalyticsController::class, 'getCustomerAnalytics'])->name('customer-analytics.show');
    Route::get('/customer-analytics/{customerId}/leads', [CustomerAnalyticsController::class, 'getCustomerLeadDetails'])->name('customer-analytics.leads');

    // Entry Types
    Route::get('/entry-type', [EntryTypeController::class, 'index'])->name('entry-type.index');
    Route::get('/entry-type/fetch', [EntryTypeController::class, 'fetch'])->name('entry-type.fetch');
    Route::post('/entry-type', [EntryTypeController::class, 'store'])->name('entry-type.store');
    Route::put('/entry-type/{id}', [EntryTypeController::class, 'update'])->name('entry-type.update');
    Route::delete('/entry-type/{id}', [EntryTypeController::class, 'destroy'])->name('entry-type.destroy');

    // Leave Application Management
    Route::get('/leave/approvals', [LeaveController::class, 'approvals'])->name('leave.approvals');
    Route::get('/leave/approvals/fetch', [LeaveController::class, 'fetchApprovals'])->name('leave.approvals.fetch');
    Route::post('/leave/approvals/{id}/approve', [LeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/approvals/{id}/reject', [LeaveController::class, 'reject'])->name('leave.reject');
    Route::get('/leave/user-history/{userId}', [LeaveController::class, 'userHistory'])->name('leave.user-history');

    Route::get('/leave', [LeaveController::class, 'index'])->name('leave.index');
    Route::get('/leave/fetch', [LeaveController::class, 'fetch'])->name('leave.fetch');
    Route::get('/leave/ledger', [LeaveController::class, 'fetchLedger'])->name('leave.ledger');
    Route::get('/leave/types', [LeaveController::class, 'fetchLeaveTypes'])->name('leave.types');
    Route::post('/leave', [LeaveController::class, 'store'])->name('leave.store');
    Route::put('/leave/{id}', [LeaveController::class, 'update'])->name('leave.update');
    Route::delete('/leave/{id}', [LeaveController::class, 'destroy'])->name('leave.destroy');
    Route::post('/leave/{id}/curtail', [LeaveController::class, 'curtail'])->name('leave.curtail');

    // Worklog
    Route::get('/worklog', [WorklogController::class, 'index'])->name('worklog');
    Route::get('/worklog/entry-types', [WorklogController::class, 'getEntryTypes'])->name('worklog.entry-types');
    Route::get('/worklog/customers', [WorklogController::class, 'getCustomers'])->name('worklog.customers');
    Route::get('/worklog/projects', [WorklogController::class, 'getProjects'])->name('worklog.projects');
    Route::get('/worklog/projects/customer/{customerId}', [WorklogController::class, 'getProjectsByCustomer'])->name('worklog.projects-by-customer');
    Route::get('/worklog/projects-only/customer/{customerId}', [WorklogController::class, 'getProjectsByCustomerOnly'])->name('worklog.projects-only-by-customer');
    Route::get('/worklog/services/customer/{customerId}', [WorklogController::class, 'getServicesByCustomerProject'])->name('worklog.services-by-customer-project');
    Route::get('/worklog/modules/{serviceId}', [WorklogController::class, 'getModulesByService'])->name('worklog.modules');
    Route::post('/worklog/check-date', [WorklogController::class, 'checkDateValidation'])->name('worklog.check-date');
    Route::get('/worklog/missing-users', [WorklogController::class, 'getMissingUsersForDate'])->name('worklog.missing-users');
    Route::get('/worklog/missing-summary', [WorklogController::class, 'getMissingEntriesSummary'])->name('worklog.missing-summary');
    Route::post('/worklog/can-submit', [WorklogController::class, 'canSubmitWorklog'])->name('worklog.can-submit');
    Route::get('/worklog-missing-summary', function() {
        return view('worklog.missing-summary');
    })->name('worklog-missing-summary');
    Route::post('/worklog/add-to-session', [WorklogController::class, 'addToSession'])->name('worklog.add-to-session');
    Route::get('/worklog/pending-approvals', [WorklogController::class, 'getPendingApprovals'])->name('worklog.pending-approvals');
    Route::post('/worklog/{id}/approve', [WorklogController::class, 'approveWorklog'])->name('worklog.approve');
    Route::post('/worklog/{id}/reject', [WorklogController::class, 'rejectWorklog'])->name('worklog.reject');
    Route::post('/worklog/approve-group', [WorklogController::class, 'approveGroup'])->name('worklog.approve-group');
    Route::post('/worklog/reject-group', [WorklogController::class, 'rejectGroup'])->name('worklog.reject-group');
    Route::get('/worklog/session-entries', [WorklogController::class, 'getSessionEntries'])->name('worklog.session-entries');
    Route::post('/worklog/remove-from-session', [WorklogController::class, 'removeFromSession'])->name('worklog.remove-from-session');
    Route::post('/worklog/clear-session', [WorklogController::class, 'clearSession'])->name('worklog.clear-session');
    Route::post('/worklog/submit', [WorklogController::class, 'submitWorklog'])->name('worklog.submit');
    Route::delete('/worklog/{id}', [WorklogController::class, 'destroy'])->name('worklog.destroy');

    // Task Statuses
    Route::get('/task-status', [TaskStatusController::class, 'index'])->name('task-status.index');
    Route::get('/task-status/fetch', [TaskStatusController::class, 'fetch'])->name('task-status.fetch');
    Route::post('/task-status/store', [TaskStatusController::class, 'store'])->name('task-status.store');
    Route::put('/task-status/{id}', [TaskStatusController::class, 'update'])->name('task-status.update');
    Route::delete('/task-status/{id}', [TaskStatusController::class, 'destroy'])->name('task-status.destroy');

    // Task & Work Management
    Route::get('/all-tasks', [TaskController::class, 'allTasks'])->name('all-tasks.index');
    Route::get('/all-tasks/fetch', [TaskController::class, 'fetchAllTasks'])->name('all-tasks.fetch');

    Route::get('/task', [TaskController::class, 'index'])->name('task.index');
    Route::get('/task/fetch', [TaskController::class, 'fetch'])->name('task.fetch');
    Route::get('/task/project/{projectId}', [TaskController::class, 'fetchByCustomerProject'])->name('task.fetchByProject');
    Route::get('/task/users', [TaskController::class, 'getUsers'])->name('task.users');
    Route::get('/task/customers', [TaskController::class, 'getCustomers'])->name('task.customers');
    Route::get('/task/statuses', [TaskController::class, 'getTaskStatuses'])->name('task.statuses');
    Route::get('/task/priorities', [TaskController::class, 'getTaskPriorities'])->name('task.priorities');
    Route::post('/task/store', [TaskController::class, 'store'])->name('task.store');
    Route::put('/task/{id}', [TaskController::class, 'update'])->name('task.update');
    Route::get('/task/{taskId}/image/{imageId}', [TaskController::class, 'serveImage'])->name('task.image');
    Route::delete('/task/{taskId}/image/{imageId}', [TaskController::class, 'deleteImage'])->name('task.image.delete');
    Route::post('/task/{id}/toggle-done', [TaskController::class, 'toggleDone'])->name('task.toggle-done');
    Route::post('/task/{id}/update-status', [TaskController::class, 'updateStatus'])->name('task.update-status');
    Route::post('/task/{id}/update-status-id', [TaskController::class, 'updateStatusById'])->name('task.update-status-id');
    Route::post('/task/{id}/poke', [TaskController::class, 'poke'])->name('task.poke');
    Route::delete('/task/{id}', [TaskController::class, 'destroy'])->name('task.destroy');

    Route::get('/my-tasks', [TaskController::class, 'myTasks'])->name('my-tasks.index');
    Route::get('/my-tasks/fetch', [TaskController::class, 'fetchMyTasks'])->name('my-tasks.fetch');
    Route::get('/task/export', [TaskController::class, 'exportTasks'])->name('task.export');
    Route::post('/task/remark/save', [TaskController::class, 'saveRemark'])->name('task.remark.save');

    // Worklog History & Approvals
    Route::get('/worklog-history', [WorklogHistoryController::class, 'index'])->name('worklog-history');
    Route::get('/worklog-history/fetch', [WorklogHistoryController::class, 'fetchWorklogs'])->name('worklog-history.fetch');
    Route::get('/worklog-history/stats', [WorklogHistoryController::class, 'getWorklogStats'])->name('worklog-history.stats');
    Route::delete('/worklog-history/{id}', [WorklogHistoryController::class, 'destroy'])->name('worklog-history.destroy');

    Route::get('/worklog-approvals', function() {
        return view('worklog.approvals');
    })->name('worklog-approvals');

    // Holidays
    Route::get('/holiday', [HolidayController::class, 'index'])->name('holiday');
    Route::get('/holiday/fetch', [HolidayController::class, 'fetchHolidays'])->name('holiday.fetch');
    Route::post('/holiday', [HolidayController::class, 'store'])->name('holiday.store');
    Route::put('/holiday/{id}', [HolidayController::class, 'update'])->name('holiday.update');
    Route::delete('/holiday/{id}', [HolidayController::class, 'destroy'])->name('holiday.destroy');

    // Attendance Approval
    Route::get('/attendance/approval', [AttendanceApprovalController::class, 'index'])->name('attendance.approval');
    Route::get('/attendance/approval/fetch', [AttendanceApprovalController::class, 'fetch'])->name('attendance.approval.fetch');
    Route::post('/attendance/mark', [AttendanceApprovalController::class, 'markAttendance'])->name('attendance.mark-attendance');
    Route::post('/attendance/void', [AttendanceApprovalController::class, 'voidAttendance'])->name('attendance.void');
    Route::post('/attendance/approve/{id}', [AttendanceApprovalController::class, 'approve'])->name('attendance.approve');
    Route::post('/attendance/reject/{id}', [AttendanceApprovalController::class, 'reject'])->name('attendance.reject');
    Route::post('/attendance/approve-bulk', [AttendanceApprovalController::class, 'bulkApprove'])->name('attendance.approve-bulk');
    Route::post('/attendance/post-daily', [AttendanceApprovalController::class, 'postDaily'])->name('attendance.post-daily');
    Route::post('/attendance/update-times/{id}', [AttendanceApprovalController::class, 'updateTimes'])->name('attendance.update-times');
    Route::get('/attendance/leave-balances/{userId}', [AttendanceApprovalController::class, 'getLeaveBalances'])->name('attendance.leave-balances');
    Route::post('/attendance/apply-quick-leave', [AttendanceApprovalController::class, 'applyQuickLeave'])->name('attendance.apply-quick-leave');

    // Unlock Attendance
    Route::get('/attendance/unlock', [UnlockAttendanceController::class, 'index'])->name('attendance.unlock');
    Route::get('/attendance/unlock/fetch', [UnlockAttendanceController::class, 'fetch'])->name('attendance.unlock.fetch');
    Route::post('/attendance/unlock/{id}', [UnlockAttendanceController::class, 'unlock'])->name('attendance.unlock.process');
    Route::post('/attendance/unlock-bulk', [UnlockAttendanceController::class, 'unlockBulk'])->name('attendance.unlock-bulk');
    Route::post('/attendance/unlock-by-date', [UnlockAttendanceController::class, 'unlockByDate'])->name('attendance.unlock-by-date');

    // Attendance Actions & Reports
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance/punch-in', [AttendanceController::class, 'punchIn'])->name('attendance.punch-in');
    Route::post('/attendance/punch-out', [AttendanceController::class, 'punchOut'])->name('attendance.punch-out');
    Route::post('/attendance/task-reminder-response', [AttendanceController::class, 'saveTaskReminderResponse'])->name('attendance.task-reminder-response');
    Route::post('/attendance/start-break', [AttendanceController::class, 'startBreak'])->name('attendance.start-break');
    Route::post('/attendance/end-break', [AttendanceController::class, 'endBreak'])->name('attendance.end-break');
    Route::get('/attendance/today-status', [AttendanceController::class, 'getTodayStatus'])->name('attendance.today-status');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/attendance/history/data', [AttendanceController::class, 'getHistoryData'])->name('attendance.history.data');
    Route::get('/attendance/stats', [AttendanceController::class, 'getAttendanceStats'])->name('attendance.stats');

    Route::get('/attendance/report', [AttendanceController::class, 'reportView'])->name('attendance.report');
    Route::post('/attendance/report-data', [AttendanceController::class, 'getReportData'])->name('attendance.report-data');
    Route::post('/attendance/monthly-report-data', [AttendanceController::class, 'getMonthlyReportData'])->name('attendance.monthly-report-data');
    Route::post('/attendance/date-report-data', [AttendanceController::class, 'getDateReportData'])->name('attendance.date-report-data');
    Route::get('/attendance/export-monthly-report', [AttendanceController::class, 'exportMonthlyReport'])->name('attendance.export-monthly-report');
    Route::get('/attendance/export-monthly-report-pdf', [AttendanceController::class, 'exportMonthlyReportPdf'])->name('attendance.export-monthly-report-pdf');
    Route::get('/attendance/export-user-report-pdf', [AttendanceController::class, 'exportUserReportPdf'])->name('attendance.export-user-report-pdf');
    Route::get('/attendance/export-date-report-pdf', [AttendanceController::class, 'exportDateReportPdf'])->name('attendance.export-date-report-pdf');
    Route::get('/attendance/check-worklog-validation', [AttendanceController::class, 'checkWorklogValidation'])->name('attendance.check-worklog-validation');

    Route::get('/reports/worklog/stats', [WorklogReportController::class, 'getStats'])->name('reports.worklog.stats');

    // Assigned Leads
    Route::get('/assignedleads', [AssignedLeadsController::class, 'index'])->name('assignedleads');
    Route::get('/assignedleads/data', [AssignedLeadsController::class, 'getAssignedLeads'])->name('assignedleads.data');
    Route::get('/assignedleads/summary-stats', [AssignedLeadsController::class, 'getSummaryStats'])->name('assignedleads.summary-stats');
    Route::get('/assignedleads/status-counts', [AssignedLeadsController::class, 'getStatusCounts'])->name('assignedleads.status-counts');
    Route::post('/assignedleads/filter', [AssignedLeadsController::class, 'filterAssignedLeads'])->name('assignedleads.filter');
    Route::get('/assignedleads/filter-options', [AssignedLeadsController::class, 'getFilterOptions'])->name('assignedleads.filter-options');
    Route::get('/assignedleads/cities/{stateId}', [AssignedLeadsController::class, 'getCitiesByState'])->name('assignedleads.cities');

    // Callings Master
    Route::get('/calling', [CallingController::class, 'index'])->name('calling');
    Route::get('/calling/list', [CallingListController::class, 'index'])->name('calling.list.index');
    Route::get('/calling/list/data', [CallingListController::class, 'getData'])->name('calling.list.data');
    Route::get('/calling/list/create', [CallingListController::class, 'create'])->name('calling.list.create');
    Route::get('/calling/list/download-template', [CallingListController::class, 'downloadTemplate'])->name('calling.list.download-template');
    Route::post('/calling/list/store', [CallingListController::class, 'store'])->name('calling.list.store');
    Route::delete('/calling/list/{id}', [CallingListController::class, 'destroy'])->name('calling.list.destroy');
    Route::get('/calling/lock', [CallingController::class, 'lockIndex'])->name('calling.lock');
    Route::get('/calling/data', [CallingController::class, 'getCallings'])->name('calling.data');
    Route::post('/calling/filter', [CallingController::class, 'filterCallings'])->name('calling.filter');
    Route::get('/calling/campaigns', [CallingController::class, 'getCampaigns'])->name('calling.campaigns');
    Route::get('/calling/filter-options', [CallingController::class, 'getFilterOptions'])->name('calling.filter-options');
    Route::post('/calling/lock-leads', [CallingController::class, 'lockLeads'])->name('calling.lock-leads');
    Route::get('/calling/cities/{stateId}', [CallingController::class, 'getCitiesByState'])->name('calling.cities');
    Route::post('/calling/update-type', [CallingController::class, 'updateCallingType'])->name('calling.update-type');
    Route::post('/calling/create-campaign', [CallingController::class, 'createCampaign'])->name('calling.create-campaign');
    Route::post('/calling/selection/toggle', [CallingController::class, 'toggleSelection'])->name('calling.selection.toggle');
    Route::post('/calling/selection/clear', [CallingController::class, 'clearSelection'])->name('calling.selection.clear');
    Route::get('/calling/selection/status', [CallingController::class, 'getSelectionStatus'])->name('calling.selection.status');
    Route::post('/calling/selection/all-matching', [CallingController::class, 'selectAllMatching'])->name('calling.selection.all-matching');
    Route::get('/todays-calling', [TodaysCallingController::class, 'index'])->name('todays-calling');
    Route::get('/calling/todays', [TodaysCallingController::class, 'index'])->name('calling.todays');
    Route::get('/calling/todays/data', [TodaysCallingController::class, 'getCallings'])->name('calling.todays.data');
    Route::post('/calling/todays/filter', [TodaysCallingController::class, 'filterCallings'])->name('calling.todays.filter');
    Route::get('/calling/todays/filter-options', [TodaysCallingController::class, 'getFilterOptions'])->name('calling.todays.filter-options');
    Route::get('/calling/todays/cities/{stateId}', [TodaysCallingController::class, 'getCitiesByState'])->name('calling.todays.cities');
    Route::get('/calling/{id}/remarks', [CallingController::class, 'remarks'])->name('calling.remarks.show');
    Route::post('/calling/{id}/remarks', [CallingController::class, 'storeRemark'])->name('calling.remarks.store');
    Route::post('/calling/remarks/{id}/update', [CallingController::class, 'updateRemark'])->name('calling.remarks.update');

    // My Calling & Junk Calling
    Route::get('/calling/my', [MyCallingController::class, 'index'])->name('calling.my');
    Route::get('/calling/my/data', [MyCallingController::class, 'getCallings'])->name('calling.my.data');
    Route::post('/calling/my/filter', [MyCallingController::class, 'filterCallings'])->name('calling.my.filter');
    Route::get('/calling/my/filter-options', [MyCallingController::class, 'getFilterOptions'])->name('calling.my.filter-options');
    Route::get('/calling/my/cities/{stateId}', [MyCallingController::class, 'getCitiesByState'])->name('calling.my.cities');
    Route::get('/calling/my/my-campaigns', [MyCallingController::class, 'getMyCampaigns'])->name('calling.my.my-campaigns');
    Route::post('/calling/my/update-type', [MyCallingController::class, 'updateCallingType'])->name('calling.my.update-type');
    Route::post('/calling/my/reassign', [MyCallingController::class, 'reassignCalling'])->name('calling.my.reassign');
    Route::get('/calling/my/team-members', [MyCallingController::class, 'getTeamMembers'])->name('calling.my.team-members');

    // Lead Remarks
    Route::get('/calling/remarks', [AssignedCallingController::class, 'getLeadDetailsWithRemarks'])->name('calling.remarks');

    // Assigned Calling
    Route::get('/calling/assigned', [AssignedCallingController::class, 'index'])->name('calling.assigned');
    Route::get('/calling/assigned/data', [AssignedCallingController::class, 'getAssignedCallings'])->name('calling.assigned.data');
    Route::post('/calling/assigned/filter', [AssignedCallingController::class, 'filterAssignedCallings'])->name('calling.assigned.filter');
    Route::get('/calling/assigned/filter-options', [AssignedCallingController::class, 'getFilterOptions'])->name('calling.assigned.filter-options');
    Route::get('/calling/assigned/cities/{stateId}', [AssignedCallingController::class, 'getCitiesByState'])->name('calling.assigned.cities');
    Route::get('/calling/assigned/lead-details/{id}', [AssignedCallingController::class, 'getLeadDetailsWithRemarks'])->name('calling.assigned.lead-details');
    Route::get('/calling/assigned/team-members', [AssignedCallingController::class, 'getTeamMembers'])->name('calling.assigned.team-members');
    Route::post('/calling/assigned/reassign', [AssignedCallingController::class, 'reassignCalling'])->name('calling.assigned.reassign');
    Route::post('/calling/assigned/update-type', [AssignedCallingController::class, 'updateCallingType'])->name('calling.assigned.update-type');

    // Converted Calling
    Route::get('/calling/converted', [ConvertedCallingController::class, 'index'])->name('calling.converted');
    Route::get('/calling/converted/data', [ConvertedCallingController::class, 'getConvertedCallings'])->name('calling.converted.data');
    Route::post('/calling/converted/filter', [ConvertedCallingController::class, 'filterConvertedCallings'])->name('calling.converted.filter');
    Route::get('/calling/converted/filter-options', [ConvertedCallingController::class, 'getFilterOptions'])->name('calling.converted.filter-options');
    Route::get('/calling/converted/cities/{stateId}', [ConvertedCallingController::class, 'getCitiesByState'])->name('calling.converted.cities');

    // Team Calling
    Route::get('/calling/team', [TeamCallingController::class, 'index'])->name('calling.team');
    Route::get('/calling/team/data', [TeamCallingController::class, 'getTeamCallings'])->name('calling.team.data');
    Route::post('/calling/team/filter', [TeamCallingController::class, 'filterTeamCallings'])->name('calling.team.filter');
    Route::get('/calling/team/filter-options', [TeamCallingController::class, 'getFilterOptions'])->name('calling.team.filter-options');
    Route::post('/calling/team/reassign', [TeamCallingController::class, 'reassignCalling'])->name('calling.team.reassign');
    Route::get('/calling/team/team-members', [TeamCallingController::class, 'getTeamMembers'])->name('calling.team.team-members');
    Route::get('/calling/team/cities/{stateId}', [TeamCallingController::class, 'getCitiesByState'])->name('calling.team.cities');
    Route::post('/calling/team/update-type', [TeamCallingController::class, 'updateCallingType'])->name('calling.team.update-type');

    // Junk Calling
    Route::get('/calling/junk', [JunkCallingController::class, 'index'])->name('calling.junk');
    Route::get('/calling/junk/data', [JunkCallingController::class, 'getCallings'])->name('calling.junk.data');
    Route::post('/calling/junk/filter', [JunkCallingController::class, 'filterCallings'])->name('calling.junk.filter');
    Route::get('/calling/junk/filter-options', [JunkCallingController::class, 'getFilterOptions'])->name('calling.junk.filter-options');
    Route::get('/calling/junk/cities/{stateId}', [JunkCallingController::class, 'getCitiesByState'])->name('calling.junk.cities');
    Route::post('/calling/junk/{id}/restore', [JunkCallingController::class, 'restore'])->name('calling.junk.restore');
    Route::delete('/calling/junk/{id}', [JunkCallingController::class, 'destroy'])->name('calling.junk.destroy');

    // All Calling
    Route::get('/calling/all', [CallingController::class, 'allCallings'])->name('calling.all');
    Route::get('/calling/all/data', [CallingController::class, 'getAllCallingsData'])->name('calling.all.data');
    Route::post('/calling/all/filter', [CallingController::class, 'filterAllCallings'])->name('calling.all.filter');
    Route::get('/calling/all/filter-options', [CallingController::class, 'getFilterOptions'])->name('calling.all.filter-options');
    Route::get('/calling/all/cities/{stateId}', [CallingController::class, 'getCitiesByState'])->name('calling.all.cities');

    // Calling Analytics
    Route::get('/calling/todayfollowups', [CallingDashboardController::class, 'todayFollowups']);
    Route::get('/calling/underprocess', [CallingDashboardController::class, 'underProcess']);
    Route::get('/calling/todaycompleted', [CallingDashboardController::class, 'todayCompleted']);
    Route::get('/calling/todaypending', [CallingDashboardController::class, 'todayPending']);
    Route::get('/calling/todaynew', [CallingDashboardController::class, 'todayNew']);
    Route::get('/calling/allleads', [CallingDashboardController::class, 'allLeads']);
    Route::get('/calling/analytics', [CallingDashboardController::class, 'analytics'])->name('calling.analytics');
    Route::get('/calling/analytics/data', [CallingDashboardController::class, 'getAnalyticsData'])->name('calling.analytics.data');

    Route::get('/calling/todayfollowupstable', [CallingDashboardController::class, 'todayFollowupsTable'])->name('calling.todayfollowupstable');
    Route::get('/calling/underprocesstable', [CallingDashboardController::class, 'underProcessTable'])->name('calling.underprocesstable');
    Route::get('/calling/todaycompletedtable', [CallingDashboardController::class, 'todayCompletedTable'])->name('calling.todaycompletedtable');
    Route::get('/calling/todaypendingtable', [CallingDashboardController::class, 'todaypendingtable'])->name('calling.todaypendingtable');
    Route::get('/calling/todaynewtable', [CallingDashboardController::class, 'todayNewTable'])->name('calling.todaynewtable');
    Route::get('/calling/allleadstable', [CallingDashboardController::class, 'allLeadsTable'])->name('calling.allleadstable');

    Route::get('/calling/todayfollowupstabledata', [CallingDashboardController::class, 'todayFollowupsTableData'])->name('calling.todayfollowupstabledata');
    Route::get('/calling/underprocesstabledata', [CallingDashboardController::class, 'underProcessTableData'])->name('calling.underprocesstabledata');
    Route::get('/calling/todaycompletedtabledata', [CallingDashboardController::class, 'todayCompletedTableData'])->name('calling.todaycompletedtabledata');
    Route::get('/calling/todaypendingtabledata', [CallingDashboardController::class, 'todayPendingTableData'])->name('calling.todaypendingtabledata');
    Route::get('/calling/todaynewtabledata', [CallingDashboardController::class, 'todayNewTableData'])->name('calling.todaynewtabledata');
    Route::get('/calling/allleadstabledata', [CallingDashboardController::class, 'allLeadsTableData'])->name('calling.allleadstabledata');

    // Calling Type
    Route::get('/calling-type', [CallingTypeController::class, 'index'])->name('calling-type.index');
    Route::get('/calling-type/fetch', [CallingTypeController::class, 'fetch'])->name('calling-type.fetch');
    Route::post('/calling-type/store', [CallingTypeController::class, 'store'])->name('calling-type.store');
    Route::get('/calling-type/{id}/edit', [CallingTypeController::class, 'edit'])->name('calling-type.edit');
    Route::put('/calling-type/{id}', [CallingTypeController::class, 'update'])->name('calling-type.update');
    Route::delete('/calling-type/{id}', [CallingTypeController::class, 'destroy'])->name('calling-type.destroy');

    // WhatsApp Template
    Route::get('/whatsapp-template', [WhatsappTemplateController::class, 'index'])->name('whatsapp-template.index');
    Route::get('/whatsapp-template/fetch', [WhatsappTemplateController::class, 'fetch'])->name('whatsapp-template.fetch');
    Route::post('/whatsapp-template/store', [WhatsappTemplateController::class, 'store'])->name('whatsapp-template.store');
    Route::get('/whatsapp-template/{id}/edit', [WhatsappTemplateController::class, 'edit'])->name('whatsapp-template.edit');
    Route::put('/whatsapp-template/{id}', [WhatsappTemplateController::class, 'update'])->name('whatsapp-template.update');
    Route::delete('/whatsapp-template/{id}', [WhatsappTemplateController::class, 'destroy'])->name('whatsapp-template.destroy');
    Route::get('/getcallingtypes', [CallingTypeController::class, 'getCallingTypes'])->name('getcallingtypes');

    // IndiaMart
    Route::get('/indiamart/leads', [IndiaMartLeadsController::class, 'index'])->name('indiamart.index');
    Route::get('/indiamart/leads/summary-stats', [IndiaMartLeadsController::class, 'summaryStats'])->name('indiamart.summary-stats');
    Route::get('/indiamart/leads/status-counts', [IndiaMartLeadsController::class, 'statusCounts'])->name('indiamart.status-counts');
    Route::get('/indiamart/leads/fetch', [IndiaMartLeadsController::class, 'fetch'])->name('indiamart.fetch');
    Route::get('/indiamart/leads/filter-options', [IndiaMartLeadsController::class, 'filterOptions'])->name('indiamart.filter-options');
    Route::post('/indiamart/leads/assign', [IndiaMartLeadsController::class, 'assign'])->name('indiamart.assign');
    Route::post('/indiamart/leads/bulk-assign', [IndiaMartLeadsController::class, 'bulkAssign'])->name('indiamart.bulk-assign');
    Route::post('/indiamart/leads/junk', [IndiaMartLeadsController::class, 'junk'])->name('indiamart.junk');
    Route::post('/indiamart/leads/bulk-junk', [IndiaMartLeadsController::class, 'bulkJunk'])->name('indiamart.bulk-junk');
    Route::get('/indiamart/junk', [IndiaMartLeadsController::class, 'junkIndex'])->name('indiamart.junk.index');
    Route::get('/indiamart/junk/fetch', [IndiaMartLeadsController::class, 'junkFetch'])->name('indiamart.junk.fetch');
    Route::post('/indiamart/junk/delete', [IndiaMartLeadsController::class, 'junkDelete'])->name('indiamart.junk.delete');
    Route::post('/indiamart/leads/followup', [IndiaMartLeadsController::class, 'storeFollowup'])->name('indiamart.store-followup');
    Route::get('/indiamart/leads/{lead}/followups', [IndiaMartLeadsController::class, 'getFollowups'])->name('indiamart.get-followups');
    Route::post('/indiamart/junk/restore', [IndiaMartLeadsController::class, 'junkRestore'])->name('indiamart.junk.restore');
    Route::post('/indiamart/notify-sales', [IndiaMartLeadsController::class, 'notifySalesUsers'])->name('indiamart.notify');

    // Document Management
    Route::get('/document', [DocumentController::class, 'index'])->name('document.index');
    Route::get('/document/fetch', [DocumentController::class, 'fetch'])->name('document.fetch');
    Route::post('/document/store', [DocumentController::class, 'store'])->name('document.store');
    Route::get('/document/users', [DocumentController::class, 'getUsers'])->name('document.users');
    Route::post('/document/category-access', [DocumentController::class, 'saveCategoryUserAccess'])->name('document.category.access');
    Route::get('/document/subcategory-users', [DocumentController::class, 'getSubcategoryUsers'])->name('document.subcategory.users');
    Route::post('/document/subcategory-access', [DocumentController::class, 'saveSubcategoryUserAccess'])->name('document.subcategory.access');
    Route::get('/document/document-users', [DocumentController::class, 'getDocumentUsers'])->name('document.document.users');
    Route::post('/document/document-access', [DocumentController::class, 'saveDocumentUserAccess'])->name('document.document.access');
    Route::get('/document/my-access', [DocumentController::class, 'showUserAccess'])->name('document.user-access');
    Route::get('/document/{category}', [DocumentController::class, 'show'])->name('document.show');
    Route::get('/document/{id}/show', [DocumentController::class, 'showDocument'])->name('document.show.details');
    Route::put('/document/{id}', [DocumentController::class, 'update'])->name('document.update');
    Route::delete('/document/{id}', [DocumentController::class, 'destroy'])->name('document.destroy');
    Route::get('/document/{id}/download', [DocumentController::class, 'download'])->name('document.download');

    Route::get('/document/categories', [DocumentController::class, 'getCategories'])->name('document.categories');
    Route::post('/document/categories', [DocumentController::class, 'storeCategory'])->name('document.categories.store');
    Route::put('/document/categories/{id}', [DocumentController::class, 'updateCategory'])->name('document.categories.update');
    Route::delete('/document/categories/{id}', [DocumentController::class, 'destroyCategory'])->name('document.categories.destroy');

    Route::post('/document/subcategories', [DocumentController::class, 'storeSubcategory'])->name('document.subcategories.store');
    Route::put('/document/subcategories/{id}', [DocumentController::class, 'updateSubcategory'])->name('document.subcategories.update');
    Route::delete('/document/subcategories/{id}', [DocumentController::class, 'destroySubcategory'])->name('document.subcategories.destroy');
    Route::get('/document/{category}/{subcategory}', [DocumentController::class, 'showSubcategory'])->name('document.subcategory.show');

    // Petty Cash
    Route::get('/petty-cash', [PettyCashController::class, 'index'])->name('petty-cash.index');
    Route::get('/petty-cash/fetch', [PettyCashController::class, 'fetch'])->name('petty-cash.fetch');
    Route::get('/petty-cash/stats', [PettyCashController::class, 'getStats'])->name('petty-cash.stats');
    Route::get('/petty-cash/fetch-expenses', [PettyCashController::class, 'fetchExpenses'])->name('petty-cash.fetch-expenses');
    Route::post('/petty-cash/store', [PettyCashController::class, 'store'])->name('petty-cash.store');
    Route::put('/petty-cash/{id}', [PettyCashController::class, 'update'])->name('petty-cash.update');
    Route::delete('/petty-cash/{id}', [PettyCashController::class, 'destroy'])->name('petty-cash.destroy');
    Route::get('/approvals/petty', [PettyCashController::class, 'approvals'])->name('approvals.petty');
    Route::post('/petty-cash/approve-bulk', [PettyCashController::class, 'approveBulk'])->name('petty-cash.approve-bulk');
    Route::post('/petty-cash/{id}/toggle-approval', [PettyCashController::class, 'toggleApproval'])->name('petty-cash.toggle-approval');
    Route::get('/petty-cash/department-summary', [PettyCashController::class, 'departmentSummary'])->name('petty-cash.department-summary');
    Route::get('/petty-cash/department/{id}/expenses', [PettyCashController::class, 'departmentExpenses'])->name('petty-cash.department.expenses');

    // Petty Opening Balance
    Route::get('/petty-opening-balance', [PettyOpeningBalanceController::class, 'index'])->name('petty-opening-balance.index');
    Route::get('/petty-opening-balance/fetch', [PettyOpeningBalanceController::class, 'fetch'])->name('petty-opening-balance.fetch');
    Route::post('/petty-opening-balance/store', [PettyOpeningBalanceController::class, 'store'])->name('petty-opening-balance.store');
    Route::put('/petty-opening-balance/{id}', [PettyOpeningBalanceController::class, 'update'])->name('petty-opening-balance.update');
    Route::delete('/petty-opening-balance/{id}', [PettyOpeningBalanceController::class, 'destroy'])->name('petty-opening-balance.destroy');

    // Expenses Master
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/fetch', [ExpenseController::class, 'fetchExpenses'])->name('expenses.fetch');
    Route::post('/expenses/store', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Contact Management
    Route::get('/contact-management', [ContactManagementController::class, 'index'])->name('contactmanagement.index');
    Route::get('/contact-management/fetch', [ContactManagementController::class, 'fetch'])->name('contactmanagement.fetch');
    Route::get('/contact-management/stats', [ContactManagementController::class, 'getSummaryStats'])->name('contactmanagement.stats');
    Route::post('/contact-management/store', [ContactManagementController::class, 'store'])->name('contactmanagement.store');
    Route::get('/contact-management/{id}/edit', [ContactManagementController::class, 'edit'])->name('contactmanagement.edit');
    Route::put('/contact-management/{id}', [ContactManagementController::class, 'update'])->name('contactmanagement.update');
    Route::delete('/contact-management/{id}', [ContactManagementController::class, 'destroy'])->name('contactmanagement.destroy');

    // Asset Tracking & Management
    Route::get('/asset-management', [AssetManagementController::class, 'index'])->name('asset-management.index');
    Route::get('/asset-management/fetch', [AssetManagementController::class, 'fetch'])->name('asset-management.fetch');
    Route::get('/asset-management/stats', [AssetManagementController::class, 'getSummaryStats'])->name('asset-management.stats');
    Route::get('/asset-management/get-assets', [AssetManagementController::class, 'getAssetsByCategory'])->name('asset-management.get-assets');
    Route::post('/asset-management/store', [AssetManagementController::class, 'store'])->name('asset-management.store');
    Route::get('/asset-management/{id}', [AssetManagementController::class, 'show'])->name('asset-assignment.show');
    Route::put('/asset-management/{id}', [AssetManagementController::class, 'update'])->name('asset-management.update');
    Route::delete('/asset-management/{id}', [AssetManagementController::class, 'destroy'])->name('asset-management.destroy');

    Route::get('/asset-type', [AssetTypeController::class, 'index'])->name('asset-type.index');
    Route::get('/asset-type/fetch', [AssetTypeController::class, 'fetch'])->name('asset-type.fetch');
    Route::post('/asset-type', [AssetTypeController::class, 'store'])->name('asset-type.store');
    Route::put('/asset-type/{id}', [AssetTypeController::class, 'update'])->name('asset-type.update');
    Route::delete('/asset-type/{id}', [AssetTypeController::class, 'destroy'])->name('asset-type.destroy');

    Route::get('/asset-category', [AssetCategoryController::class, 'index'])->name('asset-category.index');
    Route::get('/asset-category/fetch', [AssetCategoryController::class, 'fetch'])->name('asset-category.fetch');
    Route::get('/asset-category/{id}/fields', [AssetCategoryController::class, 'manageFields'])->name('asset-category.fields');
    Route::post('/asset-category', [AssetCategoryController::class, 'store'])->name('asset-category.store');
    Route::get('/asset-category/{id}', [AssetCategoryController::class, 'show'])->name('asset-category.show');
    Route::put('/asset-category/{id}', [AssetCategoryController::class, 'update'])->name('asset-category.update');
    Route::delete('/asset-category/{id}', [AssetCategoryController::class, 'destroy'])->name('asset-category.destroy');

    Route::get('/asset-status', [AssetStatusController::class, 'index'])->name('asset-status.index');
    Route::get('/asset-status/fetch', [AssetStatusController::class, 'fetch'])->name('asset-status.fetch');
    Route::post('/asset-status', [AssetStatusController::class, 'store'])->name('asset-status.store');
    Route::put('/asset-status/{id}', [AssetStatusController::class, 'update'])->name('asset-status.update');
    Route::delete('/asset-status/{id}', [AssetStatusController::class, 'destroy'])->name('asset-status.destroy');

    Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/supplier/fetch', [SupplierController::class, 'fetch'])->name('supplier.fetch');
    Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
    Route::put('/supplier/{id}', [SupplierController::class, 'update'])->name('supplier.update');
    Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/fetch', [AssetController::class, 'fetch'])->name('assets.fetch');
    Route::get('/assets/search-employees', [AssetController::class, 'searchEmployees'])->name('assets.search-employees');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/{id}', [AssetController::class, 'show'])->name('assets.show');
    Route::get('/assets/{id}/history', [AssetController::class, 'history'])->name('assets.history');
    Route::put('/assets/{id}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{id}', [AssetController::class, 'destroy'])->name('assets.destroy');

    Route::get('/email-marketing', [EmailMarketingController::class, 'index'])->name('emailmarketing.index');

    // Notifications API
    Route::get('/notifications', function() {
        $userId = session('user_id');
        $tenantId = session('tenant_id');
        if (!$userId || !$tenantId) {
            return response()->json(['notifications' => [], 'debug' => 'no_session_user']);
        }
        TenantDatabaseService::setDefaultConnection((int) $tenantId);
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['notifications' => [], 'debug' => 'user_not_found_in_tenant_db']);
        }
        $notifications = $user->notifications()->latest()->take(20)->get();
        return response()->json(['notifications' => $notifications]);
    })->name('notifications.index');

    Route::post('/notifications/{id}/mark-read', function($id) {
        $userId = session('user_id');
        $tenantId = session('tenant_id');
        if (!$userId || !$tenantId) {
            return response()->json(['success' => false], 401);
        }
        TenantDatabaseService::setDefaultConnection((int) $tenantId);
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false], 401);
        }
        $notification = $user->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    })->name('notifications.mark-read');

    Route::post('/notifications/{id}/delete', function($id) {
        $userId = session('user_id');
        $tenantId = session('tenant_id');
        if (!$userId || !$tenantId) {
            return response()->json(['success' => false], 401);
        }
        TenantDatabaseService::setDefaultConnection((int) $tenantId);
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false], 401);
        }
        $notification = $user->notifications()->find($id);
        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    })->name('notifications.delete');

    Route::post('/notifications/mark-all-read', function() {
        $userId = session('user_id');
        $tenantId = session('tenant_id');
        if (!$userId || !$tenantId) {
            return response()->json(['success' => false], 401);
        }
        TenantDatabaseService::setDefaultConnection((int) $tenantId);
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false], 401);
        }
        $user->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.mark-all-read');

    Route::post('/notifications/clear-all', function() {
        $userId = session('user_id');
        $tenantId = session('tenant_id');
        if (!$userId || !$tenantId) {
            return response()->json(['success' => false], 401);
        }
        TenantDatabaseService::setDefaultConnection((int) $tenantId);
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false], 401);
        }
        $user->notifications()->delete();
        return response()->json(['success' => true]);
    })->name('notifications.clear-all');
});