<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        if ($notification->link && $this->isSafeRedirectUrl($notification->link)) {
            return redirect($notification->link);
        }

        return redirect()->route('notifications.index');
    }

    public function markAllAsRead(): RedirectResponse
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('notifications.index')
            ->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }

    public function unreadCount(): JsonResponse
    {
        $count = Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Validate that a redirect URL is safe (internal only)
     */
    private function isSafeRedirectUrl(string $url): bool
    {
        $parsed = parse_url($url);

        // Allow relative URLs (no host)
        if (!isset($parsed['host'])) {
            return true;
        }

        // Allow only same-host URLs
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        return $parsed['host'] === $appHost;
    }
}
