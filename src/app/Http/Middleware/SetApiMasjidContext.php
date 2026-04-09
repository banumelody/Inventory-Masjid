<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiMasjidContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->isSuperAdmin()) {
            // Superadmin can specify masjid_id via header or query
            $masjidId = $request->header('X-Masjid-Id') ?? $request->input('masjid_id');
            if ($masjidId) {
                app()->instance('current_masjid_id', (int) $masjidId);
            }
        } elseif ($user->masjid_id) {
            app()->instance('current_masjid_id', $user->masjid_id);
        }

        return $next($request);
    }
}
