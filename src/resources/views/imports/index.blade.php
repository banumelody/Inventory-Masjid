@extends('layouts.app')

@section('title', 'Import Data - Inventory Masjid')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4 md:mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800">📥 Import Data</h1>
    <a href="{{ route('imports.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-semibold text-center touch-target">
        + Import Baru
    </a>
</div>

<!-- Import History -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-4 py-3 border-b bg-gray-50">
        <h2 class="font-semibold text-gray-700">Riwayat Import</h2>
    </div>
    
    <div class="divide-y divide-gray-200">
        @forelse($imports as $import)
        <div class="p-4 hover:bg-gray-50">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-medium text-gray-900">{{ $import->filename }}</span>
                        <span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-600">{{ $import->type_label }}</span>
                        <span class="px-2 py-0.5 text-xs rounded bg-{{ $import->status_color }}-100 text-{{ $import->status_color }}-700">
                            {{ $import->status_label }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                        <span>{{ $import->user?->name ?? 'System' }}</span>
                        <span>{{ $import->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="mt-2 text-sm">
                        <span class="text-green-600">✓ {{ $import->success_rows }} berhasil</span>
                        @if($import->failed_rows > 0)
                        <span class="text-red-600 ml-3">✗ {{ $import->failed_rows }} gagal</span>
                        @endif
                        <span class="text-gray-400 ml-3">dari {{ $import->total_rows }} data</span>
                    </div>
                </div>
                <a href="{{ route('imports.show', $import) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">
            <div class="text-4xl mb-2">📥</div>
            <p>Belum ada riwayat import.</p>
            <a href="{{ route('imports.create') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800">Import data sekarang →</a>
        </div>
        @endforelse
    </div>
</div>

<div class="mt-4">
    {{ $imports->links() }}
</div>
@endsection
