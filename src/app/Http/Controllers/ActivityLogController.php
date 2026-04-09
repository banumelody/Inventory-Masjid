<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(20)->withQueryString();

        // Get filter options (scoped to current tenant)
        $actions = ActivityLog::distinct()->pluck('action')->filter();
        $masjidId = app()->bound('current_masjid_id') ? app('current_masjid_id') : null;
        $userQuery = \App\Models\User::where('is_superadmin', false)->orderBy('name');
        if ($masjidId) {
            $userQuery->where('masjid_id', $masjidId);
        }
        $users = $userQuery->get(['id', 'name']);
        $modelTypes = ActivityLog::distinct()->pluck('model_type')->filter();

        return view('activity-logs.index', compact('logs', 'actions', 'users', 'modelTypes'));
    }

    public function show(ActivityLog $activityLog): View
    {
        $activityLog->load('user');
        return view('activity-logs.show', compact('activityLog'));
    }
}
