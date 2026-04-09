<?php

namespace App\Http\Controllers;

use App\Models\Masjid;
use App\Models\Item;
use App\Models\User;
use App\Models\Loan;
use App\Models\ActivityLog;
use App\Scopes\MasjidScope;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MasjidController extends Controller
{
    public function index(): View
    {
        $masjids = Masjid::withCount([
            'users',
            'items',
            'loans',
            'loans as active_loans_count' => function ($q) {
                $q->whereNull('returned_at');
            },
        ])->orderBy('name')->paginate(12);

        $stats = [
            'total_masjids' => Masjid::count(),
            'active_masjids' => Masjid::where('status', 'active')->count(),
            'total_items' => Item::withoutGlobalScope(MasjidScope::class)->count(),
            'total_users' => User::where('is_superadmin', false)->count(),
        ];

        return view('masjids.index', compact('masjids', 'stats'));
    }

    public function show(Masjid $masjid): View
    {
        $masjid->loadCount([
            'users',
            'items',
            'categories',
            'locations',
            'loans',
            'loans as active_loans_count' => function ($q) {
                $q->whereNull('returned_at');
            },
            'loans as overdue_loans_count' => function ($q) {
                $q->whereNull('returned_at')
                    ->whereNotNull('due_at')
                    ->where('due_at', '<', now());
            },
        ]);

        $users = User::where('masjid_id', $masjid->id)->with('role')->get();

        $recentItems = Item::withoutGlobalScope(MasjidScope::class)
            ->where('masjid_id', $masjid->id)
            ->with(['category', 'location'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $activeLoans = Loan::withoutGlobalScope(MasjidScope::class)
            ->where('masjid_id', $masjid->id)
            ->whereNull('returned_at')
            ->with('item')
            ->orderBy('due_at')
            ->limit(10)
            ->get();

        return view('masjids.show', compact('masjid', 'users', 'recentItems', 'activeLoans'));
    }

    public function create(): View
    {
        return view('masjids.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:masjids,slug',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $validated['status'] = 'active';
        $validated['verified_at'] = now();

        $masjid = Masjid::create($validated);

        ActivityLog::withoutGlobalScopes()->create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'model_type' => Masjid::class,
            'model_id' => $masjid->id,
            'description' => "Menambahkan masjid: {$masjid->name}",
            'new_values' => $validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('masjids.show', $masjid)
            ->with('success', 'Masjid berhasil ditambahkan.');
    }

    public function edit(Masjid $masjid): View
    {
        return view('masjids.edit', compact('masjid'));
    }

    public function update(Request $request, Masjid $masjid): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:masjids,slug,' . $masjid->id,
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $oldValues = $masjid->toArray();
        $masjid->update($validated);

        ActivityLog::withoutGlobalScopes()->create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'model_type' => Masjid::class,
            'model_id' => $masjid->id,
            'description' => "Mengupdate masjid: {$masjid->name}",
            'old_values' => $oldValues,
            'new_values' => $validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('masjids.show', $masjid)
            ->with('success', 'Data masjid berhasil diperbarui.');
    }

    public function switchContext(Request $request): RedirectResponse
    {
        $masjidId = $request->input('masjid_id');

        if ($masjidId === 'all' || $masjidId === null || $masjidId === '') {
            $request->session()->forget('current_masjid_id');

            ActivityLog::withoutGlobalScopes()->create([
                'user_id' => auth()->id(),
                'action' => 'switch_context',
                'description' => 'Beralih ke mode Global View (semua masjid)',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->back()->with('success', 'Mode: Semua Masjid');
        }

        $masjid = Masjid::findOrFail($masjidId);
        $request->session()->put('current_masjid_id', $masjid->id);

        ActivityLog::withoutGlobalScopes()->create([
            'user_id' => auth()->id(),
            'action' => 'switch_context',
            'model_type' => Masjid::class,
            'model_id' => $masjid->id,
            'description' => "Beralih ke konteks masjid: {$masjid->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', "Beralih ke: {$masjid->name}");
    }

    public function destroy(Masjid $masjid): RedirectResponse
    {
        $masjidName = $masjid->name;

        // Delete all tenant data in order (respecting FK constraints)
        $masjidId = $masjid->id;

        \App\Models\MaintenancePhoto::withoutGlobalScopes()->where('masjid_id', $masjidId)->delete();
        \App\Models\Maintenance::withoutGlobalScopes()->where('masjid_id', $masjidId)->delete();
        \App\Models\StockMovement::withoutGlobalScopes()->where('masjid_id', $masjidId)->delete();
        Loan::withoutGlobalScopes()->where('masjid_id', $masjidId)->delete();
        \App\Models\ScanLog::withoutGlobalScopes()->where('masjid_id', $masjidId)->delete();
        Item::withoutGlobalScopes()->where('masjid_id', $masjidId)->delete();
        \App\Models\Category::withoutGlobalScopes()->where('masjid_id', $masjidId)->delete();
        \App\Models\Location::withoutGlobalScopes()->where('masjid_id', $masjidId)->delete();
        \App\Models\ImportLog::withoutGlobalScopes()->where('masjid_id', $masjidId)->delete();
        \App\Models\Feedback::withoutGlobalScopes()->where('masjid_id', $masjidId)->delete();
        \App\Models\Backup::withoutGlobalScopes()->where('masjid_id', $masjidId)->delete();
        \App\Models\Setting::withoutGlobalScopes()->where('masjid_id', $masjidId)->delete();
        ActivityLog::withoutGlobalScopes()->where('masjid_id', $masjidId)->delete();
        User::where('masjid_id', $masjidId)->delete();

        $masjid->delete();

        ActivityLog::withoutGlobalScopes()->create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'model_type' => Masjid::class,
            'model_id' => $masjidId,
            'description' => "Menghapus masjid: {$masjidName}",
            'old_values' => ['name' => $masjidName, 'id' => $masjidId],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Clear context if current session was on this masjid
        if (session('current_masjid_id') == $masjidId) {
            session()->forget('current_masjid_id');
        }

        return redirect()->route('masjids.index')
            ->with('success', "Masjid \"{$masjidName}\" beserta seluruh datanya berhasil dihapus.");
    }
}
