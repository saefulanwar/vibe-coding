<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectMemberFromAdmin
{
    /**
     * Redirect member users away from the main admin dashboard 
     * back to their student dashboard, but allow access to the profile page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If user is a member and is trying to access the exact '/admin' root route
        if ($user && $user->hasRole('member') && $request->is('admin')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
