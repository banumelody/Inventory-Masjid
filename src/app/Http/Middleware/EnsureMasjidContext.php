<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMasjidContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->is_superadmin && !app()->bound('current_masjid_id')) {
            return redirect()->route('dashboard')
                ->with('warning', 'Silakan pilih masjid terlebih dahulu sebelum mengakses fitur ini.');
        }

        return $next($request);
    }
}
