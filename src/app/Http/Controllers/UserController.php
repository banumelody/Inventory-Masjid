<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    private function scopedUsers()
    {
        $query = User::with('role')->where('is_superadmin', false);

        if (app()->bound('current_masjid_id')) {
            $query->where('masjid_id', app('current_masjid_id'));
        }

        return $query;
    }

    public function index(): View
    {
        $users = $this->scopedUsers()->orderBy('name')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('display_name')->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(6)],
            'role_id' => 'required|exists:roles,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if (app()->bound('current_masjid_id')) {
            $validated['masjid_id'] = app('current_masjid_id');
        }

        $user = User::create($validated);

        ActivityLog::log('create', $user);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $this->authorizeAccess($user);
        $roles = Role::orderBy('display_name')->get();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAccess($user);

        $oldValues = $user->only(['name', 'email', 'role_id']);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(6)],
            'role_id' => 'required|exists:roles,id',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        ActivityLog::log('update', $user, $oldValues, $user->only(['name', 'email', 'role_id']));

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorizeAccess($user);

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        if ($user->is_superadmin) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus superadmin.');
        }

        // Check if this is the last admin for this masjid
        if ($user->isAdmin()) {
            $adminCount = User::where('masjid_id', $user->masjid_id)
                ->where('is_superadmin', false)
                ->whereHas('role', fn($q) => $q->where('name', 'admin'))
                ->count();
            if ($adminCount <= 1) {
                return redirect()->route('users.index')
                    ->with('error', 'Tidak dapat menghapus admin terakhir masjid ini.');
            }
        }

        ActivityLog::log('delete', $user);
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    private function authorizeAccess(User $user): void
    {
        if ($user->is_superadmin) {
            abort(403, 'Tidak dapat mengakses superadmin.');
        }

        if (app()->bound('current_masjid_id') && $user->masjid_id !== app('current_masjid_id')) {
            abort(403, 'Pengguna bukan milik masjid ini.');
        }
    }
}
