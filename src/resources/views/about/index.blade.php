@extends('layouts.app')

@section('title', 'Tentang - ' . \App\Models\Setting::appName())

@section('content')
<div class="max-w-3xl mx-auto">
    <x-breadcrumb :items="[['label' => 'Tentang']]" />
    <div class="mb-6">
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-8 bg-gradient-to-r from-green-600 to-green-700 text-white text-center">
            @if(\App\Models\Setting::hasLogo())
                <img src="{{ \App\Models\Setting::logoUrl() }}" 
                     alt="{{ \App\Models\Setting::appName() }}"
                     class="h-20 w-20 mx-auto mb-4 bg-white rounded-lg p-2 object-contain">
            @else
                <div class="text-5xl mb-4">🕌</div>
            @endif
            <h1 class="text-2xl font-bold">{{ \App\Models\Setting::appName() }}</h1>
            @if(\App\Models\Setting::orgName())
                <p class="text-green-100 mt-2">{{ \App\Models\Setting::orgName() }}</p>
            @endif
        </div>

        <div class="p-6 space-y-6">
            <!-- Description -->
            @if(\App\Models\Setting::get('about_description'))
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Tentang Kami</h2>
                <p class="text-gray-600 whitespace-pre-line">{{ \App\Models\Setting::get('about_description') }}</p>
            </div>
            @endif

            <!-- Vision -->
            @if(\App\Models\Setting::get('about_vision'))
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Visi</h2>
                <p class="text-gray-600 whitespace-pre-line">{{ \App\Models\Setting::get('about_vision') }}</p>
            </div>
            @endif

            <!-- Mission -->
            @if(\App\Models\Setting::get('about_mission'))
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Misi</h2>
                <p class="text-gray-600 whitespace-pre-line">{{ \App\Models\Setting::get('about_mission') }}</p>
            </div>
            @endif

            <!-- Contact Info -->
            @php
                $hasContact = \App\Models\Setting::get('org_address') || 
                              \App\Models\Setting::get('org_phone') || 
                              \App\Models\Setting::get('org_whatsapp') || 
                              \App\Models\Setting::get('org_email');
            @endphp
            
            @if($hasContact)
            <div class="border-t pt-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Kontak</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(\App\Models\Setting::get('org_address'))
                    <div class="flex gap-3">
                        <span class="text-xl">📍</span>
                        <div>
                            <p class="text-sm text-gray-500">Alamat</p>
                            <p class="text-gray-700 whitespace-pre-line">{{ \App\Models\Setting::get('org_address') }}</p>
                        </div>
                    </div>
                    @endif

                    @if(\App\Models\Setting::get('org_phone'))
                    <div class="flex gap-3">
                        <span class="text-xl">📞</span>
                        <div>
                            <p class="text-sm text-gray-500">Telepon</p>
                            <a href="tel:{{ \App\Models\Setting::get('org_phone') }}" class="text-blue-600 hover:text-blue-800">
                                {{ \App\Models\Setting::get('org_phone') }}
                            </a>
                        </div>
                    </div>
                    @endif

                    @if(\App\Models\Setting::get('org_whatsapp'))
                    <div class="flex gap-3">
                        <span class="text-xl">💬</span>
                        <div>
                            <p class="text-sm text-gray-500">WhatsApp</p>
                            <a href="https://wa.me/{{ \App\Models\Setting::get('org_whatsapp') }}" 
                               target="_blank"
                               class="text-green-600 hover:text-green-800">
                                {{ \App\Models\Setting::get('org_whatsapp') }}
                            </a>
                        </div>
                    </div>
                    @endif

                    @if(\App\Models\Setting::get('org_email'))
                    <div class="flex gap-3">
                        <span class="text-xl">✉️</span>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <a href="mailto:{{ \App\Models\Setting::get('org_email') }}" class="text-blue-600 hover:text-blue-800">
                                {{ \App\Models\Setting::get('org_email') }}
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- App Info -->
            <div class="border-t pt-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Aplikasi</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Aplikasi</dt>
                            <dd class="font-medium">{{ \App\Models\Setting::appName() }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Versi</dt>
                            <dd class="font-medium">5.0</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Framework</dt>
                            <dd class="font-medium">Laravel {{ app()->version() }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">PHP</dt>
                            <dd class="font-medium">{{ PHP_VERSION }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t text-center text-sm text-gray-500">
            @if(\App\Models\Setting::get('show_powered_by', '1') === '1')
                Powered by <a href="https://github.com" target="_blank" class="text-green-600 hover:text-green-800">Inventory Masjid</a> - Open Source
            @endif
        </div>
    </div>
</div>
@endsection
