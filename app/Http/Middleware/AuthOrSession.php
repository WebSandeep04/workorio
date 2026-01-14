<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthOrSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via Auth facade (master database users)
        if (Auth::check()) {
            return $next($request);
        }
        
        // Check if user is authenticated via session (tenant database users)
        if (session()->has('user_id')) {
            return $next($request);
        }
        
        // If neither, redirect to login
        return redirect()->route('login');
    }
}
