<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display settings page
     */
    public function index(): View
    {
        $groups = [
            'appearance' => [
                'title' => '🎨 Tampilan',
                'description' => 'Kustomisasi nama dan logo aplikasi',
            ],
            'organization' => [
                'title' => '🏢 Organisasi',
                'description' => 'Informasi masjid/organisasi',
            ],
            'about' => [
                'title' => '📄 Tentang',
                'description' => 'Informasi untuk halaman About',
            ],
            'footer' => [
                'title' => '📝 Footer',
                'description' => 'Pengaturan footer aplikasi',
            ],
        ];

        $settings = [];
        foreach ($groups as $groupKey => $groupInfo) {
            $settings[$groupKey] = [
                'info' => $groupInfo,
                'items' => Setting::getByGroup($groupKey),
            ];
        }

        return view('settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request): RedirectResponse
    {
        $masjidId = app()->bound('current_masjid_id') ? app('current_masjid_id') : null;

        $allSettings = Setting::withoutGlobalScopes()
            ->when($masjidId, fn($q) => $q->where('masjid_id', $masjidId))
            ->when(!$masjidId, fn($q) => $q->whereNull('masjid_id'))
            ->get();

        foreach ($allSettings as $setting) {
            $key = $setting->key;
            
            // Handle image upload
            if ($setting->type === 'image') {
                if ($request->hasFile($key)) {
                    // Validate image
                    $request->validate([
                        $key => 'image|mimes:jpeg,png,jpg,gif,ico|max:2048',
                    ]);

                    // Delete old image
                    if ($setting->value) {
                        Storage::disk('public')->delete($setting->value);
                    }

                    // Store new image
                    $file = $request->file($key);
                    $filename = 'settings/' . $key . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('public', $filename);
                    
                    Setting::set($key, $filename);
                } elseif ($request->boolean("remove_{$key}")) {
                    // Remove image
                    if ($setting->value) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    Setting::set($key, null);
                }
            }
            // Handle boolean
            elseif ($setting->type === 'boolean') {
                Setting::set($key, $request->boolean($key) ? '1' : '0');
            }
            // Handle text/textarea
            else {
                if ($request->has($key)) {
                    Setting::set($key, $request->input($key));
                }
            }
        }

        // Clear cache
        Setting::clearCache();

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }

    /**
     * Public about page
     */
    public function about(): View
    {
        return view('about.index');
    }
}
