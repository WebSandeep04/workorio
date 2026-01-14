<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.superadmin-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $remember = $request->filled('remember');

        // Only try to login with master database (for super admin)
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is super admin
            if ($user->role_id == 3) {
                $request->session()->regenerate();
                return redirect('/superadmindashboard')->with('success', 'Welcome Super Admin!');
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Only Super Admins can access this area.',
                ]);
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
        
        return redirect('/superadmin/login')->with('success', 'You have been logged out successfully.');
    }
}
