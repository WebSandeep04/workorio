<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow Laravel-authenticated admins first
        if (auth()->check() && in_array(auth()->user()->role_id, [1, 5])) {
            return $next($request);
        }

        // Allow session-authenticated tenant admins (role stored as 'user_role')
        if (session()->has('user_id')) {
            $roleId = (int) session('user_role');
            if (in_array($roleId, [1, 5])) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Only Admins can access this resource.'
            ], 403);
        }

        abort(403, 'Unauthorized access. Only Admins can access this resource.');
    }
}


