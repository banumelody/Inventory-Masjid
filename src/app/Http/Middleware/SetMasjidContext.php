<?php

namespace App\Http\Middleware;

use App\Models\Masjid;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetMasjidContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->is_superadmin) {
                $masjidId = session('current_masjid_id');
            } else {
                $masjidId = $user->masjid_id;

                // Enforce masjid status for non-superadmin users
                if ($masjidId) {
                    $masjid = Masjid::find($masjidId);
                    if (!$masjid || $masjid->status !== 'active') {
                        Auth::logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                        return redirect()->route('login')
                            ->with('error', 'Masjid Anda sedang tidak aktif. Hubungi administrator.');
                    }
                }
            }

            if ($masjidId) {
                app()->instance('current_masjid_id', $masjidId);
            }
        }

        return $next($request);
    }
}
