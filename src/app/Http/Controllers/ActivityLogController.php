<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Masjid;
use App\Scopes\MasjidScope;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate(['search' => 'nullable|string|max:255']);
        $user = auth()->user();
        $isGlobalView = $user->is_superadmin && !app()->bound('current_masjid_id');

        $query = $isGlobalView
            ? ActivityLog::withoutGlobalScope(MasjidScope::class)->with(['user', 'masjid'])
            : ActivityLog::with('user');

        $query->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }
        if ($request->filled('masjid_id') && $isGlobalView) {
            $query->where('masjid_id', $request->masjid_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(20)->withQueryString();

        // Filter options
        $actionsQuery = $isGlobalView
            ? ActivityLog::withoutGlobalScope(MasjidScope::class)
            : new ActivityLog;
        $actions = $actionsQuery->newQuery()->distinct()->pluck('action')->filter();

        $masjidId = app()->bound('current_masjid_id') ? app('current_masjid_id') : null;
        $userQuery = \App\Models\User::where('is_superadmin', false)->orderBy('name');
        if ($masjidId) {
            $userQuery->where('masjid_id', $masjidId);
        }
        $users = $userQuery->get(['id', 'name']);

        $modelTypes = $actionsQuery->newQuery()->distinct()->pluck('model_type')->filter();

        $masjids = $isGlobalView ? Masjid::orderBy('name')->get(['id', 'name']) : collect();

        return view('activity-logs.index', compact('logs', 'actions', 'users', 'modelTypes', 'isGlobalView', 'masjids'));
    }

    public function show(ActivityLog $activityLog): View
    {
        $activityLog->load('user');
        return view('activity-logs.show', compact('activityLog'));
    }
}
