<?php

namespace App\Http\Controllers;

use App\Models\Masjid;
use App\Models\User;
use App\Models\Role;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('registration.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'masjid_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'masjid_phone' => 'nullable|string|max:20',
            'masjid_email' => 'nullable|email|max:255',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $slug = Str::slug($validated['masjid_name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Masjid::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            $masjid = Masjid::create([
                'name' => $validated['masjid_name'],
                'slug' => $slug,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'phone' => $validated['masjid_phone'] ?? null,
                'email' => $validated['masjid_email'] ?? null,
                'status' => 'active',
            ]);

            $adminRole = Role::where('name', 'admin')->first();

            $admin = User::create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'role_id' => $adminRole->id,
                'masjid_id' => $masjid->id,
            ]);

            // Create default settings for the new masjid
            $defaults = [
                ['key' => 'app_name', 'value' => $validated['masjid_name'], 'group' => 'general', 'label' => 'Nama Aplikasi'],
                ['key' => 'loan_max_days', 'value' => '7', 'group' => 'loan', 'label' => 'Maks Hari Peminjaman'],
                ['key' => 'loan_max_quantity', 'value' => '5', 'group' => 'loan', 'label' => 'Maks Jumlah Peminjaman'],
            ];
            foreach ($defaults as $setting) {
                Setting::withoutGlobalScopes()->create(array_merge($setting, ['masjid_id' => $masjid->id]));
            }

            ActivityLog::withoutGlobalScopes()->create([
                'user_id' => $admin->id,
                'action' => 'register',
                'model_type' => Masjid::class,
                'model_id' => $masjid->id,
                'description' => "Pendaftaran masjid baru: {$masjid->name}",
                'new_values' => ['masjid' => $masjid->name, 'admin' => $admin->name],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'masjid_id' => $masjid->id,
            ]);

            DB::commit();

            auth()->login($admin);
            session(['current_masjid_id' => $masjid->id]);

            return redirect()->route('dashboard')
                ->with('success', "Selamat! Masjid \"{$masjid->name}\" berhasil didaftarkan. Silakan mulai tambah data inventaris.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mendaftarkan masjid: ' . $e->getMessage());
        }
    }
}
