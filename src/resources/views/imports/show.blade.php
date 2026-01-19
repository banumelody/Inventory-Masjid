@extends('layouts.app')

@section('title', 'Detail Import - Inventory Masjid')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('imports.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali ke Riwayat</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $import->filename }}</h1>
                <p class="text-gray-500">Import {{ $import->type_label }}</p>
            </div>
            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-{{ $import->status_color }}-100 text-{{ $import->status_color }}-800">
                {{ $import->status_label }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-gray-700">{{ $import->total_rows }}</div>
                <div class="text-xs text-gray-500">Total Baris</div>
            </div>
            <div class="bg-green-50 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-green-700">{{ $import->success_rows }}</div>
                <div class="text-xs text-green-600">Berhasil</div>
            </div>
            <div class="bg-red-50 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-red-700">{{ $import->failed_rows }}</div>
                <div class="text-xs text-red-600">Gagal</div>
            </div>
            <div class="bg-blue-50 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-blue-700">{{ $import->total_rows > 0 ? round(($import->success_rows / $import->total_rows) * 100) : 0 }}%</div>
                <div class="text-xs text-blue-600">Sukses Rate</div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Diimport oleh</label>
                    <p class="text-gray-900">{{ $import->user?->name ?? 'System' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Waktu Import</label>
                    <p class="text-gray-900">{{ $import->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        </div>

        @if($import->errors && count($import->errors) > 0)
        <div class="border-t mt-6 pt-6">
            <h2 class="text-lg font-semibold text-red-800 mb-4">❌ Error Log ({{ count($import->errors) }})</h2>
            <div class="bg-red-50 rounded-lg p-4 max-h-64 overflow-y-auto">
                <ul class="space-y-1 text-sm text-red-700">
                    @foreach($import->errors as $error)
                    <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
