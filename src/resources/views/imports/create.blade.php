@extends('layouts.app')

@section('title', 'Import Data Baru - Inventory Masjid')

@section('content')
<div class="max-w-2xl mx-auto">
    <x-breadcrumb :items="[['label' => 'Import', 'url' => route('imports.index')], ['label' => 'Import Baru']]" />
    <div class="mb-6">
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">📥 Import Data Baru</h1>

        <form action="{{ route('imports.preview') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Data *</label>
                    <select name="type" id="type" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="">-- Pilih Tipe Data --</option>
                        <option value="items" {{ old('type') == 'items' ? 'selected' : '' }}>Barang</option>
                        <option value="categories" {{ old('type') == 'categories' ? 'selected' : '' }}>Kategori</option>
                        <option value="locations" {{ old('type') == 'locations' ? 'selected' : '' }}>Lokasi</option>
                    </select>
                    @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">File CSV/Excel *</label>
                    <input type="file" name="file" id="file" required accept=".csv,.xlsx,.xls,.txt"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                    <p class="text-xs text-gray-500 mt-1">Format: CSV, Excel (max 5MB)</p>
                    @error('file')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                <a href="{{ route('imports.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold">Batal</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">Preview Data</button>
            </div>
        </form>
    </div>

    <!-- Download Templates -->
    <div class="bg-blue-50 rounded-lg p-4 mt-6">
        <h3 class="font-semibold text-blue-800 mb-3">📄 Download Template</h3>
        <p class="text-sm text-blue-700 mb-3">Download template CSV untuk format yang benar:</p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('imports.template', 'items') }}" class="bg-white hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-sm border border-blue-200">
                📦 Template Barang
            </a>
            <a href="{{ route('imports.template', 'categories') }}" class="bg-white hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-sm border border-blue-200">
                📁 Template Kategori
            </a>
            <a href="{{ route('imports.template', 'locations') }}" class="bg-white hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-sm border border-blue-200">
                📍 Template Lokasi
            </a>
        </div>
    </div>

    <!-- Info -->
    <div class="bg-yellow-50 rounded-lg p-4 mt-4">
        <h3 class="font-semibold text-yellow-800 mb-2">⚠️ Perhatian</h3>
        <ul class="text-sm text-yellow-700 space-y-1">
            <li>• Baris pertama file harus berisi header kolom</li>
            <li>• Untuk import barang, kategori & lokasi yang belum ada akan otomatis dibuat</li>
            <li>• Data duplikat (kategori/lokasi dengan nama sama) akan dilewati</li>
            <li>• Pastikan format data sesuai template</li>
        </ul>
    </div>
</div>
@endsection
