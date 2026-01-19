@extends('layouts.app')

@section('title', 'Scan Logs - Inventory Masjid')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">📷 Riwayat Scan QR</h1>
    <p class="text-gray-500 mt-1">Catatan semua aktivitas scan QR code barang</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Total Scan</p>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_scans']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Hari Ini</p>
        <p class="text-2xl font-bold text-green-600">{{ number_format($stats['today_scans']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Minggu Ini</p>
        <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['week_scans']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Barang Unik</p>
        <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['unique_items_scanned']) }}</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm text-gray-600 mb-1">Barang</label>
            <select name="item_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Barang</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Tujuan</label>
            <select name="purpose" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Tujuan</option>
                @foreach(\App\Models\ScanLog::PURPOSES as $key => $label)
                    <option value="{{ $key }}" {{ request('purpose') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                Filter
            </button>
            <a href="{{ route('scan-logs.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Export Button -->
<div class="flex justify-end mb-4">
    <a href="{{ route('scan-logs.export', request()->query()) }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
        📥 Export CSV
    </a>
</div>

<!-- Scan Logs Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    @if($scanLogs->count() > 0)
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tujuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($scanLogs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $log->scanned_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($log->item)
                                <a href="{{ route('items.show', $log->item) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    {{ $log->item->name }}
                                </a>
                                <p class="text-xs text-gray-400 font-mono">{{ $log->item->qr_code_key }}</p>
                            @else
                                <span class="text-gray-400">Item dihapus</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($log->user)
                                {{ $log->user->name }}
                            @else
                                <span class="text-gray-400">Guest</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->purpose)
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $log->purpose === 'audit' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $log->purpose === 'check' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $log->purpose === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $log->purpose === 'other' ? 'bg-gray-100 text-gray-800' : '' }}
                                ">
                                    {{ $log->purpose_label }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                            {{ $log->notes ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400 font-mono">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-gray-200">
            @foreach($scanLogs as $log)
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        @if($log->item)
                            <a href="{{ route('items.show', $log->item) }}" class="text-blue-600 font-medium">
                                {{ $log->item->name }}
                            </a>
                        @else
                            <span class="text-gray-400">Item dihapus</span>
                        @endif
                    </div>
                    <span class="text-xs text-gray-400">{{ $log->scanned_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex flex-wrap gap-2 text-sm">
                    <span class="text-gray-500">👤 {{ $log->user->name ?? 'Guest' }}</span>
                    @if($log->purpose)
                        <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100">{{ $log->purpose_label }}</span>
                    @endif
                </div>
                @if($log->notes)
                    <p class="text-sm text-gray-500 mt-2">{{ $log->notes }}</p>
                @endif
            </div>
            @endforeach
        </div>
    @else
        <div class="p-8 text-center text-gray-500">
            <span class="text-4xl">📷</span>
            <p class="mt-2">Belum ada riwayat scan</p>
        </div>
    @endif
</div>

<!-- Pagination -->
@if($scanLogs->hasPages())
<div class="mt-4">
    {{ $scanLogs->links() }}
</div>
@endif
@endsection
