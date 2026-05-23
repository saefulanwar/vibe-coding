<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Course Gating Workflow: intercept transaction actions if profile is incomplete.
     * Stores the intended URL so the user is auto-redirected back after completing their profile.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && !$user->isProfileComplete()) {
            // Store the previous URL so user returns to the course page after profile completion
            session()->put('url.intended', url()->previous());

            $missing = implode(', ', $user->getMissingProfileFields());

            return redirect()
                ->to('/admin/my-profile')
                ->with('warning', "Profil Anda belum lengkap ({$missing}). Silakan lengkapi data profil terlebih dahulu untuk melanjutkan transaksi.");
        }

        return $next($request);
    }
}
