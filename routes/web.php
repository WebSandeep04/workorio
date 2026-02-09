<?php

use App\Http\Controllers\AllDataController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdminAuthController;

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerAnalyticsController;
use App\Http\Controllers\CustomerProjectController;
use App\Http\Controllers\EntryTypeController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProspectusController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RemarkController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WorklogController;
use App\Http\Controllers\WorklogHistoryController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SalesBusinessTypeController;
use App\Http\Controllers\SalesCityController;
use App\Http\Controllers\SalesDashboardController;
use App\Http\Controllers\SalesLeadController;
use App\Http\Controllers\SalesLeadSourceController;
use App\Http\Controllers\SalesProductController;
use App\Http\Controllers\SalesStateController;
use App\Http\Controllers\SalesStatusController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\MyLeadsController;
use App\Http\Controllers\PaymentFollowupController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceFollowupController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TeamLeadsController;
use App\Http\Controllers\TeamAnalyticsController;
use App\Http\Controllers\AssignedLeadsController;
use App\Http\Controllers\SalesAnalyticsController;
use App\Http\Controllers\CallingController;
use App\Http\Controllers\MyCallingController;
use App\Http\Controllers\JunkCallingController;
use App\Http\Controllers\TodaysCallingController;
use App\Http\Controllers\CallingTypeController;
use App\Http\Controllers\IndiaMartLeadsController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CalendarStatusController;
use App\Http\Controllers\CalendarSocialHandleController;
use App\Http\Controllers\CalendarClientController;
use App\Http\Controllers\CalendarClientSocialController;
use App\Http\Controllers\CalendarEventClientController;
use App\Http\Controllers\CalendarEventsSetupController;
use App\Http\Controllers\SubscriptionStatusController;
use App\Http\Controllers\CalendarMissedReasonController;
use App\Http\Controllers\FormBuilderController;
use App\Http\Controllers\CriticalPathController;
use App\Http\Controllers\WorkflowTemplateController;
use App\Http\Controllers\WorkflowDependencyController;
use App\Http\Controllers\WorkflowTaskDependencyController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\EmploymentTypeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\LateReasonController;

// Public Download Route
Route::view('/download', 'download')->name('download');

// Main login page - choose login type
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Super Admin Authentication routes
Route::get('/superadmin/login', [SuperAdminAuthController::class, 'showLoginForm'])->name('superadmin.login');
Route::post('/superadmin/login', [SuperAdminAuthController::class, 'login']);
Route::post('/superadmin/logout', [SuperAdminAuthController::class, 'logout'])->name('superadmin.logout');



// Registration and Password Reset (for tenants only)
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Forgot Password Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendOtp'])->name('password.email');
Route::get('/verify-otp', [AuthController::class, 'showVerifyOtpForm'])->name('password.otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('password.otp.verify');
Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Main login route (for tenants)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Show dashboard page (protected)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth.or.session']);

Route::get('/superadmindashboard', function () {
    return view('superadmindashboard');
})->middleware('auth');


// Root URL logic
Route::get('/', function () {
    if (Auth::check() || session()->has('user_id')) {
        return redirect('/dashboard');
    }
    return redirect()->route('login');
});

Route::get('/prospect', function(){
    return View('/prospect');
})->name('prospect');


//status routes
Route::middleware(['auth.or.session'])->group(function () {
Route::get('/status/fetch', [SalesStatusController::class, 'fetchSaleStatus'])->name('status.fetch');
Route::get('/status', [SalesStatusController::class, 'index'])->name('status');
Route::put('/status/{id}', [SalesStatusController::class, 'update']);
Route::delete('/status/{id}', [SalesStatusController::class, 'destroy']);
Route::post('/status/store', [SalesStatusController::class, 'store'])->name('status.store');
Route::get('/getStatuses', [SalesStatusController::class, 'getStatuses'])->name('getStatuses');
});

// Subscription status routes
Route::middleware(['auth.or.session'])->group(function () {
Route::get('/subscription-status/fetch', [SubscriptionStatusController::class, 'fetch'])->name('subscription-status.fetch');
Route::get('/subscription-status', [SubscriptionStatusController::class, 'index'])->name('subscription-status.index');
Route::put('/subscription-status/{id}', [SubscriptionStatusController::class, 'update']);
Route::delete('/subscription-status/{id}', [SubscriptionStatusController::class, 'destroy']);
Route::post('/subscription-status/store', [SubscriptionStatusController::class, 'store'])->name('subscription-status.store');
Route::get('/get-subscription-statuses', [SubscriptionStatusController::class, 'getStatuses'])->name('subscription-status.list');
});

Route::middleware(['auth.or.session', 'tenant.db'])->group(function () {
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

    Route::get('/places', [\App\Http\Controllers\PlaceController::class, 'index'])->name('places.index');
    Route::get('/places/list', [\App\Http\Controllers\PlaceController::class, 'list'])->name('places.list');
    Route::post('/places', [\App\Http\Controllers\PlaceController::class, 'store'])->name('places.store');
    Route::put('/places/{place}', [\App\Http\Controllers\PlaceController::class, 'update'])->name('places.update');
    Route::delete('/places/{place}', [\App\Http\Controllers\PlaceController::class, 'destroy'])->name('places.destroy');
});

Route::middleware(['auth.or.session', 'tenant.db'])->group(function () {
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
});

// Calendar (dummy view)
Route::middleware(['auth.or.session'])->group(function () {
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
    Route::get('/calendar/event/{id}/details', [CalendarController::class, 'eventDetails'])->name('calendar.event.details');
    Route::get('/calendar/grid', [CalendarController::class, 'grid'])->name('calendar.grid');
    Route::get('/calendar/date/{date}/handles', [CalendarController::class, 'dateHandles'])->name('calendar.date.handles');
    Route::post('/calendar/date/handle/toggle', [CalendarController::class, 'toggleDateHandle'])->name('calendar.date.handle.toggle');
    Route::post('/calendar/date/client/status', [CalendarController::class, 'saveDateClientStatus'])->name('calendar.date.client.status');
    Route::get('/calendar/status/{id}/checklists', [CalendarController::class, 'statusChecklists'])->name('calendar.status.checklists');
    
    
    // Checklist simple view
    Route::get('/checklist', function(){ return view('checklist.index'); })->name('checklist.index');
    // Checklist CRUD
    Route::get('/checklist/fetch', [\App\Http\Controllers\ChecklistController::class, 'fetch'])->name('checklist.fetch');
    Route::post('/checklist/store', [\App\Http\Controllers\ChecklistController::class, 'store'])->name('checklist.store');
    Route::put('/checklist/{id}', [\App\Http\Controllers\ChecklistController::class, 'update'])->name('checklist.update');
    Route::delete('/checklist/{id}', [\App\Http\Controllers\ChecklistController::class, 'destroy'])->name('checklist.destroy');
    // Checklist Options CRUD
    Route::get('/checklist/options/fetch', [\App\Http\Controllers\ChecklistController::class, 'fetchOptions'])->name('checklist.options.fetch');
    Route::post('/checklist/options/store', [\App\Http\Controllers\ChecklistController::class, 'storeOption'])->name('checklist.options.store');
    Route::put('/checklist/options/{id}', [\App\Http\Controllers\ChecklistController::class, 'updateOption'])->name('checklist.options.update');
    Route::delete('/checklist/options/{id}', [\App\Http\Controllers\ChecklistController::class, 'destroyOption'])->name('checklist.options.destroy');
    // Manage and CRUD routes removed

    // Calendar Status-Checklist linking (setup)
    Route::get('/calendar/status-checklist', [\App\Http\Controllers\CalendarStatusChecklistController::class, 'index'])->name('calendar-status-checklist.index');
    Route::get('/calendar/status-checklist/fetch', [\App\Http\Controllers\CalendarStatusChecklistController::class, 'fetch'])->name('calendar-status-checklist.fetch');
    Route::post('/calendar/status-checklist/update', [\App\Http\Controllers\CalendarStatusChecklistController::class, 'updateRelationships'])->name('calendar-status-checklist.update');

    // Client-Event Links (two-step)
    Route::get('/calendar/client-event-links', [\App\Http\Controllers\ClientEventLinkController::class, 'clientsView'])->name('calendar-client-event.links');
    Route::get('/calendar/client-event-links/clients', [\App\Http\Controllers\ClientEventLinkController::class, 'fetchClients'])->name('calendar-client-event.clients');
    Route::get('/calendar/client-event-links/{clientId}', [\App\Http\Controllers\ClientEventLinkController::class, 'eventsView'])->name('calendar-client-event.events.view');
    Route::get('/calendar/client-event-links/{clientId}/events', [\App\Http\Controllers\ClientEventLinkController::class, 'fetchEvents'])->name('calendar-client-event.events');
    Route::post('/calendar/client-event-links/{clientId}/save', [\App\Http\Controllers\ClientEventLinkController::class, 'saveLinks'])->name('calendar-client-event.save');
    Route::get('/calendar/client-event-links/{clientId}/common-events', [\App\Http\Controllers\ClientEventLinkController::class, 'fetchCommonEvents'])->name('calendar-client-event.common.fetch');
    Route::post('/calendar/client-event-links/{clientId}/common-events/save', [\App\Http\Controllers\ClientEventLinkController::class, 'saveCommonEvents'])->name('calendar-client-event.common.save');

    // Common Events CRUD
    Route::get('/calendar/common-events', [\App\Http\Controllers\CommonEventController::class, 'index'])->name('common-events.index');
    Route::get('/calendar/common-events/fetch', [\App\Http\Controllers\CommonEventController::class, 'fetch'])->name('common-events.fetch');
    Route::post('/calendar/common-events/store', [\App\Http\Controllers\CommonEventController::class, 'store'])->name('common-events.store');
    Route::put('/calendar/common-events/{id}', [\App\Http\Controllers\CommonEventController::class, 'update'])->name('common-events.update');
    Route::delete('/calendar/common-events/{id}', [\App\Http\Controllers\CommonEventController::class, 'destroy'])->name('common-events.destroy');
    // Calendar Clients setup - MUST come before /calendar/{id} route
    Route::get('/calendar/clients', [CalendarClientController::class, 'index'])->name('calendar-clients.index');
    Route::get('/calendar/clients/fetch', [CalendarClientController::class, 'fetch'])->name('calendar-clients.fetch');
    Route::post('/calendar/clients/store', [CalendarClientController::class, 'store'])->name('calendar-clients.store');
    Route::put('/calendar/clients/{id}', [CalendarClientController::class, 'update'])->name('calendar-clients.update');
    Route::delete('/calendar/clients/{id}', [CalendarClientController::class, 'destroy'])->name('calendar-clients.destroy');

    // Calendar Status setup - MUST come before /calendar/{id} route
    Route::get('/calendar/status', [CalendarStatusController::class, 'index'])->name('calendar-status.index');
    Route::get('/calendar/status/fetch', [CalendarStatusController::class, 'fetch'])->name('calendar-status.fetch');
    Route::post('/calendar/status/store', [CalendarStatusController::class, 'store'])->name('calendar-status.store');
    Route::put('/calendar/status/{id}', [CalendarStatusController::class, 'update'])->name('calendar-status.update');
    Route::delete('/calendar/status/{id}', [CalendarStatusController::class, 'destroy'])->name('calendar-status.destroy');

    // Calendar Social Handle setup - MUST come before /calendar/{id} route
    Route::get('/calendar/social', [CalendarSocialHandleController::class, 'index'])->name('calendar-social.index');
    Route::get('/calendar/social/fetch', [CalendarSocialHandleController::class, 'fetch'])->name('calendar-social.fetch');
    Route::post('/calendar/social/store', [CalendarSocialHandleController::class, 'store'])->name('calendar-social.store');
    Route::put('/calendar/social/{id}', [CalendarSocialHandleController::class, 'update'])->name('calendar-social.update');
    Route::delete('/calendar/social/{id}', [CalendarSocialHandleController::class, 'destroy'])->name('calendar-social.destroy');

    // Calendar Client Social setup - MUST come before /calendar/{id} route
    Route::get('/calendar/client-social', [CalendarClientSocialController::class, 'index'])->name('calendar-client-social.index');
    Route::get('/calendar/client-social/fetch', [CalendarClientSocialController::class, 'fetch'])->name('calendar-client-social.fetch');
    Route::post('/calendar/client-social/update', [CalendarClientSocialController::class, 'updateRelationships'])->name('calendar-client-social.update');

    // Calendar Events setup (CRUD UI)
    Route::get('/calendar/events-setup', [CalendarEventsSetupController::class, 'index'])->name('calendar-events.index');
    Route::get('/calendar/events-setup/fetch', [CalendarEventsSetupController::class, 'fetch'])->name('calendar-events.fetch');
    Route::post('/calendar/events-setup/store', [CalendarEventsSetupController::class, 'store'])->name('calendar-events.store');
    Route::put('/calendar/events-setup/{id}', [CalendarEventsSetupController::class, 'update'])->name('calendar-events.update');
    Route::delete('/calendar/events-setup/{id}', [CalendarEventsSetupController::class, 'destroy'])->name('calendar-events.destroy');

    // Calendar Missed Reasons setup (CRUD UI)
    Route::get('/calendar/missed-reasons', [CalendarMissedReasonController::class, 'index'])->name('calendar-missed-reasons.index');
    Route::get('/calendar/missed-reasons/fetch', [CalendarMissedReasonController::class, 'fetch'])->name('calendar-missed-reasons.fetch');
    Route::post('/calendar/missed-reasons/store', [CalendarMissedReasonController::class, 'store'])->name('calendar-missed-reasons.store');
    Route::put('/calendar/missed-reasons/{id}', [CalendarMissedReasonController::class, 'update'])->name('calendar-missed-reasons.update');
    Route::delete('/calendar/missed-reasons/{id}', [CalendarMissedReasonController::class, 'destroy'])->name('calendar-missed-reasons.destroy');

    // Calendar Event Client setup removed

    // Parameterized routes removed (no direct CRUD)
});

// 

//lead source
Route::middleware(['auth.or.session'])->group(function () {
Route::get('/source/fetch', [SalesLeadSourceController::class, 'fetchSalesources'])->name('source.fetch');
Route::get('/source', [SalesLeadSourceController::class, 'index'])->name('source');
Route::put('/source/{id}', [SalesLeadSourceController::class, 'update']);
Route::delete('/source/{id}', [SalesLeadSourceController::class, 'destroy']);
Route::post('/source/store', [SalesLeadSourceController::class, 'store'])->name('source.store');
Route::get('/getsource', [SalesLeadSourceController::class, 'getsource'])->name('getsource');
});


//sales product
Route::middleware(['auth.or.session'])->group(function () {
Route::get('/product/fetch', [SalesProductController::class, 'fetchSalesProducts'])->name('product.fetch');
Route::get('/product', [SalesProductController::class, 'index'])->name('product');
Route::put('/product/{id}', [SalesProductController::class, 'update']);
Route::delete('/product/{id}', [SalesProductController::class, 'destroy']);
Route::post('/product/store', [SalesProductController::class, 'store'])->name('product.store');
Route::get('/getproduct', [SalesProductController::class, 'getproduct'])->name('getproduct');
});


//sales business type
Route::middleware(['auth.or.session'])->group(function () {
Route::get('/business/fetch', [SalesBusinessTypeController::class, 'fetchSalesBusiness'])->name('business.fetch');
Route::get('/business', [SalesBusinessTypeController::class, 'index'])->name('business');
Route::put('/business/{id}', [SalesBusinessTypeController::class, 'update']);
Route::delete('/business/{id}', [SalesBusinessTypeController::class, 'destroy']);
Route::post('/business/store', [SalesBusinessTypeController::class, 'store'])->name('business.store');
Route::get('/getbusiness', [SalesBusinessTypeController::class, 'getbusiness'])->name('getbusiness');
});


//sales state
Route::middleware(['auth.or.session', 'tenant.db'])->group(function () {
Route::get('/state/fetch', [SalesStateController::class, 'fetchSalesStates'])->name('state.fetch');
Route::get('/state', [SalesStateController::class, 'index'])->name('state');
Route::put('/state/{id}', [SalesStateController::class, 'update']);
Route::delete('/state/{id}', [SalesStateController::class, 'destroy']);
Route::post('/state/store', [SalesStateController::class, 'store'])->name('state.store');
});


//sales city
Route::middleware(['auth.or.session'])->group(function () {
Route::get('/city/fetch', [SalesCityController::class, 'fetchSalesCities'])->name('city.fetch');
Route::get('/city', [SalesCityController::class, 'index'])->name('city');
Route::put('/city/{id}', [SalesCityController::class, 'update']);
Route::delete('/city/{id}', [SalesCityController::class, 'destroy']);
Route::post('/city/store', [SalesCityController::class, 'store'])->name('city.store');
Route::get('/city/{state_id}', [SalesCityController::class, 'getCities'])->name('get.city');
Route::get('/allcity', [SalesCityController::class, 'allcity'])->name('allcity');
});


// prospectus
Route::middleware(['auth.or.session'])->group(function () {
Route::post('/prospectus', [ProspectusController::class, 'store']);
Route::get('/getProspectus', [ProspectusController::class, 'getProspectus'])->name('getProspectus');
Route::get('/fillprospectus/{id}', [ProspectusController::class, 'fillprospectus'])->name('fillprospectus');
Route::post('/updateprospectus/{id}', [ProspectusController::class, 'update'])->name('updateprospectus');
});

//sales lead
Route::middleware(['auth.or.session'])->group(function () {
Route::get('/lead', [SalesLeadController::class, 'index'])->name('lead');
Route::Post('/savelead', [SalesLeadController::class, 'store'])->name('savelead');
});

// sales followup + quotations (need tenant context)
Route::middleware(['auth.or.session', 'tenant.db'])->group(function () {
Route::get('/followup', [FollowupController::class, 'index'])->name('followup');
Route::get('/followup/summary-stats', [FollowupController::class, 'getSummaryStats'])->name('followup.summary-stats');
Route::get('/followup/status-counts', [FollowupController::class, 'getStatusCounts'])->name('followup.status-counts');
Route::post('/filter', [FollowupController::class, 'filter'])->name('filter');
Route::post('/filterdate', [FollowupController::class, 'filterdate'])->name('filterdate');
Route::get('/sales-records', [FollowupController::class, 'getSalesRecords'])->name('sales.records');
Route::get('/search', [FollowupController::class, 'search'])->name('search');
Route::get('/quotation', [\App\Http\Controllers\QuotationController::class, 'index'])->name('quotation');
Route::get('/quotation/create', [\App\Http\Controllers\QuotationController::class, 'create'])->name('quotation.create');
Route::get('/quotation/customers', [\App\Http\Controllers\QuotationController::class, 'getCustomers'])->name('quotation.customers');
Route::get('/quotation/prospects', [\App\Http\Controllers\QuotationController::class, 'getProspects'])->name('quotation.prospects');
Route::get('/quotation/products', [\App\Http\Controllers\QuotationController::class, 'getSalesProducts'])->name('quotation.products');
Route::get('/quotation/generate-number', [\App\Http\Controllers\QuotationController::class, 'generateQuotationNumber'])->name('quotation.generate-number');
Route::get('/quotation/current-date', [\App\Http\Controllers\QuotationController::class, 'getCurrentDate'])->name('quotation.current-date');
Route::post('/quotation/store', [\App\Http\Controllers\QuotationController::class, 'store'])->name('quotation.store');
Route::get('/quotation/list', [\App\Http\Controllers\QuotationController::class, 'list'])->name('quotation.list');
Route::get('/quotation/show/{number}', [\App\Http\Controllers\QuotationController::class, 'showByNumber'])->name('quotation.show');
Route::get('/quotation/latest', [\App\Http\Controllers\QuotationController::class, 'latestForEntity'])->name('quotation.latest');
Route::get('/quotation/{id}/revisions', [\App\Http\Controllers\QuotationController::class, 'revisions'])->name('quotation.revisions');
Route::get('/quotation/payment-terms', [\App\Http\Controllers\QuotationController::class, 'getPaymentTerms'])->name('quotation.payment-terms');
Route::get('/quotation/{quotation}/download', [\App\Http\Controllers\QuotationController::class, 'download'])->name('quotation.download');
Route::get('/quotation/setup', [\App\Http\Controllers\QuotationSetupController::class, 'index'])->name('quotation.setup');
Route::get('/quotation/setup/fetch', [\App\Http\Controllers\QuotationSetupController::class, 'fetch'])->name('quotation.setup.fetch');
Route::post('/quotation/setup/store', [\App\Http\Controllers\QuotationSetupController::class, 'store'])->name('quotation.setup.store');
Route::get('/quotation/setup/get', [\App\Http\Controllers\QuotationSetupController::class, 'getSettings'])->name('quotation.setup.get');
});




// Worklog reports
Route::middleware(['auth.or.session'])->group(function () {
    Route::get('/reports/worklog', [\App\Http\Controllers\WorklogReportController::class, 'index'])->name('reports.worklog');
    Route::get('/reports/worklog/fetch', [\App\Http\Controllers\WorklogReportController::class, 'fetchWorklogs']);
    Route::get('/reports/user-worklog', [\App\Http\Controllers\WorklogReportController::class, 'userReport'])->name('reports.user-worklog');
    Route::get('/reports/user-worklog/fetch', [\App\Http\Controllers\WorklogReportController::class, 'fetchUserWorklogs']);
    Route::get('/reports/user-worklog/customers', [\App\Http\Controllers\WorklogReportController::class, 'fetchCustomersForUser']);
});

// sales Remark
Route::middleware(['auth.or.session'])->group(function () {
Route::get('/remark', [RemarkController::class, 'index'])->name('remark');
Route::post('/saveremark', [RemarkController::class, 'store'])->name('saveremark');
});


// dashboard
Route::middleware(['auth.or.session'])->group(function () {
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
Route::get('/piedata', [SalesDashboardController::class, 'piedata'])->name('piedata');
Route::get('/bardata', [SalesDashboardController::class, 'bardata'])->name('bardata');
Route::get('/user-tasks', [SalesDashboardController::class, 'userTasks'])->name('user-tasks');
Route::get('/todayfollowupstable', [SalesDashboardController::class, 'todayfollowupstable'])->name('todayfollowupstable');
Route::get('/underprocesstable', [SalesDashboardController::class, 'underprocesstable'])->name('underprocesstable');
Route::get('/todaycompletedtable', [SalesDashboardController::class, 'todaycompletedtable'])->name('todaycompletedtable');
Route::get('/todaypendingtable', [SalesDashboardController::class, 'todaypendingtable'])->name('todaypendingtable');
Route::get('/todaynewtable', [SalesDashboardController::class, 'todaynewtable'])->name('todaynewtable');

// todal followups
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
});




// user

Route::middleware(['auth.or.session'])->group(function () {
Route::get('/user', [UserController::class, 'index'])->name('user');
Route::get('/fetchuser', [UserController::class, 'fetchuser'])->name('fetchuser');
Route::get('/user/fetch-for-manager', [UserController::class, 'fetchUsersForManager'])->name('fetchUsersForManager');
Route::get('/user/sales-users', [UserController::class, 'fetchSalesUsers'])->name('user.sales-users');
Route::get('/user/fetch-employees', [UserController::class, 'fetchEmployees'])->name('user.fetch-employees');
Route::get('/fetchrole', [RoleController::class, 'fetchrole'])->name('fetchrole');
Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.destroy');
});

// User Profile (Employee Information)
Route::middleware(['auth.or.session'])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/documents', [\App\Http\Controllers\ProfileController::class, 'storeDocument'])->name('profile.documents.store');
    Route::delete('/profile/documents/{document}', [\App\Http\Controllers\ProfileController::class, 'destroyDocument'])->name('profile.documents.destroy');
    Route::get('/profile/picture/{id}', [\App\Http\Controllers\ProfileController::class, 'getProfilePicture'])->name('profile.picture');
});

// Role Master routes
Route::middleware(['auth.or.session'])->group(function () {
Route::get('/role-master', [App\Http\Controllers\RoleMasterController::class, 'index'])->name('role-master');
Route::get('/role-master/fetch', [App\Http\Controllers\RoleMasterController::class, 'fetch'])->name('role-master.fetch');
Route::get('/role-master/permissions', [App\Http\Controllers\RoleMasterController::class, 'getPermissions'])->name('role-master.permissions');
Route::post('/role-master', [App\Http\Controllers\RoleMasterController::class, 'store'])->name('role-master.store');
Route::get('/role-master/{role}/edit', [App\Http\Controllers\RoleMasterController::class, 'edit'])->name('role-master.edit');
Route::put('/role-master/{role}', [App\Http\Controllers\RoleMasterController::class, 'update'])->name('role-master.update');
Route::delete('/role-master/{role}', [App\Http\Controllers\RoleMasterController::class, 'destroy'])->name('role-master.destroy');
});


// all data for admin (role_id = 1)
Route::middleware(['auth.or.session'])->group(function () {
    Route::get('/alldata', [AllDataController::class, 'index'])->name('alldata');
    Route::get('/fetchalldata', [AllDataController::class, 'fetchalldata'])->name('fetchalldata');
    Route::get('/alldata/summary-stats', [AllDataController::class, 'getSummaryStats'])->name('alldata.summary-stats');
    Route::get('/alldata/status-counts', [AllDataController::class, 'getStatusCounts'])->name('alldata.status-counts');
    Route::post('/alldatafilter', [AllDataController::class, 'alldatafilter'])->name('alldatafilter');
    Route::get('/alldatasearch', [AllDataController::class, 'alldatasearch'])->name('alldatasearch');
    Route::post('/alldatafilterdate', [AllDataController::class, 'alldatafilterdate'])->name('alldatafilterdate');
    Route::post('/alldata/reassign', [AllDataController::class, 'reassignLead'])->name('alldata.reassign');
    Route::get('/alldata/team-members', [AllDataController::class, 'getTeamMembers'])->name('alldata.team-members');

    // Alldata Today Followups
    Route::get('/alldata/today-followups', [AllDataController::class, 'todayFollowupsTable'])->name('alldata.today-followups');
    Route::get('/alldata/today-followups-data', [AllDataController::class, 'todayFollowupsData'])->name('alldata.today-followups.data');

    // Alldata Under Process
    Route::get('/alldata/under-process', [AllDataController::class, 'underProcessTable'])->name('alldata.under-process');
    Route::get('/alldata/under-process-data', [AllDataController::class, 'underProcessData'])->name('alldata.under-process.data');

    // Alldata Today Completed
    Route::get('/alldata/today-completed', [AllDataController::class, 'todayCompletedTable'])->name('alldata.today-completed');
    Route::get('/alldata/today-completed-data', [AllDataController::class, 'todayCompletedData'])->name('alldata.today-completed.data');

    // Alldata Today Pending
    Route::get('/alldata/today-pending', [AllDataController::class, 'todayPendingTable'])->name('alldata.today-pending');
    Route::get('/alldata/today-pending-data', [AllDataController::class, 'todayPendingData'])->name('alldata.today-pending.data');

    // Alldata Today New
    Route::get('/alldata/today-new', [AllDataController::class, 'todayNewTable'])->name('alldata.today-new');
    Route::get('/alldata/today-new-data', [AllDataController::class, 'todayNewData'])->name('alldata.today-new.data');
    
    // Sales Analytics Dashboard
    Route::get('/sales-analytics', [SalesAnalyticsController::class, 'index'])->name('sales-analytics');
    Route::get('/sales-analytics/data', [SalesAnalyticsController::class, 'getAnalytics'])->name('sales-analytics.data');
    Route::get('/sales-analytics/user', [SalesAnalyticsController::class, 'getUserAnalytics'])->name('sales-analytics.user');
    Route::get('/sales-analytics/users', [SalesAnalyticsController::class, 'getUsers'])->name('sales-analytics.users');
});


// super admin
Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/superadmin/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/superadmin/stats', [SuperAdminController::class, 'dashboardStats'])->name('superadmin.stats');
    Route::get('/superadmin/analytics', [SuperAdminController::class, 'systemAnalytics'])->name('superadmin.analytics');
    Route::get('/superadmin/tenant/{id}/activity', [SuperAdminController::class, 'tenantActivity'])->name('superadmin.tenant.activity');
    Route::get('/superadmin/tenant/{id}/export', [SuperAdminController::class, 'exportTenantData'])->name('superadmin.tenant.export');
    Route::get('/totaltenant',[SuperAdminController::class, 'totaltenant'])->name('totaltenant');
    Route::get('/viewtenant',[SuperAdminController::class, 'viewtenant'])->name('viewtenant');
});

// tenant management
Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/tenant', [TenantController::class, 'index'])->name('tenant');
    Route::get('/tenant/fetch', [TenantController::class, 'fetchTenants'])->name('tenant.fetch');
    Route::post('/tenant/store', [TenantController::class, 'store'])->name('tenant.store');
    Route::put('/tenant/{id}', [TenantController::class, 'update'])->name('tenant.update');
    Route::delete('/tenant/{id}', [TenantController::class, 'destroy'])->name('tenant.destroy');
    Route::post('/tenant/{id}/regenerate-code', [TenantController::class, 'regenerateCode'])->name('tenant.regenerate-code');
});

// my leads
Route::middleware(['auth.or.session'])->group(function () {
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
    Route::get('/test-team-members', [MyLeadsController::class, 'getTeamMembers'])->name('test.team-members');

    // Payment Followup routes
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
    Route::get('/payment-followup/customer/{id}/leads', [PaymentFollowupController::class, 'getCloseWonLeads'])->name('payment-followup.customer-leads'); // name kept for compatibility, logic updated in controller
    Route::get('/payment-followup/details/{type}', [PaymentFollowupController::class, 'details'])->name('payment-followup.details');
    Route::get('/payment-followup/details/{type}/data', [PaymentFollowupController::class, 'getDetailsData'])->name('payment-followup.details.data');
    
    // Invoice routes
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{id}/get', [InvoiceController::class, 'getInvoice'])->name('invoices.get');
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::put('/invoices/{id}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    
    // Invoice Followup routes
    Route::get('/invoice-followup/{invoiceId}', [InvoiceFollowupController::class, 'index'])->name('invoice-followup.index');
    Route::get('/invoice-followup/{invoiceId}/data', [InvoiceFollowupController::class, 'getFollowups'])->name('invoice-followup.data');
    Route::get('/invoice-followup/{invoiceId}/followup/{id}', [InvoiceFollowupController::class, 'getFollowup'])->name('invoice-followup.get');
    Route::post('/invoice-followup/{invoiceId}', [InvoiceFollowupController::class, 'store'])->name('invoice-followup.store');
    Route::put('/invoice-followup/{invoiceId}/followup/{id}', [InvoiceFollowupController::class, 'update'])->name('invoice-followup.update');

    // Subscription routes
    // Subscription routes - specific routes must come BEFORE parameterized routes
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/customer/{customerId}', [SubscriptionController::class, 'customerSubscriptions'])->name('subscriptions.customer');
    Route::get('/subscriptions/fetch', [SubscriptionController::class, 'getSubscriptions'])->name('subscriptions.fetch');
    Route::get('/subscriptions/fetch-all', [SubscriptionController::class, 'fetchAllSubscriptions'])->name('subscriptions.fetch-all');
    Route::get('/subscriptions/filter-options', [SubscriptionController::class, 'getFilterOptions'])->name('subscriptions.filter-options');
    Route::get('/subscriptions/products', [SubscriptionController::class, 'getProducts'])->name('subscriptions.products');
    Route::get('/subscriptions/customers', [SubscriptionController::class, 'getCustomers'])->name('subscriptions.customers');
    Route::get('/subscriptions/cities/{stateId}', [SubscriptionController::class, 'getCitiesByState'])->name('subscriptions.cities-by-state');
    Route::match(['get', 'post'], '/subscriptions/filter', [SubscriptionController::class, 'filterSubscriptions'])->name('subscriptions.filter');
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    // Inline status change
    Route::patch('/subscriptions/{id}/status', [SubscriptionController::class, 'updateStatus'])->name('subscriptions.update-status');
    Route::get('/subscriptions/{id}/history', [SubscriptionController::class, 'history'])->name('subscriptions.history');
    // Parameterized routes must come AFTER specific routes
    Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::put('/subscriptions/{id}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
    Route::delete('/subscriptions/{id}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');

    // Tracking
    Route::get('/tracking', [\App\Http\Controllers\TrackingController::class, 'index'])->name('tracking.index');
    Route::get('/tracking/fetch-locations', [\App\Http\Controllers\TrackingController::class, 'fetchLocations'])->name('tracking.fetch-locations');

});

// team leads - only for managers
Route::middleware(['auth.or.session'])->group(function () {
    Route::get('/teamleads', [TeamLeadsController::class, 'index'])->name('teamleads');
    Route::get('/teamleads/data', [TeamLeadsController::class, 'getTeamLeads'])->name('teamleads.data');
    Route::post('/teamleads/filter', [TeamLeadsController::class, 'filterTeamLeads'])->name('teamleads.filter');
    Route::get('/teamleads/cities/{stateId}', [TeamLeadsController::class, 'getCitiesByState'])->name('teamleads.cities');
    
    Route::get('/teamleads/stats', [TeamLeadsController::class, 'getTeamLeadStats'])->name('teamleads.stats');
    Route::post('/teamleads/export', [TeamLeadsController::class, 'exportTeamLeads'])->name('teamleads.export');
    Route::post('/teamleads/reassign', [TeamLeadsController::class, 'reassignLead'])->name('teamleads.reassign');
    Route::get('/teamleads/team-members', [TeamLeadsController::class, 'getTeamMembers'])->name('teamleads.team-members');
    
    // Team Analytics routes
    Route::get('/team-analytics', [TeamAnalyticsController::class, 'index'])->name('team-analytics');
    Route::get('/team-analytics/members', [TeamAnalyticsController::class, 'getTeamMembers'])->name('team-analytics.members');
    Route::post('/team-analytics/member', [TeamAnalyticsController::class, 'getMemberAnalytics'])->name('team-analytics.member');
    Route::get('/team-analytics/overview', [TeamAnalyticsController::class, 'getTeamOverview'])->name('team-analytics.overview');
    Route::get('/team-analytics/remarks', [TeamAnalyticsController::class, 'getRemarks'])->name('team-analytics.remarks');
    Route::get('/team-analytics/debug-remarks', [TeamAnalyticsController::class, 'debugRemarks'])->name('team-analytics.debug-remarks');
});

// Customer Project Module Routes
Route::middleware(['auth.or.session'])->group(function () {
    // Customer routes
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
    Route::get('/customer/fetch', [CustomerController::class, 'fetchCustomers'])->name('customer.fetch');
    Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');
    Route::put('/customer/{id}', [CustomerController::class, 'update'])->name('customer.update');
    Route::delete('/customer/{id}', [CustomerController::class, 'destroy'])->name('customer.destroy');




    // Payment Terms routes
    Route::middleware(['auth.or.session', 'tenant.db'])->group(function () {
        Route::get('/payment-terms', [\App\Http\Controllers\PaymentTermController::class, 'index'])->name('payment-terms');
        Route::get('/payment-terms/fetch', [\App\Http\Controllers\PaymentTermController::class, 'fetch'])->name('payment-terms.fetch');
        Route::post('/payment-terms/store', [\App\Http\Controllers\PaymentTermController::class, 'store'])->name('payment-terms.store');
        Route::get('/payment-terms/{id}', [\App\Http\Controllers\PaymentTermController::class, 'show'])->name('payment-terms.show');
        Route::put('/payment-terms/{id}', [\App\Http\Controllers\PaymentTermController::class, 'update'])->name('payment-terms.update');
        Route::delete('/payment-terms/{id}', [\App\Http\Controllers\PaymentTermController::class, 'destroy'])->name('payment-terms.destroy');
        Route::patch('/payment-terms/{id}/toggle-status', [\App\Http\Controllers\PaymentTermController::class, 'toggleStatus'])->name('payment-terms.toggle-status');
    });

    // Quotation helper routes (tenant context) - align with customers/products fetch
    // (payment terms route defined earlier)
    
    // Simple test route without tenant middleware
    Route::get('/payment-terms/simple-test', function() {
        try {
            $count = \App\Models\PaymentTerm::count();
            return response()->json(['count' => $count, 'message' => 'PaymentTerm model works without tenant context']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    });

    // Form Builder (Lead Form)
    Route::get('/form-builder', [FormBuilderController::class, 'index'])->name('formbuilder.index');
    Route::get('/form-builder/create', [FormBuilderController::class, 'create'])->name('formbuilder.create');
    Route::get('/form-builder/fields', [FormBuilderController::class, 'fields'])->name('formbuilder.fields');
    Route::get('/form-builder/list', [FormBuilderController::class, 'list'])->name('formbuilder.list');
    Route::get('/form-builder/{form}/edit', [FormBuilderController::class, 'edit'])->name('formbuilder.edit');
    Route::get('/form-builder/{form}/config', [FormBuilderController::class, 'config'])->name('formbuilder.config');
    Route::post('/form-builder/{form}/config', [FormBuilderController::class, 'saveConfig'])->name('formbuilder.config.save');
    Route::post('/form-builder/{form}/config/test', [FormBuilderController::class, 'testConnection'])->name('formbuilder.config.test');
    Route::middleware(['tenant.db'])->group(function () {
        Route::get('/form-builder/{form}/view', [FormBuilderController::class, 'viewPage'])->name('formbuilder.view');
        Route::post('/form-builder/{form}/submit', [FormBuilderController::class, 'submit'])->name('formbuilder.submit');
    });
    Route::get('/form-builder/{form}', [FormBuilderController::class, 'show'])->name('formbuilder.show');
    Route::post('/form-builder/store', [FormBuilderController::class, 'store'])->name('formbuilder.store');
    Route::put('/form-builder/{form}', [FormBuilderController::class, 'update'])->name('formbuilder.update');
    Route::delete('/form-builder/{form}', [FormBuilderController::class, 'destroy'])->name('formbuilder.destroy');

    // Service routes (renamed from Project)
    Route::middleware(['auth.or.session', 'tenant.db'])->group(function () {
        Route::get('/service', [ProjectController::class, 'index'])->name('service');
        Route::get('/service/fetch', [ProjectController::class, 'fetchProjects'])->name('service.fetch');
        Route::post('/service/store', [ProjectController::class, 'store'])->name('service.store');
        Route::put('/service/{id}', [ProjectController::class, 'update'])->name('service.update');
        Route::delete('/service/{id}', [ProjectController::class, 'destroy'])->name('service.destroy');

        // Module routes
        Route::get('/module', [ModuleController::class, 'index'])->name('module');
        Route::get('/module/fetch', [ModuleController::class, 'fetchModules'])->name('module.fetch');
        Route::get('/module/service/{serviceId}', [ModuleController::class, 'getModulesByService'])->name('module.by-service');
        Route::post('/module/store', [ModuleController::class, 'store'])->name('module.store');
        Route::put('/module/{id}', [ModuleController::class, 'update'])->name('module.update');
        Route::delete('/module/{id}', [ModuleController::class, 'destroy'])->name('module.destroy');
    });

    // Customer Service routes (renamed from Customer Project)
    Route::middleware(['auth.or.session', 'tenant.db'])->group(function () {
        Route::get('/customer-project', [CustomerProjectController::class, 'index'])->name('customer-project');
        Route::get('/customer-project/fetch', [CustomerProjectController::class, 'fetchCustomerProjects'])->name('customer-project.fetch');
        Route::post('/customer-project/store', [CustomerProjectController::class, 'store'])->name('customer-project.store');
        Route::put('/customer-project/{id}', [CustomerProjectController::class, 'update'])->name('customer-project.update');
        Route::delete('/customer-project/{id}', [CustomerProjectController::class, 'destroy'])->name('customer-project.destroy');
        Route::get('/customer-project/customers', [CustomerProjectController::class, 'getCustomers'])->name('customer-project.customers');
        Route::get('/customer-project/services', [CustomerProjectController::class, 'getServices'])->name('customer-project.services');
        Route::put('/customer-project/{customerProjectId}/module/{moduleId}/status', [CustomerProjectController::class, 'updateModuleStatus'])->name('customer-project.module-status');
    });

    // Customer Analytics Routes
        // Calling routes
        Route::get('/calling', [CallingController::class, 'index'])->name('calling');
        Route::get('/calling/my', [CallingController::class, 'my'])->name('calling.my');
        Route::get('/calling/junk', [CallingController::class, 'junk'])->name('calling.junk');
    Route::get('/customer-analytics', [CustomerAnalyticsController::class, 'index'])->name('customer-analytics.index');
    Route::get('/customer-analytics/customers', [CustomerAnalyticsController::class, 'getCustomers'])->name('customer-analytics.get-customers');
    Route::get('/customer-analytics/{customerId}', [CustomerAnalyticsController::class, 'getCustomerAnalytics'])->name('customer-analytics.show');
    Route::get('/customer-analytics/{customerId}/leads', [CustomerAnalyticsController::class, 'getCustomerLeadDetails'])->name('customer-analytics.leads');

// Entry Type routes
Route::middleware(['auth.or.session'])->group(function () {
Route::get('/entry-type', [EntryTypeController::class, 'index'])->name('entry-type.index');
Route::get('/entry-type/fetch', [EntryTypeController::class, 'fetch'])->name('entry-type.fetch');
Route::post('/entry-type', [EntryTypeController::class, 'store'])->name('entry-type.store');
Route::put('/entry-type/{id}', [EntryTypeController::class, 'update'])->name('entry-type.update');
Route::delete('/entry-type/{id}', [EntryTypeController::class, 'destroy'])->name('entry-type.destroy');
});

// Leave routes
Route::middleware(['auth.or.session'])->group(function () {
Route::get('/leave', [App\Http\Controllers\LeaveController::class, 'index'])->name('leave.index');
Route::get('/leave/fetch', [App\Http\Controllers\LeaveController::class, 'fetch'])->name('leave.fetch');
Route::get('/leave/types', [App\Http\Controllers\LeaveController::class, 'fetchLeaveTypes'])->name('leave.types');
Route::post('/leave', [App\Http\Controllers\LeaveController::class, 'store'])->name('leave.store');
Route::put('/leave/{id}', [App\Http\Controllers\LeaveController::class, 'update'])->name('leave.update');
Route::delete('/leave/{id}', [App\Http\Controllers\LeaveController::class, 'destroy'])->name('leave.destroy');
});
             
             // Worklog routes
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
             
             // Task Status routes
             Route::get('/task-status', [\App\Http\Controllers\TaskStatusController::class, 'index'])->name('task-status.index');
             Route::get('/task-status/fetch', [\App\Http\Controllers\TaskStatusController::class, 'fetch'])->name('task-status.fetch');
             Route::post('/task-status/store', [\App\Http\Controllers\TaskStatusController::class, 'store'])->name('task-status.store');
             Route::put('/task-status/{id}', [\App\Http\Controllers\TaskStatusController::class, 'update'])->name('task-status.update');
             Route::delete('/task-status/{id}', [\App\Http\Controllers\TaskStatusController::class, 'destroy'])->name('task-status.destroy');

             // All Tasks routes (shows everything)
             Route::get('/all-tasks', [\App\Http\Controllers\TaskController::class, 'allTasks'])->name('all-tasks.index');
             Route::get('/all-tasks/fetch', [\App\Http\Controllers\TaskController::class, 'fetchAllTasks'])->name('all-tasks.fetch');

             // Task routes (shows tasks created by user)
             Route::get('/task', [\App\Http\Controllers\TaskController::class, 'index'])->name('task.index');
             Route::get('/task/fetch', [\App\Http\Controllers\TaskController::class, 'fetch'])->name('task.fetch');
            Route::get('/task/users', [\App\Http\Controllers\TaskController::class, 'getUsers'])->name('task.users');
            Route::get('/task/customers', [\App\Http\Controllers\TaskController::class, 'getCustomers'])->name('task.customers');
            Route::get('/task/statuses', [\App\Http\Controllers\TaskController::class, 'getTaskStatuses'])->name('task.statuses');
             Route::get('/task/priorities', [\App\Http\Controllers\TaskController::class, 'getTaskPriorities'])->name('task.priorities');
             Route::get('/task/debug-statuses', [\App\Http\Controllers\TaskController::class, 'debugStatuses'])->name('task.debug-statuses');
             Route::post('/task/store', [\App\Http\Controllers\TaskController::class, 'store'])->name('task.store');
             Route::put('/task/{id}', [\App\Http\Controllers\TaskController::class, 'update'])->name('task.update');
             Route::get('/task/{taskId}/image/{imageId}', [\App\Http\Controllers\TaskController::class, 'serveImage'])->name('task.image');
             Route::delete('/task/{taskId}/image/{imageId}', [\App\Http\Controllers\TaskController::class, 'deleteImage'])->name('task.image.delete');
             Route::post('/task/{id}/toggle-done', [\App\Http\Controllers\TaskController::class, 'toggleDone'])->name('task.toggle-done');
             Route::post('/task/{id}/update-status', [\App\Http\Controllers\TaskController::class, 'updateStatus'])->name('task.update-status');
             Route::post('/task/{id}/update-status-id', [\App\Http\Controllers\TaskController::class, 'updateStatusById'])->name('task.update-status-id');
             Route::post('/task/{id}/poke', [\App\Http\Controllers\TaskController::class, 'poke'])->name('task.poke');
             Route::delete('/task/{id}', [\App\Http\Controllers\TaskController::class, 'destroy'])->name('task.destroy');
             
            // My Tasks routes (shows tasks assigned to user)
            Route::get('/my-tasks', [\App\Http\Controllers\TaskController::class, 'myTasks'])->name('my-tasks.index');
            Route::get('/my-tasks/fetch', [\App\Http\Controllers\TaskController::class, 'fetchMyTasks'])->name('my-tasks.fetch');
            Route::post('/task/remark/save', [\App\Http\Controllers\TaskController::class, 'saveRemark'])->name('task.remark.save');
             
             // Worklog History routes
             Route::get('/worklog-history', [WorklogHistoryController::class, 'index'])->name('worklog-history');
             Route::get('/worklog-history/fetch', [WorklogHistoryController::class, 'fetchWorklogs'])->name('worklog-history.fetch');
             Route::get('/worklog-history/stats', [WorklogHistoryController::class, 'getWorklogStats'])->name('worklog-history.stats');
             Route::delete('/worklog-history/{id}', [WorklogHistoryController::class, 'destroy'])->name('worklog-history.destroy');
             
             // Worklog Approvals route
             Route::get('/worklog-approvals', function() {
                 return view('worklog.approvals');
             })->name('worklog-approvals');
             
             // Holiday routes
             Route::get('/holiday', [HolidayController::class, 'index'])->name('holiday');
             Route::get('/holiday/fetch', [HolidayController::class, 'fetchHolidays'])->name('holiday.fetch');
             Route::post('/holiday', [HolidayController::class, 'store'])->name('holiday.store');
             Route::put('/holiday/{id}', [HolidayController::class, 'update'])->name('holiday.update');
             Route::delete('/holiday/{id}', [HolidayController::class, 'destroy'])->name('holiday.destroy');
             
            // Attendance routes (tenant DB)
            Route::middleware(['tenant.db'])->group(function () {
                // Attendance Approval
                Route::get('/attendance/approval', [App\Http\Controllers\AttendanceApprovalController::class, 'index'])->name('attendance.approval');
                Route::get('/attendance/approval/fetch', [App\Http\Controllers\AttendanceApprovalController::class, 'fetch'])->name('attendance.approval.fetch');
                Route::post('/attendance/approve/{id}', [App\Http\Controllers\AttendanceApprovalController::class, 'approve'])->name('attendance.approve');
                Route::post('/attendance/approve-bulk', [App\Http\Controllers\AttendanceApprovalController::class, 'bulkApprove'])->name('attendance.approve-bulk');
                Route::post('/attendance/update-times/{id}', [App\Http\Controllers\AttendanceApprovalController::class, 'updateTimes'])->name('attendance.update-times');
                
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
                Route::get('/attendance/stats-view', [AttendanceController::class, 'statsView'])->name('attendance.stats-view');
                Route::get('/attendance/advanced-stats', [AttendanceController::class, 'getAdvancedStats'])->name('attendance.advanced-stats');
                // Attendance report
                Route::get('/attendance/report', [AttendanceController::class, 'reportView'])->name('attendance.report');
                Route::post('/attendance/report-data', [AttendanceController::class, 'getReportData'])->name('attendance.report-data');
                Route::post('/attendance/monthly-report-data', [AttendanceController::class, 'getMonthlyReportData'])->name('attendance.monthly-report-data');
                Route::post('/attendance/date-report-data', [AttendanceController::class, 'getDateReportData'])->name('attendance.date-report-data');
                Route::get('/attendance/export-monthly-report', [AttendanceController::class, 'exportMonthlyReport'])->name('attendance.export-monthly-report');
                Route::get('/attendance/check-worklog-validation', [AttendanceController::class, 'checkWorklogValidation'])->name('attendance.check-worklog-validation');
                // Worklog report
                Route::get('/reports/worklog', [\App\Http\Controllers\WorklogReportController::class, 'index'])->name('reports.worklog');
                Route::get('/reports/worklog/fetch', [\App\Http\Controllers\WorklogReportController::class, 'fetchWorklogs'])->name('reports.worklog.fetch');
                Route::get('/reports/worklog/stats', [\App\Http\Controllers\WorklogReportController::class, 'getStats'])->name('reports.worklog.stats');
            });
         });

// assigned leads - for team members to see leads assigned to them
Route::middleware(['auth.or.session'])->group(function () {
    Route::get('/assignedleads', [AssignedLeadsController::class, 'index'])->name('assignedleads');
    Route::get('/assignedleads/data', [AssignedLeadsController::class, 'getAssignedLeads'])->name('assignedleads.data');
    Route::get('/assignedleads/summary-stats', [AssignedLeadsController::class, 'getSummaryStats'])->name('assignedleads.summary-stats');
    Route::get('/assignedleads/status-counts', [AssignedLeadsController::class, 'getStatusCounts'])->name('assignedleads.status-counts');
    Route::post('/assignedleads/filter', [AssignedLeadsController::class, 'filterAssignedLeads'])->name('assignedleads.filter');
    Route::get('/assignedleads/filter-options', [AssignedLeadsController::class, 'getFilterOptions'])->name('assignedleads.filter-options');
    Route::get('/assignedleads/cities/{stateId}', [AssignedLeadsController::class, 'getCitiesByState'])->name('assignedleads.cities');
});

// calling routes (tenant DB)
Route::middleware(['auth.or.session', 'tenant.db'])->group(function () {
    Route::get('/calling', [CallingController::class, 'index'])->name('calling');
    Route::get('/calling/data', [CallingController::class, 'getCallings'])->name('calling.data');
    Route::post('/calling/filter', [CallingController::class, 'filterCallings'])->name('calling.filter');
    Route::get('/calling/filter-options', [CallingController::class, 'getFilterOptions'])->name('calling.filter-options');
    Route::get('/calling/cities/{stateId}', [CallingController::class, 'getCitiesByState'])->name('calling.cities');
    Route::post('/calling/update-type', [CallingController::class, 'updateCallingType'])->name('calling.update-type');
    Route::post('/calling/lock-selected', [CallingController::class, 'lockSelected'])->name('calling.lock-selected');
    Route::get('/todays-calling', [TodaysCallingController::class, 'index'])->name('todays-calling');
    // Alias for legacy/menu usage
    Route::get('/calling/todays', [TodaysCallingController::class, 'index'])->name('calling.todays');
    Route::get('/calling/todays/data', [TodaysCallingController::class, 'getCallings'])->name('calling.todays.data');
    Route::post('/calling/todays/filter', [TodaysCallingController::class, 'filterCallings'])->name('calling.todays.filter');
    Route::get('/calling/todays/filter-options', [TodaysCallingController::class, 'getFilterOptions'])->name('calling.todays.filter-options');
    Route::get('/calling/todays/cities/{stateId}', [TodaysCallingController::class, 'getCitiesByState'])->name('calling.todays.cities');
    Route::get('/calling/{calling}/remarks', [CallingController::class, 'remarks'])->name('calling.remarks.show');
    Route::post('/calling/{calling}/remarks', [CallingController::class, 'storeRemark'])->name('calling.remarks.store');
    // separate controllers for my & junk
    Route::get('/calling/my', [MyCallingController::class, 'index'])->name('calling.my');
    Route::get('/calling/my/data', [MyCallingController::class, 'getCallings'])->name('calling.my.data');
    Route::post('/calling/my/filter', [MyCallingController::class, 'filterCallings'])->name('calling.my.filter');
    Route::get('/calling/my/filter-options', [MyCallingController::class, 'getFilterOptions'])->name('calling.my.filter-options');
    Route::get('/calling/my/cities/{stateId}', [MyCallingController::class, 'getCitiesByState'])->name('calling.my.cities');
    Route::post('/calling/my/update-type', [MyCallingController::class, 'updateCallingType'])->name('calling.my.update-type');

    Route::get('/calling/junk', [JunkCallingController::class, 'index'])->name('calling.junk');
    Route::get('/calling/junk/data', [JunkCallingController::class, 'getCallings'])->name('calling.junk.data');
    Route::post('/calling/junk/filter', [JunkCallingController::class, 'filterCallings'])->name('calling.junk.filter');
    Route::get('/calling/junk/filter-options', [JunkCallingController::class, 'getFilterOptions'])->name('calling.junk.filter-options');
    Route::get('/calling/junk/cities/{stateId}', [JunkCallingController::class, 'getCitiesByState'])->name('calling.junk.cities');
    Route::delete('/calling/junk/{id}', [JunkCallingController::class, 'destroy'])->name('calling.junk.destroy');

    // removed my-calling and junk-calling routes
});

// calling type routes
Route::middleware(['auth.or.session'])->group(function () {
    Route::get('/calling-type', [CallingTypeController::class, 'index'])->name('calling-type.index');
    Route::get('/calling-type/fetch', [CallingTypeController::class, 'fetch'])->name('calling-type.fetch');
    Route::post('/calling-type/store', [CallingTypeController::class, 'store'])->name('calling-type.store');
    Route::get('/calling-type/{id}/edit', [CallingTypeController::class, 'edit'])->name('calling-type.edit');
    Route::put('/calling-type/{id}', [CallingTypeController::class, 'update'])->name('calling-type.update');
    Route::delete('/calling-type/{id}', [CallingTypeController::class, 'destroy'])->name('calling-type.destroy');
    Route::get('/getcallingtypes', [CallingTypeController::class, 'getCallingTypes'])->name('getcallingtypes');
});

Route::middleware(['auth.or.session'])->group(function () {
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
});

// Document Management routes
Route::middleware(['auth.or.session'])->group(function () {
    Route::get('/document', [DocumentController::class, 'index'])->name('document.index');
    
    // Document CRUD routes
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
    
    // Document Category Management routes
    Route::get('/document/categories', [DocumentController::class, 'getCategories'])->name('document.categories');
    Route::post('/document/categories', [DocumentController::class, 'storeCategory'])->name('document.categories.store');
    Route::put('/document/categories/{id}', [DocumentController::class, 'updateCategory'])->name('document.categories.update');
    Route::delete('/document/categories/{id}', [DocumentController::class, 'destroyCategory'])->name('document.categories.destroy');
    
    // Document Subcategory Management routes
    Route::post('/document/subcategories', [DocumentController::class, 'storeSubcategory'])->name('document.subcategories.store');
    Route::put('/document/subcategories/{id}', [DocumentController::class, 'updateSubcategory'])->name('document.subcategories.update');
    Route::delete('/document/subcategories/{id}', [DocumentController::class, 'destroySubcategory'])->name('document.subcategories.destroy');
    
    // Document Subcategory View route
    Route::get('/document/{category}/{subcategory}', [DocumentController::class, 'showSubcategory'])->name('document.subcategory.show');
});

// Petty Cash Route
Route::middleware(['auth.or.session'])->group(function () {
    Route::get('/petty-cash', [\App\Http\Controllers\PettyCashController::class, 'index'])->name('petty-cash.index');
    Route::get('/petty-cash/fetch', [\App\Http\Controllers\PettyCashController::class, 'fetch'])->name('petty-cash.fetch');
    Route::get('/petty-cash/stats', [\App\Http\Controllers\PettyCashController::class, 'getStats'])->name('petty-cash.stats');
    Route::get('/petty-cash/fetch-expenses', [\App\Http\Controllers\PettyCashController::class, 'fetchExpenses'])->name('petty-cash.fetch-expenses');
    Route::post('/petty-cash/store', [\App\Http\Controllers\PettyCashController::class, 'store'])->name('petty-cash.store');
    Route::put('/petty-cash/{id}', [\App\Http\Controllers\PettyCashController::class, 'update'])->name('petty-cash.update');
    Route::delete('/petty-cash/{id}', [\App\Http\Controllers\PettyCashController::class, 'destroy'])->name('petty-cash.destroy');
    Route::get('/approvals/petty', [\App\Http\Controllers\PettyCashController::class, 'approvals'])->name('approvals.petty');
    Route::post('/petty-cash/approve-bulk', [\App\Http\Controllers\PettyCashController::class, 'approveBulk'])->name('petty-cash.approve-bulk');

    Route::post('/petty-cash/{id}/toggle-approval', [\App\Http\Controllers\PettyCashController::class, 'toggleApproval'])->name('petty-cash.toggle-approval');
    Route::get('/petty-cash/department-summary', [\App\Http\Controllers\PettyCashController::class, 'departmentSummary'])->name('petty-cash.department-summary');
    Route::get('/petty-cash/department/{id}/expenses', [\App\Http\Controllers\PettyCashController::class, 'departmentExpenses'])->name('petty-cash.department.expenses');
});

// Petty Opening Balance Routes
Route::middleware(['auth.or.session'])->group(function () {
    Route::get('/petty-opening-balance', [\App\Http\Controllers\PettyOpeningBalanceController::class, 'index'])->name('petty-opening-balance.index');
    Route::get('/petty-opening-balance/fetch', [\App\Http\Controllers\PettyOpeningBalanceController::class, 'fetch'])->name('petty-opening-balance.fetch');
    Route::post('/petty-opening-balance/store', [\App\Http\Controllers\PettyOpeningBalanceController::class, 'store'])->name('petty-opening-balance.store');
    Route::put('/petty-opening-balance/{id}', [\App\Http\Controllers\PettyOpeningBalanceController::class, 'update'])->name('petty-opening-balance.update');
    Route::delete('/petty-opening-balance/{id}', [\App\Http\Controllers\PettyOpeningBalanceController::class, 'destroy'])->name('petty-opening-balance.destroy');
});

// Expenses Master Route
Route::middleware(['auth.or.session'])->group(function () {
    Route::get('/expenses', [\App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/fetch', [\App\Http\Controllers\ExpenseController::class, 'fetchExpenses'])->name('expenses.fetch');
    Route::post('/expenses/store', [\App\Http\Controllers\ExpenseController::class, 'store'])->name('expenses.store');
    Route::put('/expenses/{id}', [\App\Http\Controllers\ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{id}', [\App\Http\Controllers\ExpenseController::class, 'destroy'])->name('expenses.destroy');
});
Route::post('/indiamart/notify-sales', [IndiaMartLeadsController::class, 'notifySalesUsers'])->name('indiamart.notify');

// Notification routes (protected by auth.or.session middleware)
Route::middleware(['auth.or.session'])->group(function () {
    Route::get('/notifications', function() {
        // Check session-based authentication first
        $userId = session('user_id');
        $tenantId = session('tenant_id');
        
        if (!$userId || !$tenantId) {
            return response()->json(['notifications' => [], 'debug' => 'no_session_user']);
        }
        
        // Set tenant database connection (same as user's login)
        \App\Services\TenantDatabaseService::setDefaultConnection((int) $tenantId);
        
        // Get user from tenant database
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return response()->json(['notifications' => [], 'debug' => 'user_not_found_in_tenant_db']);
        }
        
        $notifications = $user->notifications()->latest()->take(20)->get();
        return response()->json([
            'notifications' => $notifications
        ]);
    })->name('notifications.index');

    Route::post('/notifications/{id}/mark-read', function($id) {
        // Check session-based authentication
        $userId = session('user_id');
        $tenantId = session('tenant_id');
        
        if (!$userId || !$tenantId) {
            return response()->json(['success' => false], 401);
        }
        
        // Set tenant database connection (same as user's login)
        \App\Services\TenantDatabaseService::setDefaultConnection((int) $tenantId);
        
        // Get user from tenant database
        $user = \App\Models\User::find($userId);
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
        // Check session-based authentication
        $userId = session('user_id');
        $tenantId = session('tenant_id');
        
        if (!$userId || !$tenantId) {
            return response()->json(['success' => false], 401);
        }
        
        // Set tenant database connection
        \App\Services\TenantDatabaseService::setDefaultConnection((int) $tenantId);
        
        // Get user from tenant database
        $user = \App\Models\User::find($userId);
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
        // Check session-based authentication
        $userId = session('user_id');
        $tenantId = session('tenant_id');
        
        if (!$userId || !$tenantId) {
            return response()->json(['success' => false], 401);
        }
        
        // Set tenant database connection
        \App\Services\TenantDatabaseService::setDefaultConnection((int) $tenantId);
        
        // Get user from tenant database
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return response()->json(['success' => false], 401);
        }
        
        // Mark all unread notifications as read
        $user->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true]);
    })->name('notifications.mark-all-read');

    Route::post('/notifications/clear-all', function() {
        // Check session-based authentication
        $userId = session('user_id');
        $tenantId = session('tenant_id');
        
        if (!$userId || !$tenantId) {
            return response()->json(['success' => false], 401);
        }
        
        // Set tenant database connection
        \App\Services\TenantDatabaseService::setDefaultConnection((int) $tenantId);
        
        // Get user from tenant database
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return response()->json(['success' => false], 401);
        }
        
        // Delete all notifications for this user
        $user->notifications()->delete();
        
        return response()->json(['success' => true]);
    })->name('notifications.clear-all');
});





route::get('/sandeep', function() {
    return view('sandeep');
});

// Public Form Builder Routes (No Auth Required)
Route::group(['middleware' => ['web']], function () {
    Route::get('/public/form/{tenant}/{form}', [FormBuilderController::class, 'publicView'])->name('public.form.view');
    Route::post('/public/form/{tenant}/{form}/submit', [FormBuilderController::class, 'publicSubmit'])->name('public.form.submit');
});

// Contact Management
Route::middleware(['auth.or.session'])->group(function () {
    Route::get('/contact-management', [\App\Http\Controllers\ContactManagementController::class, 'index'])->name('contactmanagement.index');
    Route::get('/contact-management/fetch', [\App\Http\Controllers\ContactManagementController::class, 'fetch'])->name('contactmanagement.fetch');
    Route::get('/contact-management/stats', [\App\Http\Controllers\ContactManagementController::class, 'getSummaryStats'])->name('contactmanagement.stats');
    Route::post('/contact-management/store', [\App\Http\Controllers\ContactManagementController::class, 'store'])->name('contactmanagement.store');
    Route::get('/contact-management/{id}/edit', [\App\Http\Controllers\ContactManagementController::class, 'edit'])->name('contactmanagement.edit');
    Route::put('/contact-management/{id}', [\App\Http\Controllers\ContactManagementController::class, 'update'])->name('contactmanagement.update');
    Route::delete('/contact-management/{id}', [\App\Http\Controllers\ContactManagementController::class, 'destroy'])->name('contactmanagement.destroy');
});

// Asset Management
Route::middleware(['auth.or.session'])->group(function () {
    Route::get('/asset-management', [\App\Http\Controllers\AssetManagementController::class, 'index'])->name('asset-management.index');
    Route::get('/asset-management/fetch', [\App\Http\Controllers\AssetManagementController::class, 'fetch'])->name('asset-management.fetch');
    Route::get('/asset-management/stats', [\App\Http\Controllers\AssetManagementController::class, 'getSummaryStats'])->name('asset-management.stats');
    Route::get('/asset-management/get-assets', [\App\Http\Controllers\AssetManagementController::class, 'getAssetsByCategory'])->name('asset-management.get-assets');
    Route::post('/asset-management/store', [\App\Http\Controllers\AssetManagementController::class, 'store'])->name('asset-management.store');
    Route::get('/asset-management/{id}', [\App\Http\Controllers\AssetManagementController::class, 'show'])->name('asset-assignment.show');
    Route::put('/asset-management/{id}', [\App\Http\Controllers\AssetManagementController::class, 'update'])->name('asset-management.update');
    Route::delete('/asset-management/{id}', [\App\Http\Controllers\AssetManagementController::class, 'destroy'])->name('asset-management.destroy');
    
    // Asset Types
    Route::get('/asset-type', [\App\Http\Controllers\AssetTypeController::class, 'index'])->name('asset-type.index');
    Route::get('/asset-type/fetch', [\App\Http\Controllers\AssetTypeController::class, 'fetch'])->name('asset-type.fetch');
    Route::post('/asset-type', [\App\Http\Controllers\AssetTypeController::class, 'store'])->name('asset-type.store');
    Route::put('/asset-type/{id}', [\App\Http\Controllers\AssetTypeController::class, 'update'])->name('asset-type.update');
    Route::delete('/asset-type/{id}', [\App\Http\Controllers\AssetTypeController::class, 'destroy'])->name('asset-type.destroy');

    // Asset Categories
    Route::get('/asset-category', [\App\Http\Controllers\AssetCategoryController::class, 'index'])->name('asset-category.index');
    Route::get('/asset-category/fetch', [\App\Http\Controllers\AssetCategoryController::class, 'fetch'])->name('asset-category.fetch');
    Route::get('/asset-category/{id}/fields', [\App\Http\Controllers\AssetCategoryController::class, 'manageFields'])->name('asset-category.fields');
    Route::post('/asset-category', [\App\Http\Controllers\AssetCategoryController::class, 'store'])->name('asset-category.store');
    Route::get('/asset-category/{id}', [\App\Http\Controllers\AssetCategoryController::class, 'show'])->name('asset-category.show');
    Route::put('/asset-category/{id}', [\App\Http\Controllers\AssetCategoryController::class, 'update'])->name('asset-category.update');
    Route::delete('/asset-category/{id}', [\App\Http\Controllers\AssetCategoryController::class, 'destroy'])->name('asset-category.destroy');

    // Asset Statuses
    Route::get('/asset-status', [\App\Http\Controllers\AssetStatusController::class, 'index'])->name('asset-status.index');
    Route::get('/asset-status/fetch', [\App\Http\Controllers\AssetStatusController::class, 'fetch'])->name('asset-status.fetch');
    Route::post('/asset-status', [\App\Http\Controllers\AssetStatusController::class, 'store'])->name('asset-status.store');
    Route::put('/asset-status/{id}', [\App\Http\Controllers\AssetStatusController::class, 'update'])->name('asset-status.update');
    Route::delete('/asset-status/{id}', [\App\Http\Controllers\AssetStatusController::class, 'destroy'])->name('asset-status.destroy');

    // Suppliers
    Route::get('/supplier', [\App\Http\Controllers\SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/supplier/fetch', [\App\Http\Controllers\SupplierController::class, 'fetch'])->name('supplier.fetch');
    Route::post('/supplier', [\App\Http\Controllers\SupplierController::class, 'store'])->name('supplier.store');
    Route::put('/supplier/{id}', [\App\Http\Controllers\SupplierController::class, 'update'])->name('supplier.update');
    Route::delete('/supplier/{id}', [\App\Http\Controllers\SupplierController::class, 'destroy'])->name('supplier.destroy');

    // Assets (Individual Items)
    Route::get('/assets', [\App\Http\Controllers\AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/fetch', [\App\Http\Controllers\AssetController::class, 'fetch'])->name('assets.fetch');
    Route::get('/assets/search-employees', [\App\Http\Controllers\AssetController::class, 'searchEmployees'])->name('assets.search-employees');
    Route::post('/assets', [\App\Http\Controllers\AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/{id}', [\App\Http\Controllers\AssetController::class, 'show'])->name('assets.show');
    Route::get('/assets/{id}/history', [\App\Http\Controllers\AssetController::class, 'history'])->name('assets.history');
    Route::put('/assets/{id}', [\App\Http\Controllers\AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{id}', [\App\Http\Controllers\AssetController::class, 'destroy'])->name('assets.destroy');
});

// Email Marketing
Route::middleware(['auth.or.session'])->group(function () {
    Route::get('/email-marketing', [\App\Http\Controllers\EmailMarketingController::class, 'index'])->name('emailmarketing.index');
});