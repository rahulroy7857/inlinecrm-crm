<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotAdmin
{
    public function handle(Request $request, Closure $next, $guard = 'admin'): Response
    {
        \Log::info('Admin middleware triggered', [
            'isAuthenticated' => Auth::guard($guard)->check(),
            'user' => Auth::guard($guard)->user()
        ]);
    
        // Check if admin is authenticated
        if (!Auth::guard('admin')->check()) {
            \Log::info('Redirecting to login');
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}