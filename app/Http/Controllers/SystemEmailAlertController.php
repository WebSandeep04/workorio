<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Branch;
use App\Models\Department;

class SystemEmailAlertController extends Controller
{
    /**
     * Display the Centralized Email Alerts Matrix.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user && $user->role && $user->role->role_name !== 'admin' && !$user->hasPermission('setup.email_alerts')) {
            abort(403, 'Unauthorized access to Email Alerts.');
        }

        // 1. Fetch all alerts grouped by category
        $alerts = DB::table('system_email_alerts')->orderBy('id')->get();
        $categories = $alerts->groupBy('category');

        // 2. Fetch all active employees / users with their branches and departments
        $usersQuery = User::with(['employee.branch', 'employee.departmentRelation', 'role'])
            ->where(function($q) {
                $q->whereHas('employee', function ($eq) {
                    $eq->where('status', 'active');
                })->orWhere('role_id', 1);
            });

        if ($request->filled('branch_id')) {
            $usersQuery->whereHas('employee', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->filled('department_id')) {
            $usersQuery->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $usersQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('employee', function($eq) use ($search) {
                      $eq->where('employee_code', 'like', "%{$search}%");
                  });
            });
        }

        $users = $usersQuery->orderBy('name')->get();

        // 3. Fetch all current recipient mappings
        $recipients = DB::table('system_email_alert_recipients')->get();
        $recipientMap = [];
        foreach ($recipients as $r) {
            $recipientMap[$r->mail_code][$r->user_id] = true;
        }

        $branches = Branch::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('software-setup.email-alerts.matrix-table', [
                    'tableAlerts' => $alerts,
                    'categories' => $categories,
                    'users' => $users,
                    'recipientMap' => $recipientMap
                ])->render(),
                'user_count' => $users->count()
            ]);
        }

        return view('software-setup.email-alerts.index', compact(
            'alerts',
            'categories',
            'users',
            'recipientMap',
            'branches',
            'departments'
        ));
    }

    /**
     * Display the Delivery Logs of sent system emails.
     */
    public function logs(Request $request)
    {
        $user = auth()->user();
        if ($user && $user->role && $user->role->role_name !== 'admin' && !$user->hasPermission('setup.email_alerts')) {
            abort(403, 'Unauthorized access to Email Alerts.');
        }

        $query = \App\Models\SystemEmailLog::with(['alert', 'user'])->orderBy('sent_at', 'desc');

        // Filter by mail code
        if ($request->filled('mail_code')) {
            $query->where('mail_code', $request->mail_code);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('sent_at', $request->date);
        }

        // Filter by search (recipient email or name or subject)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('recipient_email', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        // Get list of alerts for dropdown
        $alerts = DB::table('system_email_alerts')->orderBy('name')->get();

        // Metric summary for today
        $today = now()->format('Y-m-d');
        $todaySentCount = \App\Models\SystemEmailLog::whereDate('sent_at', $today)->where('status', 'sent')->count();
        $todayFailedCount = \App\Models\SystemEmailLog::whereDate('sent_at', $today)->where('status', 'failed')->count();
        $totalLogsCount = \App\Models\SystemEmailLog::count();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('software-setup.email-alerts.logs-table', compact('logs'))->render(),
                'total' => $logs->total(),
            ]);
        }

        return view('software-setup.email-alerts.logs', compact(
            'logs',
            'alerts',
            'todaySentCount',
            'todayFailedCount',
            'totalLogsCount'
        ));
    }

    /**
     * Toggle a single user mapping for a mail code.
     */
    public function toggleRecipient(Request $request)
    {
        $request->validate([
            'mail_code' => 'required|string|exists:system_email_alerts,mail_code',
            'user_id' => 'required|integer|exists:users,id',
            'assigned' => 'required|boolean',
        ]);

        $mailCode = $request->mail_code;
        $userId = $request->user_id;

        if ($request->assigned) {
            DB::table('system_email_alert_recipients')->updateOrInsert(
                ['mail_code' => $mailCode, 'user_id' => $userId],
                ['updated_at' => now(), 'created_at' => now()]
            );
        } else {
            DB::table('system_email_alert_recipients')
                ->where('mail_code', $mailCode)
                ->where('user_id', $userId)
                ->delete();
        }

        $newCount = DB::table('system_email_alert_recipients')
            ->where('mail_code', $mailCode)
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Recipient preference updated successfully.',
            'new_count' => $newCount
        ]);
    }

    /**
     * Bulk assign or clear recipients for an entire mail code.
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'mail_code' => 'required|string|exists:system_email_alerts,mail_code',
            'action' => 'required|in:all_admins,all_active,clear_all',
        ]);

        $mailCode = $request->mail_code;

        if ($request->action === 'clear_all') {
            DB::table('system_email_alert_recipients')->where('mail_code', $mailCode)->delete();
        } elseif ($request->action === 'all_admins') {
            // Assign all users with role_id = 1 or role name admin
            $adminIds = User::where(function($q) {
                    $q->where('role_id', 1)->orWhereHas('role', function($rq) {
                        $rq->where('role_name', 'admin');
                    });
                })->pluck('id');

            foreach ($adminIds as $uid) {
                DB::table('system_email_alert_recipients')->updateOrInsert(
                    ['mail_code' => $mailCode, 'user_id' => $uid],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        } elseif ($request->action === 'all_active') {
            // Assign all active employees + admins
            $userIds = User::where(function($q) {
                $q->whereHas('employee', function ($eq) {
                    $eq->where('status', 'active');
                })->orWhere('role_id', 1);
            })->pluck('id');

            foreach ($userIds as $uid) {
                DB::table('system_email_alert_recipients')->updateOrInsert(
                    ['mail_code' => $mailCode, 'user_id' => $uid],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        $newCount = DB::table('system_email_alert_recipients')
            ->where('mail_code', $mailCode)
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Bulk assignment updated successfully.',
            'new_count' => $newCount
        ]);
    }

    /**
     * Toggle master active status for a mail alert.
     */
    public function toggleAlertStatus(Request $request)
    {
        $request->validate([
            'mail_code' => 'required|string|exists:system_email_alerts,mail_code',
            'is_active' => 'required|boolean',
        ]);

        DB::table('system_email_alerts')
            ->where('mail_code', $request->mail_code)
            ->update([
                'is_active' => $request->is_active ? 1 : 0,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Email alert status updated successfully.'
        ]);
    }
}
