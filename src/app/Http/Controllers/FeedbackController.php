<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(): View
    {
        $feedbacks = Feedback::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('feedbacks.index', compact('feedbacks'));
    }

    public function create(): View
    {
        return view('feedbacks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'module' => 'required|string|max:100',
            'type' => 'required|in:bug,suggestion,question,other',
            'message' => 'required|string|max:2000',
        ]);

        $validated['user_id'] = auth()->id();

        $feedback = Feedback::create($validated);

        // Notify admins of this masjid about new feedback
        if (app()->bound('current_masjid_id') && app('current_masjid_id')) {
            Notification::notifyMasjidAdmins(
                app('current_masjid_id'),
                'feedback_new',
                'Feedback Baru',
                "Feedback baru dari " . auth()->user()->name . ": {$feedback->type} - " . \Str::limit($feedback->message, 80),
                route('feedbacks.index')
            );
        }

        return redirect()->route('feedbacks.create')
            ->with('success', 'Terima kasih! Feedback Anda telah dikirim.');
    }

    public function update(Request $request, Feedback $feedback): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:new,in_progress,resolved,closed',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $feedback->update($validated);

        return redirect()->route('feedbacks.index')
            ->with('success', 'Feedback berhasil diperbarui.');
    }

    public function destroy(Feedback $feedback): RedirectResponse
    {
        $feedback->delete();

        return redirect()->route('feedbacks.index')
            ->with('success', 'Feedback berhasil dihapus.');
    }
}
