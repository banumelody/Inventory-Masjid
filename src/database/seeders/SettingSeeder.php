<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Appearance Group
            [
                'key' => 'app_name',
                'value' => 'Inventory Masjid',
                'type' => 'text',
                'group' => 'appearance',
                'label' => 'Nama Aplikasi',
                'description' => 'Nama yang ditampilkan di header dan judul halaman',
                'sort_order' => 1,
            ],
            [
                'key' => 'app_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'appearance',
                'label' => 'Logo Aplikasi',
                'description' => 'Logo yang ditampilkan di sidebar (ukuran rekomendasi: 200x200px)',
                'sort_order' => 2,
            ],
            [
                'key' => 'app_favicon',
                'value' => null,
                'type' => 'image',
                'group' => 'appearance',
                'label' => 'Favicon',
                'description' => 'Icon kecil di tab browser (ukuran: 32x32px atau 64x64px)',
                'sort_order' => 3,
            ],

            // Organization Group
            [
                'key' => 'org_name',
                'value' => '',
                'type' => 'text',
                'group' => 'organization',
                'label' => 'Nama Organisasi/Masjid',
                'description' => 'Nama lengkap masjid atau organisasi',
                'sort_order' => 1,
            ],
            [
                'key' => 'org_address',
                'value' => '',
                'type' => 'textarea',
                'group' => 'organization',
                'label' => 'Alamat',
                'description' => 'Alamat lengkap masjid/organisasi',
                'sort_order' => 2,
            ],
            [
                'key' => 'org_phone',
                'value' => '',
                'type' => 'text',
                'group' => 'organization',
                'label' => 'Nomor Telepon',
                'description' => 'Nomor telepon yang bisa dihubungi',
                'sort_order' => 3,
            ],
            [
                'key' => 'org_whatsapp',
                'value' => '',
                'type' => 'text',
                'group' => 'organization',
                'label' => 'WhatsApp',
                'description' => 'Nomor WhatsApp (format: 628xxxxxxxxxx)',
                'sort_order' => 4,
            ],
            [
                'key' => 'org_email',
                'value' => '',
                'type' => 'text',
                'group' => 'organization',
                'label' => 'Email',
                'description' => 'Alamat email organisasi',
                'sort_order' => 5,
            ],

            // About Group
            [
                'key' => 'about_description',
                'value' => '',
                'type' => 'textarea',
                'group' => 'about',
                'label' => 'Deskripsi Singkat',
                'description' => 'Deskripsi singkat tentang masjid/organisasi (ditampilkan di halaman About)',
                'sort_order' => 1,
            ],
            [
                'key' => 'about_vision',
                'value' => '',
                'type' => 'textarea',
                'group' => 'about',
                'label' => 'Visi',
                'description' => 'Visi organisasi (opsional)',
                'sort_order' => 2,
            ],
            [
                'key' => 'about_mission',
                'value' => '',
                'type' => 'textarea',
                'group' => 'about',
                'label' => 'Misi',
                'description' => 'Misi organisasi (opsional)',
                'sort_order' => 3,
            ],

            // Footer Group
            [
                'key' => 'footer_text',
                'value' => '',
                'type' => 'text',
                'group' => 'footer',
                'label' => 'Teks Footer',
                'description' => 'Teks tambahan di footer (opsional)',
                'sort_order' => 1,
            ],
            [
                'key' => 'show_powered_by',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'footer',
                'label' => 'Tampilkan "Powered by"',
                'description' => 'Tampilkan credit "Powered by Inventory Masjid" di footer',
                'sort_order' => 2,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
