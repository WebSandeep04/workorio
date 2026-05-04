<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PasswordResetOtp;
use App\Models\Tenant;
use App\Mail\PasswordResetOtpMail;
use App\Services\TenantDatabaseService;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Try to find user in tenant databases
        $user = $this->findUserInTenantDatabases($credentials['email'], $credentials['password']);
        
        if ($user) {
            // Use tenant_id from lookup result instead of deriving from email
            $tenantId = $user['tenant_id'] ?? null;
            if ($tenantId) {
                // Ensure tenant connection is the default for this request
                TenantDatabaseService::setDefaultConnection((int) $tenantId);

                session(['tenant_id' => $tenantId]);
                session(['user_id' => $user['id']]);
                session(['user_name' => $user['name']]);
                session(['user_role' => $user['role_id']]);
                
                // Regenerate session for security
                $request->session()->regenerate();
                
                return redirect()->intended('/dashboard')->with('success', 'Login successful! Welcome back, ' . $user['name']);
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->forget(['user_id', 'user_name', 'tenant_id', 'user_role']);
        return redirect('/');
    }

    // Show forgot password form
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // Send OTP for password reset
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Find user across all tenants
        $foundUser = $this->findUserByEmailInTenants($request->email);

        if (!$foundUser) {
            return back()->withErrors(['email' => 'No account found with this email address.']);
        }

        $user = $foundUser['user'];
        $tenantId = $foundUser['tenant_id'];

        try {
            // Set connection to the found tenant
            TenantDatabaseService::setDefaultConnection((int)$tenantId);

            // Create OTP
            $otp = PasswordResetOtp::createOtp($request->email);
            
            // Send OTP email
            Mail::to($request->email)->send(new PasswordResetOtpMail($otp->otp, $user->name));
            
            // Store email and tenant_id in session for next step
            session(['reset_email' => $request->email]);
            session(['reset_tenant_id' => $tenantId]);
            
            return redirect('/verify-otp')->with('success', 'OTP has been sent to your email address.');
            
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send OTP. Please try again.']);
        }
    }

    // Show OTP verification form
    public function showVerifyOtpForm()
    {
        if (!session('reset_email')) {
            return redirect('/forgot-password')->withErrors(['email' => 'Please request OTP first.']);
        }
        
        return view('auth.verify-otp');
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        // Check if already verified in session (prevent double submission error)
        if (session('verified_otp') === $request->otp) {
            return redirect('/reset-password');
        }

        $tenantId = session('reset_tenant_id');
        
        if ($tenantId) {
             TenantDatabaseService::setDefaultConnection((int)$tenantId);
        }
        
        $otp = PasswordResetOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->first();

        if (!$otp || $otp->isExpired()) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please request a new one.']);
        }

        // Mark OTP as used
        $otp->update(['used' => true]);

        // Store verified OTP in session
        session(['verified_otp' => $request->otp]);
        
        // Get user name for display
        $user = User::where('email', $request->email)->first();
        session(['user_name' => $user->name]);

        return redirect('/reset-password')->with('success', 'OTP verified successfully. Please enter your new password.');
    }

    // Show reset password form
    public function showResetPasswordForm()
    {
        if (!session('reset_email') || !session('verified_otp')) {
            return redirect('/forgot-password')->withErrors(['email' => 'Please complete OTP verification first.']);
        }
        
        return view('auth.reset-password');
    }

    // Reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $tenantId = session('reset_tenant_id');
        if ($tenantId) {
             TenantDatabaseService::setDefaultConnection((int)$tenantId);
        }

        // Verify OTP again for security
        $otp = PasswordResetOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('used', true)
            ->first();

        if (!$otp) {
            return redirect('/forgot-password')->withErrors(['email' => 'Invalid OTP. Please start over.']);
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Clear session data
        session()->forget(['reset_email', 'verified_otp', 'user_name', 'reset_tenant_id']);

        return redirect('/login')->with('success', 'Password reset successfully! Please login with your new password.');
    }

    /**
     * Find user in tenant databases
     */
    private function findUserInTenantDatabases($email, $password)
    {
        // Get all tenants from master database
        DB::setDefaultConnection('mysql');
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            try {
                // Create tenant connection if it doesn't exist
                if (!TenantDatabaseService::connectionExists($tenant->id)) {
                    TenantDatabaseService::createConnection($tenant);
                }
                
                // Set tenant connection
                TenantDatabaseService::setDefaultConnection($tenant->id);
                
                // Try to find user in this tenant database
                $user = User::where('email', $email)->first();
                $passwordOk = $user ? Hash::check($password, $user->password) : false;
                
                // Check if user has login permission
                $isLoginAllowed = $user ? ($user->is_login ?? 1) : 0;
                
                if ($user && $passwordOk && $isLoginAllowed) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role_id' => $user->role_id,
                        'tenant_id' => $tenant->id
                    ];
                }
            } catch (\Exception $e) {
                // Continue to next tenant if this one fails
                continue;
            }
        }
        
        return null;
    }

    /**
     * Get tenant ID from email (helper)
     */
    private function getTenantIdFromEmail($email)
    {
        // Extract tenant ID from email pattern (admin@tenant1.com)
        if (preg_match('/admin@tenant(\d+)\.com/', $email, $matches)) {
            return (int) $matches[1];
        }
        
        // If not in expected format, try to find by checking all tenants
        DB::setDefaultConnection('mysql');
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            try {
                $connectionName = TenantDatabaseService::getConnectionName($tenant->id);
                TenantDatabaseService::setDefaultConnection($tenant->id);
                
                $user = User::on($connectionName)
                    ->where('email', $email)
                    ->first();
                
                if ($user) {
                    return $tenant->id;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        return null;
    }

    /**
     * Find user by email across all tenants
     */
    private function findUserByEmailInTenants($email)
    {
        // Get all tenants from master database
        DB::setDefaultConnection('mysql');
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            try {
                // Create tenant connection if it doesn't exist
                if (!TenantDatabaseService::connectionExists($tenant->id)) {
                    TenantDatabaseService::createConnection($tenant);
                }
                
                // Set tenant connection
                TenantDatabaseService::setDefaultConnection($tenant->id);
                
                // Try to find user in this tenant database (active employees only)
                $user = User::where('email', $email)
                    ->whereHas('employee', function ($q) {
                        $q->where('status', 'active');
                    })->first();
                
                if ($user) {
                    return [
                        'user' => $user,
                        'tenant_id' => $tenant->id
                    ];
                }
            } catch (\Exception $e) {
                // Continue to next tenant if this one fails
                continue;
            }
        }
        
        return null;
    }
}
