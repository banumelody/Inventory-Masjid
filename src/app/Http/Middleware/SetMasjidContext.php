<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetMasjidContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->is_superadmin) {
                // Superadmin: use masjid from session (set via tenant switching)
                $masjidId = session('current_masjid_id');
            } else {
                $masjidId = $user->masjid_id;
            }

            if ($masjidId) {
                app()->instance('current_masjid_id', $masjidId);
            }
        }

        return $next($request);
    }
}
