@extends('layouts.app')

@section('title', 'Pengaturan Aplikasi - ' . \App\Models\Setting::appName())

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">⚙️ Pengaturan Aplikasi</h1>
    <p class="text-gray-500 mt-1">Kustomisasi aplikasi sesuai kebutuhan organisasi Anda</p>
</div>

<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="space-y-6">
        @foreach($settings as $groupKey => $group)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold text-gray-800">{{ $group['info']['title'] }}</h2>
                <p class="text-sm text-gray-500">{{ $group['info']['description'] }}</p>
            </div>
            
            <div class="p-6 space-y-6">
                @foreach($group['items'] as $setting)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700">
                            {{ $setting->label }}
                        </label>
                        @if($setting->description)
                        <p class="text-xs text-gray-400 mt-1">{{ $setting->description }}</p>
                        @endif
                    </div>
                    
                    <div class="md:col-span-2">
                        @if($setting->type === 'text')
                            <input type="text" 
                                   name="{{ $setting->key }}" 
                                   id="{{ $setting->key }}"
                                   value="{{ old($setting->key, $setting->value) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        
                        @elseif($setting->type === 'textarea')
                            <textarea name="{{ $setting->key }}" 
                                      id="{{ $setting->key }}"
                                      rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">{{ old($setting->key, $setting->value) }}</textarea>
                        
                        @elseif($setting->type === 'boolean')
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" 
                                       name="{{ $setting->key }}" 
                                       id="{{ $setting->key }}"
                                       value="1"
                                       {{ $setting->value ? 'checked' : '' }}
                                       class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                <span class="text-sm text-gray-700">Aktif</span>
                            </label>
                        
                        @elseif($setting->type === 'image')
                            <div class="space-y-3">
                                @if($setting->value)
                                <div class="flex items-center gap-4">
                                    <img src="{{ asset('storage/' . $setting->value) }}" 
                                         alt="{{ $setting->label }}"
                                         class="h-16 w-16 object-contain bg-gray-100 rounded-lg border">
                                    <label class="flex items-center gap-2 text-sm text-red-600 cursor-pointer">
                                        <input type="checkbox" name="remove_{{ $setting->key }}" value="1" class="rounded">
                                        Hapus gambar
                                    </label>
                                </div>
                                @endif
                                <input type="file" 
                                       name="{{ $setting->key }}" 
                                       id="{{ $setting->key }}"
                                       accept="image/*"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Submit Button -->
    <div class="mt-6 flex justify-end">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold">
            💾 Simpan Pengaturan
        </button>
    </div>
</form>

<!-- Preview Section -->
<div class="mt-8 bg-blue-50 rounded-lg p-6">
    <h3 class="font-semibold text-blue-800 mb-3">💡 Tips:</h3>
    <ul class="text-sm text-blue-700 space-y-1">
        <li>• <strong>Nama Aplikasi</strong> akan muncul di header, judul tab, dan laporan</li>
        <li>• <strong>Logo</strong> akan muncul di sidebar dan halaman login</li>
        <li>• <strong>Informasi Organisasi</strong> akan muncul di halaman About dan laporan cetak</li>
        <li>• Perubahan langsung terlihat setelah menyimpan</li>
    </ul>
</div>

<!-- Version Info -->
<div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b">
        <h2 class="text-lg font-semibold text-gray-800">ℹ️ Informasi Aplikasi</h2>
        <p class="text-sm text-gray-500">Detail versi dan informasi sistem</p>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Current Version -->
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">🚀</span>
                    </div>
                    <div>
                        <p class="text-sm text-green-600 font-medium">Versi Aplikasi</p>
                        <p class="text-2xl font-bold text-green-700">v{{ config('version.version') }}</p>
                    </div>
                </div>
                <div class="text-sm text-green-700 space-y-1">
                    <p><strong>Codename:</strong> {{ config('version.name') }}</p>
                    <p><strong>Tanggal Rilis:</strong> {{ \Carbon\Carbon::parse(config('version.release_date'))->format('d F Y') }}</p>
                </div>
            </div>
            
            <!-- System Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">⚙️</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Informasi Sistem</p>
                        <p class="text-lg font-bold text-gray-700">Laravel {{ app()->version() }}</p>
                    </div>
                </div>
                <div class="text-sm text-gray-600 space-y-1">
                    <p><strong>PHP:</strong> {{ phpversion() }}</p>
                    <p><strong>Environment:</strong> {{ ucfirst(app()->environment()) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Version History -->
        <div class="mt-6">
            <h3 class="font-semibold text-gray-700 mb-3">📋 Riwayat Versi</h3>
            <div class="space-y-3">
                @foreach(config('version.history') as $ver => $info)
                <div class="border rounded-lg p-4 {{ $loop->first ? 'border-green-300 bg-green-50' : 'border-gray-200' }}">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="font-bold {{ $loop->first ? 'text-green-700' : 'text-gray-700' }}">v{{ $ver }}</span>
                            <span class="text-sm px-2 py-0.5 rounded {{ $loop->first ? 'bg-green-200 text-green-800' : 'bg-gray-200 text-gray-600' }}">
                                {{ $info['name'] }}
                            </span>
                            @if($loop->first)
                            <span class="text-xs px-2 py-0.5 bg-green-600 text-white rounded">CURRENT</span>
                            @endif
                        </div>
                        <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($info['date'])->format('d M Y') }}</span>
                    </div>
                    <ul class="text-sm {{ $loop->first ? 'text-green-700' : 'text-gray-600' }} space-y-1">
                        @foreach($info['highlights'] as $highlight)
                        <li>• {{ $highlight }}</li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
